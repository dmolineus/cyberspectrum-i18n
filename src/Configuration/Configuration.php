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

declare(strict_types=1);

namespace CyberSpectrum\I18N\Configuration;

use CyberSpectrum\I18N\Configuration\Definition\Definition;

/**
 * This stores a config.
 */
class Configuration
{
    /**
     * The list of configured jobs.
     *
     * @var Definition[]
     */
    private $jobs = [];

    /**
     * The list of dictionary configurations.
     *
     * @var Definition[]
     */
    private $dictionaries = [];

    /**
     * Check if the job exists.
     *
     * @param string $name The name of the job.
     *
     * @return bool
     */
    public function hasJob(string $name): bool
    {
        return array_key_exists($name, $this->jobs);
    }

    /**
     * Get the configuration array for a job.
     *
     * @param string $name The name of the job.
     *
     * @return Definition
     *
     * @throws \InvalidArgumentException When the job does not exist.
     */
    public function getJob(string $name): Definition
    {
        if (!$this->hasJob($name)) {
            throw new \InvalidArgumentException('Job not found: ' . $name);
        }

        return $this->jobs[$name];
    }

    /**
     * Update the definition of a job.
     *
     * @param Definition $definition The definition to set.
     *
     * @return void
     */
    public function setJob(Definition $definition): void
    {
        $this->jobs[$definition->getName()] = $definition;
    }

    /**
     * Obtain the job names.
     *
     * @return array
     */
    public function getJobNames(): array
    {
        return array_keys($this->jobs);
    }

    /**
     * Check if a dictionary is defined.
     *
     * @param string $name The name of the dictionary.
     *
     * @return bool
     */
    public function hasDictionary(string $name): bool
    {
        return array_key_exists($name, $this->dictionaries);
    }

    /**
     * Get the configuration for a dictionary.
     *
     * @param string $name The dictionary name.
     *
     * @return Definition
     *
     * @throws \InvalidArgumentException When the dictionary is not found.
     */
    public function getDictionary(string $name): Definition
    {
        if (!$this->hasDictionary($name)) {
            throw new \InvalidArgumentException('Dictionary not found: ' . $name);
        }

        return $this->dictionaries[$name];
    }

    /**
     * Update the definition of a dictionary.
     *
     * @param Definition $definition The definition to set.
     *
     * @return void
     */
    public function setDictionary(Definition $definition): void
    {
        $this->dictionaries[$definition->getName()] = $definition;
    }

    /**
     * Obtain the job names.
     *
     * @return array
     */
    public function getDictionaryNames(): array
    {
        return array_keys($this->dictionaries);
    }
}
