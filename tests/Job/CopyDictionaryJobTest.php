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

use CyberSpectrum\I18N\Memory\MemoryDictionary;
use CyberSpectrum\I18N\Job\CopyDictionaryJob;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * This tests the translation copy class.
 *
 * @covers \CyberSpectrum\I18N\Job\CopyDictionaryJob
 */
class CopyDictionaryJobTest extends TestCase
{
    /**
     * Test.
     *
     * @return void
     */
    public function testCopyBoth(): void
    {
        $sourceDictionary = new MemoryDictionary('en', 'de', [
            'test-key1' => [
                'source' => 'Source 1',
                'target' => 'Target 1',
            ],
            'test-key2' => [
                'source' => 'Source 2',
                'target' => 'Target 2',
            ],
        ]);

        $targetDictionary = new MemoryDictionary('en', 'de', [
            'test-key1' => [
                'source' => '-old-value-',
                'target' => '-old-value-',
            ],
        ]);

        CopyDictionaryJob::create($sourceDictionary, $targetDictionary, $this->mockLogger())
            ->setCopySource()
            ->setCopyTarget()
            ->run();

        $reader = $targetDictionary->get('test-key1');
        $this->assertSame('Source 1', $reader->getSource());
        $this->assertSame('Target 1', $reader->getTarget());

        $reader = $targetDictionary->get('test-key2');
        $this->assertSame('Source 2', $reader->getSource());
        $this->assertSame('Target 2', $reader->getTarget());
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testCopySource(): void
    {
        $sourceDictionary = new MemoryDictionary('en', 'de', [
            'test-key1' => [
                'source' => 'Source 1',
                'target' => 'Target 1',
            ],
            'test-key2' => [
                'source' => 'Source 2',
                'target' => 'Target 2',
            ],
        ]);

        $targetDictionary = new MemoryDictionary('en', 'de', [
            'test-key1' => [
                'source' => '-old-value-',
                'target' => '-old-value-',
            ],
        ]);

        CopyDictionaryJob::create($sourceDictionary, $targetDictionary, $this->mockLogger())
            ->setCopySource()
            ->setCopyTarget(CopyDictionaryJob::DO_NOT_COPY)
            ->run();

        $reader = $targetDictionary->get('test-key1');
        $this->assertSame('Source 1', $reader->getSource());
        $this->assertSame('-old-value-', $reader->getTarget());

        $reader = $targetDictionary->get('test-key2');
        $this->assertSame('Source 2', $reader->getSource());
        $this->assertNull($reader->getTarget());
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testCopyTarget(): void
    {
        $sourceDictionary = new MemoryDictionary('en', 'de', [
            'test-key1' => [
                'source' => 'Source 1',
                'target' => 'Target 1',
            ],
            'test-key2' => [
                'source' => 'Source 2',
                'target' => 'Target 2',
            ],
        ]);

        $targetDictionary = new MemoryDictionary('en', 'de', [
            'test-key1' => [
                'source' => '-old-value-',
                'target' => '-old-value-',
            ],
        ]);

        CopyDictionaryJob::create($sourceDictionary, $targetDictionary, $this->mockLogger())
            ->setCopySource(CopyDictionaryJob::DO_NOT_COPY)
            ->setCopyTarget()
            ->run();

        $reader = $targetDictionary->get('test-key1');
        $this->assertSame('-old-value-', $reader->getSource());
        $this->assertSame('Target 1', $reader->getTarget());

        $reader = $targetDictionary->get('test-key2');
        $this->assertNull($reader->getSource());
        $this->assertSame('Target 2', $reader->getTarget());
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testCopyIfEmpty(): void
    {
        $sourceDictionary = new MemoryDictionary('en', 'de', [
            'test-key1' => [
                'source' => 'Source 1',
                'target' => 'Target 1',
            ],
            'test-key2' => [
                'source' => 'Source 2',
                'target' => 'Target 2',
            ],
            'test-key3' => [
                'source' => 'Source 3',
                'target' => 'Target 3',
            ],
        ]);

        $targetDictionary = new MemoryDictionary('en', 'de', [
            'test-key1' => [
                'source' => '-old-value-',
                'target' => '-old-value-',
            ],
            'test-key3' => [
                'source' => '',
                'target' => '',
            ],
        ]);

        CopyDictionaryJob::create($sourceDictionary, $targetDictionary, $this->mockLogger())
            ->setCopySource(CopyDictionaryJob::COPY_IF_EMPTY)
            ->setCopyTarget(CopyDictionaryJob::COPY_IF_EMPTY)
            ->run();

        $reader = $targetDictionary->get('test-key1');
        $this->assertSame('-old-value-', $reader->getSource());
        $this->assertSame('-old-value-', $reader->getTarget());

        $reader = $targetDictionary->get('test-key2');
        $this->assertSame('Source 2', $reader->getSource());
        $this->assertSame('Target 2', $reader->getTarget());

        $reader = $targetDictionary->get('test-key3');
        $this->assertSame('Source 3', $reader->getSource());
        $this->assertSame('Target 3', $reader->getTarget());
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testRemoveObsolete(): void
    {
        $sourceDictionary = new MemoryDictionary('en', 'de', [
            'test-key1' => [
                'source' => 'Source 1',
                'target' => 'Target 1',
            ],
        ]);

        $targetDictionary = new MemoryDictionary('en', 'de', [
            'test-key1' => [
                'source' => '-old-value-',
                'target' => '-old-value-',
            ],
            'test-key2' => [
                'source' => 'Source 2',
                'target' => 'Target 2',
            ],
        ]);

        CopyDictionaryJob::create($sourceDictionary, $targetDictionary, $this->mockLogger())
            ->setCopySource()
            ->setCopyTarget()
            ->setRemoveObsolete()
            ->run();

        $reader = $targetDictionary->get('test-key1');
        $this->assertSame('Source 1', $reader->getSource());
        $this->assertSame('Target 1', $reader->getTarget());

        $this->assertFalse($targetDictionary->has('test-key2'));
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testDryRunDoesNothing(): void
    {
        $sourceDictionary = new MemoryDictionary('en', 'de', [
            'test-key1' => [
                'source' => 'Source 1',
                'target' => 'Target 1',
            ],
            'test-key3' => [
                'source' => 'Source 3',
                'target' => 'Target 3',
            ],
        ]);

        $targetDictionary = new MemoryDictionary('en', 'de', [
            'test-key1' => [
                'source' => '-old-value-',
                'target' => '-old-value-',
            ],
            'test-key2' => [
                'source' => 'Source 2',
                'target' => 'Target 2',
            ],
        ]);

        CopyDictionaryJob::create($sourceDictionary, $targetDictionary, $this->mockLogger())
            ->setCopySource()
            ->setCopyTarget()
            ->setRemoveObsolete()
            ->setDryRun()
            ->run();

        $reader = $targetDictionary->get('test-key1');
        $this->assertSame('-old-value-', $reader->getSource());
        $this->assertSame('-old-value-', $reader->getTarget());
        $reader = $targetDictionary->get('test-key2');
        $this->assertSame('Source 2', $reader->getSource());
        $this->assertSame('Target 2', $reader->getTarget());
        $this->assertFalse($targetDictionary->has('test-key3'));
    }

    /**
     * Mock a logger.
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|LoggerInterface
     */
    private function mockLogger()
    {
        return $this->getMockForAbstractClass(LoggerInterface::class);
    }
}
