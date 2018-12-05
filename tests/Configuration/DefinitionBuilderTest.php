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

namespace CyberSpectrum\I18N\Test\Configuration;

use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\Definition;
use CyberSpectrum\I18N\Configuration\DefinitionBuilder;
use CyberSpectrum\I18N\Configuration\DefinitionBuilder\DefinitionBuilderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * This tests the definition builder.
 *
 * @covers \CyberSpectrum\I18N\Configuration\DefinitionBuilder
 */
class DefinitionBuilderTest extends TestCase
{
    /**
     * Test.
     *
     * @return void
     */
    public function testBuildDictionary(): void
    {
        $configuration      = new Configuration();
        $data               = ['type' => 'typeName'];
        $dictionary         = new Definition('dummy');
        $dictionaryBuilder  = $this->getMockForAbstractClass(DefinitionBuilderInterface::class);
        $dictionaryBuilders = new ServiceLocator(['typeName' => function () use ($dictionaryBuilder) {
            return $dictionaryBuilder;
        }]);
        $jobBuilders        = new ServiceLocator([]);

        $dictionaryBuilder
            ->expects($this->once())
            ->method('build')
            ->with($configuration, $data)
            ->willReturn($dictionary);

        $builder = new DefinitionBuilder($dictionaryBuilders, $jobBuilders);

        $this->assertSame($dictionary, $builder->buildDictionary($configuration, $data));
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testBuildDictionaryThrowsForUnknownType(): void
    {
        $builder = new DefinitionBuilder(new ServiceLocator([]), new ServiceLocator([]));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unknown dictionary type "typeName"');

        $builder->buildDictionary(new Configuration(), ['type' => 'typeName']);
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testBuildJob(): void
    {
        $configuration      = new Configuration();
        $data               = ['type' => 'typeName'];
        $dictionary         = new Definition('dummy');
        $jobBuilder         = $this->getMockForAbstractClass(DefinitionBuilderInterface::class);
        $dictionaryBuilders = new ServiceLocator([]);
        $jobBuilders        = new ServiceLocator(['typeName' => function () use ($jobBuilder) {
            return $jobBuilder;
        }]);

        $jobBuilder
            ->expects($this->once())
            ->method('build')
            ->with($configuration, $data)
            ->willReturn($dictionary);

        $builder = new DefinitionBuilder($dictionaryBuilders, $jobBuilders);

        $this->assertSame($dictionary, $builder->buildJob($configuration, $data));
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testBuildJobThrowsForUnknownType(): void
    {
        $builder = new DefinitionBuilder(new ServiceLocator([]), new ServiceLocator([]));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unknown job type "typeName"');

        $builder->buildJob(new Configuration(), ['type' => 'typeName']);
    }
}
