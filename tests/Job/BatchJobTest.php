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

namespace CyberSpectrum\I18N\Test\Job;

use CyberSpectrum\I18N\Job\BatchJob;
use CyberSpectrum\I18N\Job\TranslationJobInterface;
use PHPUnit\Framework\TestCase;

/**
 * This tests the batch job.
 *
 * @covers \CyberSpectrum\I18N\Job\BatchJob
 */
class BatchJobTest extends TestCase
{
    /**
     * Test the run.
     *
     * @return void
     */
    public function testDryRun(): void
    {
        $batch = new BatchJob([
            'child1' => $child1 = $this->getMockForAbstractClass(TranslationJobInterface::class),
            'child2' => $child2 = $this->getMockForAbstractClass(TranslationJobInterface::class),
        ]);

        $child1->expects($this->once())->method('run')->with(true);
        $child2->expects($this->once())->method('run')->with(true);

        $batch->run(true);
    }

    /**
     * Test the run.
     *
     * @return void
     */
    public function testNoDryRun(): void
    {
        $batch = new BatchJob([
            'child1' => $child1 = $this->getMockForAbstractClass(TranslationJobInterface::class),
            'child2' => $child2 = $this->getMockForAbstractClass(TranslationJobInterface::class),
        ]);

        $child1->expects($this->once())->method('run')->with(false);
        $child2->expects($this->once())->method('run')->with(false);

        $batch->run(false);
    }

    /**
     * Test the run.
     *
     * @return void
     */
    public function testDefaultRun(): void
    {
        $batch = new BatchJob([
            'child1' => $child1 = $this->getMockForAbstractClass(TranslationJobInterface::class),
            'child2' => $child2 = $this->getMockForAbstractClass(TranslationJobInterface::class),
        ]);

        $child1->expects($this->once())->method('run')->with(null);
        $child2->expects($this->once())->method('run')->with(null);

        $batch->run();
    }
}
