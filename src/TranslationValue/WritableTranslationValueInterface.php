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
