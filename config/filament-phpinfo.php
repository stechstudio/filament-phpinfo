<?php

use STS\FilamentPHPInfo\Redaction;

return [
    'navigation-group' => 'System Management',
    'navigation-icon' => 'heroicon-o-information-circle',
    'page-slug' => 'phpinfo',

    /*
     * phpinfo() prints the whole process environment three times, under Environment as
     * APP_KEY and under PHP Variables as $_ENV['APP_KEY'] and $_SERVER['APP_KEY']. On a
     * Laravel app that puts the encryption key, the database password and every third-party
     * credential on one page. Set this to false to print the values.
     */
    'redact-environment' => true,

    /*
     * An environment variable whose name contains any of these strings has its value replaced.
     * Matching ignores case, and applies only to the two environment modules above, so PHP
     * settings such as "Max keys" and "Tokenizer Support" keep their values. Replace this with
     * your own array to change the set.
     */
    'redact-patterns' => Redaction::DEFAULT_PATTERNS,

    /*
     * What a redacted value shows instead. The row itself stays, so the page still tells you
     * which variables are set.
     */
    'redact-placeholder' => Redaction::DEFAULT_PLACEHOLDER,
];
