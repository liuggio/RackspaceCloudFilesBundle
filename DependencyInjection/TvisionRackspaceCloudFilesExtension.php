<?php

namespace Tvision\RackspaceCloudFilesBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class TvisionRackspaceCloudFilesExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        

        $container->setParameter($this->getAlias().'.container_prefix', $config['container_prefix']);
        
        foreach ($config['stream_wrapper'] as $k => $v) {
           $container->setParameter($this->getAlias().'.stream_wrapper.'.$k, $v);
        }
        
        foreach ($config['auth'] as $k => $v) {
           $container->setParameter($this->getAlias().'.auth.'.$k, $v);
        }    
        
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        
    }
}
