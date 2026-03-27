<?php

namespace STS\FilamentPHPInfo\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use STS\Phpinfo as InfoWrapper;
use UnitEnum;

class PHPInfo extends Page
{
    protected static ?string $title = 'PHPInfo';

    protected string $view = 'filament-phpinfo::phpinfo';

    protected static ?string $navigationLabel = 'PHPInfo';

    protected mixed $info;

    public function getViewData(): array
    {
        return [
            'info' => $this->getInfo(),
        ];
    }

    protected function getInfo(): mixed
    {
        return $this->info ??= InfoWrapper\Info::capture();
    }

    public static function getDefaultSlug(): string
    {
        return config('filament-phpinfo.page-slug', 'phpinfo');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return config('filament-phpinfo.navigation-group');
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return config('filament-phpinfo.navigation-icon', 'heroicon-o-information-circle');
    }
}
