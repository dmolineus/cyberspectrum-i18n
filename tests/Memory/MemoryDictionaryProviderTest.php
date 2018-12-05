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

use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use CyberSpectrum\I18N\Exception\DictionaryNotFoundException;
use CyberSpectrum\I18N\Memory\MemoryDictionary;
use CyberSpectrum\I18N\Memory\MemoryDictionaryProvider;
use PHPUnit\Framework\TestCase;

/**
 * This tests the memory dictionary provider.
 *
 * @covers \CyberSpectrum\I18N\Memory\MemoryDictionaryProvider
 */
class MemoryDictionaryProviderTest extends TestCase
{
    /**
     * Test.
     *
     * @return void
     */
    public function testIsInitiallyEmpty(): void
    {
        $provider = new MemoryDictionaryProvider();

        $this->assertSame([], \iterator_to_array($provider->getAvailableDictionaries()));
        $this->assertSame([], \iterator_to_array($provider->getAvailableWritableDictionaries()));
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testCanCreateDictionary(): void
    {
        $provider = new MemoryDictionaryProvider();

        $dictionary   = $provider->createDictionary('foo', 'en', 'de');
        $dictionaries = \iterator_to_array($provider->getAvailableDictionaries());

        $this->assertInstanceOf(DictionaryInterface::class, $dictionary);
        $this->assertInstanceOf(MemoryDictionary::class, $dictionary);
        $this->assertSame('en', $dictionary->getSourceLanguage());
        $this->assertSame('de', $dictionary->getTargetLanguage());

        $this->assertSame('foo', $dictionaries[0]->getName());
        $this->assertSame('en', $dictionaries[0]->getSourceLanguage());
        $this->assertSame('de', $dictionaries[0]->getTargetLanguage());
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testCreateDictionaryThrowsForAlreadyExisting(): void
    {
        $provider = new MemoryDictionaryProvider();
        $provider->createDictionary('foo', 'en', 'de');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Dictionary foo already exists.');

        $provider->createDictionary('foo', 'en', 'de');
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testCanGetDictionary(): void
    {
        $provider = new MemoryDictionaryProvider();
        $dictionary = $provider->createDictionary('foo', 'en', 'de');
        $this->assertSame('en', $dictionary->getSourceLanguage());
        $this->assertSame('de', $dictionary->getTargetLanguage());

        $dict2 = $provider->getDictionary('foo', 'en', 'de');
        $this->assertInstanceOf(MemoryDictionary::class, $dict2);
        $this->assertSame($dictionary, $dict2);
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testGetDictionaryThrowsForNonExisting(): void
    {
        $provider = new MemoryDictionaryProvider();

        $this->expectException(DictionaryNotFoundException::class);
        $this->expectExceptionMessage('Dictionary foo not found (requested source language: "en", requested target language: "de").');

        $provider->getDictionary('foo', 'en', 'de');
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testCanGetWritableDictionary(): void
    {
        $provider    = new MemoryDictionaryProvider();
        $dictionary1 = $provider->createDictionary('foo', 'en', 'de');
        $dictionary2 = $provider->getDictionaryForWrite('foo', 'en', 'de');
        $this->assertSame('en', $dictionary1->getSourceLanguage());
        $this->assertSame('de', $dictionary1->getTargetLanguage());

        $this->assertInstanceOf(MemoryDictionary::class, $dictionary2);
        $this->assertSame($dictionary1, $dictionary2);
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testGetGetWritableDictionaryThrowsForNonExisting(): void
    {
        $provider = new MemoryDictionaryProvider();

        $this->expectException(DictionaryNotFoundException::class);
        $this->expectExceptionMessage('Dictionary foo not found (requested source language: "en", requested target language: "de").');

        $provider->getDictionaryForWrite('foo', 'en', 'de');
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testGetAvailableMethodsReturnSameInstances(): void
    {
        $provider = new MemoryDictionaryProvider();

        $provider->createDictionary('foo1', 'en', 'de');
        $provider->createDictionary('foo2', 'en', 'de');
        $dictionaries1 = array_map('strval', \iterator_to_array($provider->getAvailableDictionaries()));
        $dictionaries2 = array_map('strval', \iterator_to_array($provider->getAvailableWritableDictionaries()));

        $this->assertSame(['foo1 en => de', 'foo2 en => de'], $dictionaries1);
        $this->assertSame(['foo1 en => de', 'foo2 en => de'], $dictionaries2);
    }
}
