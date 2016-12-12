<?php

namespace Chain\CommandBundle\Tests\Helper;


use Chain\CommandBundle\Command\ChainCommand;
use Chain\CommandBundle\Helper\ChainCommandHelper;

class ChainCommandHelperTest extends \PHPUnit_Framework_TestCase
{
	/** @var ChainCommandHelper */
	private $CCH;
	
	public function testItShouldFindParent() {
		$this->assertEquals('foo:single', $this->CCH->getParentName($this->prepareChainCommand('first')));
	}
	
	/**
	 * @param string $name
	 * @return ChainCommand
	 */
	private function prepareChainCommand($name) {
		$command = $this->getMockBuilder(ChainCommand::class)->disableOriginalConstructor()
		                ->getMock();
		
		$command
			->method('getName')
			->willReturn($name);
		
		return $command;
	}
	
	public function testItShouldNotFindParent() {
		$this->assertNull($this->CCH->getParentName($this->prepareChainCommand('non-existent')));
	}
	
	public function testItShouldFindChain() {
		$this->assertEquals(['first'], $this->CCH->getNames($this->prepareChainCommand('foo:single')));
		$this->assertEquals(
			['first', 'second'],
			$this->CCH->getNames($this->prepareChainCommand('foo:multiple'))
		);
	}
	
	public function testItShouldNotFindChain() {
		$this->assertEmpty($this->CCH->getNames($this->prepareChainCommand('non-existent')));
	}
	
	protected function setUp() {
		$this->CCH = new ChainCommandHelper(
			[
				'foo:single' => ['first'],
				'foo:multiple' => ['first', 'second'],
			]
		);
	}
}
