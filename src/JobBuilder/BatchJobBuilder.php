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

namespace CyberSpectrum\I18N\JobBuilder;

use CyberSpectrum\I18N\Configuration\Definition\ReferencedJobDefinition;
use CyberSpectrum\I18N\Job\BatchJob;
use CyberSpectrum\I18N\Job\TranslationJobInterface;
use CyberSpectrum\I18N\Configuration\Definition\Definition;
use CyberSpectrum\I18N\Configuration\Definition\BatchJobDefinition;
use CyberSpectrum\I18N\Job\JobFactory;

/**
 * This creates a batch job from a job definition.
 */
class BatchJobBuilder implements JobBuilderInterface
{
    /**
     * Build a copy job from the passed definition.
     *
     * @param JobFactory $factory    The job builder for recursive calls.
     * @param Definition $definition The definition.
     *
     * @return BatchJob
     *
     * @throws \InvalidArgumentException When the passed definition is not a BatchJobDefinition.
     */
    public function build(JobFactory $factory, Definition $definition): TranslationJobInterface
    {
        if ($definition instanceof ReferencedJobDefinition) {
            $definition = $definition->getDelegated();
        }

        if (!$definition instanceof BatchJobDefinition) {
            throw new \InvalidArgumentException('Invalid definition passed.');
        }

        $jobs = [];
        foreach ($definition->getJobs() as $job) {
            $jobs[$job->getName()] = $factory->createJob($job);
        }

        return new BatchJob($jobs);
    }
}
