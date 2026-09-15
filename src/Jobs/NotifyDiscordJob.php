<?php

namespace TomatoPHP\FilamentDiscord\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class NotifyDiscordJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public ?string $webhook;

    public ?string $title;

    public ?string $message;

    public ?string $url;

    public ?string $image;

    /**
     * @param  array{webhook?: ?string, title?: ?string, message?: ?string, url?: ?string, image?: ?string}  $arg
     */
    public function __construct(array $arg)
    {
        $this->webhook = $arg['webhook'] ?? null;
        $this->title = $arg['title'] ?? null;
        $this->message = $arg['message'] ?? null;
        $this->url = $arg['url'] ?? null;
        $this->image = $arg['image'] ?? null;
    }

    public function handle(): void
    {
        $webhook = $this->webhook ?: config('filament-discord.webhook');

        if (blank($webhook)) {
            return;
        }

        Http::post($webhook, $this->payload());
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        $embed = [];

        if ($this->message) {
            $embed = [
                'title' => $this->title,
                'description' => $this->message,
            ];
        }

        if ($this->url && ! $this->message) {
            $embed = [
                'title' => $this->title,
            ];
        }

        if ($this->url) {
            $embed['url'] = $this->url;
        }

        if ($this->image) {
            $embed['title'] ??= $this->title;
            $embed['image'] = [
                'url' => $this->image,
            ];
        }

        if (count($embed) > 0) {
            return [
                'content' => '@everyone',
                'embeds' => [
                    $embed,
                ],
            ];
        }

        return [
            'content' => $this->title,
        ];
    }
}
