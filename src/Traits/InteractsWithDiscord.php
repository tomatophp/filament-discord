<?php

namespace TomatoPHP\FilamentDiscord\Traits;

use TomatoPHP\FilamentDiscord\Jobs\NotifyDiscordJob;

trait InteractsWithDiscord
{
    /**
     * Send a message to the model's own Discord webhook (the `webhook` column),
     * falling back to the given webhook and then to `filament-discord.webhook`.
     */
    public function notifyDiscord(
        string $title,
        ?string $message = null,
        ?string $url = null,
        ?string $image = null,
        ?string $webhook = null
    ): void {
        dispatch(new NotifyDiscordJob([
            'webhook' => $this->webhook ?: $webhook,
            'title' => $title,
            'message' => $message,
            'url' => $url,
            'image' => $image,
        ]));
    }
}
