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

namespace CyberSpectrum\I18N\Memory;

use CyberSpectrum\I18N\Dictionary\DictionaryInformation;
use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use CyberSpectrum\I18N\Dictionary\DictionaryProviderInterface;
use CyberSpectrum\I18N\Dictionary\WritableDictionaryInterface;
use CyberSpectrum\I18N\Dictionary\WritableDictionaryProviderInterface;
use CyberSpectrum\I18N\Exception\DictionaryNotFoundException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

/**
 * This provides access to the xliff translations in the store.
 */
class MemoryDictionaryProvider implements
    DictionaryProviderInterface,
    WritableDictionaryProviderInterface,
    LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * The dictionaries.
     *
     * @var MemoryDictionary[]
     */
    private $dictionaries = [];

    /**
     * Create a new instance.
     */
    public function __construct()
    {
        $this->setLogger(new NullLogger());
    }

    /**
     * {@inheritDoc}
     *
     * @return \Traversable|DictionaryInformation[]
     */
    public function getAvailableDictionaries(): \Traversable
    {
        foreach ($this->dictionaries as $name => $dictionary) {
            yield $this->createInformation($name, $dictionary);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @throws DictionaryNotFoundException When the dictionary has not been created.
     */
    public function getDictionary(
        string $name,
        string $sourceLanguage,
        string $targetLanguage,
        array $customData = []
    ): DictionaryInterface {
        $this->logger->debug('Memory: opening dictionary ' . $name);
        foreach ($this->dictionaries as $dictionaryName => $dictionary) {
            if ($dictionaryName === $name
                && $sourceLanguage === $dictionary->getSourceLanguage()
                && $targetLanguage === $dictionary->getTargetLanguage()
            ) {
                return $dictionary;
            }
        }

        throw new DictionaryNotFoundException($name, $sourceLanguage, $targetLanguage);
    }

    /**
     * {@inheritDoc}
     */
    public function getAvailableWritableDictionaries(): \Traversable
    {
        foreach ($this->dictionaries as $name => $dictionary) {
            yield $this->createInformation($name, $dictionary);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @throws DictionaryNotFoundException When the dictionary has not been created.
     */
    public function getDictionaryForWrite(
        string $name,
        string $sourceLanguage,
        string $targetLanguage,
        array $customData = []
    ): WritableDictionaryInterface {
        $this->logger->debug('Memory: opening writable dictionary ' . $name);

        foreach ($this->dictionaries as $dictionaryName => $dictionary) {
            if ($dictionaryName === $name
                && $sourceLanguage === $dictionary->getSourceLanguage()
                && $targetLanguage === $dictionary->getTargetLanguage()
            ) {
                return $dictionary;
            }
        }

        throw new DictionaryNotFoundException($name, $sourceLanguage, $targetLanguage);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException When the dictionary has already been created.
     */
    public function createDictionary(
        string $name,
        string $sourceLanguage,
        string $targetLanguage,
        array $customData = []
    ): WritableDictionaryInterface {
        $this->logger->debug('Memory: creating new dictionary ' . $name);

        foreach ($this->dictionaries as $dictionaryName => $dictionary) {
            if ($dictionaryName === $name
                && $sourceLanguage === $dictionary->getSourceLanguage()
                && $targetLanguage === $dictionary->getTargetLanguage()
            ) {
                throw new \InvalidArgumentException('Dictionary ' . $name . ' already exists.');
            }
        }

        return $this->dictionaries[$name] = new MemoryDictionary($sourceLanguage, $targetLanguage, []);
    }

    /**
     * Create an information container for the passed xlf file.
     *
     * @param string           $name       The internal name.
     * @param MemoryDictionary $dictionary The dictionary.
     *
     * @return DictionaryInformation
     */
    private function createInformation(string $name, MemoryDictionary $dictionary): DictionaryInformation
    {
        return new DictionaryInformation($name, $dictionary->getSourceLanguage(), $dictionary->getTargetLanguage());
    }
}
