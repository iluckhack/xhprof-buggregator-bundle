<?php

declare(strict_types=1);

namespace Iluckhack\XhprofBuggregatorBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class XhprofBuggregatorExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        if (!\extension_loaded('xhprof')) {
            throw new \LogicException('PHP extension "xhprof" is required.');
        }

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../config')
        );

        $loader->load('services.yaml');
    }
}