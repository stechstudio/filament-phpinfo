<?php

use STS\FilamentPHPInfo\FilamentPHPInfoPlugin;

it('hands what the panel configures to the redactor', function () {
    $info = FilamentPHPInfoPlugin::make()
        ->redact('SESSION_FINGERPRINT')
        ->reveal('STRIPE_KEY')
        ->placeholder('***')
        ->getRedactor()
        ->apply(fixture(['Environment' => [
            'SESSION_FINGERPRINT' => 'abc',
            'STRIPE_KEY' => 'pk_test_1',
            'APP_KEY' => 'base64:abc',
        ]]));

    expect(valueOf($info, 'SESSION_FINGERPRINT'))->toBe('***')
        ->and(valueOf($info, 'STRIPE_KEY'))->toBe('pk_test_1')
        ->and(valueOf($info, 'APP_KEY'))->toBe('***');
});

it('turns redaction off', function () {
    $info = FilamentPHPInfoPlugin::make()->withoutRedaction()->getRedactor()
        ->apply(fixture(['Environment' => ['APP_KEY' => 'base64:abc']]));

    expect(valueOf($info, 'APP_KEY'))->toBe('base64:abc');
});
