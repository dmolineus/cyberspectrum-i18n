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
use CyberSpectrum\I18N\Configuration\Definition\ExtendedDictionaryDefinition;
use CyberSpectrum\I18N\Configuration\Definition\CopyJobDefinition;

/**
 * This is a simple key value store.
 */
class CopyJobDefinitionBuilder implements DefinitionBuilderInterface
{
    /**
     * Build a definition from the passed values.
     *
     * @param Configuration $configuration The configuration.
     * @param array         $data          The configuration values.
     *
     * @return Definition
     *
     * @throws \InvalidArgumentException When a required key is missing.
     */
    public function build(Configuration $configuration, array $data): Definition
    {
        foreach (['name', 'source', 'target'] as $key) {
            if (!array_key_exists($key, $data)) {
                throw new \InvalidArgumentException('Missing key "' . $key . '"');
            }
        }
        $overrides = $this->getDictionaryOverrides($data);
        $name      = $data['name'];
        $source    = $this->makeDictionary($configuration, $data['source'], $overrides, $name . '.source');
        $target    = $this->makeDictionary($configuration, $data['target'], $overrides, $name . '.target');
        unset($data['name'], $data['source'], $data['target']);

        return new CopyJobDefinition($name, $source, $target, $data);
    }

    /**
     * Obtain the overrides for a dictionary.
     *
     * @param array $data The job configuration data.
     *
     * @return array
     */
    private function getDictionaryOverrides(array &$data): array
    {
        $overrides = [];
        foreach (['source_language', 'target_language'] as $key) {
            if (array_key_exists($key, $data)) {
                $overrides[$key] = $data[$key];
                unset($data[$key]);
            }
        }

        return $overrides;
    }

    /**
     * Make the passed value a valid dictionary.
     *
     * @param Configuration $configuration The configuration.
     * @param string|array  $dictionary    The dictionary.
     * @param array         $overrides     The values to be overridden.
     * @param string        $path          The path for exceptions.
     *
     * @return ExtendedDictionaryDefinition
     *
     * @throws \InvalidArgumentException When the name key is missing.
     */
    private function makeDictionary(
        Configuration $configuration,
        $dictionary,
        array $overrides,
        string $path
    ): ExtendedDictionaryDefinition {
        $name = $dictionary;
        if (is_array($dictionary)) {
            if (!isset($dictionary['name'])) {
                throw new \InvalidArgumentException('Dictionary "' . $path . '" information is missing key "name".');
            }
            $name      = $dictionary['name'];
            $overrides = array_merge($overrides, $dictionary);
            unset($overrides['name']);
        }

        return new ExtendedDictionaryDefinition($name, $configuration, $overrides);
    }
}
