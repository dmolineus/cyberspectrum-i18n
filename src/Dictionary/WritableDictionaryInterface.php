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

use CyberSpectrum\I18N\Exception\TranslationAlreadyContainedException;
use CyberSpectrum\I18N\Exception\TranslationNotFoundException;
use CyberSpectrum\I18N\TranslationValue\WritableTranslationValueInterface;

/**
 * This interface describes a writable dictionary.
 */
interface WritableDictionaryInterface extends DictionaryInterface
{
    /**
     * Add a value.
     *
     * @param string $key The language key to add.
     *
     * @return WritableTranslationValueInterface
     *
     * @throws TranslationAlreadyContainedException When the key is already contained and the dictionary does not allow.
     */
    public function add(string $key): WritableTranslationValueInterface;

    /**
     * Remove a translation value.
     *
     * @param string $key The language key to remove.
     *
     * @return void
     *
     * @throws TranslationNotFoundException When the key is not found.
     */
    public function remove(string $key): void;

    /**
     * Obtain a value for a translation key.
     *
     * @param string $key The key to obtain.
     *
     * @return WritableTranslationValueInterface
     *
     * @throws TranslationNotFoundException When the key is not found and the dictionary does not add automatically.
     */
    public function getWritable($key): WritableTranslationValueInterface;
}
