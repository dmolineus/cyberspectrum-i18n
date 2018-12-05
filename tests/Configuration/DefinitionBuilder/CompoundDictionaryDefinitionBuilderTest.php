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
use CyberSpectrum\I18N\Configuration\DefinitionBuilder;
use CyberSpectrum\I18N\Configuration\DefinitionBuilder\CompoundDictionaryDefinitionBuilder;
use PHPUnit\Framework\TestCase;

/**
 * This tests the copy job builder.
 *
 * @covers \CyberSpectrum\I18N\Configuration\DefinitionBuilder\CompoundDictionaryDefinitionBuilder
 */
class CompoundDictionaryDefinitionBuilderTest extends TestCase
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
            'dictionaries' => ['dictionaries', ['name' => 'foo']],
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
        $builder = new CompoundDictionaryDefinitionBuilder(
            $this->getMockBuilder(DefinitionBuilder::class)->disableOriginalConstructor()->getMock()
        );

        $configuration = new Configuration();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing key "' . $key . '"');

        $builder->build($configuration, $data);
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testBuildingForDelegated(): void
    {
        $definitionBuilder = $this
            ->getMockBuilder(DefinitionBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['buildDictionary'])
            ->getMock();
        $definitionBuilder->expects($this->never())->method('buildDictionary');

        $configuration = new Configuration();

        $configuration->setDictionary(new DictionaryDefinition('base-dict1'));
        $configuration->setDictionary(new DictionaryDefinition('base-dict2'));

        $builder = new DefinitionBuilder\CompoundDictionaryDefinitionBuilder($definitionBuilder);

        $dictionary = $builder->build($configuration, [
            'type'   => 'compound',
            'name'   => 'test',
            'dictionaries' => ['base-dict1', 'base-dict2']
        ]);

        $this->assertInstanceOf(DictionaryDefinition::class, $dictionary);
        $this->assertCount(2, $dictionaries = $dictionary->get('dictionaries'));
        $this->assertInstanceOf(ExtendedDictionaryDefinition::class, $dictionaries[0]);
        $this->assertSame('base-dict1', $dictionaries[0]->getName());
        $this->assertInstanceOf(ExtendedDictionaryDefinition::class, $dictionaries[1]);
        $this->assertSame('base-dict2', $dictionaries[1]->getName());
    }

    /**
     * Test building.
     *
     * @return void
     */
    public function testBuildForInlined(): void
    {
        $inline            = new DictionaryDefinition('inlined');
        $configuration     = new Configuration();
        $definitionBuilder = $this
            ->getMockBuilder(DefinitionBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['buildDictionary'])
            ->getMock();
        $definitionBuilder
            ->expects($this->once())
            ->method('buildDictionary')
            ->with($configuration, ['type' => 'inline', 'name' => 'name', 'prefix' => 'prefix'])
            ->willReturn($inline);

        $builder = new CompoundDictionaryDefinitionBuilder($definitionBuilder);

        $dictionary = $builder->build($configuration, [
            'type'   => 'compound',
            'name'   => 'test',
            'dictionaries' => ['name' => ['type' => 'inline', 'prefix' => 'prefix']]
        ]);

        $this->assertInstanceOf(DictionaryDefinition::class, $dictionary);
        /** @var DictionaryDefinition $dictionary */
        $this->assertCount(1, $jobs = $dictionary->get('dictionaries'));
        $this->assertSame($inline, $jobs[0]);
    }

    /**
     * Test building.
     *
     * @return void
     */
    public function testBuildForInlinedWithoutNameAndPrefix(): void
    {
        $inline            = new DictionaryDefinition('inlined');
        $configuration     = new Configuration();
        $definitionBuilder = $this
            ->getMockBuilder(DefinitionBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['buildDictionary'])
            ->getMock();
        $definitionBuilder
            ->expects($this->once())
            ->method('buildDictionary')
            ->with($configuration, ['type' => 'inline', 'name' => 'name', 'prefix' => 'name'])
            ->willReturn($inline);

        $builder = new CompoundDictionaryDefinitionBuilder($definitionBuilder);

        $dictionary = $builder->build($configuration, [
            'type'   => 'compound',
            'name'   => 'test',
            'dictionaries' => ['name' => ['type' => 'inline']]
        ]);

        $this->assertInstanceOf(DictionaryDefinition::class, $dictionary);
        /** @var DictionaryDefinition $dictionary */
        $this->assertCount(1, $jobs = $dictionary->get('dictionaries'));
        $this->assertSame($inline, $jobs[0]);
    }
}
