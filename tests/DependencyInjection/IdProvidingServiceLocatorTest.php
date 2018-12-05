<?php

/**
 * This file is part of cyberspectrum/i18n-bundle.
 *
 * (c) 2018 CyberSpectrum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    cyberspectrum/i18n-bundle
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright  2018 CyberSpectrum.
 * @license    https://github.com/cyberspectrum/i18n-bundle/blob/master/LICENSE MIT
 * @filesource
 */

declare(strict_types = 1);

namespace CyberSpectrum\I18N\Test\DependencyInjection;

use CyberSpectrum\I18N\DependencyInjection\IdProvidingServiceLocator;
use PHPUnit\Framework\TestCase;

/**
 * This tests the service locator.
 *
 * @covers \CyberSpectrum\I18N\DependencyInjection\IdProvidingServiceLocator
 */
class IdProvidingServiceLocatorTest extends TestCase
{
    /**
     * Test that the id list is correctly returned.
     *
     * @return void
     */
    public function testReturnsIds(): void
    {
        $locator = new IdProvidingServiceLocator([
            'test1' => \Closure::fromCallable(function () {}),
            'test2' => \Closure::fromCallable(function () {}),
            'test3' => \Closure::fromCallable(function () {}),
        ]);

        $this->assertSame(['test1', 'test2', 'test3'], $locator->ids());
    }
}
