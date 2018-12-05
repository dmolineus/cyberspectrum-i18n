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

namespace CyberSpectrum\I18N\Test\Compound;

use CyberSpectrum\I18N\Compound\TranslationValue;
use CyberSpectrum\I18N\TranslationValue\TranslationValueInterface;
use PHPUnit\Framework\TestCase;

/**
 * This tests the configuration resolver.
 *
 * @covers \CyberSpectrum\I18N\Compound\TranslationValue
 */
class TranslationValueTest extends TestCase
{
    /**
     * Test.
     *
     * @return void
     */
    public function testDelegates(): void
    {
        $value = $this->getMockForAbstractClass(TranslationValueInterface::class);
        $value->expects($this->once())->method('getKey')->with()->willReturn('key');
        $value->expects($this->once())->method('getSource')->with()->willReturn('source');
        $value->expects($this->once())->method('getTarget')->with()->willReturn('target');
        $value->expects($this->once())->method('isSourceEmpty')->with()->willReturn(false);
        $value->expects($this->once())->method('isTargetEmpty')->with()->willReturn(false);

        $compound = new TranslationValue('child', $value);

        $this->assertInstanceOf(TranslationValue::class, $compound);
        $this->assertSame('child.key', $compound->getKey());
        $this->assertSame('source', $compound->getSource());
        $this->assertSame('target', $compound->getTarget());
        $this->assertFalse($compound->isSourceEmpty());
        $this->assertFalse($compound->isTargetEmpty());
    }
}
