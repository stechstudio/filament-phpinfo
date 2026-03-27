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

| Screenshot |
|---|
| ![PHPInfo Page](/screenshots/phpinfo.png) |
