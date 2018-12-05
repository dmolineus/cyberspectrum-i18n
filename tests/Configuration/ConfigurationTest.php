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
use PHPUnit\Framework\TestCase;

/**
 * This tests the definition class.
 *
 * @covers \CyberSpectrum\I18N\Configuration\Configuration
 */
class ConfigurationTest extends TestCase
{
    /**
     * Test.
     *
     * @return void
     */
    public function testEmpty(): void
    {
        $configuration = new Configuration();

        $this->assertFalse($configuration->hasJob('test'));
        $this->assertFalse($configuration->hasDictionary('test'));
        $this->assertSame([], $configuration->getJobNames());
        $this->assertSame([], $configuration->getDictionaryNames());
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testGetDictionaryThrowsForUnknown(): void
    {
        $configuration = new Configuration();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Dictionary not found: test');

        $configuration->getDictionary('test');
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testGetJobThrowsForUnknown(): void
    {
        $configuration = new Configuration();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Job not found: test');

        $configuration->getJob('test');
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testGetJob(): void
    {
        $configuration = new Configuration();

        $configuration->setJob($definition = new Definition('test'));

        $this->assertTrue($configuration->hasJob('test'));
        $this->assertSame($definition, $configuration->getJob('test'));
        $this->assertSame(['test'], $configuration->getJobNames());
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testGetDictionary(): void
    {
        $configuration = new Configuration();
        $configuration->setDictionary($definition = new Definition('test'));

        $this->assertTrue($configuration->hasDictionary('test'));
        $this->assertSame($definition, $configuration->getDictionary('test'));
        $this->assertSame(['test'], $configuration->getDictionaryNames());
    }
}
