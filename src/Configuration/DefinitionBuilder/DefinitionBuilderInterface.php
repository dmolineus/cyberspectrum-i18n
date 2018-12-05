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

namespace CyberSpectrum\I18N\Configuration\DefinitionBuilder;

use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\Definition;

/**
 * This is a simple key value store.
 */
interface DefinitionBuilderInterface
{
    /**
     * Build a definition from the passed values.
     *
     * @param Configuration $configuration The configuration.
     * @param array         $data          The configuration values.
     *
     * @return \CyberSpectrum\I18N\Configuration\Definition\Definition
     */
    public function build(Configuration $configuration, array $data): Definition;
}
