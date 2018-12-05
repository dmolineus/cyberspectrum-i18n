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

namespace CyberSpectrum\I18N\Test\Configuration\Definition;

use CyberSpectrum\I18N\Configuration\Definition\ExtendedDefinitionTrait;
use CyberSpectrum\I18N\Configuration\Definition\Definition;

/**
 * This is a mock for the extended definition trait.
 */
class ExtendedDefinitionTraitMock extends Definition
{
    use ExtendedDefinitionTrait;

    /**
     * @var Definition
     */
    private $delegated;

    /**
     * Create a new instance.
     *
     * @param string     $name      The name.
     * @param Definition $delegated The delegator.
     * @param array      $data      The data.
     */
    public function __construct(string $name, Definition $delegated, array $data = [])
    {
        parent::__construct($name, $data);
        $this->delegated = $delegated;
    }

    /**
     * {@inheritDoc}
     */
    protected function getDelegated(): Definition
    {
        return $this->delegated;
    }
}
