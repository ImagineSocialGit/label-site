<?php

namespace App\Integrations\ArtistSites;

use App\Models\Artist;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

final class ArtistSitesPublishedContentClient
{
    /**
     * @return array<string, mixed>
     */
    public function fetch(Artist $artist): array
    {
        $baseUrl = rtrim(trim((string) $artist->url), '/');
        $token = trim((string) $artist->token);

        if (! $artist->syndication_enabled || $baseUrl === '' || $token === '') {
            throw new RuntimeException(
                "Artist [{$artist->name}] does not have an active syndication source.",
            );
        }

        $endpoint = $baseUrl.'/api/v1/published-content';

        try {
            $response = Http::acceptJson()
                ->withToken($token)
                ->timeout(15)
                ->retry(3, 250, throw: false)
                ->get($endpoint);
        } catch (Throwable $exception) {
            throw new RuntimeException(
                "Published-content request failed for [{$artist->name}].",
                previous: $exception,
            );
        }

        if (! $response->successful()) {
            throw new RuntimeException(
                "Published-content request for [{$artist->name}] returned HTTP {$response->status()}.",
            );
        }

        $payload = $response->json();

        if (! is_array($payload)) {
            throw new RuntimeException(
                "Published-content response for [{$artist->name}] was not a JSON object.",
            );
        }

        $this->assertContract($artist, $payload);

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function assertContract(Artist $artist, array $payload): void
    {
        if (($payload['schema'] ?? null) !== 'engage-artist-sites.published-content') {
            throw new RuntimeException(
                "Published-content response for [{$artist->name}] used an unsupported schema.",
            );
        }

        if ((int) ($payload['version'] ?? 0) !== 1) {
            throw new RuntimeException(
                "Published-content response for [{$artist->name}] used an unsupported version.",
            );
        }

        $revision = $payload['revision'] ?? null;

        if (! is_string($revision)
            || preg_match('/^[a-f0-9]{64}$/', $revision) !== 1
        ) {
            throw new RuntimeException(
                "Published-content response for [{$artist->name}] did not contain a valid revision.",
            );
        }

        foreach (['capabilities', 'source'] as $requiredObject) {
            if (! is_array($payload[$requiredObject] ?? null)) {
                throw new RuntimeException(
                    "Published-content response for [{$artist->name}] is missing [{$requiredObject}].",
                );
            }
        }

        foreach (['music', 'news', 'videos'] as $requiredList) {
            if (! is_array($payload[$requiredList] ?? null)
                || ! array_is_list($payload[$requiredList])
            ) {
                throw new RuntimeException(
                    "Published-content response for [{$artist->name}] is missing [{$requiredList}].",
                );
            }
        }

        if (($payload['design'] ?? null) !== null
            && ! is_array($payload['design'])
        ) {
            throw new RuntimeException(
                "Published-content response for [{$artist->name}] contains invalid design data.",
            );
        }
    }
}