<?php

namespace App\Actions\Artist;

use App\Integrations\ArtistSites\ArtistSitesPublishedContentClient;
use App\Models\Artist;
use App\Models\Music;
use App\Models\PageStyle;
use App\Models\Post;
use App\Models\Video;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

final class SyncArtistPublishedContentAction
{
    public function __construct(
        private readonly ArtistSitesPublishedContentClient $client,
    ) {
    }

    public function handle(Artist $artist): bool
    {
        $artist->forceFill([
            'syndication_last_attempt_at' => now(),
        ])->save();

        try {
            $payload = $this->client->fetch($artist);
            $revision = (string) $payload['revision'];

            if ($artist->syndication_revision !== null
                && hash_equals($artist->syndication_revision, $revision)
            ) {
                $this->markSuccessful($artist, $revision);

                return false;
            }

            DB::transaction(function () use ($artist, $payload, $revision): void {
                $this->reconcileMusic($artist, $payload['music']);
                $this->reconcileNews($artist, $payload['news']);
                $this->reconcileVideos($artist, $payload['videos']);
                $this->reconcileDesign($artist, $payload['design'] ?? null);

                $this->markSuccessful($artist, $revision);
            });

            return true;
        } catch (Throwable $exception) {
            $artist->refresh();

            $artist->forceFill([
                'syndication_last_error' => Str::limit(
                    $exception->getMessage(),
                    1000,
                    '',
                ),
            ])->save();

            throw $exception;
        }
    }

    /**
     * @param  list<mixed>  $items
     */
    private function reconcileMusic(Artist $artist, array $items): void
    {
        $keys = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                throw new RuntimeException('Published music item is not an object.');
            }

            $key = $this->requiredString($item, 'key', 'music');
            $keys[] = $key;

