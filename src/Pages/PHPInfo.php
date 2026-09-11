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

    public function getViewData(): array
    {
        return [
            'info' => $this->getInfo(),
            'redactor' => $this->getRedactor(),
        ];
    }

    protected function getInfo(): mixed
    {
        return $this->info ??= InfoWrapper\Info::capture();
    }

    /**
     * The plugin carries whatever the panel configured fluently. Fall back to config alone, for a
     * panel that registers the page without the plugin.
     */
    protected function getRedactor(): Redactor
    {
        // The page can be instantiated with no panel resolved, so ask the container first.
        $panel = app()->bound('filament') ? Filament::getCurrentPanel() : null;

        if ($panel?->hasPlugin('filament-phpinfo')) {
            $plugin = $panel->getPlugin('filament-phpinfo');

            if ($plugin instanceof FilamentPHPInfoPlugin) {
                return $plugin->redactor();
            }
        }

        return Redactor::fromConfig();
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
