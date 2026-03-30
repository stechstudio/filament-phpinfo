<?php

use STS\Phpinfo\Info;

it('captures phpinfo and returns modules', function () {
    $info = Info::capture();

    expect($info->modules())->not->toBeEmpty();
});

it('contains a PHP version', function () {
    $info = Info::capture();

    expect($info->version())->toMatch('/^\d+\.\d+/');
});

it('includes the Core module', function () {
    $info = Info::capture();

    expect($info->hasModule('Core'))->toBeTrue();
    expect($info->module('Core')->name())->toBe('Core');
});

it('has configs with names and values', function () {
    $info = Info::capture();

    expect($info->configs())->not->toBeEmpty();
    expect($info->hasConfig('display_errors'))->toBeTrue();
});
