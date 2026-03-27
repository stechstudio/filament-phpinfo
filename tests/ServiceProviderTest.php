<?php

it('registers the config file', function () {
    $config = config('filament-phpinfo');
    expect($config)->toBeArray()
        ->and($config)->toHaveKeys(['navigation-group', 'navigation-icon', 'page-slug']);
});

it('registers the views', function () {
    $viewFactory = app('view');
    expect($viewFactory->exists('filament-phpinfo::phpinfo'))->toBeTrue();
});
