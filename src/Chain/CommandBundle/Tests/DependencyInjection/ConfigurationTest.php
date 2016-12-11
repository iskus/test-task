<?php

namespace ChainCommandBundle\Tests\DependencyInjection;

use ChainCommandBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testItShouldParseValidConfigs()
    {
        $configs = [
            [
                'foo:hello' => ['bar:hi'],
                'foo:bar' => ['bar:foo'],
            ],
            [
                'bar:hello' => ['foo:hi', 'foo:hello'],
            ],
        ];

        $this->assertEquals([
            'foo:hello' => ['bar:hi'],
            'foo:bar' => ['bar:foo'],
            'bar:hello' => ['foo:hi', 'foo:hello'],
        ], $this->processConfigs($configs));
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidTypeException
     * @expectedExceptionMessage Invalid type for path "chain_commands.non:array". Expected array, but got string
     */
    public function testItShouldThrowExceptionOnInvalidType()
    {
        $this->processConfigs([
            [
                'non:array' => 'string',
            ],
        ]);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The path "chain_commands.foo:hello" should have at least 1 element(s) defined.
     */
    public function testItShouldThrowExceptionOnEmptyChain()
    {
        $this->processConfigs([
            [
                'foo:hello' => [],
            ],
        ]);
    }

    /**
     * @param array $configs
     * @return array
     */
    private function processConfigs(array $configs)
    {
        $processor = new Processor();

        return $processor->processConfiguration(new Configuration(), $configs);
    }
}
