# PHPInfo for Filament

![Tests](https://github.com/stechstudio/filament-phpinfo/actions/workflows/tests.yml/badge.svg)

This package adds a new page to the Filament admin panel that displays the output of `phpinfo()` in a nicely formatted way.

## Compatibility

| Version | PHP | Laravel | Filament |
|---------|-----|---------|----------|
| 1.2     | 8.3+ | 11, 12, 13 | 3, 4, 5 |

## Installation
```bash
composer require stechstudio/filament-phpinfo
```

In your `AdminPanelProvider` (or other `\Filament\PanelProvider`), add this package to your plugins:
```php
$panel
    ->plugins([
        \STS\FilamentPHPInfo\FilamentPHPInfoPlugin::make(),
    ])
```

## Secrets

`phpinfo()` prints the whole process environment three times: once under `Environment` as
`APP_KEY`, and twice under `PHP Variables` as `$_ENV['APP_KEY']` and `$_SERVER['APP_KEY']`. On a
Laravel app that puts the encryption key, the database password and every third-party credential
on one page.

This package replaces those values with `[redacted]` by default. It keeps the row, so the page
still tells you which variables are set. Matching applies only to the two environment modules, so
PHP settings such as `Max keys` and `Tokenizer Support` keep their values.

The page is still worth gating to your most trusted users. Redaction removes the credentials, not
the rest of the host's configuration.

## Configuration
The navigation group, icon, and secret redaction are configurable.

Publish the `filament-phpinfo` config file with:
```bash
php artisan vendor:publish --tag=filament-phpinfo-config
```

| Option                | Description                                                                                                          |
|-----------------------|----------------------------------------------------------------------------------------------------------------------|
| `navigation-group`    | The PHPInfo page's [navigation group](https://filamentphp.com/docs/3.x/panels/navigation#grouping-navigation-items). |
| `navigation-icon`     | The PHPInfo page's icon. See Filament's [documentation](https://filamentphp.com/docs/3.x/support/icons) for values.  |
| `page-slug`           | The PHPInfo page's URL slug.                                                                                        |
| `redact-environment`  | Whether to replace the values of secret-bearing environment variables. Defaults to `true`.                          |
| `redact-patterns`     | The strings that mark an environment variable name as secret-bearing. Matching ignores case.                        |
| `redact-placeholder`  | What a redacted value shows instead. Defaults to `[redacted]`.                                                      |

| Screenshot |
|---|
| ![PHPInfo Page](/screenshots/phpinfo.png) |
