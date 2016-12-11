<?php

namespace Foo\Bundle\Command;

use Chain\CommandBundle\Command\ChainCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FooHelloCommand extends ChainCommand
{
	protected function configure() {
		$this
			->setName('foo:hello')
			->setDescription('Testing command');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		parent::execute($input, $output);
		$output->writeln('Hello from Foo!');
	}
	
}
