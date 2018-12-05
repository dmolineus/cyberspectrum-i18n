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

namespace CyberSpectrum\I18N\Memory;

use CyberSpectrum\I18N\TranslationValue\WritableTranslationValueInterface;

/**
 * This implements a simple translation value - the value is stored in a local property.
 */
class MemoryTranslationValue implements WritableTranslationValueInterface
{
    /**
     * The translation key.
     *
     * @var string
     */
    private $key;

    /**
     * The source value.
     *
     * @var string|null
     */
    private $source;

    /**
     * The target value.
     *
     * @var string|null
     */
    private $target;

    /**
     * Create a new instance.
     *
     * @param string      $key
     * @param string      $source
     * @param string|null $target
     */
    public function __construct(string $key, string $source = null, string $target = null)
    {
        $this->key    = $key;
        $this->source = $source;
        $this->target = $target;
    }

    /**
     * {@inheritDoc}
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * {@inheritDoc}
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * {@inheritDoc}
     */
    public function getTarget(): ?string
    {
        return $this->target;
    }

    /**
     * Check if the source value is empty.
     *
     * @return bool
     */
    public function isSourceEmpty(): bool
    {
        return !$this->getSource();
    }

    /**
     * Check if the target value is empty.
     *
     * @return bool
     */
    public function isTargetEmpty(): bool
    {
        return !$this->getTarget();
    }

    /**
     * {@inheritDoc}
     */
    public function setSource(string $value): self
    {
        $this->source = $value;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setTarget(string $value): self
    {
        $this->target = $value;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function clearSource(): self
    {
        $this->source = null;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function clearTarget(): self
    {
        $this->target = null;

        return $this;
    }
}
