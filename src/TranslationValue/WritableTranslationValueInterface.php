<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\TranslationValue;

/**
 * This interface describes a writable translation value.
 */
interface WritableTranslationValueInterface extends TranslationValueInterface
{
    /**
     * Set the source value.
     *
     * @param string $value The new value.
     *
     * @return static
     */
    public function setSource(string $value);

    /**
     * Set the target value.
     *
     * @param string $value The new value.
     *
     * @return static
     */
    public function setTarget(string $value);

    /**
     * Clear the source value.
     *
     * @return static
     */
    public function clearSource();

    /**
     * Clear the target value.
     *
     * @return static
     */
    public function clearTarget();
}
