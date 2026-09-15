<?php

namespace TomatoPHP\FilamentDiscord;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use TomatoPHP\FilamentDiscord\Jobs\NotifyDiscordJob;

class FilamentDiscordServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/filament-discord.php', 'filament-discord');

        $this->publishes([
            __DIR__ . '/../config/filament-discord.php' => config_path('filament-discord.php'),
        ], 'filament-discord-config');
    }

    public function boot(): void
    {
        Notification::macro('sendToDiscord', function (?Model $user = null, ?string $image = null): static {
            /** @var Notification $this */
            $firstAction = collect($this->getActions())->first(fn (mixed $action): bool => $action instanceof Action);
            $title = (string) $this->getTitle();
            $message = filled($this->getBody()) ? (string) $this->getBody() : null;
            $url = $firstAction?->getUrl();

            if ($user) {
                $user->notifyDiscord(
                    title: $title,
                    message: $message,
                    url: $url,
                    image: $image,
                    webhook: config('filament-discord.webhook'),
                );

                return $this;
            }

            dispatch(new NotifyDiscordJob([
                'title' => $title,
                'message' => $message,
                'url' => $url,
                'webhook' => config('filament-discord.webhook'),
                'image' => $image,
            ]));

            return $this;
        });
    }
}
