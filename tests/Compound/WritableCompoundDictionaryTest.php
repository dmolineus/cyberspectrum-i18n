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

use CyberSpectrum\I18N\Compound\WritableCompoundDictionary;
use CyberSpectrum\I18N\Compound\WritableTranslationValue;
use CyberSpectrum\I18N\Dictionary\WritableDictionaryInterface;
use CyberSpectrum\I18N\Exception\NotSupportedException;
use CyberSpectrum\I18N\TranslationValue\WritableTranslationValueInterface;
use PHPUnit\Framework\TestCase;

/**
 * This tests the configuration resolver.
 *
 * @covers \CyberSpectrum\I18N\Compound\WritableCompoundDictionary
 */
class WritableCompoundDictionaryTest extends TestCase
{
    /**
     * Test.
     *
     * @return void
     */
    public function testLanguages(): void
    {
        $compound = new WritableCompoundDictionary('en', 'de');

        $this->assertSame('en', $compound->getSourceLanguage());
        $this->assertSame('de', $compound->getTargetLanguage());
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testAddChecksSourceLanguage(): void
    {
        $compound = new WritableCompoundDictionary('en', 'de');

        $child = $this->getMockForAbstractClass(WritableDictionaryInterface::class);
        $child->expects($this->once())->method('getSourceLanguage')->with()->willReturn('de');

        $this->expectException(NotSupportedException::class);
        $this->expectExceptionMessage('Languages can not be mixed in compounds, expected source "en" but got "de"');

        $compound->addDictionary('child', $child);
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testAddChecksTargetLanguage(): void
    {
        $compound = new WritableCompoundDictionary('en', 'fr');

        $child = $this->getMockForAbstractClass(WritableDictionaryInterface::class);
        $child->expects($this->once())->method('getSourceLanguage')->with()->willReturn('en');
        $child->expects($this->once())->method('getTargetLanguage')->with()->willReturn('de');

        $this->expectException(NotSupportedException::class);
        $this->expectExceptionMessage('Languages can not be mixed in compounds, expected target "fr" but got "de"');

        $compound->addDictionary('child', $child);
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testCanNotAddTwice(): void
    {
        $compound = new WritableCompoundDictionary('en', 'de');

        $child = $this->getMockForAbstractClass(WritableDictionaryInterface::class);
        $child->expects($this->once())->method('getSourceLanguage')->with()->willReturn('en');
        $child->expects($this->once())->method('getTargetLanguage')->with()->willReturn('de');

        $compound->addDictionary('child', $child);

        $child2 = $this->getMockForAbstractClass(WritableDictionaryInterface::class);
        $child2->expects($this->never())->method('getSourceLanguage');
        $child2->expects($this->never())->method('getTargetLanguage');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('A dictionary with prefix "child" has already been added.');

        $compound->addDictionary('child', $child2);
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testAdd(): void
    {
        $value = $this->getMockForAbstractClass(WritableTranslationValueInterface::class);
        $value->expects($this->once())->method('getKey')->with()->willReturn('key');

        $child = $this->getMockForAbstractClass(WritableDictionaryInterface::class);
        $child->expects($this->once())->method('getSourceLanguage')->with()->willReturn('en');
        $child->expects($this->once())->method('getTargetLanguage')->with()->willReturn('de');
        $child->expects($this->once())->method('add')->with('key')->willReturn($value);

        $compound = new WritableCompoundDictionary('en', 'de');
        $compound->addDictionary('child', $child);

        $this->assertInstanceOf(WritableTranslationValue::class, $cValue = $compound->add('child.key'));
        $this->assertSame('child.key', $cValue->getKey());
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testRemove(): void
    {
        $child = $this->getMockForAbstractClass(WritableDictionaryInterface::class);
        $child->expects($this->once())->method('getSourceLanguage')->with()->willReturn('en');
        $child->expects($this->once())->method('getTargetLanguage')->with()->willReturn('de');
        $child->expects($this->once())->method('remove')->with('key');

        $compound = new WritableCompoundDictionary('en', 'de');
        $compound->addDictionary('child', $child);

        $compound->remove('child.key');
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testGetWritable(): void
    {
        $value = $this->getMockForAbstractClass(WritableTranslationValueInterface::class);
        $value->expects($this->once())->method('getKey')->with()->willReturn('key');

        $child = $this->getMockForAbstractClass(WritableDictionaryInterface::class);
        $child->expects($this->once())->method('getSourceLanguage')->with()->willReturn('en');
        $child->expects($this->once())->method('getTargetLanguage')->with()->willReturn('de');
        $child->expects($this->once())->method('getWritable')->with('key')->willReturn($value);

        $compound = new WritableCompoundDictionary('en', 'de');
        $compound->addDictionary('child', $child);

        $this->assertInstanceOf(WritableTranslationValue::class, $cValue = $compound->getWritable('child.key'));
        $this->assertSame('child.key', $cValue->getKey());
    }
}
