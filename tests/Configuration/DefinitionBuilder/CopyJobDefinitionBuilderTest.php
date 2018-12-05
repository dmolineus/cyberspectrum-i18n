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

namespace CyberSpectrum\I18N\Test\Configuration\DefinitionBuilder;

use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition;
use CyberSpectrum\I18N\Configuration\Definition\ExtendedDictionaryDefinition;
use CyberSpectrum\I18N\Configuration\Definition\CopyJobDefinition;
use CyberSpectrum\I18N\Configuration\DefinitionBuilder\CopyJobDefinitionBuilder;
use PHPUnit\Framework\TestCase;

/**
 * This tests the copy job builder.
 *
 * @covers \CyberSpectrum\I18N\Configuration\DefinitionBuilder\CopyJobDefinitionBuilder
 */
class CopyJobDefinitionBuilderTest extends TestCase
{
    /**
     * Data provider
     *
     * @return array
     */
    public function throwsForMissingKeyProvider(): array
    {
        return [
            'name'   => ['name', []],
            'source' => ['source', ['name' => 'foo']],
            'target' => ['target', ['name' => 'foo', 'source' => 'source']],
        ];
    }

    /**
     * Test that building throws when key is missing.
     *
     * @param string $key The key to expect.
     *
     * @param array  $data
     *
     * @return void
     *
     * @dataProvider throwsForMissingKeyProvider
     */
    public function testThrowsForMissingKey(string $key, array $data): void
    {
        $builder = new CopyJobDefinitionBuilder();

        $configuration = new Configuration();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing key "' . $key . '"');

        $builder->build($configuration, $data);
    }

    /**
     * Test building.
     *
     * @return void
     */
    public function testBuild(): void
    {
        $builder = new CopyJobDefinitionBuilder();

        $configuration = new Configuration();
        $configuration->setDictionary(new DictionaryDefinition('source'));
        $configuration->setDictionary(new DictionaryDefinition('target'));

        $job = $builder->build($configuration, [
            'type'   => 'copy',
            'name'   => 'test',
            'source' => 'source',
            'target' => 'target',
            'additional_value' => 'foo'
        ]);

        $this->assertInstanceOf(CopyJobDefinition::class, $job);
        /** @var CopyJobDefinition $job */
        $this->assertSame(['type'   => 'copy','additional_value' => 'foo'], $job->getData());
        $this->assertInstanceOf(ExtendedDictionaryDefinition::class, $source = $job->getSource());
        $this->assertSame('source', $source->getName());
        $this->assertSame([], $source->getData());
        $this->assertInstanceOf(ExtendedDictionaryDefinition::class, $target = $job->getTarget());
        $this->assertSame('target', $target->getName());
        $this->assertSame([], $target->getData());
    }

    /**
     * Test building.
     *
     * @return void
     */
    public function testBuildWithOverride(): void
    {
        $builder = new CopyJobDefinitionBuilder();

        $configuration = new Configuration();
        $configuration->setDictionary(new DictionaryDefinition('source'));
        $configuration->setDictionary(new DictionaryDefinition('target'));

        $job = $builder->build($configuration, [
            'type'   => 'copy',
            'name'   => 'test',
            'source' => 'source',
            'target' => ['name' => 'target', 'custom' => 'value'],
            'additional_value' => 'foo'
        ]);

        $this->assertInstanceOf(CopyJobDefinition::class, $job);
        /** @var CopyJobDefinition $job */
        $this->assertSame(['type'   => 'copy','additional_value' => 'foo'], $job->getData());
        $this->assertInstanceOf(ExtendedDictionaryDefinition::class, $source = $job->getSource());
        $this->assertSame('source', $source->getName());
        $this->assertSame([], $source->getData());
        $this->assertInstanceOf(ExtendedDictionaryDefinition::class, $target = $job->getTarget());
        $this->assertSame('target', $target->getName());
        $this->assertSame(['custom' => 'value'], $target->getData());
    }

    /**
     * Test building.
     *
     * @return void
     */
    public function testBuildWithOverriddenLanguages(): void
    {
        $builder = new CopyJobDefinitionBuilder();

        $configuration = new Configuration();
        $configuration->setDictionary(new DictionaryDefinition('source'));
        $configuration->setDictionary(new DictionaryDefinition('target'));

        $job = $builder->build($configuration, [
            'type'   => 'copy',
            'name'   => 'test',
            'source' => 'source',
            'target' => ['name' => 'target', 'custom' => 'value'],
            'source_language' => 'fr',
            'target_language' => 'de',
        ]);

        $this->assertInstanceOf(CopyJobDefinition::class, $job);
        /** @var CopyJobDefinition $job */
        $source = $job->getSource();
        $target = $job->getTarget();

        $this->assertSame('fr', $source->getSourceLanguage());
        $this->assertSame('de', $source->getTargetLanguage());
        $this->assertSame('fr', $target->getSourceLanguage());
        $this->assertSame('de', $target->getTargetLanguage());
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testThrowsForMissingNameInOverride(): void
    {
        $builder = new CopyJobDefinitionBuilder();

        $configuration = new Configuration();
        $configuration->setDictionary(new DictionaryDefinition('source'));
        $configuration->setDictionary(new DictionaryDefinition('target'));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Dictionary "test.target" information is missing key "name".');

        $builder->build($configuration, [
            'type'   => 'copy',
            'name'   => 'test',
            'source' => 'source',
            'target' => ['custom' => 'value']
        ]);
    }
}
