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

namespace CyberSpectrum\I18N\Configuration;

/**
 * This loads a config file.
 */
abstract class AbstractConfigurationLoader
{
    /**
     * Load a configuration.
     *
     * @param mixed              $source The configuration to load (file name or the like).
     * @param Configuration|null $config The configuration to load or null.
     *
     * @return Configuration
     *
     * @throws \InvalidArgumentException When the configuration type is unsupported.
     */
    public function load($source, Configuration $config = null): Configuration
    {
        if (null === $config) {
            $config = new Configuration();
        }
        $loader = $this->getLoader($source, $config);
        if (!$loader->supports($source)) {
            throw new \InvalidArgumentException('Unsupported configuration.');
        }

        $loader->load($source);

        return $config;
    }

    /**
     * Get a loader to populate the passed configuration.
     *
     * @param mixed         $source        The configuration source.
     * @param Configuration $configuration The configuration to populate.
     *
     * @return LoaderInterface
     */
    abstract protected function getLoader($source, Configuration $configuration): LoaderInterface;
}
