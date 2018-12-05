<?php

/**
 * This file is part of cyberspectrum/i18n.
 *
 * (c) 2018 CyberSpectrum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    cyberspectrum/i18n
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright  2018 CyberSpectrum.
 * @license    https://github.com/cyberspectrum/i18n/blob/master/LICENSE MIT
 * @filesource
 */

declare(strict_types = 1);

namespace CyberSpectrum\I18N\TranslationValue;

/**
 * This interface describes a translation value.
 */
interface TranslationValueInterface
{
    /**
     * Obtain the translation key (this might be the same as the source value in some implementations).
     *
     * @return string
     */
    public function getKey(): string;

    /**
     * Obtain the source language value.
     *
     * @return string
     */
    public function getSource(): ?string;

    /**
     * Obtain the target language value.
     *
     * @return string|null
     */
    public function getTarget(): ?string;

    /**
     * Check if the source value is empty.
     *
     * @return bool
     */
    public function isSourceEmpty(): bool;

    /**
     * Check if the target value is empty.
     *
     * @return bool
     */
    public function isTargetEmpty(): bool;
}
