<?php

namespace Medelse\AriaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('medelse_aria');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('base_url')->end()
                ->scalarNode('client_id')->end()
                ->scalarNode('client_secret')->end()
                ->scalarNode('audience')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
