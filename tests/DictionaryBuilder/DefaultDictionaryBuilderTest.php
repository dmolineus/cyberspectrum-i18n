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

use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use CyberSpectrum\I18N\Dictionary\DictionaryProviderInterface;
use CyberSpectrum\I18N\Dictionary\WritableDictionaryInterface;
use CyberSpectrum\I18N\Dictionary\WritableDictionaryProviderInterface;
use CyberSpectrum\I18N\Exception\DictionaryNotFoundException;
use CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition;
use CyberSpectrum\I18N\DictionaryBuilder\DefaultDictionaryBuilder;
use CyberSpectrum\I18N\Job\JobFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * This tests the default dictionary builder.
 *
 * @covers \CyberSpectrum\I18N\DictionaryBuilder\DefaultDictionaryBuilder
 */
class DefaultDictionaryBuilderTest extends TestCase
{
    /**
     * Test.
     *
     * @return void
     */
    public function testGetDictionary(): void
    {
        $provider   = $this->getMockForAbstractClass(DictionaryProviderInterface::class);
        $providers  = new ServiceLocator(['test' => function () use ($provider) {
            return $provider;
        }]);
        $dictionary = $this->getMockForAbstractClass(DictionaryInterface::class);
        $definition = new DictionaryDefinition(
            'test',
            [
                'type'            => 'test',
                'additional'      => 'data',
                'source_language' => 'en',
                'target_language' => 'de',
            ]
        );

        $instance = new DefaultDictionaryBuilder($providers);

        $provider
            ->expects($this->once())
            ->method('getDictionary')
            ->with(
                'test',
                'en',
                'de',
                [
                    'type'            => 'test',
                    'additional'      => 'data',
                    'source_language' => 'en',
                    'target_language' => 'de',
                ]
            )
            ->willReturn($dictionary);

        $this->assertSame($dictionary, $instance->build($this->mockJobFactory(), $definition));
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testGetDictionaryThrowsForWriteOnlyProvider(): void
    {
        $provider   = $this->getMockForAbstractClass(WritableDictionaryInterface::class);
        $providers  = new ServiceLocator(['test' => function () use ($provider) {
            return $provider;
        }]);
        $definition = new DictionaryDefinition(
            'test',
            [
                'type'            => 'test',
                'additional'      => 'data',
                'source_language' => 'en',
                'target_language' => 'de',
            ]
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Dictionary provider "test" can not create readable dictionaries.');

        $instance = new DefaultDictionaryBuilder($providers);
        $instance->build($this->mockJobFactory(), $definition);
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testGetDictionaryThrowsForUnknownProvider(): void
    {
        $providers  = new ServiceLocator([]);
        $definition = new DictionaryDefinition(
            'test',
            [
                'type'            => 'test',
                'additional'      => 'data',
                'source_language' => 'en',
                'target_language' => 'de',
            ]
        );

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('No provider named "test" registered.');

        $instance = new DefaultDictionaryBuilder($providers);
        $instance->build($this->mockJobFactory(), $definition);
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testGetDictionaryForWriteThrowsForReadOnlyProvider(): void
    {
        $provider   = $this->getMockForAbstractClass(DictionaryProviderInterface::class);
        $providers  = new ServiceLocator(['test' => function () use ($provider) {
            return $provider;
        }]);
        $definition = new DictionaryDefinition(
            'test',
            [
                'type'            => 'test',
                'additional'      => 'data',
                'source_language' => 'en',
                'target_language' => 'de',
            ]
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Dictionary provider "test" can not create writable dictionaries.');

        $instance = new DefaultDictionaryBuilder($providers);
        $instance->buildWritable($this->mockJobFactory(), $definition);
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testGetDictionaryForWriteThrowsForUnknownProvider(): void
    {
        $providers  = new ServiceLocator([]);
        $definition = new DictionaryDefinition(
            'test',
            [
                'type'            => 'test',
                'additional'      => 'data',
                'source_language' => 'en',
                'target_language' => 'de',
            ]
        );

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('No provider named "test" registered.');

        $instance = new DefaultDictionaryBuilder($providers);
        $instance->buildWritable($this->mockJobFactory(), $definition);
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testGetDictionaryForWrite(): void
    {
        $provider      = $this->getMockForAbstractClass(WritableDictionaryProviderInterface::class);
        $providers     = new ServiceLocator(['test' => function () use ($provider) {
            return $provider;
        }]);
        $dictionary    = $this->getMockForAbstractClass(WritableDictionaryInterface::class);
        $definition    = new DictionaryDefinition(
            'test',
            [
                'type'            => 'test',
                'additional'      => 'data',
                'source_language' => 'en',
                'target_language' => 'de',
            ]
        );

        $provider
            ->expects($this->once())
            ->method('getDictionaryForWrite')
            ->with(
                'test',
                'en',
                'de',
                [
                    'type'            => 'test',
                    'additional'      => 'data',
                    'source_language' => 'en',
                    'target_language' => 'de',
                ]
            )
            ->willReturn($dictionary);

        $instance = new DefaultDictionaryBuilder($providers);
        $this->assertSame($dictionary, $instance->buildWritable($this->mockJobFactory(), $definition));
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testGetDictionaryForWriteWillCreateNewIfNotFound(): void
    {
        $provider      = $this->getMockForAbstractClass(WritableDictionaryProviderInterface::class);
        $providers     = new ServiceLocator(['test' => function () use ($provider) {
            return $provider;
        }]);
        $dictionary    = $this->getMockForAbstractClass(WritableDictionaryInterface::class);
        $definition    = new DictionaryDefinition(
            'test',
            [
                'type'            => 'test',
                'additional'      => 'data',
                'source_language' => 'en',
                'target_language' => 'de',
            ]
        );
        $provider
            ->expects($this->once())
            ->method('getDictionaryForWrite')
            ->with(
                'test',
                'en',
                'de',
                [
                    'type'            => 'test',
                    'additional'      => 'data',
                    'source_language' => 'en',
                    'target_language' => 'de',
                ]
            )
            ->willThrowException(new DictionaryNotFoundException('test', 'en', 'de'));

        $provider
            ->expects($this->once())
            ->method('createDictionary')
            ->with(
                'test',
                'en',
                'de',
                [
                    'type'            => 'test',
                    'additional'      => 'data',
                    'source_language' => 'en',
                    'target_language' => 'de',
                ]
            )
            ->willReturn($dictionary);

        $instance = new DefaultDictionaryBuilder($providers);
        $this->assertSame($dictionary, $instance->buildWritable($this->mockJobFactory(), $definition));
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
}
