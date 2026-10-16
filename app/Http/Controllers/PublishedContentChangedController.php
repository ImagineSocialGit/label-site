<?php

namespace App\Http\Controllers;

use App\Jobs\Syndication\SyncArtistPublishedContentJob;
use App\Models\Artist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PublishedContentChangedController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $schema = $request->input('schema');
        $version = $request->input('version');
        $sourceUrl = $request->input('source.url');
        $revision = $request->input('revision');
        $hint = $request->input('hint');

        if ($schema !== 'engage-artist-sites.published-content.changed'
            || (int) $version !== 1
            || ! is_string($sourceUrl)
            || $this->normalizeUrl($sourceUrl) === null
            || ! is_string($revision)
            || preg_match('/^[a-f0-9]{64}$/', $revision) !== 1
            || ! is_string($hint)
            || ! in_array($hint, ['music', 'news', 'videos', 'design'], true)
        ) {
            abort(404);
        }

        $headerSourceUrl = $request->header('X-SITE-URL');

        if (! is_string($headerSourceUrl)
            || $this->normalizeUrl($headerSourceUrl)
                !== $this->normalizeUrl($sourceUrl)
        ) {
            abort(404);
        }

        $artist = Artist::query()
            ->where('syndication_enabled', true)
            ->whereNotNull('url')
            ->whereNotNull('token')
            ->get()
            ->first(
                fn (Artist $candidate): bool =>
                    $this->normalizeUrl((string) $candidate->url)
                    === $this->normalizeUrl($sourceUrl),
            );

        if (! $artist) {
            abort(404);
        }

        $providedToken = $request->bearerToken();

        if (! is_string($providedToken) || trim($providedToken) === '') {
            $providedToken = $request->header('X-API-TOKEN');
        }

        $expectedToken = trim((string) $artist->token);
        $providedToken = trim((string) $providedToken);

        if ($expectedToken === ''
            || $providedToken === ''
            || ! hash_equals($expectedToken, $providedToken)
        ) {
            abort(404);
        }

        if (is_string($artist->syndication_revision)
            && hash_equals($artist->syndication_revision, $revision)
        ) {
            return response()->json([], 202);
        }

        SyncArtistPublishedContentJob::dispatch(
            $artist->getKey(),
            $revision,
        );

        return response()->json([], 202);
    }

    private function normalizeUrl(string $url): ?string
    {
        $url = trim($url);

        if ($url === '') {
            return null;
        }

        if (! str_contains($url, '://')) {
            $url = 'https://'.$url;
        }

        $parts = parse_url($url);

        if (! is_array($parts)) {
            return null;
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));

        if (! in_array($scheme, ['http', 'https'], true) || $host === '') {
            return null;
        }

        $port = isset($parts['port']) ? ':'.$parts['port'] : '';
        $path = trim((string) ($parts['path'] ?? ''), '/');

        return $scheme.'://'.$host.$port.($path !== '' ? '/'.$path : '');
    }
}