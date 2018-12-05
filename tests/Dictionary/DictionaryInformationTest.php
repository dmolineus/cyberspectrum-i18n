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

namespace CyberSpectrum\I18N\Test\Dictionary;

use CyberSpectrum\I18N\Dictionary\DictionaryInformation;
use PHPUnit\Framework\TestCase;

/**
 * This tests the dictionary information class.
 *
 * @covers \CyberSpectrum\I18N\Dictionary\DictionaryInformation
 */
class DictionaryInformationTest extends TestCase
{
    /**
     * Test the getters.
     *
     * @return void
     */
    public function testGetters(): void
    {
        $information = new DictionaryInformation('foo', 'en', 'de');

        $this->assertSame('foo', $information->getName());
        $this->assertSame('en', $information->getSourceLanguage());
        $this->assertSame('de', $information->getTargetLanguage());
        $this->assertSame('foo en => de', (string) $information);
    }
}
