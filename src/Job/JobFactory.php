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

use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use CyberSpectrum\I18N\Dictionary\WritableDictionaryInterface;
use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\Definition;
use CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition;
use CyberSpectrum\I18N\DictionaryBuilder\DictionaryBuilderInterface;
use CyberSpectrum\I18N\JobBuilder\JobBuilderInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * This builds translation jobs.
 */
class JobFactory
{
    /**
     * The dictionary builders.
     *
     * @var ServiceLocator
     */
    private $dictionaryBuilders;

    /**
     * The job type builders.
     *
     * @var ServiceLocator
     */
    private $jobBuilders;

    /**
     * The configuration.
     *
     * @var Configuration
     */
    private $configuration;

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
     * @param ServiceLocator  $jobBuilders        The job type builders.
     * @param Configuration   $configuration      The configuration to process.
     * @param LoggerInterface $logger             The logger.
     */
    public function __construct(
        ServiceLocator $dictionaryBuilders,
        ServiceLocator $jobBuilders,
        Configuration $configuration,
        LoggerInterface $logger
    ) {
        $this->dictionaryBuilders = $dictionaryBuilders;
        $this->jobBuilders        = $jobBuilders;
        $this->configuration      = $configuration;
        $this->logger             = $logger;
    }

    /**
     * Obtain the job names.
     *
     * @return array
     */
    public function getJobNames(): array
    {
        return $this->configuration->getJobNames();
    }

    /**
     * Process a configuration.
     *
     * @param string $name The name of the job to get.
     *
     * @return TranslationJobInterface
     *
     * @throws \UnexpectedValueException When the requested job is not configured.
     */
    public function createJobByName(string $name): TranslationJobInterface
    {
        if (!$this->configuration->hasJob($name)) {
            throw new \UnexpectedValueException('Job "' . $name . '" not found in configuration');
        }

        return $this->createJob($this->configuration->getJob($name));
    }

    /**
     * Process a configuration.
     *
     * @param Definition $configuration The job definition.
     *
     * @return TranslationJobInterface
     *
     * @throws \UnexpectedValueException When the requested job is not configured.
     */
    public function createJob(Definition $configuration): TranslationJobInterface
    {
        if (!$this->jobBuilders->has($type = $configuration->get('type'))) {
            throw new \UnexpectedValueException('Unknown job type "' . $type . '"');
        }

        $builder = $this->jobBuilders->get($type);
        /** @var JobBuilderInterface $builder */
        $job = $builder->build($this, $configuration);
        if ($job instanceof LoggerAwareInterface) {
            $job->setLogger($this->logger);
        }

        return $job;
    }

    /**
     * Create a dictionary.
     *
     * @param DictionaryDefinition $definition The dictionary configuration.
     *
     * @return DictionaryInterface
     */
    public function createDictionary(DictionaryDefinition $definition): DictionaryInterface
    {
        $builder = $this->getDictionaryBuilder($definition);

        /** @var DictionaryBuilderInterface $builder */
        return $builder->build($this, $definition);
    }

    /**
     * Create a dictionary.
     *
     * @param DictionaryDefinition $definition The dictionary configuration.
     *
     * @return WritableDictionaryInterface
     */
    public function createWritableDictionary(DictionaryDefinition $definition): WritableDictionaryInterface
    {
        $builder = $this->getDictionaryBuilder($definition);

        /** @var DictionaryBuilderInterface $builder */
        return $builder->buildWritable($this, $definition);
    }

    /**
     * Get a dictionary builder for the passed definition.
     *
     * @param DictionaryDefinition $definition The definition.
     *
     * @return DictionaryBuilderInterface
     */
    private function getDictionaryBuilder(DictionaryDefinition $definition): DictionaryBuilderInterface
    {
        if (!$this->dictionaryBuilders->has($typeName = $definition->getType())) {
            $typeName = 'default';
        }

        return $this->dictionaryBuilders->get($typeName);
    }
}
