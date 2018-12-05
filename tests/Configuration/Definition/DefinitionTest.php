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
 * This tests the definition class.
 *
 * @covers \CyberSpectrum\I18N\Configuration\Definition\Definition
 */
class DefinitionTest extends TestCase
{
    /**
     * Test.
     *
     * @return void
     */
    public function testInitialization(): void
    {
        $definition = new Definition('foo', ['initial' => 'value']);

        $this->assertSame('foo', $definition->getName());
        $this->assertSame(['initial' => 'value'], $definition->getData());
        $this->assertTrue($definition->has('initial'));
        $this->assertSame('value', $definition->get('initial'));
        $this->assertFalse($definition->has('undefined'));
        $this->assertSame(['initial' => 'value'], \iterator_to_array($definition->getIterator()));
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testDataCanGetReplaced(): void
    {
        $definition = new Definition('foo', ['initial' => 'value', 'set' => 'it']);

        $definition->setData(['new' => 'value']);
        $definition->set('set', 'this');

        $this->assertSame(['new' => 'value', 'set' => 'this'], $definition->getData());
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testGetThrowsForUnknownKeys(): void
    {
        $definition = new Definition('foo');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Key "unknown" does not exist.');

        $definition->get('unknown');
    }
}
