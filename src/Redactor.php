<?php

namespace STS\FilamentPHPInfo;

class Redactor
{
    /**
     * An environment variable whose name contains one of these is redacted.
     */
    public const DEFAULT_TERMS = [
        'password',
        'key',
        'secret',
        'token',
        'credential',
        'private',
        'salt',
        'signing',
        'signature',
        'dsn',
        'license',
        'webhook',
    ];

    public const DEFAULT_PLACEHOLDER = '[redacted]';

    /**
     * phpinfo() prints the process environment under these two headings, and nowhere else. Term
     * matching is limited to them, so PHP settings named "Max keys", "Cached keys" or "Tokenizer
     * Support" keep their values. Names you list yourself are redacted wherever they appear.
     */
    public const ENVIRONMENT_MODULES = ['Environment', 'PHP Variables'];

    protected bool $enabled = true;

    protected string $placeholder = self::DEFAULT_PLACEHOLDER;

    /** @var array<int, string> */
    protected array $terms = self::DEFAULT_TERMS;

    /** @var array<int, string> */
    protected array $always = [];

    /** @var array<int, string> */
    protected array $never = [];

    public static function make(): static
    {
        return new static;
    }

    /**
     * Build from config/filament-phpinfo.php. Every key is optional, so a config file published
     * before these keys existed still gets the defaults.
     */
    public static function fromConfig(): static
    {
        $config = config('filament-phpinfo.redact', []);

        // 1.2.x used three flat keys. Honour them when the new ones are absent, so upgrading
        // does not silently drop a term a consumer added and start printing that value again.
        $terms = $config['terms'] ?? config('filament-phpinfo.redact-patterns') ?? static::DEFAULT_TERMS;
        $placeholder = $config['placeholder'] ?? config('filament-phpinfo.redact-placeholder') ?? static::DEFAULT_PLACEHOLDER;
        $enabled = $config['enabled'] ?? config('filament-phpinfo.redact-environment') ?? true;

        return static::make()
            ->placeholder($placeholder)
            ->setTerms($terms)
            ->redact(...($config['always'] ?? []))
            ->reveal(...($config['never'] ?? []))
            ->enabled((bool) $enabled);
    }

    /**
     * Always redact these variables, wherever they appear. Names are exact and case-insensitive,
     * and you may write APP_KEY or $_ENV['APP_KEY'] — both match the same variable.
     */
    public function redact(string ...$names): static
    {
        foreach ($names as $name) {
            $this->always[] = static::normalize($name);
        }

        return $this;
    }

    /**
     * Redact environment variables whose name contains any of these. Adds to the defaults rather
     * than replacing them.
     */
    public function redactContaining(string ...$terms): static
    {
        foreach ($terms as $term) {
            $this->terms[] = $term;
        }

        return $this;
    }

    /**
     * Never redact these variables, whatever else matches. Wins over every other rule.
     */
    public function reveal(string ...$names): static
    {
        foreach ($names as $name) {
            $this->never[] = static::normalize($name);
        }

        return $this;
    }

    public function placeholder(string $text): static
    {
        $this->placeholder = $text;

        return $this;
    }

    /**
     * Replace the default terms outright. Prefer redactContaining() unless you mean to drop them.
     *
     * @param  array<int, string>  $terms
     */
    public function setTerms(array $terms): static
    {
        $this->terms = array_values($terms);

        return $this;
    }

    public function enabled(bool $enabled = true): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function disable(): static
    {
        return $this->enabled(false);
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    public function shouldRedact(?string $module, string $name): bool
    {
        if (! $this->enabled) {
            return false;
        }

        $variable = static::normalize($name);

        if (in_array($variable, $this->never, true)) {
            return false;
        }

        if (in_array($variable, $this->always, true)) {
            return true;
        }

        if (! in_array($module, static::ENVIRONMENT_MODULES, true)) {
            return false;
        }

        foreach ($this->terms as $term) {
            if ($term !== '' && str_contains($variable, strtoupper($term))) {
                return true;
            }
        }

        return false;
    }

    public function apply(?string $module, string $name, ?string $value): ?string
    {
        // An unset or empty variable leaks nothing. Leave it, so the page keeps showing which
        // variables carry a value and which do not.
        if ($value === null || $value === '') {
            return $value;
        }

        return $this->shouldRedact($module, $name) ? $this->placeholder : $value;
    }

    /**
     * phpinfo() prints the same variable as APP_KEY under Environment and as $_ENV['APP_KEY'] and
     * $_SERVER['APP_KEY'] under PHP Variables. Reduce every form to the bare name.
     */
    protected static function normalize(string $name): string
    {
        $name = trim($name);

        if (preg_match('/^\$?_?([A-Z]+)\[[\'"]?(.*?)[\'"]?\]$/', $name, $matches)) {
            $name = $matches[2];
        }

        return strtoupper($name);
    }
}
