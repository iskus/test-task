<?php

namespace Test\Bundle\Command;

use Chain\CommandBundle\Command\ChainCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestYoCommand extends ChainCommand
{
	protected function configure() {
		$this
			->setName('test:yo')
			->setDescription('...');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		parent::execute($input, $output);
		$output->writeln('Yo from Test');
	}
	
}
