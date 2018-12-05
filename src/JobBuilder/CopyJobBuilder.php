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
use CyberSpectrum\I18N\Job\CopyDictionaryJob;
use CyberSpectrum\I18N\Job\TranslationJobInterface;
use CyberSpectrum\I18N\Configuration\Definition\Definition;
use CyberSpectrum\I18N\Configuration\Definition\CopyJobDefinition;
use CyberSpectrum\I18N\Job\JobFactory;

/**
 * This creates a copy job from a job definition.
 */
class CopyJobBuilder implements JobBuilderInterface
{
    /**
     * Build a copy job from the passed definition.
     *
     * @param JobFactory $factory    The job builder for recursive calls.
     * @param Definition $definition The definition.
     *
     * @return CopyDictionaryJob
     *
     * @throws \InvalidArgumentException When the passed definition is not a CopyJobDefinition.
     */
    public function build(JobFactory $factory, Definition $definition): TranslationJobInterface
    {
        if ($definition instanceof ReferencedJobDefinition) {
            $definition = $definition->getDelegated();
        }

        if (!$definition instanceof CopyJobDefinition) {
            throw new \InvalidArgumentException('Invalid definition passed.');
        }

        $job = new CopyDictionaryJob(
            $factory->createDictionary($definition->getSource()),
            $factory->createWritableDictionary($definition->getTarget())
        );

        if ($definition->has('copy-source')) {
            $job->setCopySource($this->copyStringToFlag($definition->get('copy-source')));
        }

        if ($definition->has('copy-target')) {
            $job->setCopyTarget($this->copyStringToFlag($definition->get('copy-target')));
        }

        if ($definition->has('remove-obsolete')) {
            $job->setRemoveObsolete($this->boolishToFlag($definition->get('remove-obsolete')));
        }

        if ($definition->has('filter')) {
            $job->setFilters($definition->get('filter'));
        }

        return $job;
    }

    /**
     * Convert the passed value to a copy flag.
     *
     * @param string|bool|null $value The value.
     *
     * @return int
     *
     * @throws \InvalidArgumentException When the value can not be converted.
     */
    private function copyStringToFlag($value): int
    {
        switch (true) {
            case 'true' === $value:
            case true === $value:
            case 'yes' === $value:
                return CopyDictionaryJob::COPY;
            case 'no' === $value:
            case 'false' === $value:
            case false === $value:
                return CopyDictionaryJob::DO_NOT_COPY;
            case 'if-empty' === $value:
                return CopyDictionaryJob::COPY_IF_EMPTY;
            default:
                throw new \InvalidArgumentException('Invalid value for copy flag.');
        }
    }

    /**
     * Convert the passed value to a bool.
     *
     * @param string|bool|null $value The value.
     *
     * @return bool
     *
     * @throws \InvalidArgumentException When the value can not be converted.
     */
    private function boolishToFlag($value): bool
    {
        switch (true) {
            case 'true' === $value:
            case true === $value:
            case 'yes' === $value:
                return true;
            case 'no' === $value:
            case 'false' === $value:
            case false === $value:
                return false;
            default:
                throw new \InvalidArgumentException('Invalid value for remove-obsolete flag.');
        }
    }
}
