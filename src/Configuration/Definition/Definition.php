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

use IteratorAggregate;
use Traversable;

/**
 * This is a simple key value store.
 */
class Definition implements IteratorAggregate
{
    /**
     * The name of the definition.
     *
     * @var string
     */
    protected $name;

    /**
     * The data.
     *
     * @var array
     */
    protected $data;

    /**
     * Create a new instance.
     *
     * @param string $name The name.
     * @param array  $data The initial data.
     */
    public function __construct(string $name, array $data = [])
    {
        $this->name = $name;
        $this->data = $data;
    }

    /**
     * Retrieve name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Retrieve data.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Set data.
     *
     * @param array $data The new value.
     *
     * @return Definition
     *
     * @deprecated We should make this immutable.
     */
    public function setData($data): Definition
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Check if a value has been set for the passed key.
     *
     * @param string $key The key to check.
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Set a value for the given key.
     *
     * @param string $key   The key to set.
     * @param mixed  $value The value to set.
     *
     * @return Definition
     *
     * @deprecated We should make this immutable.
     */
    public function set(string $key, $value): Definition
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Obtain the value for the given key.
     *
     * @param string $key The key.
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException When the key is not set.
     */
    public function get(string $key)
    {
        if (!$this->has($key)) {
            throw new \InvalidArgumentException('Key "' . $key . '" does not exist.');
        }
        return $this->data[$key];
    }

    /**
     * Get an iterator over the internal array.
     *
     * @return \ArrayIterator|Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getData());
    }
}
