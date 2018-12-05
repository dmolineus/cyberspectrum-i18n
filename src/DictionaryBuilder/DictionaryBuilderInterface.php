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

namespace CyberSpectrum\I18N\DictionaryBuilder;

use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use CyberSpectrum\I18N\Dictionary\WritableDictionaryInterface;
use CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition;
use CyberSpectrum\I18N\Job\JobFactory;

/**
 * This describes a dictionary builder.
 */
interface DictionaryBuilderInterface
{
    /**
     * Build a dictionary from the passed definition.
     *
     * @param JobFactory           $factory    The job builder for recursive calls.
     * @param DictionaryDefinition $definition The definition.
     *
     * @return DictionaryInterface
     */
    public function build(JobFactory $factory, DictionaryDefinition $definition): DictionaryInterface;

    /**
     * Build a writable dictionary from the passed definition.
     *
     * @param JobFactory           $factory    The job builder for recursive calls.
     * @param DictionaryDefinition $definition The definition.
     *
     * @return WritableDictionaryInterface
     */
    public function buildWritable(JobFactory $factory, DictionaryDefinition $definition): WritableDictionaryInterface;
}
