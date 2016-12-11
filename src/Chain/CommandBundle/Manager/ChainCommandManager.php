<?php

namespace Chain\CommandBundle\Manager;

use Chain\CommandBundle\Command\ChainCommand;

class ChainCommandManager
{
	/** @var array $parentCommandName => [$chainCommand, $chainCommand2 ...] */
	private $chains = [];
	
	/**
	 * @param array $chains
	 */
	public function __construct(array $chains) {
		$this->chains = $chains;
	}
	
	/**
	 * @param ChainCommand $parentCommand
	 * @return string[]
	 */
	public function getChainCommandNames(ChainCommand $parentCommand) {
		$parentCommandName = $parentCommand->getName();
		if (isset($this->chains[$parentCommandName])) {
			return $this->chains[$parentCommandName];
		}
		
		return [];
	}
	
	/**
	 * @param ChainCommand $chainCommand
	 * @return null|string
	 */
	public function getParentChainCommandName(ChainCommand $chainCommand) {
		foreach ($this->chains as $parentCommandName => $chainCommands) {
			if (in_array($chainCommand->getName(), $chainCommands)) {
				return $parentCommandName;
			}
		}
		
		return null;
	}
}
