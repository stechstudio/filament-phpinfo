<?php

namespace STS\FilamentPHPInfo\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use STS\FilamentPHPInfo\FilamentPHPInfoServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            FilamentPHPInfoServiceProvider::class,
        ];
    }
}
