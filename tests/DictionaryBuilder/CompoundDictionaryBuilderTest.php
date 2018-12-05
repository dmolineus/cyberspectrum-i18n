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

namespace CyberSpectrum\I18N\Test\DictionaryBuilder;

use CyberSpectrum\I18N\Compound\CompoundDictionary;
use CyberSpectrum\I18N\Configuration\Definition\DecoratedDictionaryDefinition;
use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition;
use CyberSpectrum\I18N\Dictionary\WritableDictionaryInterface;
use CyberSpectrum\I18N\DictionaryBuilder\CompoundDictionaryBuilder;
use CyberSpectrum\I18N\Job\JobFactory;
use PHPUnit\Framework\TestCase;

/**
 * This tests the default dictionary builder.
 *
 * @covers \CyberSpectrum\I18N\DictionaryBuilder\CompoundDictionaryBuilder
 */
class CompoundDictionaryBuilderTest extends TestCase
{
    /**
     * Test.
     *
     * @return void
     */
    public function testGetDictionary(): void
    {
        $factory         = $this->mockJobFactory();
        $this->getMockForAbstractClass(DictionaryInterface::class);
        $childDefinition = new DictionaryDefinition(
            'child',
            [
                'prefix' => 'something',
            ]
        );
        $definition      = new DictionaryDefinition(
            'test',
            [
                'type'            => 'test',
                'additional'      => 'data',
                'source_language' => 'en',
                'target_language' => 'de',
                'dictionaries'    => [$childDefinition]
            ]
        );

        $childDictionary = $this->getMockForAbstractClass(DictionaryInterface::class);
        $childDictionary->expects($this->once())->method('getSourceLanguage')->willReturn('en');
        $childDictionary->expects($this->once())->method('getTargetLanguage')->willReturn('de');
        $childDictionary->expects($this->once())->method('keys')->willReturn($this->arrayAsGenerator(['key']));

        $factory
            ->expects($this->once())
            ->method('createDictionary')
            ->willReturnCallback(function ($dictionary) use ($childDictionary) {
                $this->assertInstanceOf(DecoratedDictionaryDefinition::class, $dictionary);
                /** @var DecoratedDictionaryDefinition $dictionary */
                $this->assertSame('en', $dictionary->getSourceLanguage());
                $this->assertSame('de', $dictionary->getTargetLanguage());

                return $childDictionary;
            });

        $instance = new CompoundDictionaryBuilder();

        $this->assertInstanceOf(CompoundDictionary::class, $dictionary = $instance->build($factory, $definition));
        $this->assertSame(['something.key'], \iterator_to_array($dictionary->keys()));
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testGetDictionaryForWrite(): void
    {
        $factory         = $this->mockJobFactory();
        $childDefinition = new DictionaryDefinition(
            'child',
            [
                'prefix' => 'something',
                'source_language' => 'en',
                'target_language' => 'de',
            ]
        );
        $definition      = new DictionaryDefinition(
            'test',
            [
                'type'            => 'test',
                'additional'      => 'data',
                'source_language' => 'en',
                'target_language' => 'de',
                'dictionaries'    => [$childDefinition]
            ]
        );

        $childDictionary = $this->getMockForAbstractClass(WritableDictionaryInterface::class);
        $childDictionary->expects($this->once())->method('getSourceLanguage')->willReturn('en');
        $childDictionary->expects($this->once())->method('getTargetLanguage')->willReturn('de');

        $factory
            ->expects($this->once())
            ->method('createWritableDictionary')
            ->willReturnCallback(function ($dictionary) use ($childDictionary) {
                $this->assertInstanceOf(DecoratedDictionaryDefinition::class, $dictionary);
                /** @var DecoratedDictionaryDefinition $dictionary */
                $this->assertSame('en', $dictionary->getSourceLanguage());
                $this->assertSame('de', $dictionary->getTargetLanguage());

                return $childDictionary;
            });

        $instance = new CompoundDictionaryBuilder();

        $this->assertInstanceOf(CompoundDictionary::class, $dictionary = $instance->buildWritable($factory, $definition));
    }

    /**
     * Mock a job factory.
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|JobFactory
     */
    private function mockJobFactory(): \PHPUnit\Framework\MockObject\MockObject
    {
        $jobBuilder = $this->getMockBuilder(JobFactory::class)->disableOriginalConstructor()->getMock();

        return $jobBuilder;
    }

    /*
     * @param array @array
     *
     * @return \Generator
     */
    protected function arrayAsGenerator(array $array): \Generator
    {
        yield from $array;
    }
}
