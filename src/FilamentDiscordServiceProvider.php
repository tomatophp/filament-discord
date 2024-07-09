<?php

namespace TomatoPHP\FilamentDiscord;

use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use TomatoPHP\FilamentDiscord\Jobs\NotifyDiscordJob;

class FilamentDiscordServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //Register Config file
        $this->mergeConfigFrom(__DIR__.'/../config/filament-discord.php', 'filament-discord');

        //Publish Config
        $this->publishes([
           __DIR__.'/../config/filament-discord.php' => config_path('filament-discord.php'),
        ], 'filament-discord-config');
    }

    public function boot(): void
    {
        Notification::macro('sendToDiscord', function (?Model $user=null, ?string $image=null): static
        {
            if($user){
                /** @var Notification $this */
                $user->notifyDiscord(
                    title: $this->title,
                    message: $this->body,
                    url: count($this->actions)? $this->actions[0]->getUrl()  : null,
                    webhook: config('filament-discord.webhook'),
                    image: $image
                );
            }
            else {
                dispatch(new NotifyDiscordJob([
                    "title" => $this->title,
                    "message"=> $this->body,
                    "url"=> count($this->actions)? $this->actions[0]->getUrl()  : null,
                    "webhook"=> config('filament-discord.webhook'),
                    "image" => $image
                ]));
            }


            return $this;
        });
    }
}
