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

namespace CyberSpectrum\I18N\Exception;

use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use Throwable;

/**
 * This exception is thrown by dictionaries.
 */
abstract class AbstractDictionaryException extends \RuntimeException
{
    /**
     * The dictionary.
     *
     * @var DictionaryInterface
     */
    private $dictionary;

    /**
     * Create a new instance.
     *
     * @param DictionaryInterface $dictionary The dictionary emitting the exception.
     * @param string              $message    The Exception message to throw.
     * @param int                 $code       The Exception code.
     * @param Throwable|null      $previous   The optional previous exception.
     */
    public function __construct(DictionaryInterface $dictionary, $message = '', $code = 0, Throwable $previous = null)
    {
        $this->dictionary = $dictionary;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Retrieve dictionary.
     *
     * @return DictionaryInterface
     */
    public function getDictionary(): DictionaryInterface
    {
        return $this->dictionary;
    }
}
