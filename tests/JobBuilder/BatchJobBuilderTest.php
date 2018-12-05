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
use CyberSpectrum\I18N\Configuration\Definition\BatchJobDefinition;
use CyberSpectrum\I18N\Configuration\Definition\Definition;
use CyberSpectrum\I18N\Configuration\Definition\ReferencedJobDefinition;
use CyberSpectrum\I18N\Job\BatchJob;
use CyberSpectrum\I18N\Job\JobFactory;
use CyberSpectrum\I18N\Job\TranslationJobInterface;
use CyberSpectrum\I18N\JobBuilder\BatchJobBuilder;
use PHPUnit\Framework\TestCase;

/**
 * This tests the batch job builder.
 *
 * @covers \CyberSpectrum\I18N\JobBuilder\BatchJobBuilder
 */
class BatchJobBuilderTest extends TestCase
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

        $child1 = new Definition('child1');
        $child2 = new Definition('child2');

        $builder
            ->expects($this->exactly(2))
            ->method('createJob')
            ->withConsecutive($child1, $child2)
            ->willReturn($this->getMockForAbstractClass(TranslationJobInterface::class));

        $definition = new BatchJobDefinition('test', [$child1, $child2]);

        $instance = new BatchJobBuilder();

        $this->assertInstanceOf(BatchJob::class, $instance->build($builder, $definition));
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

        $child1 = new Definition('child1');
        $child2 = new Definition('child2');

        $builder
            ->expects($this->exactly(2))
            ->method('createJob')
            ->withConsecutive($child1, $child2)
            ->willReturn($this->getMockForAbstractClass(TranslationJobInterface::class));

        $configuration = new Configuration();
        $configuration->setJob(new BatchJobDefinition('test', [$child1, $child2]));

        $definition = new ReferencedJobDefinition('test', $configuration);
        $instance   = new BatchJobBuilder();

        $this->assertInstanceOf(BatchJob::class, $instance->build($builder, $definition));
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
        $instance   = new BatchJobBuilder();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid definition passed.');

        $instance->build($builder, $definition);
    }
}
