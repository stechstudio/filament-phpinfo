<?php

use STS\FilamentPHPInfo\Pages\PHPInfo;

it('registers the config file', function () {
    $config = config('filament-phpinfo');
    expect($config)->toBeArray()
        ->and($config)->toHaveKeys(['navigation-group', 'navigation-icon', 'page-slug']);
});

it('registers the views', function () {
    $viewFactory = app('view');
    expect($viewFactory->exists('filament-phpinfo::phpinfo'))->toBeTrue();
});

it('sets navigation group from config on boot', function () {
    expect(PHPInfo::getNavigationGroup())->toBe('System Management');
});

it('sets navigation icon from config on boot', function () {
    expect(PHPInfo::getNavigationIcon())->toBe('heroicon-o-information-circle');
});
