<?php

namespace Tests\Feature\Syndication;

use App\Actions\Artist\SyncArtistPublishedContentAction;
use App\Models\Artist;
use App\Models\Label;
use App\Models\Music;
use App\Models\PageStyle;
use App\Models\Post;
use App\Models\Video;
use App\Services\PageStyleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class ArtistPublishedContentSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_reconciles_authoritative_snapshot_and_preserves_local_content(): void
    {
        $artist = $this->artist();
        $localMusic = Music::query()->create([
            'artist_id' => $artist->getKey(),
            'title' => 'Label-owned Release',
            'artwork' => 'https://label.example/local.jpg',
            'release_date' => '2026-01-01',
            'from_api' => false,
            'env' => 'production',
        ]);

        Music::query()->create([
            'artist_id' => $artist->getKey(),
            'external_site_id' => 91,
            'title' => 'Legacy Projection',
            'artwork' => 'https://old.example/legacy.jpg',
            'release_date' => '2025-01-01',
            'from_api' => true,
            'env' => 'production',
        ]);

        Post::query()->create([
            'artist_id' => $artist->getKey(),
            'external_site_id' => 92,
            'title' => 'Removed Upstream',
            'slug' => 'removed-upstream',
            'body' => 'Old',
            'publish_date' => '2025-01-01 10:00:00',
            'from_api' => true,
            'env' => 'production',
        ]);

        Video::query()->create([
            'artist_id' => $artist->getKey(),
            'external_site_id' => 93,
            'service' => 'youtube',
            'title' => 'Removed Video',
            'link' => 'https://youtube.example/old',
            'release_date' => '2025-01-01 10:00:00',
            'from_api' => true,
            'env' => 'production',
        ]);

        PageStyle::query()->create([
            'artist_id' => $artist->getKey(),
            'device_type' => 'desktop',
            'image' => 'https://label.example/local-hero',
            'image_extension' => '.jpg',
            'from_api' => false,
            'env' => 'staging',
        ]);

        Http::fake([
            'https://artist.example.test/api/v1/published-content' => Http::response(
                $this->snapshot(),
            ),
        ]);

        $changed = app(SyncArtistPublishedContentAction::class)
            ->handle($artist);

        $this->assertTrue($changed);

        $this->assertDatabaseHas('music', [
            'artist_id' => $artist->getKey(),
            'source_key' => 'music:source-1',
            'title' => 'Current Release',
            'from_api' => true,
        ]);
        $this->assertDatabaseHas('music', [
            'id' => $localMusic->getKey(),
            'title' => 'Label-owned Release',
            'from_api' => false,
        ]);
        $this->assertDatabaseMissing('music', [
            'external_site_id' => 91,
        ]);

        $this->assertDatabaseHas('posts', [
            'artist_id' => $artist->getKey(),
            'source_key' => 'latest-news',
            'slug' => 'latest-news',
            'from_api' => true,
        ]);
        $this->assertDatabaseMissing('posts', [
            'external_site_id' => 92,
        ]);

        $this->assertDatabaseHas('videos', [
            'artist_id' => $artist->getKey(),
            'source_key' => 'video:source-1',
            'is_featured' => true,
            'from_api' => true,
        ]);
        $this->assertDatabaseMissing('videos', [
            'external_site_id' => 93,
        ]);

        $this->assertDatabaseCount('page_styles', 5);
        $this->assertDatabaseHas('page_styles', [
            'artist_id' => $artist->getKey(),
            'device_type' => 'desktop',
            'image' => 'https://cdn.example/hero/desktop',
            'from_api' => true,
            'env' => 'staging',
        ]);
        $this->assertDatabaseHas('page_styles', [
            'artist_id' => $artist->getKey(),
            'device_type' => 'mobile',
            'image' => 'https://cdn.example/hero/mobile',
            'from_api' => true,
            'env' => 'production',
        ]);

        $artist->refresh();

        $this->assertSame(str_repeat('a', 64), $artist->syndication_revision);
        $this->assertNotNull($artist->syndication_last_attempt_at);
        $this->assertNotNull($artist->syndication_last_success_at);
        $this->assertNull($artist->syndication_last_error);
        $this->assertFalse($artist->music_requires_refresh);
        $this->assertFalse($artist->posts_requires_refresh);
        $this->assertFalse($artist->videos_requires_refresh);
        $this->assertFalse($artist->design_requires_refresh);

        Http::assertSent(
            fn ($request): bool => $request->url()
                === 'https://artist.example.test/api/v1/published-content'
                && $request->hasHeader(
                    'Authorization',
                    'Bearer sync-token',
                ),
        );
    }

    public function test_unchanged_revision_records_success_without_rewriting_projection(): void
    {
        $artist = $this->artist([
            'syndication_revision' => str_repeat('a', 64),
        ]);

        $music = Music::query()->create([
            'artist_id' => $artist->getKey(),
            'source_key' => 'music:source-1',
            'title' => 'Locally Untouched Projection',
            'artwork' => 'https://cdn.example/art.jpg',
            'release_date' => '2026-01-01',
            'from_api' => true,
            'env' => 'production',
        ]);

        Http::fake([
            '*' => Http::response($this->snapshot()),
        ]);

        $changed = app(SyncArtistPublishedContentAction::class)
            ->handle($artist);

        $this->assertFalse($changed);
        $this->assertSame(
            'Locally Untouched Projection',
            $music->fresh()->title,
        );
        $this->assertNotNull(
            $artist->fresh()->syndication_last_success_at,
        );
    }

    public function test_failed_fetch_preserves_existing_projection_and_records_error(): void
    {
        $artist = $this->artist();

        $music = Music::query()->create([
            'artist_id' => $artist->getKey(),
            'source_key' => 'existing',
            'title' => 'Last Known Good',
            'artwork' => 'https://cdn.example/last-good.jpg',
            'release_date' => '2026-01-01',
            'from_api' => true,
            'env' => 'production',
        ]);

        Http::fake([
            '*' => Http::response([], 503),
        ]);

        try {
            app(SyncArtistPublishedContentAction::class)->handle($artist);
            $this->fail('Expected the sync request to fail.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString(
                'HTTP 503',
                $exception->getMessage(),
            );
        }

        $this->assertDatabaseHas('music', [
            'id' => $music->getKey(),
            'title' => 'Last Known Good',
        ]);
        $this->assertNull($artist->fresh()->syndication_revision);
        $this->assertNotNull(
            $artist->fresh()->syndication_last_error,
        );
    }

    public function test_source_design_has_rendering_precedence_without_deleting_local_fallback(): void
    {
        $artist = $this->artist();

        PageStyle::query()->create([
            'artist_id' => $artist->getKey(),
            'device_type' => 'desktop',
            'image' => 'https://label.example/local',
            'image_extension' => '.jpg',
            'from_api' => false,
            'env' => config('app.env'),
        ]);

        PageStyle::query()->create([
            'artist_id' => $artist->getKey(),
            'device_type' => 'desktop',
            'image' => 'https://artist.example/source',
            'image_extension' => '.webp',
            'from_api' => true,
            'env' => config('app.env'),
        ]);

        $styles = (new PageStyleService($artist))
            ->getStylesByDevice();

        $this->assertSame(
            'https://artist.example/source',
            $styles['desktop']['image'],
        );

        $artist->update([
            'syndication_enabled' => false,
        ]);

        $styles = (new PageStyleService($artist->fresh()))
            ->getStylesByDevice();

        $this->assertSame(
            'https://label.example/local',
            $styles['desktop']['image'],
        );
    }

    public function test_sync_command_processes_only_active_sources(): void
    {
        $active = $this->artist();
        $this->artist([
            'name' => 'Manual Artist',
            'slug' => 'manual-artist',
            'url' => 'https://manual.example.test',
            'token' => null,
            'syndication_enabled' => false,
            'order' => 2,
        ]);

        Http::fake([
            'https://artist.example.test/api/v1/published-content' => Http::response(
                $this->snapshot(),
            ),
        ]);

        $exitCode = Artisan::call('artists:sync');

        $this->assertSame(0, $exitCode);
        $this->assertSame(
            str_repeat('a', 64),
            $active->fresh()->syndication_revision,
        );

        Http::assertSentCount(1);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function artist(array $overrides = []): Artist
    {
        $label = Label::query()->firstOrCreate([
            'name' => 'Example Label',
        ]);

        return Artist::query()->create([
            'label_id' => $label->getKey(),
            'name' => 'Example Artist',
            'slug' => 'example-artist',
            'url' => 'https://artist.example.test',
            'order' => 1,
            'token' => 'sync-token',
            'syndication_enabled' => true,
            ...$overrides,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function snapshot(): array
    {
        return [
            'schema' => 'engage-artist-sites.published-content',
            'version' => 1,
            'source' => [
                'name' => 'Example Artist',
                'url' => 'https://artist.example.test',
            ],
            'capabilities' => [
                'design' => true,
                'music' => true,
                'news' => true,
                'videos' => true,
            ],
            'design' => [
                'hero' => [
                    'desktop' => [
                        'image_base_url' => 'https://cdn.example/hero/desktop',
                        'image_extension' => '.webp',
                        'image_position' => 'object-center',
                        'image_custom_position_x' => null,
                        'image_custom_position_y' => null,
                    ],
                    'mobile' => [
                        'image_base_url' => 'https://cdn.example/hero/mobile',
                        'image_extension' => '.webp',
                        'image_position' => 'object-top',
                        'image_custom_position_x' => null,
                        'image_custom_position_y' => null,
                    ],
                ],
            ],
            'music' => [
                [
                    'key' => 'music:source-1',
                    'title' => 'Current Release',
                    'url' => 'https://listen.example/current',
                    'artwork_url' => 'https://cdn.example/current.jpg',
                    'artwork_alt_text' => 'Current release artwork',
                    'banner_image_url' => null,
                    'banner_image_alt_text' => null,
                    'is_holiday_release' => false,
                    'release_at' => '2026-10-01T00:00:00-04:00',
                    'presave_at' => null,
                ],
            ],
            'news' => [
                [
                    'key' => 'latest-news',
                    'title' => 'Latest News',
                    'slug' => 'latest-news',
                    'subtitle_one' => null,
                    'subtitle_two' => null,
                    'image_url' => 'https://cdn.example/news.jpg',
                    'image_alt_text' => 'News',
                    'image_position' => 'object-center',
                    'body' => 'Published body',
                    'published_at' => '2026-10-01T10:00:00-04:00',
                ],
            ],
            'videos' => [
                [
                    'key' => 'video:source-1',
                    'service' => 'youtube',
                    'title' => 'Current Video',
                    'description' => 'Current video description',
                    'url' => 'https://youtube.example/current',
                    'release_at' => '2026-10-01T10:00:00-04:00',
                    'is_featured' => true,
                    'is_holiday' => false,
                ],
            ],
            'revision' => str_repeat('a', 64),
            'generated_at' => '2026-10-07T19:00:00+00:00',
        ];
    }
}