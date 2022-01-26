<?php

namespace Medelse\AriaBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class MedelseAriaExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('medelse.aria.base_url', $config['base_url']);
        $container->setParameter('medelse.aria.client_id', $config['client_id']);
        $container->setParameter('medelse.aria.client_secret', $config['client_secret']);
        $container->setParameter('medelse.aria.audience', $config['audience']);
    }
}
