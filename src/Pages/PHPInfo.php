<?php

namespace STS\FilamentPHPInfo\Pages;

use Filament\Pages\Page;
use STS\Phpinfo as InfoWrapper;

class PHPInfo extends Page
{
    protected static ?string $title = 'PHPInfo';

    protected static ?string $navigationLabel = 'PHPInfo';

    protected mixed $info;

    public function getView(): string
    {
        return 'filament-phpinfo::phpinfo';
    }

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

    public static function getNavigationIcon(): ?string
    {
        return config('filament-phpinfo.navigation-icon', 'heroicon-o-information-circle');
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-phpinfo.navigation-group');
    }
}
