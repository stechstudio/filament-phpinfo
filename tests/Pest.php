<?php

use STS\FilamentPHPInfo\Tests\TestCase;
use STS\Phpinfo\Models\Config;
use STS\Phpinfo\Models\Group;
use STS\Phpinfo\Models\Module;
use STS\Phpinfo\PhpInfo;

uses(TestCase::class)->in(__DIR__);

/**
 * @param  array<string, array<string, string>>  $modules  module name => [row name => value]
 */
function fixture(array $modules): PhpInfo
{
    return new PhpInfo('8.4.0', items(array_map(
        fn (string $module, array $rows) => new Module($module, items([
            new Group(items(array_map(
                fn (string $name, string $value) => new Config($name, $value),
                array_keys($rows),
                $rows,
            ))),
        ])),
        array_keys($modules),
        $modules,
    )));
}

function valueOf(PhpInfo $info, string $name): ?string
{
    return $info->configs()->first(fn (Config $config) => $config->name() === $name)?->localValue();
}
