<?php

use STS\FilamentPHPInfo\FilamentPHPInfoPlugin;

it('configures the redactor fluently', function () {
    $redactor = FilamentPHPInfoPlugin::make()
        ->redact('SESSION_FINGERPRINT')
        ->redactContaining('tenant')
        ->reveal('AWS_ACCESS_KEY_ID')
        ->placeholder('***')
        ->redactor();

    expect($redactor->apply('Core', 'SESSION_FINGERPRINT', 'x'))->toBe('***')
        ->and($redactor->apply('Environment', 'TENANT_ID', 'x'))->toBe('***')
        ->and($redactor->apply('Environment', 'APP_KEY', 'x'))->toBe('***')
        ->and($redactor->apply('Environment', 'AWS_ACCESS_KEY_ID', 'AKIA1'))->toBe('AKIA1');
});

it('turns redaction off fluently', function () {
    $redactor = FilamentPHPInfoPlugin::make()->withoutRedaction()->redactor();

    expect($redactor->apply('Environment', 'APP_KEY', 'x'))->toBe('x');
});
