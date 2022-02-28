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

declare(strict_types=1);

namespace CyberSpectrum\I18N\Dictionary;

use CyberSpectrum\I18N\Exception\TranslationNotFoundException;
use CyberSpectrum\I18N\TranslationValue\TranslationValueInterface;
use Traversable;

/**
 * This interface describes a dictionary.
 */
interface DictionaryInterface
{
    /**
     * Obtain all keys within the dictionary.
     *
     * @return Traversable<int, string>
     */
    public function keys(): Traversable;

    /**
     * Obtain the value for a translation key.
     *
     * @param string $key The key to obtain.
     *
     * @return TranslationValueInterface
     *
     * @throws TranslationNotFoundException When the key is not found.
     */
    public function get(string $key): TranslationValueInterface;

    /**
     * Test if the key is contained.
     *
     * @param string $key The key to test.
     *
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Obtain the source language
     *
     * @return string
     */
    public function getSourceLanguage(): string;

    /**
     * Obtain the source language
     *
     * @return string
     */
    public function getTargetLanguage(): string;
}
