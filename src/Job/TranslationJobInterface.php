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

namespace CyberSpectrum\I18N\Job;

/**
 * This interface describes translation jobs.
 */
interface TranslationJobInterface
{
    /**
     * Run the job.
     *
     * @param bool|null $dryRun Flag if the job should run as dry run - if null, the default value in the job is used.
     *
     * @return void
     */
    public function run(bool $dryRun = null): void;
}
