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
 * This describes a copy job.
 */
class CopyJobDefinition extends Definition
{
    /**
     * The definition of the source dictionary.
     *
     * @var DictionaryDefinition
     */
    private $source;

    /**
     * The definition of the target dictionary.
     *
     * @var DictionaryDefinition
     */
    private $target;

    /**
     * Create a new instance.
     *
     * @param string               $name   The name of the job.
     * @param DictionaryDefinition $source The source dictionary.
     * @param DictionaryDefinition $target The target dictionary.
     * @param array                $data   The additional data.
     */
    public function __construct(
        string $name,
        DictionaryDefinition $source,
        DictionaryDefinition $target,
        array $data = []
    ) {
        parent::__construct($name, $data);
        $this->source = $source;
        $this->target = $target;
    }

    /**
     * Retrieve source.
     *
     * @return DictionaryDefinition
     */
    public function getSource(): DictionaryDefinition
    {
        return $this->source;
    }

    /**
     * Retrieve target.
     *
     * @return DictionaryDefinition
     */
    public function getTarget(): DictionaryDefinition
    {
        return $this->target;
    }
}
