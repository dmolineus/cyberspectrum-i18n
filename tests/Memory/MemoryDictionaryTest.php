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

namespace CyberSpectrum\I18N\Test\Memory;

use CyberSpectrum\I18N\Exception\TranslationAlreadyContainedException;
use CyberSpectrum\I18N\Exception\TranslationNotFoundException;
use CyberSpectrum\I18N\Memory\MemoryDictionary;
use CyberSpectrum\I18N\Memory\MemoryTranslationValue;
use PHPUnit\Framework\TestCase;

/**
 * This tests the simple translation dictionary.
 *
 * @covers \CyberSpectrum\I18N\Memory\MemoryDictionary
 */
class MemoryDictionaryTest extends TestCase
{
    /**
     * Test.
     *
     * @return void
     */
    public function testInstantiation(): void
    {
        $dictionary = new MemoryDictionary('en', 'de', []);

        $this->assertSame('en', $dictionary->getSourceLanguage());
        $this->assertSame('de', $dictionary->getTargetLanguage());
        $this->assertSame([], \iterator_to_array($dictionary->keys()));
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testInstantiationWithInvalidDictionary(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid translation array: ' . var_export(['dictionary'], true));

        new MemoryDictionary('en', 'de', ['broken' => ['dictionary']]);
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testThrowsForUnknownKey(): void
    {
        $dictionary = new MemoryDictionary('en', 'de', []);

        $this->expectException(TranslationNotFoundException::class);
        $this->expectExceptionMessage('Key "unknown-key" not found');

        $dictionary->get('unknown-key');
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testCreatingWithValuesWorks(): void
    {
        $dictionary = new MemoryDictionary('en', 'de', [
            'test-key' => [
                'source' => 'Source value',
                'target' => 'Target value',
            ],
            'null-key' => [
                'source' => null,
                'target' => null,
            ],
        ]);

        $this->assertSame(['test-key', 'null-key'], \iterator_to_array($dictionary->keys()));

        $this->assertInstanceOf(MemoryTranslationValue::class, $value = $dictionary->get('test-key'));
        $this->assertSame('Source value', $value->getSource());
        $this->assertSame('Target value', $value->getTarget());
        $this->assertFalse($value->isSourceEmpty());
        $this->assertFalse($value->isTargetEmpty());

        $this->assertInstanceOf(MemoryTranslationValue::class, $value = $dictionary->get('null-key'));
        $this->assertSame('null-key', $value->getKey());
        $this->assertNull($value->getSource());
        $this->assertNull($value->getTarget());
        $this->assertTrue($value->isSourceEmpty());
        $this->assertTrue($value->isTargetEmpty());
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testAddingValuesWorks(): void
    {
        $dictionary = new MemoryDictionary('en', 'de', []);

        $this->assertInstanceOf(MemoryTranslationValue::class, $value = $dictionary->add('test-key'));
        $this->assertTrue($dictionary->has('test-key'));
        $this->assertSame(['test-key'], \iterator_to_array($dictionary->keys()));
        $this->assertSame('test-key', $value->getKey());
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testAddingExistingValuesThrows(): void
    {
        $this->expectException(TranslationAlreadyContainedException::class);
        $this->expectExceptionMessage('Key "test-key" already contained');

        $dictionary = new MemoryDictionary('en', 'de', ['test-key' => [
            'source' => 'Source value',
            'target' => 'Target value',
        ]]);

        $dictionary->add('test-key');
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testRemovalOfValuesWorks(): void
    {
        $dictionary = new MemoryDictionary('en', 'de', ['test-key' => [
            'source' => 'Source value',
            'target' => 'Target value',
        ]]);

        $dictionary->remove('test-key');

        $this->assertSame([], \iterator_to_array($dictionary->keys()));
        $this->assertFalse($dictionary->has('test-key'));
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testRemovalOfNonExistentValueThrows(): void
    {
        $dictionary = new MemoryDictionary('en', 'de', []);

        $this->expectException(TranslationNotFoundException::class);
        $this->expectExceptionMessage('Key "unknown-key" not found');

        $dictionary->remove('unknown-key');
    }
}
