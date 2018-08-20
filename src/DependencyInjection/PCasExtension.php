<?php

namespace OpenEuropa\pcas\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class PCasExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    protected function loadInternal(
        array $mergedConfig,
        ContainerBuilder $container
    ) {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../Resources/config')
        );

        $loader->load('p_cas.yml');

        $definition = $container->getDefinition('pcas');
        $definition->replaceArgument(0, $mergedConfig);
    }
}
