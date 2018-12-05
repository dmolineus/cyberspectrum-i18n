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

namespace CyberSpectrum\I18N\DependencyInjection;

use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * This adds a method "ids" for being able to inspect the list of registered service ids to the service locator.
 *
 * This helps mainly for debug purposes at the expense of more memory usage.
 */
class IdProvidingServiceLocator extends ServiceLocator
{
    /**
     * The id list.
     *
     * @var array
     */
    private $serviceIds;

    /**
     * Create a new instance.
     *
     * @param \Closure[] $factories The factories.
     */
    public function __construct($factories)
    {
        parent::__construct($factories);
        $this->serviceIds = \array_keys($factories);
    }

    /**
     * Obtain the ids of registered services.
     *
     * @return string[]
     */
    public function ids(): array
    {
        return $this->serviceIds;
    }
}
