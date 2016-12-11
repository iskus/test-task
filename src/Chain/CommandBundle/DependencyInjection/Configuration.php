<?php

namespace Chain\CommandBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
	/**
	 * Generates the configuration tree builder.
	 *
	 * @return TreeBuilder The tree builder
	 */
	public function getConfigTreeBuilder() {
		$treeBuilder = new TreeBuilder();
		$rootNode    = $treeBuilder->root('chain_commands');
		
		$rootNode
			->useAttributeAsKey('name')
			->prototype('array')
			->requiresAtLeastOneElement()
			->prototype('scalar')->end()
			->end();
		
		return $treeBuilder;
	}
}
