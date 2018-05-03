<?php

namespace OpenEuropa\pcas\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('p_cas');

        $rootNode
            ->children()
              ->scalarNode('logger_startup_message')->defaultValue('')->end()
              ->arrayNode('protocol')
                ->prototype('array')
                ->children()
                      ->scalarNode('uri')->isRequired()->end()
                      ->arrayNode('query')
                        ->canBeUnset()
                        ->prototype('scalar')->end()
                      ->end()
                      ->arrayNode('allowed_parameters')
                        ->canBeUnset()
                        ->prototype('scalar')->end()
                      ->end();

        return $treeBuilder;
    }
}
