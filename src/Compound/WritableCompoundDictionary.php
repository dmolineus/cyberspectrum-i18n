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

use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use CyberSpectrum\I18N\Dictionary\WritableDictionaryInterface;
use CyberSpectrum\I18N\TranslationValue\WritableTranslationValueInterface;

/**
 * This is a writable compound dictionary.
 */
class WritableCompoundDictionary extends CompoundDictionary implements WritableDictionaryInterface
{
    /**
     * {@inheritDoc}
     */
    public function add(string $key): WritableTranslationValueInterface
    {
        /** @var WritableDictionaryInterface $dictionary */
        [$dictionary, $remainder, $prefix] = $this->splitDictionaryRemainderAndPrefix($key);

        return new WritableTranslationValue($prefix, $dictionary->add($remainder));
    }

    /**
     * {@inheritDoc}
     */
    public function remove(string $key): void
    {
        /** @var WritableDictionaryInterface $dictionary */
        [$dictionary, $remainder] = $this->splitDictionaryRemainderAndPrefix($key);

        $dictionary->remove($remainder);
    }

    /**
     * {@inheritDoc}
     */
    public function getWritable($key): WritableTranslationValueInterface
    {
        /** @var WritableDictionaryInterface $dictionary */
        [$dictionary, $remainder, $prefix] = $this->splitDictionaryRemainderAndPrefix($key);

        return new WritableTranslationValue($prefix, $dictionary->getWritable($remainder));
    }

    /**
     * Add a dictionary.
     *
     * @param string              $prefix     The prefix for the dictionary.
     * @param DictionaryInterface $dictionary The dictionary to add.
     *
     * @return static
     *
     * @throws \InvalidArgumentException When the dictionary is not writable.
     */
    public function addDictionary(string $prefix, DictionaryInterface $dictionary)
    {
        if (!$dictionary instanceof WritableDictionaryInterface) {
            throw new \InvalidArgumentException('Dictionaries in ' . __CLASS__ . ' must be writable.');
        }
        parent::addDictionary($prefix, $dictionary);

        return $this;
    }
}
