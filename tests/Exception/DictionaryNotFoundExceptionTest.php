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

namespace CyberSpectrum\I18N\Test\Exception;

use CyberSpectrum\I18N\Exception\DictionaryNotFoundException;
use PHPUnit\Framework\TestCase;

/**
 * This tests the exception.
 *
 * @covers \CyberSpectrum\I18N\Exception\DictionaryNotFoundException
 */
class DictionaryNotFoundExceptionTest extends TestCase
{
    /**
     * Test.
     *
     * @return void
     */
    public function testSetsValues(): void
    {
        $exception  = new DictionaryNotFoundException('foo', 'en', 'de');

        $this->assertSame('foo', $exception->getName());
        $this->assertSame('en', $exception->getSourceLanguage());
        $this->assertSame('de', $exception->getTargetLanguage());
        $this->assertSame(
            'Dictionary foo not found (requested source language: "en", requested target language: "de").',
            $exception->getMessage()
        );
    }
}