            Music::query()->updateOrCreate(
                [
                    'artist_id' => $artist->getKey(),
                    'source_key' => $key,
                ],
                [
                    'staging_music_id' => null,
                    'external_site_id' => null,
                    'title' => $this->requiredString($item, 'title', 'music'),
                    'artwork' => $this->requiredString(
                        $item,
                        'artwork_url',
                        'music',
                    ),
                    'artwork_alt_text' => $this->nullableString(
                        $item['artwork_alt_text'] ?? null,
                    ),
                    'banner_image' => $this->nullableString(
                        $item['banner_image_url'] ?? null,
                    ),
                    'banner_image_alt_text' => $this->nullableString(
                        $item['banner_image_alt_text'] ?? null,
                    ),
                    'is_holiday_release' => (bool) (
                        $item['is_holiday_release'] ?? false
                    ),
                    'link' => $this->nullableString($item['url'] ?? null),
                    'release_date' => $this->easternDate(
                        $item['release_at'] ?? null,
                        'music.release_at',
                    ),
                    'presave_date' => $this->nullableEasternDate(
                        $item['presave_at'] ?? null,
                    ),
                    'from_api' => true,
                    'env' => 'production',
                ],
            );
        }

        $this->deleteMissingProjectionRows(
            Music::query()
                ->where('artist_id', $artist->getKey())
                ->where('from_api', true),
            $keys,
        );
    }

    /**
     * @param  list<mixed>  $items
     */
    private function reconcileNews(Artist $artist, array $items): void
    {
        $keys = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                throw new RuntimeException('Published news item is not an object.');
            }

            $key = $this->requiredString($item, 'key', 'news');
            $keys[] = $key;

            Post::query()->updateOrCreate(
                [
                    'artist_id' => $artist->getKey(),
                    'source_key' => $key,
                ],
                [
                    'external_site_id' => null,
                    'title' => $this->requiredString($item, 'title', 'news'),
                    'slug' => $this->requiredString($item, 'slug', 'news'),
                    'subtitle_one' => $this->nullableString(
                        $item['subtitle_one'] ?? null,
                    ),
                    'subtitle_two' => $this->nullableString(
                        $item['subtitle_two'] ?? null,
                    ),
                    'image' => $this->nullableString(
                        $item['image_url'] ?? null,
                    ),
                    'image_alt_text' => $this->nullableString(
                        $item['image_alt_text'] ?? null,
                    ),
                    'image_position' => $this->nullableString(
                        $item['image_position'] ?? null,
                    ),
                    'body' => (string) ($item['body'] ?? ''),
                    'publish_date' => $this->easternDateTime(
                        $item['published_at'] ?? null,
                        'news.published_at',
                    ),
                    'from_api' => true,
                    'env' => 'production',
                ],
            );
        }

        $this->deleteMissingProjectionRows(
            Post::query()
                ->where('artist_id', $artist->getKey())
                ->where('from_api', true),
            $keys,
        );
    }

    /**
     * @param  list<mixed>  $items
     */
    private function reconcileVideos(Artist $artist, array $items): void
    {
        $keys = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                throw new RuntimeException('Published video item is not an object.');
            }

            $key = $this->requiredString($item, 'key', 'video');
            $keys[] = $key;

            Video::query()->updateOrCreate(
                [
                    'artist_id' => $artist->getKey(),
                    'source_key' => $key,
                ],
                [
                    'staging_video_id' => null,
                    'external_site_id' => null,
                    'service' => $this->requiredString(
                        $item,
                        'service',
                        'video',
                    ),
                    'title' => $this->requiredString(
                        $item,
                        'title',
                        'video',
                    ),
                    'description' => $this->nullableString(
                        $item['description'] ?? null,
                    ),
                    'link' => $this->requiredString($item, 'url', 'video'),
                    'release_date' => $this->easternDateTime(
                        $item['release_at'] ?? null,
                        'video.release_at',
                    ),
                    'is_holiday' => (bool) ($item['is_holiday'] ?? false),
                    'is_featured' => (bool) ($item['is_featured'] ?? false),
                    'from_api' => true,
                    'env' => 'production',
                ],
            );
        }

        $this->deleteMissingProjectionRows(
            Video::query()
                ->where('artist_id', $artist->getKey())
                ->where('from_api', true),
            $keys,
        );
    }

    /**
     * @param  array<string, mixed>|null  $design
     */
    private function reconcileDesign(
        Artist $artist,
        ?array $design,
    ): void {
        PageStyle::query()
            ->where('artist_id', $artist->getKey())
            ->where('from_api', true)
            ->delete();

        $hero = $design['hero'] ?? null;

        if (! is_array($hero)) {
            return;
        }

        foreach (['desktop', 'mobile'] as $device) {
            $style = $hero[$device] ?? null;

            if (! is_array($style)) {
                continue;
            }

            $image = $this->nullableString(
                $style['image_base_url'] ?? null,
            );

            if ($image === null) {
                continue;
            }

            foreach (['staging', 'production'] as $environment) {
                PageStyle::query()->create([
                    'artist_id' => $artist->getKey(),
                    'image' => $image,
                    'image_position' => $this->nullableString(
                        $style['image_position'] ?? null,
                    ) ?: 'object-center',
                    'image_custom_position_x' => $this->nullableString(
                        $style['image_custom_position_x'] ?? null,
                    ),
                    'image_custom_position_y' => $this->nullableString(
                        $style['image_custom_position_y'] ?? null,
                    ),
                    'image_extension' => $this->nullableString(
                        $style['image_extension'] ?? null,
                    ),
                    'device_type' => $device,
                    'from_api' => true,
                    'env' => $environment,
                ]);
            }
        }
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $query
     * @param  list<string>  $keys
     */
    private function deleteMissingProjectionRows(
        $query,
        array $keys,
    ): void {
        if ($keys === []) {
            $query->delete();

            return;
        }

        $query
            ->where(function ($missing) use ($keys): void {
                $missing
                    ->whereNull('source_key')
                    ->orWhereNotIn('source_key', $keys);
            })
            ->delete();
    }

    private function markSuccessful(
        Artist $artist,
        string $revision,
    ): void {
        $artist->forceFill([
            'syndication_revision' => $revision,
            'syndication_last_success_at' => now(),
            'syndication_last_error' => null,
            'music_requires_refresh' => false,
            'posts_requires_refresh' => false,
            'videos_requires_refresh' => false,
            'design_requires_refresh' => false,
        ])->save();
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function requiredString(
        array $item,
        string $key,
        string $type,
    ): string {
        $value = $this->nullableString($item[$key] ?? null);

        if ($value === null) {
            throw new RuntimeException(
                "Published {$type} item is missing [{$key}].",
            );
        }

        return $value;
    }

    private function nullableString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private function easternDate(
        mixed $value,
        string $field,
    ): string {
        return $this->parseDateTime($value, $field)->toDateString();
    }

    private function nullableEasternDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return $this->parseDateTime(
            $value,
            'music.presave_at',
        )->toDateString();
    }

    private function easternDateTime(
        mixed $value,
        string $field,
    ): string {
        return $this->parseDateTime($value, $field)->format('Y-m-d H:i:s');
    }

    private function parseDateTime(
        mixed $value,
        string $field,
    ): CarbonImmutable {
        if (! is_string($value) || trim($value) === '') {
            throw new RuntimeException(
                "Published content is missing [{$field}].",
            );
        }

        try {
            return CarbonImmutable::parse($value)
                ->setTimezone('America/New_York');
        } catch (Throwable $exception) {
            throw new RuntimeException(
                "Published content contains invalid [{$field}].",
                previous: $exception,
            );
        }
    }
}