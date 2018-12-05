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

namespace CyberSpectrum\I18N\Test\Memory;

use CyberSpectrum\I18N\Memory\MemoryTranslationValue;
use PHPUnit\Framework\TestCase;

/**
 * This tests the simple translation value.
 *
 * @covers \CyberSpectrum\I18N\Memory\MemoryTranslationValue
 */
class MemoryTranslationValueTest extends TestCase
{
    /**
     * Test.
     *
     * @return void
     */
    public function testEmptyValue(): void
    {
        $value = new MemoryTranslationValue('test-key');

        $this->assertSame('test-key', $value->getKey());
        $this->assertNull($value->getSource());
        $this->assertNull($value->getTarget());
        $this->assertTrue($value->isSourceEmpty());
        $this->assertTrue($value->isTargetEmpty());
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testCreatingWithValuesWorks(): void
    {
        $value = new MemoryTranslationValue('test-key', 'Source value', 'Target value');

        $this->assertSame('Source value', $value->getSource());
        $this->assertSame('Target value', $value->getTarget());
        $this->assertFalse($value->isSourceEmpty());
        $this->assertFalse($value->isTargetEmpty());
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testSettingValuesWorks(): void
    {
        $value = new MemoryTranslationValue('test-key');

        $this->assertSame($value, $value->setSource('Source value'));
        $this->assertSame($value, $value->setTarget('Target value'));

        $this->assertSame('Source value', $value->getSource());
        $this->assertSame('Target value', $value->getTarget());
        $this->assertFalse($value->isSourceEmpty());
        $this->assertFalse($value->isTargetEmpty());
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testClearingValuesWorks(): void
    {
        $value = new MemoryTranslationValue('test-key', 'Source value', 'Target value');

        $this->assertSame($value, $value->clearSource());
        $this->assertSame($value, $value->clearTarget());

        $this->assertNull($value->getSource());
        $this->assertNull($value->getTarget());
        $this->assertTrue($value->isSourceEmpty());
        $this->assertTrue($value->isTargetEmpty());
    }
}
