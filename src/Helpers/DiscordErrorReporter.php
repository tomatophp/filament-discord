<?php

namespace TomatoPHP\FilamentDiscord\Helpers;

use Carbon\Carbon;
use Illuminate\Foundation\Configuration\Exceptions;
use Throwable;
use TomatoPHP\FilamentDiscord\Jobs\NotifyDiscordJob;

class DiscordErrorReporter
{
    public static function make(Exceptions $exceptions): void
    {
        $exceptions->reportable(function (Throwable $e): void {
            static::report($e);
        });
    }

    public static function report(Throwable $e): void
    {
        if (! config('filament-discord.error-webhook-active') || blank(config('filament-discord.error-webhook'))) {
            return;
        }

        try {
            dispatch(new NotifyDiscordJob([
                'webhook' => config('filament-discord.error-webhook'),
                'title' => $e->getMessage(),
                'message' => collect([
                    'File: ' . $e->getFile(),
                    'Line: ' . $e->getLine(),
                    'Time: ' . Carbon::now()->toDateTimeString(),
                    'Trace: ```' . str($e->getTraceAsString())->limit(2500) . '```',
                ])->implode("\n"),
                'url' => app()->runningInConsole() ? null : url()->current(),
            ]));
        } catch (Throwable) {
            // Never let the error reporter throw while reporting another error.
        }
    }
}
