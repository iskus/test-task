<?php

namespace Chain\CommandBundle\Helper;

use Chain\CommandBundle\Command\ChainCommand;

class ChainCommandHelper
{
	/** @var array $parentName => [$chainCommand, $chainCommand2 ...] */
	private $chains = [];
	
	/**
	 * @param array $chains
	 */
	public function __construct(array $chains) {
		$this->chains = $chains;
	}
	
	/**
	 * @param ChainCommand $parent
	 * @return string[]
	 */
	public function getNames(ChainCommand $parent) {
		return isset($this->chains[$parent->getName()]) ? $this->chains[$parent->getName()] : [];
	}
	
	/**
	 * @param ChainCommand $chainCommand
	 * @return null|string
	 */
	public function getParentName(ChainCommand $chainCommand) {
		foreach ($this->chains as $parentCommandName => $chainCommands)
			if (in_array($chainCommand->getName(), $chainCommands)) return $parentCommandName;
		
		return null;
	}
}
