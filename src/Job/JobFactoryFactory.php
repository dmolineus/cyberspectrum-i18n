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

namespace CyberSpectrum\I18N\Job;

use CyberSpectrum\I18N\Configuration\Configuration;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * This builds translation jobs.
 */
class JobFactoryFactory
{
    /**
     * The dictionary providers.
     *
     * @var ServiceLocator
     */
    private $dictionaryBuilders;

    /**
     * The job type factories.
     *
     * @var ServiceLocator
     */
    private $jobBuilders;

    /**
     * The logger.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Create a new instance.
     *
     * @param ServiceLocator  $dictionaryBuilders The dictionary builders.
     * @param ServiceLocator  $jobBuilders        The job type factories.
     * @param LoggerInterface $logger             The logger.
     */
    public function __construct(
        ServiceLocator $dictionaryBuilders,
        ServiceLocator $jobBuilders,
        LoggerInterface $logger
    ) {
        $this->dictionaryBuilders = $dictionaryBuilders;
        $this->jobBuilders        = $jobBuilders;
        $this->logger             = $logger;
    }

    /**
     * Process a configuration.
     *
     * @param Configuration $configuration The configuration to process.
     *
     * @return JobFactory
     *
     * @throws \InvalidArgumentException When the type is unknown.
     */
    public function create(Configuration $configuration): JobFactory
    {
        return new JobFactory($this->dictionaryBuilders, $this->jobBuilders, $configuration, $this->logger);
    }
}
