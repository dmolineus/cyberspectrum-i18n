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

namespace CyberSpectrum\I18N\Configuration\Definition;

/**
 * This defines a dictionary.
 */
class DictionaryDefinition extends Definition
{
    /**
     * Obtain the type.
     *
     * @return string
     *
     * @throws \RuntimeException When no type has been set.
     */
    public function getType(): string
    {
        if (!$this->has('type')) {
            throw new \RuntimeException('No type set for dictionary "' . $this->getName() . '"');
        }
        return $this->get('type');
    }

    /**
     * Obtain the provider - falls back to the type if no special provider is set.
     *
     * @return string
     *
     * @throws \RuntimeException When no provider or type has been set.
     */
    public function getProvider(): string
    {
        return $this->has('provider') ? $this->get('provider') : $this->getType();
    }

    /**
     * Obtain the dictionary name - falls back to the name if no special dictionary is set.
     *
     * @return string
     *
     * @throws \RuntimeException When no provider or type has been set.
     */
    public function getDictionary(): string
    {
        return $this->has('dictionary') ? $this->get('dictionary') : $this->getName();
    }

    /**
     * Obtain the source language.
     *
     * @return string
     *
     * @throws \RuntimeException When no source language has been set.
     */
    public function getSourceLanguage(): string
    {
        if (!$this->has('source_language')) {
            throw new \RuntimeException('No source language set for dictionary "' . $this->getName() . '"');
        }
        return $this->get('source_language');
    }

    /**
     * Obtain the target language.
     *
     * @return string
     *
     * @throws \RuntimeException When no target language has been set.
     */
    public function getTargetLanguage(): string
    {
        if (!$this->has('target_language')) {
            throw new \RuntimeException('No target language set for dictionary "' . $this->getName() . '"');
        }
        return $this->get('target_language');
    }
}
