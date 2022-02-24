<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Memory;

use CyberSpectrum\I18N\TranslationValue\WritableTranslationValueInterface;

/**
 * This implements a simple translation value - the value is stored in a local property.
 */
class MemoryTranslationValue implements WritableTranslationValueInterface
{
    /** The translation key. */
    private string $key;

    /** The source value. */
    private ?string $source;

    /** The target value. */
    private ?string $target;

    public function __construct(string $key, ?string $source, ?string $target)
    {
        $this->key    = $key;
        $this->source = $source;
        $this->target = $target;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function isSourceEmpty(): bool
    {
        return !$this->getSource();
    }

    public function isTargetEmpty(): bool
    {
        return !$this->getTarget();
    }

    public function setSource(string $value): self
    {
        $this->source = $value;

        return $this;
    }

    public function setTarget(string $value): self
    {
        $this->target = $value;

        return $this;
    }

    public function clearSource(): self
    {
        $this->source = null;

        return $this;
    }

    public function clearTarget(): self
    {
        $this->target = null;

        return $this;
    }
}
