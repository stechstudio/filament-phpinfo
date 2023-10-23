<?php

namespace STS\FilamentPHPInfo\Pages;

use Filament\Pages\Page;
use STS\Phpinfo as InfoWrapper;

class PHPInfo extends Page
{
    protected static ?string $title = 'PHPInfo';

    protected static string $view = 'filament-phpinfo::phpinfo';

    protected static ?string $navigationLabel = 'PHPInfo';

    protected InfoWrapper\Result $info;

    public function getViewData(): array
    {
        return [
            'info' => $this->getInfo(),
        ];
    }

    protected function getInfo(): InfoWrapper\Result
    {
        return $this->info ??= InfoWrapper\Info::capture();
    }

    public static function getSlug(): string
    {
        return config('filament-phpinfo.page-slug', 'phpinfo');
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-phpinfo.navigation-group');
    }

    public static function getNavigationIcon(): ?string
    {
        return config('filament-phpinfo.navigation-icon', 'heroicon-o-information-circle');
    }
}
