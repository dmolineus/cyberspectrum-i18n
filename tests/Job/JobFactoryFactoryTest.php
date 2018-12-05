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

namespace CyberSpectrum\I18N\Test\Job;

use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\Definition;
use CyberSpectrum\I18N\Job\JobFactoryFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * This tests the job builder factory.
 *
 * @covers \CyberSpectrum\I18N\Job\JobFactoryFactory
 */
class JobFactoryFactoryTest extends TestCase
{
    /**
     * Test the factory.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $providers     = $this->getMockBuilder(ServiceLocator::class)->disableOriginalConstructor()->getMock();
        $jobBuilders   = $this->getMockBuilder(ServiceLocator::class)->disableOriginalConstructor()->getMock();
        $logger        = $this->getMockForAbstractClass(LoggerInterface::class);
        $configuration = new Configuration();
        $factory       = new JobFactoryFactory($providers, $jobBuilders, $logger);

        $configuration->setJob(new Definition('job'));

        $jobBuilder = $factory->create($configuration);

        $this->assertSame(['job'], $jobBuilder->getJobNames());
    }
}
