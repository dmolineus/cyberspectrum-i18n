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
use CyberSpectrum\I18N\Exception\NotSupportedException;
use CyberSpectrum\I18N\Exception\TranslationNotFoundException;
use CyberSpectrum\I18N\TranslationValue\TranslationValueInterface;

/**
 * This is a compound dictionary.
 */
class CompoundDictionary implements DictionaryInterface
{
    /**
     * The source language.
     *
     * @var string
     */
    private $sourceLanguage;

    /**
     * The target language.
     *
     * @var string
     */
    private $targetLanguage;

    /**
     * The contained dictionaries.
     *
     * @var DictionaryInterface[]
     */
    private $dictionaries;

    /**
     * Create a new instance.
     *
     * @param string $sourceLanguage The source language
     * @param string $targetLanguage The target language.
     */
    public function __construct(string $sourceLanguage, string $targetLanguage)
    {
        $this->sourceLanguage = $sourceLanguage;
        $this->targetLanguage = $targetLanguage;
    }

    /**
     * {@inheritDoc}
     */
    public function keys(): \Traversable
    {
        foreach ($this->dictionaries as $prefix => $dictionary) {
            foreach ($dictionary->keys() as $key) {
                yield $prefix . '.' . $key;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $key): TranslationValueInterface
    {
        /** @var DictionaryInterface $dictionary */
        [$dictionary, $remainder, $prefix] = $this->splitDictionaryRemainderAndPrefix($key);

        return new TranslationValue($prefix, $dictionary->get($remainder));
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $key): bool
    {
        /** @var DictionaryInterface $dictionary */
        [$dictionary, $remainder] = $this->splitDictionaryRemainderAndPrefix($key);

        return $dictionary->has($remainder);
    }

    /**
     * {@inheritDoc}
     */
    public function getSourceLanguage(): string
    {
        return $this->sourceLanguage;
    }

    /**
     * {@inheritDoc}
     */
    public function getTargetLanguage(): string
    {
        return $this->targetLanguage;
    }

    /**
     * Add a dictionary.
     *
     * @param string              $prefix     The prefix for the dictionary.
     * @param DictionaryInterface $dictionary The dictionary to add.
     *
     * @return static
     *
     * @throws \RuntimeException When already a dictionary with the prefix is contained.
     * @throws NotSupportedException On language mismatch.
     */
    public function addDictionary(string $prefix, DictionaryInterface $dictionary)
    {
        if (isset($this->dictionaries[$prefix])) {
            throw new \RuntimeException('A dictionary with prefix "' . $prefix . '" has already been added.');
        }

        if ($this->sourceLanguage !== $real = $dictionary->getSourceLanguage()) {
            throw new NotSupportedException($this, sprintf(
                'Languages can not be mixed in compounds, expected source "%1$s" but got "%2$s"',
                $this->sourceLanguage,
                $real
            ));
        }

        if ($this->targetLanguage !== $real = $dictionary->getTargetLanguage()) {
            throw new NotSupportedException($this, sprintf(
                'Languages can not be mixed in compounds, expected target "%1$s" but got "%2$s"',
                $this->targetLanguage,
                $real
            ));
        }

        $this->dictionaries[$prefix] = $dictionary;

        return $this;
    }

    /**
     * Obtain the dictionary for the key.
     *
     * Returns:
     *   [DictionaryInterface, remainder]
     *
     * @param string $key The key.
     *
     * @return array
     */
    protected function splitDictionaryRemainderAndPrefix(string $key): array
    {
        [$remainder, $prefix] = $this->splitRemainderAndPrefix($key);

        return [$this->dictionaries[$prefix], $remainder, $prefix];
    }

    /**
     * Obtain the dictionary for the key.
     *
     * Returns:
     *   [prefix, remainder]
     *
     * @param string $key The key.
     *
     * @return array
     *
     * @throws \InvalidArgumentException    When the key has an invalid format.
     * @throws TranslationNotFoundException When no dictionary for the key prefix is registered.
     */
    protected function splitRemainderAndPrefix(string $key): array
    {
        if (false === ($pos = strpos($key, '.'))) {
            throw new \InvalidArgumentException('Key "' . $key . '" has invalid format.');
        }

        $prefix = substr($key, 0, $pos);
        if (!array_key_exists($prefix, $this->dictionaries)) {
            throw new TranslationNotFoundException($key, $this);
        }

        return [substr($key, ($pos + 1)), $prefix];
    }
}
