<?php

namespace App\Classes;

use App\Models\Artist;
use Illuminate\Support\Arr;

class RefreshArtist
{
    protected Artist $artist;

    protected array $map = [
        'music' => 'fetchMusic',
        'posts' => 'fetchPosts',
        'videos' => 'fetchVideos',
        'design' => 'fetchDesign',
    ];

    //Defines abnormal pluralized words
    protected array $relationshipMap = [
        'music' => 'music',
        'design' => 'design',
    ];

    public function __construct(Artist $artist)
    {
        $this->artist = $artist;
    }

    /**
     * Main entry point: refresh based on flags
     */
    public function refresh(array $flags): void
    {
        foreach ($flags as $type => $needsRefresh) {
            if ($needsRefresh && isset($this->map[$type])) {
                $method = $this->map[$type];
                if (method_exists($this, $method)) {
                    $changed = $this->{$method}();

                    if ($changed) {
                        $column = "{$type}_requires_refresh";
                        $this->artist->update([$column => false]);
                    }
                }
            }
        }
    }

    // ------------------------------
    // Fetch Methods
    // ------------------------------

    public function fetchMusic(): bool
    {
        $items = $this->fetchFromApi('music');
        if (!$items) return false;

        $current = $this->artist->music;
        $changed = false;

        foreach ($items as $item) {
            $attributes = (array)$item;
            $externalId = $attributes['id'] ?? null;
            if (!$externalId) continue;

            $existing = $current->firstWhere('external_site_id', $externalId);

            if ($this->syncItem($existing, $attributes, 'music')) {
                $changed = true;
            }
        }

        return $changed;
    }

    public function fetchPosts(): bool
    {
        $items = $this->fetchFromApi('news');
        if (!$items) return false;

        $current = $this->artist->posts;
        $changed = false;

        foreach ($items as $item) {
            $attributes = (array)$item;
            $externalId = $attributes['id'] ?? null;
            if (!$externalId) continue;

            if (!empty($attributes['video_id']) && !empty($attributes['image'])) {
                $videoId = $attributes['image'];
                $attributes['image'] = "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg";
            }

            $existing = $current->firstWhere('external_site_id', $externalId);

            if ($this->syncItem($existing, $attributes, 'post')) {
                $changed = true;
            }
        }

        return $changed;
    }

    public function fetchVideos(): bool
    {
        $attributes = $this->fetchSingle('video');
        if ($attributes) {
            $this->artist->update($attributes);
            return true;
        }

        return false;
    }

    public function fetchDesign(): bool
    {
        $designData = $this->fetchSingle('design');

        if (!$designData || !is_array($designData)) {
            return false;
        }

        $changed = false;

        $currentEnv = config('app.env');

        // pick the 'other' env based on current
        $otherEnv = $currentEnv === 'production' ? 'staging' : 'production';

        $envs = [$currentEnv, $otherEnv];

        foreach ($designData as $device => $deviceData) {
            $deviceData = (array) $deviceData;

            $pageStyleData = [
                'image' => $deviceData['image'] ?? null,
                'image_position' => $deviceData['image_position'] ?? 'object-center',
                'image_custom_position_x' => $deviceData['image_custom_position_x'] ?? null,
                'image_custom_position_y' => $deviceData['image_custom_position_y'] ?? null,
                'image_extension' => $deviceData['image_extension'] ?? null,
                'device_type' => $device,
            ];

            foreach ($envs as $env) {
                $model = $this->artist->pageStyles()->updateOrCreate(
                    [
                        'device_type' => $device,
                        'env' => $env,
                    ],
                    $pageStyleData
                );

                if ($model->wasRecentlyCreated || $model->wasChanged()) {
                    $changed = true;
                }
            }
        }

        return $changed;
    }

    // ------------------------------
    // Helper Methods
    // ------------------------------

    /**
     * Fetches an array of items from the API
     */
    protected function fetchFromApi(string $endpoint): ?array
    {
        $url = $this->artist->url;
        $token = $this->artist->token;

        if (!$url || !$token) return null;

        $response = $this->runCurl("{$url}/api/{$token}/{$endpoint}");
        if (!$response) return null;

        return is_array($response) ? $response : [$response];
    }

    /**
     * Fetch a single object from API (for design, video)
     */
    protected function fetchSingle(string $endpoint): ?array
    {
        $items = $this->fetchFromApi($endpoint);
        if (!$items || !isset($items[0])) return null;

        return (array)$items[0];
    }

    /**
     * Sync an individual item with the database
     */
    protected function syncItem($existing, array $attributes, string $type): bool
    {
        // Capture the original ID from API
        $externalId = $attributes['id'] ?? null;

        // Then remove unwanted keys
        $unsetKeys = ['id', 'api_access'];
        if ($type === 'music') {
            $unsetKeys = array_merge($unsetKeys, [
                'staging_music_id', 'banner_image', 'banner_image_alt_text', 'available_for_meta_data', 'include_in_banner'
            ]);

            if (isset($attributes['released'])) {
                $attributes['release_date'] = $attributes['released'];
                unset($attributes['released']);
            }
            if (isset($attributes['presave'])) {
                $attributes['presave_date'] = $attributes['presave'];
                unset($attributes['presave']);
            }
        } elseif ($type === 'post') {
            $unsetKeys = array_merge($unsetKeys, [
                'music_id', 'video_id', 'hide_on_frontpage'
            ]);
        }

        $attributes = Arr::except($attributes, $unsetKeys);

        // Now safely assign external_site_id
        if (!isset($attributes['external_site_id']) && $externalId) {
            $attributes['external_site_id'] = $externalId;
        }

        $attributes['from_api'] = true;

        if ($existing) {
            $fetchedTime = strtotime($attributes['updated_at'] ?? now());
            $oldTime = strtotime($existing->updated_at->toDateTimeString());

            if ($fetchedTime !== $oldTime) {
                $existing->update($attributes);
                return true; // UPDATED
            }

            return false; // no change
        }

        $relation = $this->relationshipMap[$type] ?? $type . 's';
        $this->artist->{$relation}()->create($attributes);

        return true;
    }

    /**
     * Runs a cURL request and decodes JSON
     */
    protected function runCurl(string $url): mixed
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

        if (curl_error($ch)) {
            logger()->error('Curl error fetching artist data', [
                'url' => $url,
                'error' => curl_error($ch)
            ]);
            curl_close($ch);
            return null;
        }

        curl_close($ch);

        return json_decode($response);
    }
}