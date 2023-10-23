# PHPInfo for Filament
This package adds a new page to the Filament admin panel that displays the output of `phpinfo()` in a nicely formatted way.

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
