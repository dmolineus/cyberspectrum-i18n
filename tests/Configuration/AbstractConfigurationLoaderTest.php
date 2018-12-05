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

use CyberSpectrum\I18N\Configuration\AbstractConfigurationLoader;
use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\LoaderInterface;
use PHPUnit\Framework\TestCase;

/**
 * This tests the configuration factory.
 *
 * @covers \CyberSpectrum\I18N\Configuration\AbstractConfigurationLoader
 */
class AbstractConfigurationLoaderTest extends TestCase
{
    /**
     * Test loading.
     *
     * @return void
     */
    public function testLoad(): void
    {
        $factory = $this->getMockForAbstractClass(AbstractConfigurationLoader::class);
        $configuration = new Configuration();

        $loader = $this->getMockForAbstractClass(LoaderInterface::class);
        $loader->expects($this->once())->method('supports')->with('source')->willReturn(true);
        $loader->expects($this->once())->method('load')->with('source');

        $factory
            ->expects($this->once())
            ->method('getLoader')
            ->with('source', $configuration)
            ->willReturn($loader);

        $this->assertSame($configuration, $factory->load('source', $configuration));
    }

    /**
     * Test loading.
     *
     * @return void
     */
    public function testThrowsForUnsupported(): void
    {
        $factory = $this->getMockForAbstractClass(AbstractConfigurationLoader::class);
        $configuration = new Configuration();

        $loader = $this->getMockForAbstractClass(LoaderInterface::class);
        $loader->expects($this->once())->method('supports')->with('source')->willReturn(false);
        $loader->expects($this->never())->method('load');

        $factory
            ->expects($this->once())
            ->method('getLoader')
            ->with('source', $configuration)
            ->willReturn($loader);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported configuration.');

        $this->assertSame($configuration, $factory->load('source', $configuration));
    }

    /**
     * Test loading.
     *
     * @return void
     */
    public function testCreatesConfigIfNotGiven(): void
    {
        $factory = $this->getMockForAbstractClass(AbstractConfigurationLoader::class);

        $loader = $this->getMockForAbstractClass(LoaderInterface::class);
        $loader->expects($this->once())->method('supports')->with('source')->willReturn(false);
        $loader->expects($this->never())->method('load');

        $factory
            ->expects($this->once())
            ->method('getLoader')
            ->with('source')
            ->willReturn($loader);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported configuration.');

        $this->assertInstanceOf(Configuration::class, $factory->load('source'));
    }
}
