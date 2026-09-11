# PHPInfo for Filament

![Tests](https://github.com/stechstudio/filament-phpinfo/actions/workflows/tests.yml/badge.svg)

This package adds a new page to the Filament admin panel that displays the output of `phpinfo()` in a nicely formatted way.

## Compatibility

| Version | PHP | Laravel | Filament |
|---------|-----|---------|----------|
| 1.3     | 8.3+ | 11, 12, 13 | 3, 4, 5 |

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
still tells you which variables are set.

An environment variable is redacted when its name contains any of `password`, `key`, `secret`,
`token`, `credential`, `private`, `salt`, `signing`, `signature`, `dsn`, `license` or `webhook`.

Term matching applies to the `Environment` and `PHP Variables` sections only, because those are the
only two places `phpinfo()` prints the environment. PHP settings named `Max keys`, `Cached keys` or
`Tokenizer Support` therefore keep their values.

The page is still worth gating to your most trusted users. Redaction removes the credentials, not
the rest of the host's configuration.

### Adjusting it

Four methods on the plugin cover everything:

```php
use STS\FilamentPHPInfo\FilamentPHPInfoPlugin;

$panel->plugins([
    FilamentPHPInfoPlugin::make()
        ->redact('SESSION_FINGERPRINT', 'TENANT_ID')
        ->redactContaining('tenant')
        ->reveal('AWS_ACCESS_KEY_ID')
        ->placeholder('***'),
]);
```

| Method | What it does |
|---|---|
| `redact(...$names)` | Always redact these, wherever they appear on the page, including PHP settings. |
| `redactContaining(...$terms)` | Redact environment variables containing these, **on top of** the defaults. |
| `reveal(...$names)` | Never redact these. Wins over every other rule. |
| `placeholder($text)` | What a redacted value shows instead. |
| `withoutRedaction()` | Print every value, secrets included. |

Names are exact and ignore case, and you may write either form: `redact('APP_KEY')` and
`redact("\$_ENV['APP_KEY']")` match the same variable in all three rows.

The same settings live in the config file, if you would rather keep them there:

```php
'redact' => [
    'terms' => Redactor::DEFAULT_TERMS,
    'always' => ['SESSION_FINGERPRINT'],
    'never' => ['AWS_ACCESS_KEY_ID'],
    'placeholder' => '[redacted]',
    'enabled' => true,
],
```

`terms` replaces the defaults outright. Use `redactContaining()` to add to them instead.

> If you have published the view, republish it. A published view carries its own markup: one from
> before v1.2.1 keeps printing every value, and one from v1.2.x calls a class this version removes.

## Configuration
The navigation group, icon, and secret redaction are configurable.

Publish the `filament-phpinfo` config file with:
```bash
php artisan vendor:publish --tag=filament-phpinfo-config
```

| Option             | Description                                                                                                          |
|--------------------|----------------------------------------------------------------------------------------------------------------------|
| `navigation-group` | The PHPInfo page's [navigation group](https://filamentphp.com/docs/3.x/panels/navigation#grouping-navigation-items). |
| `navigation-icon`  | The PHPInfo page's icon. See Filament's [documentation](https://filamentphp.com/docs/3.x/support/icons) for values.  |
| `page-slug`        | The PHPInfo page's URL slug.                                                                                        |
| `redact`           | Secret redaction. See [Secrets](#secrets).                                                                          |

| Screenshot |
|---|
| ![PHPInfo Page](/screenshots/phpinfo.png) |
