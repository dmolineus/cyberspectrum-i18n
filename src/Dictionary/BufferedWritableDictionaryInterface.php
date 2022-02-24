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

namespace CyberSpectrum\I18N\Dictionary;

/**
 * This interface describes a dictionary that can buffer updates.
 *
 * All write modifications will only be performed in memory after calling `beginBuffering()` and will only be saved by
 * calling `commitBuffer()`.
 *
 * This eases disk I/O on batch modifications but will consume more memory.
 */
interface BufferedWritableDictionaryInterface
{
    /**
     * Start buffering.
     *
     * @return void
     *
     * @throws \RuntimeException When the dictionary is already buffering.
     */
    public function beginBuffering(): void;

    /**
     * Persist the modifications in the buffer and end buffering.
     *
     * @return void
     *
     * @throws \RuntimeException When the dictionary is not currently buffering.
     */
    public function commitBuffer(): void;

    /**
     * Check if the dictionary is currently buffering.
     *
     * @return bool
     */
    public function isBuffering(): bool;
}
