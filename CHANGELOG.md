# Changelog

### v5.0.0

- support Filament v5 and Laravel 12 / 13 (the Filament v3 line continues on the `v3` branch)
- read the notification title, body and first action URL through the Filament v5 notification API
- skip sending when no webhook is configured instead of posting to an empty URL
- the error reporter ignores an empty error webhook and never throws while reporting
- fix the implicitly nullable `$message` parameter deprecated on PHP 8.4
- add a Pest suite (Discord HTTP calls are faked), a phpstan config and the Laravel 12 / 13 CI matrix
- new cover
