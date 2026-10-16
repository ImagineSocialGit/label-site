<?php

namespace Tests\Feature\Syndication;

use App\Actions\Artist\SyncArtistPublishedContentAction;
use App\Jobs\Syndication\SyncArtistPublishedContentJob;
use App\Models\Artist;
use App\Models\Label;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PublishedContentChangedNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_notification_queues_authoritative_reconciliation(): void
    {
        Queue::fake();

        $artist = $this->artist();

        $this
            ->withHeaders([
                'X-API-TOKEN' => 'artist-secret',
                'X-SITE-URL' => 'https://artist.example.test',
            ])
            ->postJson(
                '/api/v1/published-content-changed',
                $this->notificationPayload('b'),
            )
            ->assertStatus(202);

        Queue::assertPushed(
            SyncArtistPublishedContentJob::class,
            static fn (SyncArtistPublishedContentJob $job): bool =>
                $job->artistId === $artist->getKey()
                && $job->notifiedRevision === str_repeat('b', 64),
        );
    }

    public function test_notification_authentication_failure_is_hidden(): void
    {
        Queue::fake();

        $this->artist();

        $this
            ->withHeaders([
                'X-API-TOKEN' => 'wrong-secret',
                'X-SITE-URL' => 'https://artist.example.test',
            ])
            ->postJson(
                '/api/v1/published-content-changed',
                $this->notificationPayload('b'),
            )
            ->assertNotFound();

        Queue::assertNothingPushed();
    }

    public function test_notification_for_current_revision_is_accepted_without_queueing(): void
    {
        Queue::fake();

        $artist = $this->artist();
        $artist->forceFill([
            'syndication_revision' => str_repeat('b', 64),
        ])->save();

        $this
            ->withHeaders([
                'X-API-TOKEN' => 'artist-secret',
                'X-SITE-URL' => 'https://artist.example.test',
            ])
            ->postJson(
                '/api/v1/published-content-changed',
                $this->notificationPayload('b'),
            )
            ->assertStatus(202);

        Queue::assertNothingPushed();
    }

    public function test_queued_notification_reuses_existing_snapshot_reconciler(): void
    {
        $artist = $this->artist();

        Http::fake([
            'https://artist.example.test/api/v1/published-content' =>
                Http::response([
                    'schema' => 'engage-artist-sites.published-content',
                    'version' => 1,
                    'source' => [
                        'name' => 'Example Artist',
                        'url' => 'https://artist.example.test',
                    ],
                    'capabilities' => [
                        'design' => false,
                        'music' => true,
                        'news' => true,
                        'videos' => true,
                    ],
                    'design' => null,
                    'music' => [],
                    'news' => [],
                    'videos' => [],
                    'revision' => str_repeat('c', 64),
                    'generated_at' => now()->toIso8601String(),
                ], 200),
        ]);

        $job = new SyncArtistPublishedContentJob(
            $artist->getKey(),
            str_repeat('c', 64),
        );

        $job->handle(app(SyncArtistPublishedContentAction::class));

        $this->assertSame(
            str_repeat('c', 64),
            $artist->fresh()->syndication_revision,
        );
        $this->assertNull($artist->fresh()->syndication_last_error);
    }

    public function test_authenticated_callback_passes_without_browser_csrf_token(): void
    {
        $this->enforceCsrfChecks();
        Queue::fake();

        $artist = $this->artist();

        $this
            ->withHeaders([
                'X-API-TOKEN' => 'artist-secret',
                'X-SITE-URL' => 'https://artist.example.test',
            ])
            ->postJson(
                '/api/v1/published-content-changed',
                $this->notificationPayload('e'),
            )
            ->assertStatus(202);

        Queue::assertPushed(
            SyncArtistPublishedContentJob::class,
            static fn (SyncArtistPublishedContentJob $job): bool =>
                $job->artistId === $artist->getKey(),
        );
    }

    public function test_callback_still_rejects_bad_credentials_when_csrf_is_exempt(): void
    {
        $this->enforceCsrfChecks();
        Queue::fake();
        $this->artist();

        $this
            ->withHeaders([
                'X-API-TOKEN' => 'wrong-secret',
                'X-SITE-URL' => 'https://artist.example.test',
            ])
            ->postJson(
                '/api/v1/published-content-changed',
                $this->notificationPayload('e'),
            )
            ->assertNotFound();

        Queue::assertNothingPushed();
    }

    public function test_regular_admin_post_still_requires_csrf_token(): void
    {
        $this->enforceCsrfChecks();

        $this
            ->post('/admin-'.config('app.initials').'/login', [])
            ->assertStatus(419);
    }

    private function artist(): Artist
    {
        $label = Label::query()->create([
            'name' => 'Example Label',
            'is_sublabel' => false,
        ]);

        return Artist::query()->create([
            'label_id' => $label->getKey(),
            'name' => 'Example Artist',
            'slug' => 'example-artist',
            'url' => 'https://artist.example.test/',
            'order' => 1,
            'token' => 'artist-secret',
            'syndication_enabled' => true,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function notificationPayload(string $revisionCharacter): array
    {
        return [
            'schema' => 'engage-artist-sites.published-content.changed',
            'version' => 1,
            'source' => [
                'name' => 'Example Artist',
                'url' => 'https://artist.example.test',
            ],
            'revision' => str_repeat($revisionCharacter, 64),
            'hint' => 'music',
            'changed_at' => now()->toIso8601String(),
        ];
    }
    /**
     * Laravel disables CSRF verification automatically in the test runtime.
     * Enforce it here so this test exercises the deployment behavior.
     */
    private function enforceCsrfChecks(): void
    {
        $this->app->bind(
            ValidateCsrfToken::class,
            static fn ($app): ValidateCsrfToken => new class($app, $app['encrypter']) extends ValidateCsrfToken
            {
                protected function runningUnitTests(): bool
                {
                    return false;
                }
            },
        );
    }
}