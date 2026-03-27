<?php

use STS\FilamentPHPInfo\FilamentPHPInfoPlugin;

it('can be instantiated via make()', function () {
    $plugin = FilamentPHPInfoPlugin::make();
    expect($plugin)->toBeInstanceOf(FilamentPHPInfoPlugin::class);
});

it('has the correct plugin ID', function () {
    $plugin = FilamentPHPInfoPlugin::make();
    expect($plugin->getId())->toBe('filament-phpinfo');
});
