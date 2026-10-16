<?php

namespace App\Jobs\Syndication;

use App\Actions\Artist\SyncArtistPublishedContentAction;
use App\Models\Artist;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class SyncArtistPublishedContentJob implements ShouldQueue, ShouldBeUniqueUntilProcessing
{
    use Queueable;

    public int $tries = 5;

    public int $uniqueFor = 300;

    public function __construct(
        public readonly int $artistId,
        public readonly string $notifiedRevision,
    ) {
        if (config('queue.default') === 'sync') {
            $this->onConnection('database');
        }
    }

    public function uniqueId(): string
    {
        return (string) $this->artistId;
    }

    /**
     * @return list<int>
     */
    public function backoff(): array
    {
        return [15, 60, 300, 900];
    }

    public function handle(SyncArtistPublishedContentAction $sync): void
    {
        $artist = Artist::query()->find($this->artistId);

        if (! $artist
            || ! (bool) $artist->syndication_enabled
            || blank($artist->url)
            || blank($artist->token)
        ) {
            return;
        }

        if (is_string($artist->syndication_revision)
            && hash_equals(
                $artist->syndication_revision,
                $this->notifiedRevision,
            )
        ) {
            return;
        }

        $sync->handle($artist);
    }
}