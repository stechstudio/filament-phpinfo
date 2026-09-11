<?php

use STS\FilamentPHPInfo\Pages\PHPInfo;

it('has the correct default slug', function () {
    expect(PHPInfo::getDefaultSlug())->toBe('phpinfo');
});

it('has the correct default navigation icon', function () {
    expect(PHPInfo::getNavigationIcon())->toBe('heroicon-o-information-circle');
});

it('has the correct default navigation group', function () {
    expect(PHPInfo::getNavigationGroup())->toBe('System Management');
});

it('respects custom slug from config', function () {
    config()->set('filament-phpinfo.page-slug', 'custom-phpinfo');
    expect(PHPInfo::getDefaultSlug())->toBe('custom-phpinfo');
});

it('respects custom navigation icon from config', function () {
    config()->set('filament-phpinfo.navigation-icon', 'heroicon-o-cog');
    expect(PHPInfo::getNavigationIcon())->toBe('heroicon-o-cog');
});

it('respects custom navigation group from config', function () {
    config()->set('filament-phpinfo.navigation-group', 'Custom Group');
    expect(PHPInfo::getNavigationGroup())->toBe('Custom Group');
});

it('returns the correct view', function () {
    $page = new class extends PHPInfo {
        public function exposeGetView(): string
        {
            return $this->getView();
        }
    };
    expect($page->exposeGetView())->toBe('filament-phpinfo::phpinfo');
});

it('hands the view a capture and a redactor', function () {
    $data = (new PHPInfo)->getViewData();

    expect($data)->toHaveKeys(['info', 'redactor'])
        ->and($data['redactor'])->toBeInstanceOf(STS\FilamentPHPInfo\Redactor::class)
        ->and($data['redactor']->apply('Environment', 'APP_KEY', 'x'))->toBe('[redacted]');
});
