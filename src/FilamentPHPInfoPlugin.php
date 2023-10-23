<?php

namespace STS\FilamentPHPInfo;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentPHPInfoPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-phpinfo';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->pages([
                Pages\PHPInfo::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
    }
}
