<?php

namespace TomatoPHP\FilamentDiscord\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;

class NotifyDiscordJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ?string $webhook;
    public ?string $title;
    public ?string $message;
    public ?string $url;
    public ?string $image;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(array $arg)
    {
        $this->webhook = $arg['webhook'];
        $this->title = $arg['title'];
        $this->message  = $arg['message'];
        $this->url  = $arg['url'];
        $this->image  = $arg['image'];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $embeds = [];
        if($this->message){
            $embeds = [
                'title' => $this->title,
                'description' => $this->message,
            ];
        }

        if($this->url && !$this->message){
            $embeds = [
                'title' => $this->title,
            ];
        }

        if($this->url){
            $embeds['url'] = $this->url;
        }

        if($this->image){
            $embeds['image'] = [
                'url' => $this->image
            ];
        }


        if(count($embeds)> 0){
            $params = [
                'content' => "@everyone",
                'embeds' => [
                    $embeds
                ]
            ];
        }
        else {
            $params = [
                'content' => $this->title,
            ];
        }

        Http::post($this->webhook ?: config('filament-discord.webhook'), $params)->json();
    }
}
