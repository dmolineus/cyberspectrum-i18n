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

namespace CyberSpectrum\I18N\Configuration\Definition;

/**
 * This provides a way to "wrap" a parent definition and enhance/override it with own values.
 */
trait ExtendedDefinitionTrait
{
    /**
     * {@inheritDoc}
     *
     * @throws \RuntimeException When a parent value is not of type array and can therefore not be merged.
     */
    public function getData(): array
    {
        $combined = $this->getDelegated()->getData();
        foreach (parent::getData() as $key => $value) {
            if (!array_key_exists($key, $combined)) {
                $combined[$key] = $value;
                continue;
            }
            if (\is_array($value)) {
                if (!is_array($combined[$key])) {
                    throw new \RuntimeException('Can not merge key "' . $key . '", parent value is not an array.');
                }

                /** @noinspection SlowArrayOperationsInLoopInspection - can not optimize here */
                $combined[$key] = array_merge($combined[$key], $value);
                continue;
            }

            $combined[$key] = $value;
        }

        return $combined;
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $key): bool
    {
        return parent::has($key) || $this->getDelegated()->has($key);
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $key)
    {
        if (parent::has($key)) {
            return parent::get($key);
        }
        return $this->getDelegated()->get($key);
    }

    /**
     * Obtain the delegator.
     *
     * @return Definition
     */
    abstract protected function getDelegated(): Definition;
}
