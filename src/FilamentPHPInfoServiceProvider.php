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
}
