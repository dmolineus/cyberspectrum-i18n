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

namespace CyberSpectrum\I18N\Test\Configuration\DefinitionBuilder;

use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\Definition;
use CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition;
use CyberSpectrum\I18N\Configuration\Definition\BatchJobDefinition;
use CyberSpectrum\I18N\Configuration\Definition\ReferencedJobDefinition;
use CyberSpectrum\I18N\Configuration\DefinitionBuilder;
use CyberSpectrum\I18N\Configuration\DefinitionBuilder\BatchJobDefinitionBuilder;
use PHPUnit\Framework\TestCase;

/**
 * This tests the copy job builder.
 *
 * @covers \CyberSpectrum\I18N\Configuration\DefinitionBuilder\BatchJobDefinitionBuilder
 */
class BatchJobDefinitionBuilderTest extends TestCase
{
    /**
     * Data provider
     *
     * @return array
     */
    public function throwsForMissingKeyProvider(): array
    {
        return [
            'name'   => ['name', []],
            'jobs' => ['jobs', ['name' => 'foo']],
        ];
    }

    /**
     * Test that building throws when key is missing.
     *
     * @param string $key The key to expect.
     *
     * @param array  $data
     *
     * @return void
     *
     * @dataProvider throwsForMissingKeyProvider
     */
    public function testThrowsForMissingKey(string $key, array $data): void
    {
        $builder = new BatchJobDefinitionBuilder(
            $this->getMockBuilder(DefinitionBuilder::class)->disableOriginalConstructor()->getMock()
        );

        $configuration = new Configuration();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing key "' . $key . '"');

        $builder->build($configuration, $data);
    }

    /**
     * Test building.
     *
     * @return void
     */
    public function testBuildForDelegated(): void
    {
        $definitionBuilder = $this
            ->getMockBuilder(DefinitionBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['buildJob'])
            ->getMock();
        $definitionBuilder->expects($this->never())->method('buildJob');

        $builder = new BatchJobDefinitionBuilder($definitionBuilder);

        $configuration = new Configuration();
        $configuration->setJob($job1 = new Definition('base-job1'));
        $configuration->setJob($job2 = new Definition('base-job2'));

        $configuration->setDictionary(new DictionaryDefinition('source'));
        $configuration->setDictionary(new DictionaryDefinition('target'));

        $job = $builder->build($configuration, [
            'type'   => 'batch',
            'name'   => 'test',
            'jobs' => ['base-job1', 'base-job2']
        ]);

        $this->assertInstanceOf(BatchJobDefinition::class, $job);
        /** @var BatchJobDefinition $job */
        $this->assertCount(2, $jobs = $job->getJobs());
        $this->assertInstanceOf(ReferencedJobDefinition::class, $jobs[0]);
        $this->assertSame($job1, $jobs[0]->getDelegated());
        $this->assertInstanceOf(ReferencedJobDefinition::class, $jobs[1]);
        $this->assertSame($job2, $jobs[1]->getDelegated());
    }

    /**
     * Test building.
     *
     * @return void
     */
    public function testBuildForInlineJob(): void
    {
        $inline            = new Definition('inlined');
        $configuration     = new Configuration();
        $definitionBuilder = $this
            ->getMockBuilder(DefinitionBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['buildJob'])
            ->getMock();
        $definitionBuilder
            ->expects($this->once())
            ->method('buildJob')
            ->with($configuration, ['type' => 'inline', 'name' => 'test.0'])
            ->willReturn($inline);

        $builder = new BatchJobDefinitionBuilder($definitionBuilder);


        $job = $builder->build($configuration, [
            'type'   => 'batch',
            'name'   => 'test',
            'jobs' => [['type' => 'inline']]
        ]);

        $this->assertInstanceOf(BatchJobDefinition::class, $job);
        /** @var BatchJobDefinition $job */
        $this->assertCount(1, $jobs = $job->getJobs());
        $this->assertSame($inline, $jobs[0]);
    }
}
