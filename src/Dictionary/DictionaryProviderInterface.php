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

namespace CyberSpectrum\I18N\Dictionary;

/**
 * This interface describes a dictionary provider.
 */
interface DictionaryProviderInterface
{
    /**
     * Obtain the list of available dictionary names.
     *
     * @return \Traversable|DictionaryInformation[]
     */
    public function getAvailableDictionaries(): \Traversable;

    /**
     * Obtain a dictionary by name.
     *
     * @param string $name           The dictionary name.
     * @param string $sourceLanguage The source language.
     * @param string $targetLanguage The target language.
     * @param array  $customData     Custom data for initialization - nature is subject to the implementation.
     *
     * @return DictionaryInterface
     */
    public function getDictionary(
        string $name,
        string $sourceLanguage,
        string $targetLanguage,
        array $customData = []
    ): DictionaryInterface;
}
