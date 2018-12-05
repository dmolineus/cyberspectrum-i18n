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

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

/**
 * This job is a composite of several other jobs.
 */
class BatchJob implements TranslationJobInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * The list of translation jobs.
     *
     * @var TranslationJobInterface[]
     */
    private $jobs;

    /**
     * Create a new instance.
     *
     * @param TranslationJobInterface[] $jobs
     */
    public function __construct(array $jobs)
    {
        $this->jobs = $jobs;
        $this->setLogger(new NullLogger());
    }

    /**
     * {@inheritDoc}
     */
    public function run(bool $dryRun = null): void
    {
        foreach ($this->jobs as $name => $job) {
            $this->logger->notice('Executing job: ' . $name);
            $job->run($dryRun);
        }
    }
}
