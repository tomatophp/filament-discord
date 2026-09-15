<?php

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use TomatoPHP\FilamentDiscord\Jobs\NotifyDiscordJob;
use TomatoPHP\FilamentDiscord\Tests\Models\User;

beforeEach(function () {
    Http::preventStrayRequests();
    Http::fake(['discord.test/*' => Http::response(null, 204)]);
});

it('sends a native Filament notification to the main webhook', function () {
    Notification::make()
        ->title('Hi')
        ->body('Welcome On The Moon!')
        ->sendToDiscord();

    Http::assertSentCount(1);
    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://discord.test/api/webhooks/main'
        && $request['content'] === '@everyone'
        && $request['embeds'][0]['title'] === 'Hi'
        && $request['embeds'][0]['description'] === 'Welcome On The Moon!');
});

it('attaches the image and the first action url', function () {
    Notification::make()
        ->title('Order shipped')
        ->body('Order #1 is on the way')
        ->actions([
            Action::make('view')->url('https://example.com/orders/1'),
        ])
        ->sendToDiscord(image: 'https://example.com/cover.jpg');

    Http::assertSent(fn (Request $request): bool => $request['embeds'][0]['url'] === 'https://example.com/orders/1'
        && $request['embeds'][0]['image']['url'] === 'https://example.com/cover.jpg');
});

it('sends a plain message when there is no body, url or image', function () {
    Notification::make()
        ->title('Just a title')
        ->sendToDiscord();

    Http::assertSent(fn (Request $request): bool => $request->data() === ['content' => 'Just a title']);
});

it('sends to the user webhook when a user is given', function () {
    $user = User::query()->create([
        'name' => 'Fady',
        'email' => 'fady@example.com',
        'password' => 'secret',
        'webhook' => 'https://discord.test/api/webhooks/user',
    ]);

    Notification::make()
        ->title('Hi')
        ->body('Only for you')
        ->sendToDiscord($user);

    Http::assertSentCount(1);
    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://discord.test/api/webhooks/user'
        && $request['embeds'][0]['description'] === 'Only for you');
});

it('falls back to the main webhook when the user has none', function () {
    $user = User::query()->create([
        'name' => 'Fady',
        'email' => 'fady@example.com',
        'password' => 'secret',
    ]);

    $user->notifyDiscord(title: 'Hi', message: 'Fallback');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://discord.test/api/webhooks/main');
});

it('does not call Discord when no webhook is configured', function () {
    config()->set('filament-discord.webhook', null);

    Notification::make()
        ->title('Hi')
        ->body('Nobody listens')
        ->sendToDiscord();

    Http::assertNothingSent();
});

it('returns the notification so it can still be sent to the database', function () {
    $notification = Notification::make()->title('Chained');

    expect($notification->sendToDiscord())->toBe($notification);
});

it('queues the job when the queue is not sync', function () {
    Queue::fake();

    Notification::make()->title('Queued')->sendToDiscord();

    Queue::assertPushed(NotifyDiscordJob::class, fn (NotifyDiscordJob $job): bool => $job->title === 'Queued');
    Http::assertNothingSent();
});
