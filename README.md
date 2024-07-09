![Screenshot](https://raw.githubusercontent.com/tomatophp/filament-discord/master/arts/3x1io-tomato-discord.jpg)

# Filament Discord Notifications

[![Latest Stable Version](https://poser.pugx.org/tomatophp/filament-discord/version.svg)](https://packagist.org/packages/tomatophp/filament-discord)
[![PHP Version Require](http://poser.pugx.org/tomatophp/filament-discord/require/php)](https://packagist.org/packages/tomatophp/filament-discord)
[![License](https://poser.pugx.org/tomatophp/filament-discord/license.svg)](https://packagist.org/packages/tomatophp/filament-discord)
[![Downloads](https://poser.pugx.org/tomatophp/filament-discord/d/total.svg)](https://packagist.org/packages/tomatophp/filament-discord)

Send Notification to discord channel webhook using native FilamentPHP Notification Facade class

## Installation

```bash
composer require tomatophp/filament-discord
```
after install on your `.env` file set 

```.dotenv
DISCORD_WEBHOOK=
DISCORD_ERROR_WEBHOOK=
DISCORD_ERROR_WEBHOOK_ACTIVE=
```

where `DISCORD_WEBHOOK` the main notification channel webhook and `DISCORD_ERROR_WEBHOOK` is the error logger channel webhook and `DISCORD_ERROR_WEBHOOK_ACTIVE` to be true or false to active and stop logger

## Using

you can send notification to discord with multi-way the first of it is using native FilamentPHP `Notification::class`

```php
use \Filament\Notifications\Notification;

Notification::make()
    ->title('Hi')
    ->body('Welcome On The Moon!')
    ->sendToDiscord()
```

you can attach image to the message like this 

```php
use \Filament\Notifications\Notification;

Notification::make()
    ->title('Hi')
    ->body('Welcome On The Moon!')
    ->sendToDiscord(image: "https://raw.githubusercontent.com/tomatophp/filament-discord/master/arts/3x1io-tomato-discord.jpg")
```

## Send to Selected User

you can send a notification to selected user webhook by add a column on your user table with name `webhook` and then add this tait to User model

```php
use TomatoPHP\FilamentDiscord\Traits\InteractsWithDiscord;

class User {
    use InteractsWithDiscord;
}
```

and now you can send notification to the user like this

```php
use \Filament\Notifications\Notification;

Notification::make()
    ->title('Hi')
    ->body('Welcome On The Moon!')
    ->sendToDiscord($user)
```

## Allow Discord Error Logger

you can use Discord channel as an error logger to log and followup your error with a very easy way, on your `bootstrap/app.php` add this class like this

```php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use ProtoneMedia\Splade\Http\SpladeMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
    })
    ->withExceptions(function (Exceptions $exceptions) {
        \TomatoPHP\FilamentDiscord\Helpers\DiscordErrorReporter::make($exceptions);
    })->create();
```

## Publish Assets

you can publish config file by use this command

```bash
php artisan vendor:publish --tag="filament-discord-config"
```

## Support

you can join our discord server to get support [TomatoPHP](https://discord.gg/Xqmt35Uh)

## Docs

you can check docs of this package on [Docs](https://docs.tomatophp.com/filament/filament-discord)

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

Please see [SECURITY](SECURITY.md) for more information about security.

## Credits

- [Fady Mondy](https://wa.me/+201207860084)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
