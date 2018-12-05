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

use CyberSpectrum\I18N\Compound\CompoundDictionary;
use CyberSpectrum\I18N\Compound\TranslationValue;
use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use CyberSpectrum\I18N\Exception\NotSupportedException;
use CyberSpectrum\I18N\Exception\TranslationNotFoundException;
use CyberSpectrum\I18N\Memory\MemoryDictionary;
use CyberSpectrum\I18N\TranslationValue\TranslationValueInterface;
use PHPUnit\Framework\TestCase;

/**
 * This tests the configuration resolver.
 *
 * @covers \CyberSpectrum\I18N\Compound\CompoundDictionary
 */
class CompoundDictionaryTest extends TestCase
{
    /**
     * Test.
     *
     * @return void
     */
    public function testLanguages(): void
    {
        $compound = new CompoundDictionary('en', 'de');

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
        $compound = new CompoundDictionary('en', 'de');

        $child = $this->getMockForAbstractClass(DictionaryInterface::class);
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
        $compound = new CompoundDictionary('en', 'fr');

        $child = $this->getMockForAbstractClass(DictionaryInterface::class);
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
        $compound = new CompoundDictionary('en', 'de');

        $child = $this->getMockForAbstractClass(DictionaryInterface::class);
        $child->expects($this->once())->method('getSourceLanguage')->with()->willReturn('en');
        $child->expects($this->once())->method('getTargetLanguage')->with()->willReturn('de');

        $compound->addDictionary('child', $child);

        $child2 = $this->getMockForAbstractClass(DictionaryInterface::class);
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
    public function testKeysReturnsAll(): void
    {
        $child1 = new MemoryDictionary('en', 'de', [
            'test-key1' => [
                'source' => 'Source 1',
                'target' => 'Target 1',
            ],
            'test-key2' => [
                'source' => 'Source 2',
                'target' => 'Target 2',
            ],
        ]);
        $child2 = new MemoryDictionary('en', 'de', [
            'test-key1' => [
                'source' => 'Source 1',
                'target' => 'Target 1',
            ],
            'test-key2' => [
                'source' => 'Source 2',
                'target' => 'Target 2',
            ],
        ]);

        $compound = new CompoundDictionary('en', 'de');
        $compound->addDictionary('child1', $child1);
        $compound->addDictionary('child2', $child2);

        $this->assertSame([
            'child1.test-key1',
            'child1.test-key2',
            'child2.test-key1',
            'child2.test-key2'
        ], \iterator_to_array($compound->keys()));
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testHas(): void
    {
        $child = $this->getMockForAbstractClass(DictionaryInterface::class);
        $child->expects($this->once())->method('has')->with('key')->willReturn(true);
        $child->expects($this->once())->method('getSourceLanguage')->with()->willReturn('en');
        $child->expects($this->once())->method('getTargetLanguage')->with()->willReturn('de');

        $compound = new CompoundDictionary('en', 'de');
        $compound->addDictionary('child', $child);

        $this->assertTrue($compound->has('child.key'));
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testGet(): void
    {
        $value = $this->getMockForAbstractClass(TranslationValueInterface::class);
        $value->expects($this->once())->method('getKey')->with()->willReturn('key');
        $value->expects($this->once())->method('getSource')->with()->willReturn('source');
        $value->expects($this->once())->method('getTarget')->with()->willReturn('target');
        $value->expects($this->once())->method('isSourceEmpty')->with()->willReturn(false);
        $value->expects($this->once())->method('isTargetEmpty')->with()->willReturn(false);

        $child = $this->getMockForAbstractClass(DictionaryInterface::class);
        $child->expects($this->once())->method('get')->with('key')->willReturn($value);
        $child->expects($this->once())->method('getSourceLanguage')->with()->willReturn('en');
        $child->expects($this->once())->method('getTargetLanguage')->with()->willReturn('de');

        $compound = new CompoundDictionary('en', 'de');
        $compound->addDictionary('child', $child);

        $childValue = $compound->get('child.key');
        $this->assertInstanceOf(TranslationValue::class, $childValue);
        $this->assertSame('child.key', $childValue->getKey());
        $this->assertSame('source', $childValue->getSource());
        $this->assertSame('target', $childValue->getTarget());
        $this->assertFalse($childValue->isSourceEmpty());
        $this->assertFalse($childValue->isTargetEmpty());
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testThrowsForInvalidKey(): void
    {
        $child = $this->getMockForAbstractClass(DictionaryInterface::class);
        $child->expects($this->never())->method('has');
        $child->expects($this->once())->method('getSourceLanguage')->with()->willReturn('en');
        $child->expects($this->once())->method('getTargetLanguage')->with()->willReturn('de');

        $compound = new CompoundDictionary('en', 'de');
        $compound->addDictionary('child', $child);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Key "key" has invalid format.');

        $compound->has('key');
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testThrowsForUnknownPrefix(): void
    {
        $child = $this->getMockForAbstractClass(DictionaryInterface::class);
        $child->expects($this->never())->method('has');
        $child->expects($this->once())->method('getSourceLanguage')->with()->willReturn('en');
        $child->expects($this->once())->method('getTargetLanguage')->with()->willReturn('de');

        $compound = new CompoundDictionary('en', 'de');
        $compound->addDictionary('child', $child);

        $this->expectException(TranslationNotFoundException::class);
        $this->expectExceptionMessage('Key "unknown.key" not found');

        $compound->has('unknown.key');
    }
}
