<?php

namespace STS\FilamentPHPInfo;

use Illuminate\Support\Str;
use STS\Phpinfo\Models\Config;
use STS\Phpinfo\Models\Group;
use STS\Phpinfo\Models\Module;
use STS\Phpinfo\PhpInfo;

class Redactor
{
    /**
     * An environment variable whose name contains one of these is redacted.
     */
    public const TERMS = [
        'PASSWORD', 'KEY', 'SECRET', 'TOKEN', 'CREDENTIAL', 'PRIVATE',
        'SALT', 'SIGNING', 'SIGNATURE', 'DSN', 'LICENSE', 'WEBHOOK',
    ];

    /**
     * phpinfo() prints the process environment under these two headings and nowhere else. The
     * terms apply only here, so PHP settings named "Max keys" or "Tokenizer Support" keep their
     * values. Names passed to redact() and reveal() apply everywhere.
     */
    public const ENVIRONMENT_MODULES = ['Environment', 'PHP Variables'];

    protected bool $enabled = true;

    protected string $placeholder = '[redacted]';

    /** @var array<int, string> */
    protected array $redact = [];

    /** @var array<int, string> */
    protected array $reveal = [];

    public static function make(): static
    {
        return new static;
    }

    /**
     * Redact these too, wherever they appear. Exact names or * wildcards.
     */
    public function redact(string ...$names): static
    {
        $this->redact = [...$this->redact, ...array_map(static::normalize(...), $names)];

        return $this;
    }

    /**
     * Always show these, whatever else matches. Exact names or * wildcards.
     */
    public function reveal(string ...$names): static
    {
        $this->reveal = [...$this->reveal, ...array_map(static::normalize(...), $names)];

        return $this;
    }

    public function placeholder(string $text): static
    {
        $this->placeholder = $text;

        return $this;
    }

    public function disable(): static
    {
        $this->enabled = false;

        return $this;
    }

    public function apply(PhpInfo $info): PhpInfo
    {
        if (! $this->enabled) {
            return $info;
        }

        return new PhpInfo($info->version(), $info->modules()->map(
            fn (Module $module) => new Module($module->name(), $module->groups()->map(
                fn (Group $group) => new Group(
                    $group->configs()->map(fn (Config $config) => $this->redactConfig($module, $config)),
                    $group->headings(),
                    $group->name(),
                    $group->note(),
                ),
            )),
        ));
    }

    public function shouldRedact(string $module, string $name): bool
    {
        $variable = static::normalize($name);

        if (Str::is($this->reveal, $variable)) {
            return false;
        }

        if (Str::is($this->redact, $variable)) {
            return true;
        }

        return in_array($module, static::ENVIRONMENT_MODULES, true)
            && Str::contains($variable, static::TERMS);
    }

    protected function redactConfig(Module $module, Config $config): Config
    {
        if (! $this->shouldRedact($module->name(), $config->name())) {
            return $config;
        }

        return new Config(
            $config->name(),
            $this->mask($config->localValue()),
            $this->mask($config->masterValue()),
            $config->hasMasterValue(),
        );
    }

    /**
     * An empty value leaks nothing. Leave it, so the page still shows the variable is unset.
     */
    protected function mask(?string $value): ?string
    {
        return $value === null || $value === '' ? $value : $this->placeholder;
    }

    /**
     * phpinfo() prints one variable as APP_KEY, $_ENV['APP_KEY'] and $_SERVER['APP_KEY'].
     * Reduce every form to APP_KEY, and ignore case.
     */
    protected static function normalize(string $name): string
    {
        $name = trim($name);

        if (preg_match('/^\$_[A-Z]+\[[\'"]?(.*?)[\'"]?\]$/', $name, $matches)) {
            $name = $matches[1];
        }

        return strtoupper($name);
    }
}
