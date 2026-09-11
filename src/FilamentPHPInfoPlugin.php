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

    /**
     * Redact these too, wherever they appear. Exact names or * wildcards.
     */
    public function redact(string ...$names): static
    {
        $this->getRedactor()->redact(...$names);

        return $this;
    }

    /**
     * Always show these, whatever else matches. Exact names or * wildcards.
     */
    public function reveal(string ...$names): static
    {
        $this->getRedactor()->reveal(...$names);

        return $this;
    }

    public function placeholder(string $text): static
    {
        $this->getRedactor()->placeholder($text);

        return $this;
    }

    public function withoutRedaction(): static
    {
        $this->getRedactor()->disable();

        return $this;
    }

    public function getRedactor(): Redactor
    {
        return $this->redactor ??= Redactor::make();
    }
}
