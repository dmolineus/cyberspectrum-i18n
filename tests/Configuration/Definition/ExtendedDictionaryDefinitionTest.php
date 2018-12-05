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

use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition;
use CyberSpectrum\I18N\Configuration\Definition\ExtendedDictionaryDefinition;
use PHPUnit\Framework\TestCase;

/**
 * This tests the dictionary definition.
 *
 * @covers \CyberSpectrum\I18N\Configuration\Definition\ExtendedDictionaryDefinition
 */
class ExtendedDictionaryDefinitionTest extends TestCase
{
    /**
     * Test that the getters are correctly evaluated.
     *
     * @return void
     */
    public function testGettersFromTrait(): void
    {
        $configuration = new Configuration();
        $configuration->setDictionary(new DictionaryDefinition('foo', [
            'type' => 'dummy',
            'source_language' => 'en',
            'target_language' => 'de'
        ]));

        $definition = new ExtendedDictionaryDefinition('foo', $configuration, [
            'type' => 'override',
        ]);

        $this->assertSame('override', $definition->getType());
        $this->assertSame('en', $definition->getSourceLanguage());
        $this->assertSame('de', $definition->getTargetLanguage());
    }
}
