<?php

namespace Chain\CommandBundle\Command;

use Chain\CommandBundle\Exception\UnchainedRun;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ChainCommand extends ContainerAwareCommand
{
	const COMMAND_ARGUMENT = 'chained';
	
	/** @var  LoggerInterface */
	private $logger;
	
	final public function run(InputInterface $input, OutputInterface $output) {
		try {
			
			if ($chainCommands = $this->getChainCommands()) {
				$this->log($chainCommands);
			}
			
			$returnCode = parent::run($input, $buffered = new BufferedOutput());
			
			$this->getLogger()->info($outputString = $buffered->fetch());
			$output->write($outputString);
			
			if ($chainCommands) {
				$this->getApplication()->setAutoExit(false);
				$this->getLogger()->info("Executing {$this->getName()} chain members:");
				
				foreach ($chainCommands as $chainCommand) {
					$chainCommand->addArgument(self::COMMAND_ARGUMENT, InputArgument::REQUIRED);
					$this->execCommand($chainCommand, $output);
				}
				$this->getLogger()->info("Execution of {$this->getName()} chain completed.");
			}
			
			return $returnCode;
		} catch (UnchainedRun $ex) {
			return -1;
		}
	}
	
	/**
	 * @return ChainCommand[]
	 */
	public function getChainCommands() {
		$chainCommands = [];
		foreach ($this->getNames() as $chainCommandName) {
			$chainCommand = $this->getByName($chainCommandName);
			if ($chainCommand) {
				$chainCommands[] = $chainCommand;
			}
		}
		
		return $chainCommands;
	}
	
	/**
	 * @return string[]
	 */
	private function getNames() {
		return $this->getContainer()->get('chain_command.helper')->getNames($this);
	}
	
	/**
	 * @param $name
	 * @return ChainCommand|null
	 */
	private function getByName($name) {
		foreach ($this->getApplication()->all() as $command) {
			if ($command->getName() === $name && $command instanceof ChainCommand) {
				return $command;
			}
		}
		
		return null;
	}
	
	/**
	 * @param ChainCommand[] $chainCommands
	 */
	private function log(array $chainCommands) {
		$this->getLogger()
		     ->info("{$this->getName()} is a master command of a command chain that has registered member commands");
		foreach ($chainCommands as $chainCommand) {
			$this->getLogger()->info(
				"{$chainCommand->getName()} registered as a member of {$this->getName()} command chain"
			);
		}
		$this->getLogger()->info("Executing {$this->getName()} command itself first:");
	}
	
	/**
	 * @return LoggerInterface
	 */
	private function getLogger() {
		return $this->logger = $this->logger ? : $this->getContainer()->get('logger');
	}
	
	/**
	 * @param ChainCommand $command
	 * @param OutputInterface $output
	 * @return int
	 * @throws \Exception
	 */
	private function execCommand(ChainCommand $command, OutputInterface $output) {
		return $this->getApplication()->run(
			new ArrayInput(
				[
					'command' => $command->getName(),
					self::COMMAND_ARGUMENT => true,
				]
			),
			$output
		);
	}
	
	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int
	 * @throws UnchainedRun When command is registered as chain, but ran separately
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		if (($parent = $this->getParent()) && !$input->hasArgument(self::COMMAND_ARGUMENT)) {
			$errorMessage = $this->createParentErrorMessage($parent);
			$this->getLogger()->error($errorMessage);
			throw new UnchainedRun();
		}
		
		return 0;
	}
	
	/**
	 * @return ChainCommand|null
	 */
	private function getParent() {
			return $this->getByName($parentChainCommandName = $this->getParentName()) ? : null;
	}
	
	/**
	 * @return string|null
	 */
	private function getParentName() {
		return $this->getContainer()->get('chain_command.helper')->getParentName($this);
	}
	
	/**
	 * @param ChainCommand $parent
	 * @return string
	 */
	private function createParentErrorMessage(ChainCommand $parent) {
		$message = "<error>Error: '{$this->getName()}' command is a member of '{$parent->getName()}'".
			"command chain and cannot be executed on its own.</error>";
		
		return $message;
		
	}
}
