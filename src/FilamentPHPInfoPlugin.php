<?php

namespace STS\FilamentPHPInfo;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentPHPInfoPlugin implements Plugin
{
    protected ?Redactor $redactor = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-phpinfo';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->pages([
                Pages\PHPInfo::class,
            ]);
    }

    public function boot(Panel $panel): void {}

    public function redactor(): Redactor
    {
        return $this->redactor ??= Redactor::fromConfig();
    }

    /**
     * Always redact these variables, wherever they appear.
     */
    public function redact(string ...$names): static
    {
        $this->redactor()->redact(...$names);

        return $this;
    }

    /**
     * Redact environment variables whose name contains any of these, on top of the defaults.
     */
    public function redactContaining(string ...$terms): static
    {
        $this->redactor()->redactContaining(...$terms);

        return $this;
    }

    /**
     * Never redact these variables, whatever else matches.
     */
    public function reveal(string ...$names): static
    {
        $this->redactor()->reveal(...$names);

        return $this;
    }

    public function placeholder(string $text): static
    {
        $this->redactor()->placeholder($text);

        return $this;
    }

    /**
     * Print every value, including secrets.
     */
    public function withoutRedaction(): static
    {
        $this->redactor()->disable();

        return $this;
    }
}
