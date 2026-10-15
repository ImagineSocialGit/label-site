<?php

namespace App\Console\Commands;

use App\Actions\Artist\SyncArtistPublishedContentAction;
use App\Models\Artist;
use Illuminate\Console\Command;
use Throwable;

final class SyncArtistPublishedContentCommand extends Command
{
    protected $signature = 'artists:sync
        {--artist= : Sync only the artist with this slug or numeric ID}';

    protected $description = 'Reconcile active Artist Sites sources into the label content projection';

    public function handle(
        SyncArtistPublishedContentAction $sync,
    ): int {
        $query = Artist::query()
            ->where('syndication_enabled', true)
            ->whereNotNull('url')
            ->whereNotNull('token')
            ->orderBy('order')
            ->orderBy('id');

        $selector = trim((string) $this->option('artist'));

        if ($selector !== '') {
            $query->where(function ($artistQuery) use ($selector): void {
                $artistQuery->where('slug', $selector);

                if (ctype_digit($selector)) {
                    $artistQuery->orWhereKey((int) $selector);
                }
            });
        }

        $artists = $query->get();

        if ($artists->isEmpty()) {
            $this->line('No active artist syndication sources matched.');

            return self::SUCCESS;
        }

        $failures = 0;

        foreach ($artists as $artist) {
            try {
                $changed = $sync->handle($artist);

                $this->line(
                    sprintf(
                        '%s: %s',
                        $artist->name,
                        $changed ? 'updated' : 'current',
                    ),
                );
            } catch (Throwable $exception) {
                $failures++;

                $this->error(
                    "{$artist->name}: {$exception->getMessage()}",
                );
            }
        }

        return $failures === 0
            ? self::SUCCESS
            : self::FAILURE;
    }
}