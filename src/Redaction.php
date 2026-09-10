<?php

namespace STS\FilamentPHPInfo;

class Redaction
{
    /**
     * phpinfo() prints the process environment under these two PHP-generated headings, and
     * nowhere else. Every other module holds PHP settings, where names such as "Max keys",
     * "Tokenizer Support" and "highlight.keyword" match the patterns below but hold no secret.
     */
    public const ENVIRONMENT_MODULES = ['Environment', 'PHP Variables'];

    public const DEFAULT_PATTERNS = [
        'KEY',
        'SECRET',
        'PASSWORD',
        'TOKEN',
        'CREDENTIAL',
        'PRIVATE',
        'SALT',
        'SIGNING',
        'SIGNATURE',
        'DSN',
        'LICENSE',
        'WEBHOOK',
    ];

    public const DEFAULT_PLACEHOLDER = '[redacted]';

    public static function value(string $module, string $name, ?string $value): ?string
    {
        // An unset or empty variable leaks nothing. Leave it, so the page keeps showing which
        // variables carry a value and which do not.
        if ($value === null || $value === '') {
            return $value;
        }

        return static::isSensitive($module, $name) ? static::placeholder() : $value;
    }

    public static function isSensitive(string $module, string $name): bool
    {
        if (! static::enabled()) {
            return false;
        }

        if (! in_array($module, static::ENVIRONMENT_MODULES, true)) {
            return false;
        }

        $variable = static::variableName($name);

        foreach (static::patterns() as $pattern) {
            if (str_contains($variable, strtoupper($pattern))) {
                return true;
            }
        }

        return false;
    }

    public static function enabled(): bool
    {
        return (bool) config('filament-phpinfo.redact-environment', true);
    }

    public static function placeholder(): string
    {
        return config('filament-phpinfo.redact-placeholder') ?? static::DEFAULT_PLACEHOLDER;
    }

    /**
     * @return array<int, string>
     */
    public static function patterns(): array
    {
        return config('filament-phpinfo.redact-patterns') ?? static::DEFAULT_PATTERNS;
    }

    /**
     * phpinfo() prints the same variable three times, as APP_KEY under Environment and as
     * $_ENV['APP_KEY'] and $_SERVER['APP_KEY'] under PHP Variables. Reduce all three forms to
     * the bare variable name before matching.
     */
    protected static function variableName(string $name): string
    {
        $name = trim($name);

        if (preg_match('/^\$_[A-Z]+\[[\'"]?(.*?)[\'"]?\]$/', $name, $matches)) {
            $name = $matches[1];
        }

        return strtoupper($name);
    }
}
