<?php

namespace TomatoPHP\FilamentDiscord\Traits;


use TomatoPHP\FilamentDiscord\Jobs\NotifyDiscordJob;

trait InteractsWithDiscord
{

    /**
     * @param string $title
     * @param string|null $message
     * @param string|null $url
     * @param string|null $image
     * @param string|null $webhook
     * @return void
     */
    public function notifyDiscord(
        string $title,
        string $message=null,
        ?string $url=null,
        ?string $image=null,
        ?string $webhook=null
    ): void
    {
        dispatch(new NotifyDiscordJob([
            'webhook' => $this->webhook ?: $webhook,
            'title' => $title,
            'message' => $message,
            'url' => $url,
            'image' => $image,
        ]));
    }
}
