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

use CyberSpectrum\I18N\Configuration\Configuration;

/**
 * This provides a way to "wrap" a parent definition and enhance it with own values.
 */
class ReferencedJobDefinition extends Definition
{
    use ExtendedDefinitionTrait;

    /**
     * The configuration.
     *
     * @var Configuration
     */
    protected $configuration;

    /**
     * Create a new instance.
     *
     * @param string        $name          The name for the dictionary.
     * @param Configuration $configuration The configuration to use.
     * @param array         $data          The configuration values.
     */
    public function __construct(string $name, Configuration $configuration, array $data = [])
    {
        parent::__construct($name, $data);
        $this->configuration = $configuration;
    }

    /**
     * Obtain the delegator.
     *
     * @return \CyberSpectrum\I18N\Configuration\Definition\Definition
     */
    public function getDelegated(): Definition
    {
        return $this->configuration->getJob($this->getName());
    }
}
