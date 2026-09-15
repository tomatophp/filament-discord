<?php

use Filament\Notifications\Notification;
use Illuminate\Support\ServiceProvider;
use TomatoPHP\FilamentDiscord\FilamentDiscordServiceProvider;

it('boots the service provider', function () {
    expect(app()->getProviders(FilamentDiscordServiceProvider::class))->not->toBeEmpty();
});

it('merges the package config', function () {
    expect(config('filament-discord'))
        ->toHaveKeys(['webhook', 'error-webhook-active', 'error-webhook']);
});

it('registers the sendToDiscord notification macro', function () {
    expect(Notification::hasMacro('sendToDiscord'))->toBeTrue();
});

it('publishes the config file', function () {
    $paths = ServiceProvider::pathsToPublish(FilamentDiscordServiceProvider::class, 'filament-discord-config');

    expect($paths)->toHaveCount(1)
        ->and(array_key_first($paths))->toEndWith('filament-discord.php')
        ->and(file_exists(array_key_first($paths)))->toBeTrue();
});
