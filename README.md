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

`phpinfo()` prints the whole process environment. On a Laravel app that means the encryption key,
the database password and every third-party credential, three times over.

This package redacts them before the page renders, so there is nothing to set up. An environment
variable is redacted when its name contains `password`, `key`, `secret`, `token`, `credential`,
`private`, `salt`, `signing`, `signature`, `dsn`, `license` or `webhook`. The row stays, so you can
still see which variables are set.

Only the environment is matched. PHP settings such as `Max keys` or `Tokenizer Support` keep their
values.

To adjust it:

```php
FilamentPHPInfoPlugin::make()
    ->redact('INTERNAL_*')   // redact these too
    ->reveal('STRIPE_KEY')   // always show these
```

Both take exact names or `*` wildcards, ignore case, and apply anywhere on the page.
`->placeholder('***')` changes what a redacted value shows, and `->withoutRedaction()` turns it off.

Redaction happens before the view receives the data, so a published view is covered too.

### Upgrading from v1.2.1

v1.2.1 configured redaction through the config file and a `Redaction` class. Both are gone.

- If you published the config file, delete its `redact-environment`, `redact-patterns` and
  `redact-placeholder` keys. They reference `Redaction`, so leaving them stops the app from booting.
- If you published the view, republish it or delete it. The v1.2.1 view calls `Redaction::value()`.

## Configuration
The navigation group and icon are configurable.

Publish the `filament-phpinfo` config file with:
```bash
php artisan vendor:publish --tag=filament-phpinfo-config
```

| Option             | Description                                                                                                          |
|--------------------|----------------------------------------------------------------------------------------------------------------------|
| `navigation-group` | The PHPInfo page's [navigation group](https://filamentphp.com/docs/3.x/panels/navigation#grouping-navigation-items). |
| `navigation-icon`  | The PHPInfo page's icon. See Filament's [documentation](https://filamentphp.com/docs/3.x/support/icons) for values.  |
| `page-slug`        | The PHPInfo page's URL slug.                                                                                        |

| Screenshot |
|---|
| ![PHPInfo Page](/screenshots/phpinfo.png) |
