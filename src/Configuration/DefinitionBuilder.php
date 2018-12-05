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

namespace CyberSpectrum\I18N\Configuration;

use CyberSpectrum\I18N\Configuration\Definition\Definition;
use CyberSpectrum\I18N\Configuration\DefinitionBuilder\DefinitionBuilderInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * This builds job and dictionary definitions from configuration arrays.
 */
class DefinitionBuilder
{
    /**
     * The services for building definitions.
     *
     * @var ServiceLocator
     */
    private $dictionaryBuilders;

    /**
     * The services for building definitions.
     *
     * @var ServiceLocator
     */
    private $jobBuilders;

    /**
     * Create a new instance.
     *
     * @param ServiceLocator $dictionaryBuilders The dictionary builders.
     * @param ServiceLocator $jobBuilders        The job builders.
     */
    public function __construct(ServiceLocator $dictionaryBuilders, ServiceLocator $jobBuilders)
    {
        $this->dictionaryBuilders = $dictionaryBuilders;
        $this->jobBuilders        = $jobBuilders;
    }

    /**
     * Build a job.
     *
     * @param Configuration $configuration The configuration to populate.
     * @param array         $data          The configuration values to use.
     *
     * @return Definition
     *
     * @throws \RuntimeException When the dictionary type is not registered.
     */
    public function buildDictionary(Configuration $configuration, array $data): Definition
    {
        try {
            $builder = $this->dictionaryBuilders->get($data['type']);
        } catch (ServiceNotFoundException $exception) {
            throw new \RuntimeException('Unknown dictionary type "' . $data['type'] . '"', 0, $exception);
        }
        /** @var DefinitionBuilderInterface $builder */
        return $builder->build($configuration, $data);
    }

    /**
     * Build a job.
     *
     * @param Configuration $configuration The configuration to populate.
     * @param array         $data          The configuration values to use.
     *
     * @return Definition
     *
     * @throws \RuntimeException When the job type is not registered.
     */
    public function buildJob(Configuration $configuration, array $data): Definition
    {
        try {
            $builder = $this->jobBuilders->get($data['type']);
        } catch (ServiceNotFoundException $exception) {
            throw new \RuntimeException('Unknown job type "' . $data['type'] . '"', 0, $exception);
        }
        /** @var DefinitionBuilderInterface $builder */
        return $builder->build($configuration, $data);
    }
}
