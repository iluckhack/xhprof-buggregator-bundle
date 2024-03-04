<?php

declare(strict_types=1);

namespace Iluckhack\XhprofBuggregatorBundle;

use Iluckhack\XhprofBuggregatorBundle\DependencyInjection\XhprofBuggregatorExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class XhprofBuggregatorBundle extends AbstractBundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new XhprofBuggregatorExtension();
    }
}