<?php

use STS\FilamentPHPInfo\Redactor;
use STS\Phpinfo\Info;
use STS\Phpinfo\Models\Config;
use STS\Phpinfo\Models\Group;
use STS\Phpinfo\Models\Module;
use STS\Phpinfo\PhpInfo;

it('redacts secret environment variables out of the box, in every row form', function () {
    $info = Redactor::make()->apply(fixture([
        'Environment' => ['APP_KEY' => 'base64:abc', 'APP_ENV' => 'production'],
        'PHP Variables' => ["\$_ENV['APP_KEY']" => 'base64:abc', "\$_SERVER['APP_KEY']" => 'base64:abc'],
    ]));

    expect(valueOf($info, 'APP_KEY'))->toBe('[redacted]')
        ->and(valueOf($info, "\$_ENV['APP_KEY']"))->toBe('[redacted]')
        ->and(valueOf($info, "\$_SERVER['APP_KEY']"))->toBe('[redacted]')
        ->and(valueOf($info, 'APP_ENV'))->toBe('production');
});

it('redacts on every built-in term', function (string $name) {
    expect(Redactor::make()->shouldRedact('Environment', $name))->toBeTrue();
})->with([
    'DB_PASSWORD', 'APP_KEY', 'STRIPE_SECRET', 'NIGHTWATCH_TOKEN', 'GOOGLE_APPLICATION_CREDENTIALS',
    'OAUTH_PRIVATE_KEY', 'JWT_SIGNING_SALT', 'JWT_RECIPIENT_SIGNATURE', 'SENTRY_DSN', 'NOVA_LICENSE_KEY',
    'SLACK_WEBHOOK', 'db_password',
]);

it('leaves PHP settings alone even when their name matches a term', function () {
    $info = Redactor::make()->apply(fixture([
        'Zend OPcache' => ['Max keys' => '7963', 'Hash keys restarts' => '0'],
        'tokenizer' => ['Tokenizer Support' => 'enabled'],
        'Core' => ['highlight.keyword' => '#007700'],
    ]));

    expect(valueOf($info, 'Max keys'))->toBe('7963')
        ->and(valueOf($info, 'Hash keys restarts'))->toBe('0')
        ->and(valueOf($info, 'Tokenizer Support'))->toBe('enabled')
        ->and(valueOf($info, 'highlight.keyword'))->toBe('#007700');
});

it('leaves an empty value alone, so the page still shows it is unset', function () {
    $info = Redactor::make()->apply(fixture(['Environment' => ['APP_KEY' => '']]));

    expect(valueOf($info, 'APP_KEY'))->toBe('');
});

it('redacts names you add, anywhere on the page', function () {
    $info = Redactor::make()->redact('SESSION_FINGERPRINT', 'max keys')->apply(fixture([
        'Environment' => ['SESSION_FINGERPRINT' => 'abc'],
        'Zend OPcache' => ['Max keys' => '7963'],
    ]));

    expect(valueOf($info, 'SESSION_FINGERPRINT'))->toBe('[redacted]')
        ->and(valueOf($info, 'Max keys'))->toBe('[redacted]');
});

it('accepts wildcards', function () {
    $redactor = Redactor::make()->redact('TENANT_*');

    expect($redactor->shouldRedact('Environment', 'TENANT_ID'))->toBeTrue()
        ->and($redactor->shouldRedact('Environment', 'MY_TENANT_ID'))->toBeFalse();
});

it('matches a name you add in any row form', function (string $added, string $row) {
    expect(Redactor::make()->redact($added)->shouldRedact('PHP Variables', $row))->toBeTrue();
})->with([
    ['tenant_id', 'TENANT_ID'],
    ["\$_ENV['TENANT_ID']", 'TENANT_ID'],
    ['TENANT_ID', "\$_SERVER['TENANT_ID']"],
]);

it('reveals the names you choose, over every other rule', function () {
    $info = Redactor::make()->redact('STRIPE_*')->reveal('STRIPE_KEY')->apply(fixture([
        'Environment' => ['STRIPE_KEY' => 'pk_test_1', 'STRIPE_SECRET' => 'sk_test_1'],
    ]));

    expect(valueOf($info, 'STRIPE_KEY'))->toBe('pk_test_1')
        ->and(valueOf($info, 'STRIPE_SECRET'))->toBe('[redacted]');
});

it('takes a custom placeholder', function () {
    $info = Redactor::make()->placeholder('***')->apply(fixture(['Environment' => ['APP_KEY' => 'x']]));

    expect(valueOf($info, 'APP_KEY'))->toBe('***');
});

it('can be turned off', function () {
    $info = Redactor::make()->disable()->apply(fixture(['Environment' => ['APP_KEY' => 'x']]));

    expect(valueOf($info, 'APP_KEY'))->toBe('x');
});

it('keeps the headings, names, notes and master values the view relies on', function () {
    $info = new PhpInfo('8.4.0', items([
        new Module('Core', items([
            new Group(items([new Config('memory_limit', '256M', '128M', true)]), items(['Directive', 'Local Value', 'Master Value']), 'Settings', 'A note'),
        ])),
    ]));

    $group = Redactor::make()->apply($info)->module('Core')->groups()->first();
    $config = $group->configs()->first();

    expect($group->headings()->all())->toBe(['Directive', 'Local Value', 'Master Value'])
        ->and($group->name())->toBe('Settings')
        ->and($group->note())->toBe('A note')
        ->and($config->masterValue())->toBe('128M');
});

it('changes nothing outside the environment sections of a real capture', function () {
    $values = fn (PhpInfo $info) => $info->modules()
        ->filter(fn (Module $module) => ! in_array($module->name(), Redactor::ENVIRONMENT_MODULES, true))
        ->flatMap(fn (Module $module) => $module->configs())
        ->map(fn (Config $config) => $config->name().'='.$config->localValue().'|'.$config->masterValue())
        ->all();

    $raw = Info::capture();

    expect($values(Redactor::make()->apply($raw)))->toBe($values($raw));
});

it('redacts a secret in a real capture', function () {
    putenv('FILAMENT_PHPINFO_TEST_SECRET=must-not-render');

    $info = Redactor::make()->apply(Info::capture());

    putenv('FILAMENT_PHPINFO_TEST_SECRET');

    expect(valueOf($info, 'FILAMENT_PHPINFO_TEST_SECRET'))->toBe('[redacted]');
});
