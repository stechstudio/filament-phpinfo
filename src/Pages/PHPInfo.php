<?php

namespace STS\FilamentPHPInfo\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Page;
use STS\FilamentPHPInfo\FilamentPHPInfoPlugin;
use STS\FilamentPHPInfo\Redactor;
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

    /**
     * Redaction happens here, before any view sees the capture, so a published view needs
     * nothing to stay safe.
     */
    public function getViewData(): array
    {
        return [
            'info' => $this->getRedactor()->apply($this->getInfo()),
        ];
    }

    protected function getInfo(): mixed
    {
        return $this->info ??= InfoWrapper\Info::capture();
    }

    protected function getRedactor(): Redactor
    {
        // The page can be built with no panel resolved, so ask the container first.
        $panel = app()->bound('filament') ? Filament::getCurrentPanel() : null;
        $plugin = $panel?->hasPlugin('filament-phpinfo') ? $panel->getPlugin('filament-phpinfo') : null;

        return $plugin instanceof FilamentPHPInfoPlugin ? $plugin->getRedactor() : Redactor::make();
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
