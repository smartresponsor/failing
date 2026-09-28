<?php

declare(strict_types=1);

namespace App\Failing;

use App\Failing\DependencyInjection\Compiler\FailureProviderCompilerPass;
use App\Failing\DependencyInjection\FailingExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Reusable Symfony bundle entrypoint for the generic failure contract.
 */
final class FailingBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new FailureProviderCompilerPass());
    }

    public function getContainerExtension(): ExtensionInterface
    {
        return parent::getContainerExtension() ?? new FailingExtension();
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
