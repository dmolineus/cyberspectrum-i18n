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

use CyberSpectrum\I18N\Configuration\Definition\Definition;
use PHPUnit\Framework\TestCase;

/**
 * This tests the extended definition class.
 *
 * @covers \CyberSpectrum\I18N\Configuration\Definition\ExtendedDefinitionTrait
 */
class ExtendedDefinitionTraitTest extends TestCase
{
    /**
     * Test.
     *
     * @return void
     */
    public function testInitialization(): void
    {
        $definition = $this->mockExtendedDefinition('foo', ['local' => 'local-value'], ['base' => 'base-value']);

        $this->assertSame(['base' => 'base-value', 'local' => 'local-value'], $definition->getData());
        $this->assertTrue($definition->has('base'));
        $this->assertTrue($definition->has('local'));
        $this->assertSame('base-value', $definition->get('base'));
        $this->assertSame('local-value', $definition->get('local'));
        $this->assertFalse($definition->has('undefined'));
        $this->assertSame(['base' => 'base-value', 'local' => 'local-value'], \iterator_to_array($definition->getIterator()));
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testReturnsFromBaseIfNotSet(): void
    {
        $definition = $this->mockExtendedDefinition('foo', [], ['key' => 'value']);

        $this->assertSame('value', $definition->get('key'));
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testGetDataMergesArrays(): void
    {
        $definition = $this->mockExtendedDefinition(
            'foo',
            ['key' => 'value', 'array' => ['local' => 'value-local']],
            ['key' => 'value-overridden', 'array' => ['base' => 'value-base']]
        );

        $this->assertSame(
            ['key' => 'value', 'array' => ['base' => 'value-base', 'local' => 'value-local']],
            $definition->getData()
        );
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testGetDataThrowsForInvalidArrayMerge(): void
    {
        $definition = $this->mockExtendedDefinition('foo', ['key' => ['key1' => 'value']], ['key' => 'value']);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Can not merge key "key", parent value is not an array.');

        $definition->getData();
    }

    /**
     * Mock an extended definition.
     *
     * @param string $name     The name.
     * @param array  $data     The data.
     * @param array  $baseData The data for the delegator.
     *
     * @return ExtendedDefinitionTraitMock
     */
    private function mockExtendedDefinition(
        string $name,
        array $data = [],
        array $baseData = []
    ): ExtendedDefinitionTraitMock {
        return new ExtendedDefinitionTraitMock($name, new Definition($name, $baseData), $data);
    }
}
