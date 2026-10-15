<?php

use App\Console\Commands\SyncArtistPublishedContentCommand;
use App\Http\Middleware\ForceStagingLogin;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        SyncArtistPublishedContentCommand::class,
    ])
    ->withSchedule(function (Schedule $schedule): void {
        $schedule
            ->command('artists:sync')
            ->everyFifteenMinutes()
            ->withoutOverlapping();
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo('/error');
        $middleware->alias([
            'staging.auth' => ForceStagingLogin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();