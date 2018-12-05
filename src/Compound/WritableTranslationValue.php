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

    /**
     * {@inheritDoc}
     */
    public function setTarget(string $value)
    {
        $this->delegate->setTarget($value);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function clearSource()
    {
        $this->delegate->clearSource();

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function clearTarget()
    {
        $this->delegate->clearTarget();

        return $this;
    }
}
