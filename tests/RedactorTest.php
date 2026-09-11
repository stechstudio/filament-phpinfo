<?php

use STS\FilamentPHPInfo\Redactor;
use STS\Phpinfo\Info;

it('replaces the value of a secret environment variable', function () {
    expect(Redactor::make()->apply('Environment', 'APP_KEY', 'base64:abc'))->toBe('[redacted]');
});

it('redacts every row form phpinfo prints for one variable', function ($module, $name) {
    expect(Redactor::make()->shouldRedact($module, $name))->toBeTrue();
})->with([
    ['Environment', 'APP_KEY'],
    ['PHP Variables', "\$_ENV['APP_KEY']"],
    ['PHP Variables', '$_SERVER["APP_KEY"]'],
    ['PHP Variables', "\$_COOKIE['XSRF-TOKEN']"],
]);

it('redacts on every default term', function ($name) {
    expect(Redactor::make()->shouldRedact('Environment', $name))->toBeTrue();
})->with([
    'DB_PASSWORD',
    'APP_KEY',
    'STRIPE_SECRET',
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
    expect(Redactor::make()->apply('Environment', $name, 'kept'))->toBe('kept');
})->with(['APP_ENV', 'DB_HOST', 'MAIL_FROM_ADDRESS', 'AWS_DEFAULT_REGION', 'PATH']);

it('keeps php settings whose name matches a term', function ($module, $name) {
    expect(Redactor::make()->apply($module, $name, 'kept'))->toBe('kept');
})->with([
    ['Zend OPcache', 'Max keys'],
    ['Zend OPcache', 'Cached keys'],
    ['Zend OPcache', 'Hash keys restarts'],
    ['tokenizer', 'Tokenizer Support'],
    ['Core', 'highlight.keyword'],
]);

it('leaves an unset variable alone, so the page still shows it is empty', function ($value) {
    expect(Redactor::make()->apply('Environment', 'APP_KEY', $value))->toBe($value);
})->with([null, '']);

it('redacts a name you list, even outside the environment sections', function () {
    $redactor = Redactor::make()->redact('Max keys');

    expect($redactor->apply('Zend OPcache', 'Max keys', 'kept'))->toBe('[redacted]');
});

it('matches a listed name case-insensitively and in any row form', function ($listed, $row) {
    expect(Redactor::make()->redact($listed)->shouldRedact('Environment', $row))->toBeTrue();
})->with([
    ['tenant_id', 'TENANT_ID'],
    ['TENANT_ID', 'tenant_id'],
    ["\$_ENV['TENANT_ID']", 'TENANT_ID'],
    ['TENANT_ID', "\$_SERVER['TENANT_ID']"],
]);

it('adds terms without dropping the defaults', function () {
    $redactor = Redactor::make()->redactContaining('tenant');

    expect($redactor->shouldRedact('Environment', 'TENANT_ID'))->toBeTrue()
        ->and($redactor->shouldRedact('Environment', 'APP_KEY'))->toBeTrue();
});

it('reveals a name you list, beating every other rule', function () {
    $redactor = Redactor::make()->redact('APP_KEY')->reveal('APP_KEY');

    expect($redactor->apply('Environment', 'APP_KEY', 'kept'))->toBe('kept');
});

it('reveals a variable that only looks like a secret', function () {
    $redactor = Redactor::make()->reveal('AWS_ACCESS_KEY_ID');

    expect($redactor->apply('Environment', 'AWS_ACCESS_KEY_ID', 'AKIA123'))->toBe('AKIA123')
        ->and($redactor->apply('Environment', 'AWS_SECRET_ACCESS_KEY', 'shh'))->toBe('[redacted]');
});

it('takes a custom placeholder', function () {
    expect(Redactor::make()->placeholder('***')->apply('Environment', 'APP_KEY', 'x'))->toBe('***');
});

it('can be turned off', function () {
    expect(Redactor::make()->disable()->apply('Environment', 'APP_KEY', 'x'))->toBe('x');
});

it('reads every setting from config', function () {
    config()->set('filament-phpinfo.redact', [
        'terms' => ['tenant'],
        'always' => ['SESSION_FINGERPRINT'],
        'never' => ['TENANT_REGION'],
        'placeholder' => '***',
        'enabled' => true,
    ]);

    $redactor = Redactor::fromConfig();

    expect($redactor->apply('Environment', 'TENANT_ID', 'x'))->toBe('***')
        ->and($redactor->apply('Environment', 'TENANT_REGION', 'us-east-2'))->toBe('us-east-2')
        ->and($redactor->apply('Core', 'SESSION_FINGERPRINT', 'x'))->toBe('***')
        ->and($redactor->apply('Environment', 'APP_KEY', 'x'))->toBe('x');
});

it('survives a config file that predates these keys', function () {
    config()->set('filament-phpinfo.redact', ['placeholder' => '***']);

    expect(Redactor::fromConfig()->apply('Environment', 'APP_KEY', 'x'))->toBe('***');
});

it('honours the 1.2.x config keys', function () {
    config()->set('filament-phpinfo.redact', null);
    config()->set('filament-phpinfo.redact-patterns', ['tenant']);
    config()->set('filament-phpinfo.redact-placeholder', '***');

    $redactor = Redactor::fromConfig();

    expect($redactor->apply('Environment', 'TENANT_ID', 'x'))->toBe('***')
        ->and($redactor->apply('Environment', 'APP_KEY', 'x'))->toBe('x');
});

it('redacts nothing outside the environment sections of a real capture', function () {
    $redactor = Redactor::make();
    $redacted = [];

    foreach (Info::capture()->modules() as $module) {
        if (in_array($module->name(), Redactor::ENVIRONMENT_MODULES, true)) {
            continue;
        }

        foreach ($module->configs() as $config) {
            if ($redactor->shouldRedact($module->name(), $config->name())) {
                $redacted[] = $module->name().' / '.$config->name();
            }
        }
    }

    expect($redacted)->toBeEmpty();
});

it('redacts a secret in a real capture', function () {
    putenv('FILAMENT_PHPINFO_TEST_SECRET=must-not-render');

    $redactor = Redactor::make();
    $seen = [];

    foreach (Info::capture()->modules() as $module) {
        foreach ($module->configs() as $config) {
            if (str_contains($config->name(), 'FILAMENT_PHPINFO_TEST_SECRET')) {
                $seen[] = $config->name();
                expect($redactor->apply($module->name(), $config->name(), $config->localValue()))
                    ->toBe('[redacted]');
            }
        }
    }

    putenv('FILAMENT_PHPINFO_TEST_SECRET');

    expect($seen)->not->toBeEmpty();
});
