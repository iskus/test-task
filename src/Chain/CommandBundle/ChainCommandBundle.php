<?php

namespace Chain\CommandBundle;

use Chain\CommandBundle\DependencyInjection\ChainCommandExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ChainCommandBundle extends Bundle
{
	public function getContainerExtension()
	{
		return new ChainCommandExtension();
	}
}