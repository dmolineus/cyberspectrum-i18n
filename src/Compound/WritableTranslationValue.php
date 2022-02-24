<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Compound;

use CyberSpectrum\I18N\TranslationValue\WritableTranslationValueInterface;

/**
 * This is a writable translation value.
 *
 * @property WritableTranslationValueInterface $delegate
 */
class WritableTranslationValue extends TranslationValue implements WritableTranslationValueInterface
{
    /**
     * {@inheritDoc}
     */
    public function setSource(string $value)
    {
        $this->delegate->setSource($value);

        return $this;
    }

    public function setTarget(string $value)
    {
        $this->delegate->setTarget($value);

        return $this;
    }

    public function clearSource()
    {
        $this->delegate->clearSource();

        return $this;
    }

    public function clearTarget()
    {
        $this->delegate->clearTarget();

        return $this;
    }
}
