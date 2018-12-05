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
 * This class provides information about a dictionary.
 */
class DictionaryInformation
{
    /**
     * The name of the dictionary.
     *
     * @var string
     */
    private $name;

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
     * @param string $name           The name of the dictionary.
     * @param string $sourceLanguage The source language.
     * @param string $targetLanguage The target language.
     */
    public function __construct(string $name, string $sourceLanguage, string $targetLanguage)
    {
        $this->name           = $name;
        $this->sourceLanguage = $sourceLanguage;
        $this->targetLanguage = $targetLanguage;
    }

    /**
     * Retrieve name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Retrieve sourceLanguage.
     *
     * @return string
     */
    public function getSourceLanguage(): string
    {
        return $this->sourceLanguage;
    }

    /**
     * Retrieve targetLanguage.
     *
     * @return string
     */
    public function getTargetLanguage(): string
    {
        return $this->targetLanguage;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return sprintf('%s %s => %s', $this->name, $this->sourceLanguage, $this->targetLanguage);
    }
}
