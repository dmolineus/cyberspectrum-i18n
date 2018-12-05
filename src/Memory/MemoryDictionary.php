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

use CyberSpectrum\I18N\Dictionary\WritableDictionaryInterface;
use CyberSpectrum\I18N\Exception\TranslationAlreadyContainedException;
use CyberSpectrum\I18N\Exception\TranslationNotFoundException;
use CyberSpectrum\I18N\TranslationValue\TranslationValueInterface;
use CyberSpectrum\I18N\TranslationValue\WritableTranslationValueInterface;

/**
 * This is a simple static memory implementation of a dictionary..
 */
class MemoryDictionary implements WritableDictionaryInterface
{
    /**
     * The translation buffer.
     *
     * @var MemoryTranslationValue[]
     */
    protected $translationBuffer = [];

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
     * Create a new instance.
     *
     * The translation buffer has to hold the values as:
     * [
     *   'key' => [
     *     'source' => 'Source string',
     *     'target' => 'Target string',
     *   ]
     * ]
     *
     * @param string $sourceLanguage    The source language.
     * @param string $targetLanguage    The target language.
     * @param array  $translationBuffer The translation buffer.
     */
    public function __construct(string $sourceLanguage, string $targetLanguage, array $translationBuffer)
    {
        $this->sourceLanguage = $sourceLanguage;
        $this->targetLanguage = $targetLanguage;
        foreach ($translationBuffer as $key => $item) {
            $this->addItem($key, $item);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function keys(): \Traversable
    {
        return new \ArrayIterator(array_keys($this->translationBuffer));
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $key): TranslationValueInterface
    {
        return $this->getWritable($key);
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->translationBuffer);
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
     * {@inheritDoc}
     *
     * @throws TranslationAlreadyContainedException When the translation already exists.
     */
    public function add(string $key): WritableTranslationValueInterface
    {
        if ($this->has($key)) {
            throw new TranslationAlreadyContainedException($key, $this);
        }

        return $this->translationBuffer[$key] = new MemoryTranslationValue($key);
    }

    /**
     * {@inheritDoc}
     *
     * @throws TranslationNotFoundException When the translation does not exist.
     */
    public function remove(string $key): void
    {
        if (!$this->has($key)) {
            throw new TranslationNotFoundException($key, $this);
        }

        unset($this->translationBuffer[$key]);
    }

    /**
     * {@inheritDoc}
     *
     * @throws TranslationNotFoundException When the translation does not exist.
     */
    public function getWritable($key): WritableTranslationValueInterface
    {
        if (!$this->has($key)) {
            throw new TranslationNotFoundException($key, $this);
        }

        return $this->translationBuffer[$key];
    }

    /**
     * Add an element with the passed values.
     *
     * @param string $key    The key to add.
     * @param array  $values The values to use.
     *
     * @return void
     *
     * @throws \InvalidArgumentException When the passed array is invalid.
     */
    protected function addItem(string $key, array $values): void
    {
        if (!array_key_exists('source', $values) || !array_key_exists('target', $values)) {
            throw new \InvalidArgumentException('Invalid translation array: ' . var_export($values, true));
        }

        $this->translationBuffer[$key] = new MemoryTranslationValue($key, $values['source'], $values['target']);
    }
}
