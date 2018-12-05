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

namespace CyberSpectrum\I18N\Configuration\DefinitionBuilder;

use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\BatchJobDefinition;
use CyberSpectrum\I18N\Configuration\Definition\Definition;
use CyberSpectrum\I18N\Configuration\Definition\ReferencedJobDefinition;
use CyberSpectrum\I18N\Configuration\DefinitionBuilder;

/**
 * Definition builder for batch jobs.
 */
class BatchJobDefinitionBuilder implements DefinitionBuilderInterface
{
    /**
     * The definition builder to use.
     *
     * @var DefinitionBuilder
     */
    private $definitionBuilder;

    /**
     * Create a new instance.
     *
     * @param DefinitionBuilder $definitionBuilder The definition builder to use.
     */
    public function __construct(DefinitionBuilder $definitionBuilder)
    {
        $this->definitionBuilder = $definitionBuilder;
    }

    /**
     * Build a definition from the passed values.
     *
     * @param Configuration $configuration The configuration.
     * @param array         $data          The configuration values.
     *
     * @return Definition
     *
     * @throws \InvalidArgumentException When a required key is missing.
     */
    public function build(Configuration $configuration, array $data): Definition
    {
        foreach (['name', 'jobs'] as $key) {
            if (!array_key_exists($key, $data)) {
                throw new \InvalidArgumentException('Missing key "' . $key . '"');
            }
        }

        $name = $data['name'];
        $jobs = [];
        foreach ($data['jobs'] as $index => $job) {
            // Global defined job?
            if (is_string($job)) {
                $jobs[] = new ReferencedJobDefinition($job, $configuration);
                continue;
            }

            // Create local job.
            if (!isset($job['name'])) {
                $job['name'] = $name . '.' . $index;
            }
            $jobs[] = $this->definitionBuilder->buildJob($configuration, $job);
        }
        unset($data['name'], $data['jobs']);

        return new BatchJobDefinition($name, $jobs, $data);
    }
}
