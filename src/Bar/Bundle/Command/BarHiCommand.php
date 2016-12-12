<?php

namespace Bar\Bundle\Command;

use Chain\CommandBundle\Command\ChainCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BarHiCommand extends ChainCommand
{
	protected function configure() {
		$this->setName('bar:hi')->setDescription('...');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		parent::execute($input, $output);
		$output->writeln('Hi from Bar!');
	}
	
	
}
