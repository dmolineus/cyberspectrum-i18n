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

namespace CyberSpectrum\I18N\Test\JobBuilder;

use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\CopyJobDefinition;
use CyberSpectrum\I18N\Configuration\Definition\Definition;
use CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition;
use CyberSpectrum\I18N\Configuration\Definition\ReferencedJobDefinition;
use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use CyberSpectrum\I18N\Dictionary\WritableDictionaryInterface;
use CyberSpectrum\I18N\Job\CopyDictionaryJob;
use CyberSpectrum\I18N\Job\JobFactory;
use CyberSpectrum\I18N\JobBuilder\CopyJobBuilder;
use PHPUnit\Framework\TestCase;

/**
 * This tests the copy job builder.
 *
 * @covers \CyberSpectrum\I18N\JobBuilder\CopyJobBuilder
 */
class CopyJobBuilderTest extends TestCase
{
    /**
     * Test.
     *
     * @return void
     */
    public function testBuild(): void
    {
        $builder = $this
            ->getMockBuilder(JobFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $source = new DictionaryDefinition('source');
        $target = new DictionaryDefinition('target');

        $builder
            ->expects($this->once())
            ->method('createDictionary')
            ->with($source)
            ->willReturn($this->getMockForAbstractClass(DictionaryInterface::class));
        $builder
            ->expects($this->once())
            ->method('createWritableDictionary')
            ->with($target)
            ->willReturn($this->getMockForAbstractClass(WritableDictionaryInterface::class));

        $definition = new CopyJobDefinition('test', $source, $target);

        $instance = new CopyJobBuilder();

        $this->assertInstanceOf(CopyDictionaryJob::class, $job = $instance->build($builder, $definition));

        $this->assertSame(CopyDictionaryJob::COPY_IF_EMPTY, $job->getCopySource());
        $this->assertSame(CopyDictionaryJob::COPY_IF_EMPTY, $job->getCopyTarget());
        $this->assertFalse($job->hasRemoveObsolete());
        $this->assertFalse($job->isDryRun());
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testBuildWithOverrides(): void
    {
        $builder = $this
            ->getMockBuilder(JobFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $source = new DictionaryDefinition('source');
        $target = new DictionaryDefinition('target');

        $builder
            ->expects($this->once())
            ->method('createDictionary')
            ->with($source)
            ->willReturn($this->getMockForAbstractClass(DictionaryInterface::class));
        $builder
            ->expects($this->once())
            ->method('createWritableDictionary')
            ->with($target)
            ->willReturn($this->getMockForAbstractClass(WritableDictionaryInterface::class));

        $definition = new CopyJobDefinition( 'test', $source, $target, [
            'copy-source'    => true,
            'copy-target'    => true,
            'remove-obsolete' => true,
        ]);

        $instance = new CopyJobBuilder();

        $this->assertInstanceOf(CopyDictionaryJob::class, $job = $instance->build($builder, $definition));

        $this->assertSame(CopyDictionaryJob::COPY, $job->getCopySource());
        $this->assertSame(CopyDictionaryJob::COPY, $job->getCopyTarget());
        $this->assertTrue($job->hasRemoveObsolete());
        $this->assertFalse($job->isDryRun());
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testBuildUnwrapsReferencedJobs(): void
    {
        $builder = $this
            ->getMockBuilder(JobFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $source = new DictionaryDefinition('source');
        $target = new DictionaryDefinition('target');

        $builder
            ->expects($this->once())
            ->method('createDictionary')
            ->with($source)
            ->willReturn($this->getMockForAbstractClass(DictionaryInterface::class));
        $builder
            ->expects($this->once())
            ->method('createWritableDictionary')
            ->with($target)
            ->willReturn($this->getMockForAbstractClass(WritableDictionaryInterface::class));

        $configuration = new Configuration();
        $configuration->setJob(new CopyJobDefinition( 'test', $source, $target, [
            'copy-source'    => true,
            'copy-target'    => true,
            'remove-obsolete' => true,
        ]));

        $definition = new ReferencedJobDefinition('test', $configuration);

        $instance = new CopyJobBuilder();
        $this->assertInstanceOf(CopyDictionaryJob::class, $job = $instance->build($builder, $definition));

        $this->assertSame(CopyDictionaryJob::COPY, $job->getCopySource());
        $this->assertSame(CopyDictionaryJob::COPY, $job->getCopyTarget());
        $this->assertTrue($job->hasRemoveObsolete());
        $this->assertFalse($job->isDryRun());
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testBuildThrowsForInvalid(): void
    {
        $builder = $this
            ->getMockBuilder(JobFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder->expects($this->never())->method('createJob');

        $configuration = new Configuration();
        $configuration->setJob(new Definition('test'));

        $definition = new ReferencedJobDefinition('test', $configuration);
        $instance   = new CopyJobBuilder();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid definition passed.');

        $instance->build($builder, $definition);
    }

    /**
     * Data provider for the flag conversion test.
     *
     * @return array
     */
    public function stringToFlagProvider(): array
    {
        return [
            [CopyDictionaryJob::COPY, 'true'],
            [CopyDictionaryJob::COPY, true],
            [CopyDictionaryJob::COPY, 'yes'],
            [CopyDictionaryJob::DO_NOT_COPY, 'no'],
            [CopyDictionaryJob::DO_NOT_COPY, 'false'],
            [CopyDictionaryJob::DO_NOT_COPY, false],
            [CopyDictionaryJob::COPY_IF_EMPTY, 'if-empty'],
        ];
    }

    /**
     * Test.
     *
     * @param int   $expected The expected result.
     * @param mixed $input    The input value.
     *
     * @return void
     *
     * @dataProvider stringToFlagProvider
     */
    public function testStringToFlag(int $expected, $input): void
    {
        $builder = $this
            ->getMockBuilder(JobFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $source = new DictionaryDefinition('source');
        $target = new DictionaryDefinition('target');

        $builder
            ->expects($this->once())
            ->method('createDictionary')
            ->with($source)
            ->willReturn($this->getMockForAbstractClass(DictionaryInterface::class));
        $builder
            ->expects($this->once())
            ->method('createWritableDictionary')
            ->with($target)
            ->willReturn($this->getMockForAbstractClass(WritableDictionaryInterface::class));

        $definition = new CopyJobDefinition( 'test', $source, $target, ['copy-source' => $input]);

        $instance = new CopyJobBuilder();

        $this->assertInstanceOf(CopyDictionaryJob::class, $job = $instance->build($builder, $definition));

        $this->assertSame($expected, $job->getCopySource());
    }

    /**
     * Test.
     *
     * @return void
     *
     * @dataProvider stringToFlagProvider
     */
    public function testInvalidStringToFlagThrows(): void
    {
        $builder = $this
            ->getMockBuilder(JobFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $source = new DictionaryDefinition('source');
        $target = new DictionaryDefinition('target');

        $builder
            ->expects($this->once())
            ->method('createDictionary')
            ->with($source)
            ->willReturn($this->getMockForAbstractClass(DictionaryInterface::class));
        $builder
            ->expects($this->once())
            ->method('createWritableDictionary')
            ->with($target)
            ->willReturn($this->getMockForAbstractClass(WritableDictionaryInterface::class));

        $definition = new CopyJobDefinition( 'test', $source, $target, ['copy-source' => 'invalid']);

        $instance = new CopyJobBuilder();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid value for copy flag.');

        $instance->build($builder, $definition);
    }

    /**
     * Data provider for the flag conversion test.
     *
     * @return array
     */
    public function boolishToFlagProvider(): array
    {
        return [
            [true, 'true'],
            [true, true],
            [true, 'yes'],
            [false, 'no'],
            [false, 'false'],
            [false, false],
        ];
    }

    /**
     * Test.
     *
     * @param bool  $expected The expected result.
     * @param mixed $input    The input value.
     *
     * @return void
     *
     * @dataProvider boolishToFlagProvider
     */
    public function testBoolishToFlag(bool $expected, $input): void
    {
        $builder = $this
            ->getMockBuilder(JobFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $source = new DictionaryDefinition('source');
        $target = new DictionaryDefinition('target');

        $builder
            ->expects($this->once())
            ->method('createDictionary')
            ->with($source)
            ->willReturn($this->getMockForAbstractClass(DictionaryInterface::class));
        $builder
            ->expects($this->once())
            ->method('createWritableDictionary')
            ->with($target)
            ->willReturn($this->getMockForAbstractClass(WritableDictionaryInterface::class));

        $definition = new CopyJobDefinition( 'test', $source, $target, ['remove-obsolete' => $input]);

        $instance = new CopyJobBuilder();

        $this->assertInstanceOf(CopyDictionaryJob::class, $job = $instance->build($builder, $definition));

        $this->assertSame($expected, $job->hasRemoveObsolete());
    }

    /**
     * Test.
     *
     * @return void
     *
     * @dataProvider stringToFlagProvider
     */
    public function testInvalidSBoolishToFlagThrows(): void
    {
        $builder = $this
            ->getMockBuilder(JobFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $source = new DictionaryDefinition('source');
        $target = new DictionaryDefinition('target');

        $builder
            ->expects($this->once())
            ->method('createDictionary')
            ->with($source)
            ->willReturn($this->getMockForAbstractClass(DictionaryInterface::class));
        $builder
            ->expects($this->once())
            ->method('createWritableDictionary')
            ->with($target)
            ->willReturn($this->getMockForAbstractClass(WritableDictionaryInterface::class));

        $definition = new CopyJobDefinition( 'test', $source, $target, ['remove-obsolete' => 'invalid']);

        $instance = new CopyJobBuilder();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid value for remove-obsolete flag.');

        $instance->build($builder, $definition);
    }
}
