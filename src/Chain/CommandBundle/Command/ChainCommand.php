<?php

namespace Chain\CommandBundle\Command;

use Chain\CommandBundle\Exception\UnchainedCommandRan;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ChainCommand extends ContainerAwareCommand
{
	const CHAINED_ARGUMENT = 'chained';
	
	/** @var  LoggerInterface */
	private $logger;
	
	final public function run(InputInterface $input, OutputInterface $output)
	{
		try {
			$chainCommands = $this->getChainCommands();
			
			if ($chainCommands) {
				$this->logChainInfo($chainCommands);
			}
			
			$bufferedOutput = new BufferedOutput();
			$returnCode = parent::run($input, $bufferedOutput);
			$outputString = $bufferedOutput->fetch();
			$this->getLogger()->info($outputString);
			$output->write($outputString);
			
			if ($chainCommands) {
				$this->getApplication()->setAutoExit(false);
				$this->getLogger()->info(sprintf('Executing %s chain members:', $this->getName()));
				foreach ($chainCommands as $chainCommand) {
					$chainCommand->addArgument(self::CHAINED_ARGUMENT, InputArgument::REQUIRED);
					$this->runChainCommand($chainCommand, $output);
				}
				$this->getLogger()->info(sprintf('Execution of %s chain completed.', $this->getName()));
			}
			
			return $returnCode;
		} catch (UnchainedCommandRan $ex) {
			return -1;
		}
	}
	
	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int
	 * @throws UnchainedCommandRan When command is registered as chain, but ran separately
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$parent = $this->getParent();
		if ($parent && !$input->hasArgument(self::CHAINED_ARGUMENT)) {
			$errorMessage = $this->prepareParentChainErrorMessage($parent);
			$this->getLogger()->error($errorMessage);
			throw new UnchainedCommandRan();
		}
		return 0;
	}
	
	/**
	 * @return ChainCommand|null
	 */
	private function getParent()
	{
		if ($parentChainCommandName = $this->getParentChainCommandName()) {
			return $this->findChainCommandByName($parentChainCommandName);
		}
		return null;
	}
	
	/**
	 * @return string|null
	 */
	private function getParentChainCommandName()
	{
		return $this->getContainer()->get('chain_command.manager')->getParentChainCommandName($this);
	}
	
	/**
	 * @param ChainCommand $parentChainCommand
	 * @return string
	 */
	private function prepareParentChainErrorMessage(ChainCommand $parentChainCommand)
	{
		return sprintf(
			'<error>Error: "%s" command is a member of "%s" command chain and cannot be executed on its own.</error>',
			$this->getName(),
			$parentChainCommand->getName()
		);
	}
	
	/**
	 * @return ChainCommand[]
	 */
	public function getChainCommands()
	{
		$chainCommands = [];
		foreach ($this->getChainCommandNames() as $chainCommandName) {
			$chainCommand = $this->findChainCommandByName($chainCommandName);
			if ($chainCommand) {
				$chainCommands[] = $chainCommand;
			}
		}
		return $chainCommands;
	}
	
	/**
	 * @return string[]
	 */
	private function getChainCommandNames()
	{
		return $this->getContainer()->get('chain_command.manager')->getChainCommandNames($this);
	}
	
	/**
	 * @param $name
	 * @return ChainCommand|null
	 */
	private function findChainCommandByName($name)
	{
		foreach ($this->getApplication()->all() as $command) {
			if ($command->getName() === $name && $command instanceof ChainCommand) {
				return $command;
			}
		}
		return null;
	}
	
	/**
	 * @param ChainCommand $command
	 * @param OutputInterface $output
	 * @return int
	 * @throws \Exception
	 */
	private function runChainCommand(ChainCommand $command, OutputInterface $output)
	{
		return $this->getApplication()->run(new ArrayInput([
			'command' => $command->getName(),
			self::CHAINED_ARGUMENT => true,
		]), $output);
	}
	
	/**
	 * @return LoggerInterface
	 */
	private function getLogger()
	{
		if (!$this->logger) {
			$this->logger = $this->getContainer()->get('logger');
		}
		return $this->logger;
	}
	
	/**
	 * @param ChainCommand[] $chainCommands
	 */
	private function logChainInfo(array $chainCommands)
	{
		$this->getLogger()->info(
			sprintf(
				'%s is a master command of a command chain that has registered member commands',
				$this->getName()
			)
		);
		foreach ($chainCommands as $chainCommand) {
			$this->getLogger()->info(sprintf(
				'%s registered as a member of %s command chain',
				$chainCommand->getName(),
				$this->getName()
			));
		}
		$this->getLogger()->info(sprintf('Executing %s command itself first:', $this->getName()));
	}
}
