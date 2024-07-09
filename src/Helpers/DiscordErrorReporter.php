<?php

namespace TomatoPHP\FilamentDiscord\Helpers;

use Throwable;
use TomatoPHP\FilamentDiscord\Jobs\NotifyDiscordJob;
use Illuminate\Foundation\Configuration\Exceptions;

class DiscordErrorReporter
{
    /**
     * @param Exceptions $exceptions
     * @return void
     */
    public static function make(Exceptions $exceptions): void
    {
        $exceptions->reportable(function (Throwable $e) {
            if(config('filament-discord.error-webhook-active')){
                try {
                    dispatch(new NotifyDiscordJob([
                        'webhook' => config('filament-discord.error-webhook'),
                        'title' => $e->getMessage(),
                        'message' => collect([
                            "File: ".$e->getFile(),
                            "Line: ".$e->getLine(),
                            "Time: ".\Carbon\Carbon::now()->toDateTimeString(),
                            "Trace: ```".str($e->getTraceAsString())->limit(2500) ."```",
                        ])->implode("\n"),
                        'url' => url()->current()
                    ]));
                }catch (\Exception $exception){
                    // do nothing
                }
            }
        });
    }
}
