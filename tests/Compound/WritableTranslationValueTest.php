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

use CyberSpectrum\I18N\Compound\WritableTranslationValue;
use CyberSpectrum\I18N\TranslationValue\WritableTranslationValueInterface;
use PHPUnit\Framework\TestCase;

/**
 * This tests the configuration resolver.
 *
 * @covers \CyberSpectrum\I18N\Compound\WritableTranslationValue
 */
class WritableTranslationValueTest extends TestCase
{
    /**
     * Test.
     *
     * @return void
     */
    public function testDelegates(): void
    {
        $value = $this->getMockForAbstractClass(WritableTranslationValueInterface::class);
        $value->expects($this->once())->method('setSource')->with('source')->willReturn($value);
        $value->expects($this->once())->method('setTarget')->with('target')->willReturn($value);
        $value->expects($this->once())->method('clearSource')->willReturn($value);
        $value->expects($this->once())->method('clearTarget')->willReturn($value);

        $compound = new WritableTranslationValue('child', $value);

        $this->assertInstanceOf(WritableTranslationValue::class, $compound);
        $this->assertSame($compound, $compound->setSource('source'));
        $this->assertSame($compound, $compound->setTarget('target'));
        $this->assertSame($compound, $compound->clearSource());
        $this->assertSame($compound, $compound->clearTarget());
    }
}
