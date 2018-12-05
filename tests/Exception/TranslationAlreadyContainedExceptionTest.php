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

use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use CyberSpectrum\I18N\Exception\TranslationAlreadyContainedException;
use PHPUnit\Framework\TestCase;

/**
 * This tests the exception.
 *
 * @covers \CyberSpectrum\I18N\Exception\TranslationAlreadyContainedException
 */
class TranslationAlreadyContainedExceptionTest extends TestCase
{
    /**
     * Test.
     *
     * @return void
     */
    public function testSetsValues(): void
    {
        $previous   = new \RuntimeException();
        $dictionary = $this->getMockForAbstractClass(DictionaryInterface::class);
        $exception  = new TranslationAlreadyContainedException('key', $dictionary, $previous);

        $this->assertSame('key', $exception->getKey());
        $this->assertSame($dictionary, $exception->getDictionary());
        $this->assertSame('Key "key" already contained', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
