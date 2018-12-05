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
use CyberSpectrum\I18N\TranslationValue\TranslationValueInterface;
use CyberSpectrum\I18N\TranslationValue\WritableTranslationValueInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;

/**
 * This copies the translations from one dictionary to another one.
 */
class CopyDictionaryJob implements TranslationJobInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Do not copy.
     */
    public const DO_NOT_COPY = 0;

    /**
     * Copy values.
     */
    public const COPY = 1;

    /**
     * Copy if empty.
     */
    public const COPY_IF_EMPTY = 2;

    /**
     * The source dictionary.
     *
     * @var DictionaryInterface
     */
    private $sourceDictionary;

    /**
     * The target dictionary.
     *
     * @var WritableDictionaryInterface
     */
    private $targetDictionary;

    /**
     * Flag if the source value shall be copied/updated.
     *
     * @var int
     */
    private $copySource = self::COPY_IF_EMPTY;

    /**
     * Flag if the target value shall be copied/updated.
     *
     * @var int
     */
    private $copyTarget = self::COPY_IF_EMPTY;

    /**
     * Flag if obsolete keys shall be removed.
     *
     * @var bool
     */
    private $removeObsolete = false;

    /**
     * Flag if this is a dry run (false => update target, true => log updates with level notice).
     *
     * @var bool
     */
    private $dryRun = false;

    /**
     * The log level to use for informal messages.
     *
     * @var string
     */
    private $logLevel;

    /**
     * The regular expressions to filter.
     *
     * @var string[]
     */
    private $filters = [];

    /**
     * Create a new instance.
     *
     * @param DictionaryInterface         $sourceDictionary The source dictionary.
     * @param WritableDictionaryInterface $targetDictionary The target dictionary.
     */
    public function __construct(
        DictionaryInterface $sourceDictionary,
        WritableDictionaryInterface $targetDictionary
    ) {
        $this->sourceDictionary = $sourceDictionary;
        $this->targetDictionary = $targetDictionary;
        $this->logger           = new NullLogger();
    }

    /**
     * Static helper for fluent coding.
     *
     * @param DictionaryInterface         $sourceDictionary The source dictionary.
     * @param WritableDictionaryInterface $targetDictionary The target dictionary.
     * @param LoggerInterface|null        $logger           The logger to use.
     *
     * @return CopyDictionaryJob
     */
    public static function create(
        DictionaryInterface $sourceDictionary,
        WritableDictionaryInterface $targetDictionary,
        LoggerInterface $logger = null
    ): CopyDictionaryJob {
        $instance = new static($sourceDictionary, $targetDictionary);
        if ($logger) {
            $instance->setLogger($logger);
        }

        return $instance;
    }

    /**
     * Set copySource.
     *
     * @param int $copySource The new value.
     *
     * @return CopyDictionaryJob
     */
    public function setCopySource(int $copySource = self::COPY): CopyDictionaryJob
    {
        $this->copySource = $copySource;

        return $this;
    }

    /**
     * Retrieve copy source flag.
     *
     * @return int
     */
    public function getCopySource(): int
    {
        return $this->copySource;
    }

    /**
     * Set copyTarget.
     *
     * @param int $copyTarget The new value.
     *
     * @return CopyDictionaryJob
     */
    public function setCopyTarget(int $copyTarget = self::COPY): CopyDictionaryJob
    {
        $this->copyTarget = $copyTarget;

        return $this;
    }

    /**
     * Retrieve copy target flag.
     *
     * @return int
     */
    public function getCopyTarget(): int
    {
        return $this->copyTarget;
    }

    /**
     * Set removeObsolete.
     *
     * @param bool $removeObsolete The new value (defaults to true).
     *
     * @return CopyDictionaryJob
     */
    public function setRemoveObsolete(bool $removeObsolete = true): CopyDictionaryJob
    {
        $this->removeObsolete = $removeObsolete;

        return $this;
    }

    /**
     * Retrieve remove obsolete flag.
     *
     * @return bool
     */
    public function hasRemoveObsolete(): bool
    {
        return $this->removeObsolete;
    }

    /**
     * Set dryRun.
     *
     * @param bool $dryRun The new value.
     *
     * @return CopyDictionaryJob
     */
    public function setDryRun(bool $dryRun = true): CopyDictionaryJob
    {
        $this->dryRun = $dryRun;

        return $this;
    }

    /**
     * Retrieve dry run flag.
     *
     * @return bool
     */
    public function isDryRun(): bool
    {
        return $this->dryRun;
    }

    /**
     * Add a regular expression.
     *
     * @param string $expression The regular expression matching keys to filter away.
     *
     * @return CopyDictionaryJob
     *
     * @throws \InvalidArgumentException When the regex is invalid.
     */
    public function addFilter(string $expression): CopyDictionaryJob
    {
        // Check if the first and last char match - if not, we must encapsulate with '/'.
        if ($expression[0] !== substr($expression, -1)) {
            $expression = '/' . $expression . '/';
        }

        // Test if the regex is valid.
        try {
            preg_match($expression, '');
        } catch (\Throwable $error) {
            throw new \InvalidArgumentException(
                sprintf('Filter "%s" is not a valid regular expression - Error: %s', $expression, $error->getMessage()),
                0,
                $error
            );
        }

        $this->filters[] = $expression;

        return $this;
    }

    /**
     * Set the filter expressions.
     *
     * @param array $expressions
     *
     * @return CopyDictionaryJob
     */
    public function setFilters(array $expressions): CopyDictionaryJob
    {
        $this->filters = [];
        foreach ($expressions as $expression) {
            $this->addFilter($expression);
        }

        return $this;
    }

    /**
     * Obtain the regular expressions.
     *
     * @return string[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * {@inheritDoc}
     */
    public function run(bool $dryRun = null): void
    {
        $prevDry = $this->dryRun;
        try {
            if (null !== $dryRun) {
                $this->dryRun = $dryRun;
            }

            $this->logLevel = $this->dryRun ? LogLevel::NOTICE : LogLevel::DEBUG;

            foreach ($this->sourceDictionary->keys() as $key) {
                if ($this->isFiltered($key)) {
                    continue;
                }
                $this->copyKey($key);
            }

            if ($this->removeObsolete) {
                $this->cleanTarget();
            }
        } finally {
            $this->dryRun = $prevDry;
        }
    }

    /**
     * Copy a key.
     *
     * @param string $key The key.
     *
     * @return void
     */
    private function copyKey(string $key): void
    {
        $source = $this->sourceDictionary->get($key);
        if ($source->isSourceEmpty()) {
            $this->logger->debug(
                '{key}: Is empty in source language and therefore skipped.',
                ['key' => $key]
            );
            return;
        }

        if (!$this->targetDictionary->has($key)) {
            $this->logger->log($this->logLevel, 'Adding key {key}.', ['key' => $key]);
            if ($this->dryRun) {
                return;
            }

            $this->targetDictionary->add($key);
        }
        $target = $this->targetDictionary->getWritable($key);

        $this->copySource($source, $target);
        $this->copyTarget($source, $target);
    }

    /**
     * Copy the source value.
     *
     * @param TranslationValueInterface         $source The source value.
     * @param WritableTranslationValueInterface $target The target value.
     *
     * @return void
     */
    private function copySource(TranslationValueInterface $source, WritableTranslationValueInterface $target): void
    {
        if (self::DO_NOT_COPY === $this->copySource) {
            return;
        }

        if (($oldValue = $target->getSource()) === ($newValue = $source->getSource())) {
            $this->logger->log(
                $this->logLevel,
                '{key}: Source is same, no need to update.',
                ['key' => $target->getKey(), 'old' => $oldValue, 'new' => $newValue]
            );
            return;
        }

        if ((self::COPY_IF_EMPTY === $this->copySource) && !$target->isSourceEmpty()) {
            $this->logger->log(
                $this->logLevel,
                '{key}: Source is not empty, no need to update.',
                ['key' => $target->getKey(), 'old' => $oldValue, 'new' => $newValue]
            );

            return;
        }

        $this->logger->log(
            LogLevel::NOTICE,
            '{key}: Updating source value.',
            ['key' => $target->getKey(), 'old' => $oldValue, 'new' => $newValue]
        );

        if ($this->dryRun) {
            return;
        }

        $target->setSource($newValue);
    }

    /**
     * Copy the target value.
     *
     * @param TranslationValueInterface         $source The source value.
     * @param WritableTranslationValueInterface $target The target value.
     *
     * @return void
     */
    private function copyTarget(TranslationValueInterface $source, WritableTranslationValueInterface $target): void
    {
        if (self::DO_NOT_COPY === $this->copyTarget) {
            return;
        }

        if ((($oldValue = $target->getTarget()) === ($newValue = $source->getTarget()))
            || ($target->isTargetEmpty() && $source->isTargetEmpty())
        ) {
            $this->logger->log(
                $this->logLevel,
                '{key}: Target is same, no need to update.',
                ['key' => $target->getKey(), 'old' => $oldValue, 'new' => $newValue]
            );
            return;
        }

        if ((self::COPY_IF_EMPTY === $this->copyTarget) && !$target->isTargetEmpty()) {
            $this->logger->log(
                $this->logLevel,
                '{key}: Target is not empty, no need to update.',
                ['key' => $target->getKey(), 'old' => $oldValue, 'new' => $newValue]
            );

            return;
        }

        $this->logger->log(
            LogLevel::NOTICE,
            '{key}: Updating target value.',
            ['key' => $target->getKey(), 'old' => $oldValue, 'new' => $newValue]
        );

        if ($this->dryRun) {
            return;
        }

        if (null === $value = $source->getTarget()) {
            $target->clearTarget();
            return;
        }
        $target->setTarget($value);
    }

    /**
     * Clean the target language.
     *
     * @return void
     */
    private function cleanTarget(): void
    {
        foreach ($this->targetDictionary->keys() as $key) {
            if (!$this->sourceDictionary->has($key) || $this->sourceDictionary->get($key)->isSourceEmpty()) {
                $this->logger->log($this->logLevel, 'Removing obsolete {key}.', ['key' => $key]);
                if ($this->dryRun) {
                    continue;
                }

                $this->targetDictionary->remove($key);
            }
        }
    }

    /**
     * Check if the passed key is filtered by any of the regexes.
     *
     * @param string $key The key to check.
     *
     * @return bool
     */
    private function isFiltered($key): bool
    {
        foreach ($this->filters as $expression) {
            if (preg_match($expression, $key)) {
                $this->logger->debug(sprintf('"%1$s" is filtered by "%2$s', $key, $expression));
                return true;
            }
        }

        return false;
    }
}
