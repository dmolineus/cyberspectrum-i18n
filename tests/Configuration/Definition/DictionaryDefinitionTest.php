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

use CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition;
use PHPUnit\Framework\TestCase;

/**
 * This tests the dictionary definition.
 *
 * @covers \CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition
 */
class DictionaryDefinitionTest extends TestCase
{
    /**
     * Test that the getters are correctly evaluated.
     *
     * @return void
     */
    public function testGetters(): void
    {
        $definition = new DictionaryDefinition('foo', [
            'type' => 'dummy',
            'source_language' => 'en',
            'target_language' => 'de'
        ]);

        $this->assertSame('dummy', $definition->getType());
        $this->assertSame('dummy', $definition->getProvider());
        $this->assertSame('en', $definition->getSourceLanguage());
        $this->assertSame('de', $definition->getTargetLanguage());
    }

    /**
     * Test that the provider may be overridden.
     *
     * @return void
     */
    public function testGetProvider(): void
    {
        $definition = new DictionaryDefinition('foo', [
            'type'            => 'dummy',
            'provider'        => 'provider',
            'source_language' => 'en',
            'target_language' => 'de'
        ]);

        $this->assertSame('dummy', $definition->getType());
        $this->assertSame('provider', $definition->getProvider());
        $this->assertSame('en', $definition->getSourceLanguage());
        $this->assertSame('de', $definition->getTargetLanguage());
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testThrowsForMissingType(): void
    {
        $definition = new DictionaryDefinition('foo', []);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No type set for dictionary "foo"');

        $definition->getType();
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testThrowsForMissingSourceLanguage(): void
    {
        $definition = new DictionaryDefinition('foo', []);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No source language set for dictionary "foo"');

        $definition->getSourceLanguage();
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testThrowsForMissingTargetLanguage(): void
    {
        $definition = new DictionaryDefinition('foo', []);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No target language set for dictionary "foo"');

        $definition->getTargetLanguage();
    }
}
