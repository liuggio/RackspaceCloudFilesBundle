<?php

namespace Liuggio\RackspaceCloudFilesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;


class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('liuggio_rackspace_cloud_files');


        $rootNode
            ->children()
                ->scalarNode('container_prefix')->defaultValue('')->end()
                ->scalarNode('service_class')->defaultValue('Liuggio\RackspaceCloudFilesBundle\Service\RSCFService')->end()
                ->arrayNode('stream_wrapper')
                    ->children()                
                        ->scalarNode('register')->defaultValue(false)->end()
                        ->scalarNode('protocol_name')->defaultValue('rscf')->end()
                        ->scalarNode('class')->defaultValue('\\Liuggio\\RackspaceCloudFilesStreamWrapper\\StreamWrapper\\RackspaceCloudFilesStreamWrapper')->end()
                    ->end()
                ->end()
                ->arrayNode('auth')
                    ->children()
                        ->scalarNode('username')->defaultValue(null)->end()
                        ->scalarNode('api_key')->defaultValue(null)->end()
                        ->scalarNode('container_name')->defaultValue(null)->end()
                        ->scalarNode('host')->defaultValue('https://lon.identity.api.rackspacecloud.com/v2.0')->end()
                        ->scalarNode('region')->defaultValue('LON')->end()
                    ->end()
              ->end();

        return $treeBuilder;
    }
}
