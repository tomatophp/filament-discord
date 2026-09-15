<?php

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use TomatoPHP\FilamentDiscord\Helpers\DiscordErrorReporter;

beforeEach(function () {
    Http::preventStrayRequests();
    Http::fake(['discord.test/*' => Http::response(null, 204)]);
});

it('does not report errors while the logger is inactive', function () {
    DiscordErrorReporter::report(new RuntimeException('Boom'));

    Http::assertNothingSent();
});

it('reports errors to the error webhook when active', function () {
    config()->set('filament-discord.error-webhook-active', true);

    DiscordErrorReporter::report(new RuntimeException('Boom'));

    Http::assertSentCount(1);
    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://discord.test/api/webhooks/errors'
        && $request['embeds'][0]['title'] === 'Boom'
        && str_contains($request['embeds'][0]['description'], 'File: ' . __FILE__));
});

it('does not report when the error webhook is empty', function () {
    config()->set('filament-discord.error-webhook-active', true);
    config()->set('filament-discord.error-webhook', null);

    DiscordErrorReporter::report(new RuntimeException('Boom'));

    Http::assertNothingSent();
});

it('never throws when Discord is unreachable', function () {
    config()->set('filament-discord.error-webhook-active', true);
    Http::fake(fn () => throw new ConnectionException('offline'));

    DiscordErrorReporter::report(new RuntimeException('Boom'));

    expect(true)->toBeTrue();
});

it('registers a reportable callback on the exceptions configuration', function () {
    config()->set('filament-discord.error-webhook-active', true);

    $handler = app(ExceptionHandler::class);
    DiscordErrorReporter::make(new Exceptions($handler));

    $handler->report(new RuntimeException('Reported through the handler'));

    Http::assertSent(fn (Request $request): bool => $request['embeds'][0]['title'] === 'Reported through the handler');
});
