<?php

namespace STS\FilamentPHPInfo;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentPHPInfoServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-phpinfo';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasViews()
            ->hasConfigFile();
    }

    public function packageBooted(): void
    {
        Pages\PHPInfo::navigationGroup(config('filament-phpinfo.navigation-group'));
        Pages\PHPInfo::navigationIcon(config('filament-phpinfo.navigation-icon', 'heroicon-o-information-circle'));
    }
}
