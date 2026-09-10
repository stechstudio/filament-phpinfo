<?php

use STS\FilamentPHPInfo\Redaction;
use STS\Phpinfo\Info;

it('replaces the value of a secret environment variable', function () {
    expect(Redaction::value('Environment', 'APP_KEY', 'base64:abc123'))->toBe('[redacted]');
});

it('redacts every row form phpinfo prints for one variable', function ($module, $name) {
    expect(Redaction::isSensitive($module, $name))->toBeTrue();
})->with([
    ['Environment', 'APP_KEY'],
    ['PHP Variables', "\$_ENV['APP_KEY']"],
    ['PHP Variables', '$_SERVER["APP_KEY"]'],
]);

it('redacts on every pattern', function ($name) {
    expect(Redaction::isSensitive('Environment', $name))->toBeTrue();
})->with([
    'AWS_SECRET_ACCESS_KEY',
    'DB_PASSWORD',
    'NIGHTWATCH_TOKEN',
    'GOOGLE_APPLICATION_CREDENTIALS',
    'OAUTH_PRIVATE_KEY',
    'JWT_SIGNING_SALT',
    'JWT_RECIPIENT_SIGNATURE',
    'SENTRY_DSN',
    'NOVA_LICENSE_KEY',
    'SLACK_WEBHOOK',
]);

it('keeps ordinary environment variables', function ($name) {
    expect(Redaction::value('Environment', $name, 'kept'))->toBe('kept');
})->with(['APP_ENV', 'DB_HOST', 'MAIL_FROM_ADDRESS', 'AWS_DEFAULT_REGION', 'PATH']);

it('keeps php settings whose name matches a pattern', function ($module, $name) {
    expect(Redaction::value($module, $name, 'kept'))->toBe('kept');
})->with([
    ['Zend OPcache', 'Max keys'],
    ['Zend OPcache', 'Cached keys'],
    ['Zend OPcache', 'Hash keys restarts'],
    ['tokenizer', 'Tokenizer Support'],
    ['Core', 'highlight.keyword'],
]);

it('leaves an unset variable alone, so the page still shows it is empty', function ($value) {
    expect(Redaction::value('Environment', 'APP_KEY', $value))->toBe($value);
})->with([null, '']);

it('can be turned off', function () {
    config()->set('filament-phpinfo.redact-environment', false);

    expect(Redaction::value('Environment', 'APP_KEY', 'base64:abc123'))->toBe('base64:abc123');
});

it('respects custom patterns from config', function () {
    config()->set('filament-phpinfo.redact-patterns', ['tenant']);

    expect(Redaction::value('Environment', 'TENANT_ID', 'acme'))->toBe('[redacted]');
    expect(Redaction::value('Environment', 'APP_KEY', 'base64:abc123'))->toBe('base64:abc123');
});

it('respects a custom placeholder from config', function () {
    config()->set('filament-phpinfo.redact-placeholder', '***');

    expect(Redaction::value('Environment', 'APP_KEY', 'base64:abc123'))->toBe('***');
});

it('redacts nothing outside the environment modules of a real capture', function () {
    $redacted = [];

    foreach (Info::capture()->modules() as $module) {
        if (in_array($module->name(), Redaction::ENVIRONMENT_MODULES, true)) {
            continue;
        }

        foreach ($module->configs() as $config) {
            if (Redaction::isSensitive($module->name(), $config->name())) {
                $redacted[] = $module->name() . ' / ' . $config->name();
            }
        }
    }

    expect($redacted)->toBeEmpty();
});

it('redacts a secret in a real capture', function () {
    putenv('FILAMENT_PHPINFO_TEST_SECRET=must-not-render');

    $names = [];

    foreach (Info::capture()->modules() as $module) {
        foreach ($module->configs() as $config) {
            if (str_contains($config->name(), 'FILAMENT_PHPINFO_TEST_SECRET')) {
                $names[] = $config->name();
                expect(Redaction::value($module->name(), $config->name(), $config->localValue()))
                    ->toBe('[redacted]');
            }
        }
    }

    expect($names)->not->toBeEmpty();

    putenv('FILAMENT_PHPINFO_TEST_SECRET');
});
