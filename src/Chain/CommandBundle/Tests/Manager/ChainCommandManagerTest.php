<?php

namespace Chain\CommandBundle\Tests\Manager;


use Chain\CommandBundle\Command\ChainCommand;
use Chain\CommandBundle\Manager\ChainCommandManager;

class ChainCommandManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var ChainCommandManager */
    private $SUT;

    protected function setUp()
    {
        $this->SUT = new ChainCommandManager([
            'foo:single' => ['first'],
            'foo:multiple' => ['first', 'second'],
        ]);
    }

    public function testItShouldFindParent()
    {
        $this->assertEquals('foo:single', $this->SUT->getParentChainCommandName($this->prepareChainCommand('first')));
    }

    public function testItShouldNotFindParent()
    {
        $this->assertNull($this->SUT->getParentChainCommandName($this->prepareChainCommand('non-existent')));
    }

    public function testItShouldFindChain()
    {
        $this->assertEquals(['first'], $this->SUT->getChainCommandNames($this->prepareChainCommand('foo:single')));
        $this->assertEquals(
            ['first', 'second'],
            $this->SUT->getChainCommandNames($this->prepareChainCommand('foo:multiple'))
        );
    }

    public function testItShouldNotFindChain()
    {
        $this->assertEmpty($this->SUT->getChainCommandNames($this->prepareChainCommand('non-existent')));
    }

    /**
     * @param string $name
     * @return ChainCommand
     */
    private function prepareChainCommand($name)
    {
        $command = $this->getMockBuilder(ChainCommand::class)->disableOriginalConstructor()
            ->getMock();

        $command
            ->method('getName')
            ->willReturn($name);

        return $command;
    }
}
