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
 * This defines a decorated dictionary.
 */
class DecoratedDictionaryDefinition extends DictionaryDefinition
{
    use ExtendedDefinitionTrait;

    /**
     * The delegated definition.
     *
     * @var DictionaryDefinition
     */
    private $delegated;

    /**
     * Create a new instance.
     *
     * @param DictionaryDefinition $definition The definition to decorate.
     * @param array                $overrides  The values to decorate with.
     */
    public function __construct(DictionaryDefinition $definition, array $overrides)
    {
        parent::__construct($definition->getName(), $overrides);
        $this->delegated = $definition;
    }

    /**
     * {@inheritDoc}
     */
    protected function getDelegated(): Definition
    {
        return $this->delegated;
    }
}
