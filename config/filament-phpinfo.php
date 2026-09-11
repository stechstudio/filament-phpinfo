<?php

use STS\FilamentPHPInfo\Redactor;

return [
    'navigation-group' => 'System Management',
    'navigation-icon' => 'heroicon-o-information-circle',
    'page-slug' => 'phpinfo',

    /*
     * phpinfo() prints the whole process environment three times, under Environment as APP_KEY
     * and under PHP Variables as $_ENV['APP_KEY'] and $_SERVER['APP_KEY']. On a Laravel app that
     * puts the encryption key, the database password and every third-party credential on one
     * page, so the values are redacted by default.
     */
    'redact' => [

        /*
         * An environment variable whose name contains one of these has its value replaced.
         * Matching ignores case, and applies to the Environment and PHP Variables sections only,
         * so PHP settings named "Max keys" or "Tokenizer Support" keep their values.
         */
        'terms' => Redactor::DEFAULT_TERMS,

        /*
         * Always redact these, wherever they appear on the page. Names are exact, ignore case,
         * and may be written as APP_KEY or as $_ENV['APP_KEY'].
         */
        'always' => [],

        /*
         * Never redact these, whatever else matches. Wins over every other rule. Use it for a
         * variable that reads like a secret but is not, such as AWS_DEFAULT_REGION_KEY.
         */
        'never' => [],

        /*
         * What a redacted value shows instead. The row stays, so the page still tells you which
         * variables are set.
         */
        'placeholder' => Redactor::DEFAULT_PLACEHOLDER,

        'enabled' => true,
    ],
];
