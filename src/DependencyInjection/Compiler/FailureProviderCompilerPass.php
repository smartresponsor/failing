<?php

declare(strict_types=1);

namespace App\Failing\DependencyInjection\Compiler;

use App\Failing\DependencyInjection\FailingExtension;
use App\Failing\Inventory\FailureOperationInventory;
use App\Failing\Registry\FailureRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Injects consumer-declared failure and operation providers without enumerating consumers.
 */
final class FailureProviderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(FailureRegistry::class) || !$container->hasDefinition(FailureOperationInventory::class)) {
            return;
        }

        $failureProviders = [];
        foreach ($container->findTaggedServiceIds(FailingExtension::FAILURE_PROVIDER_TAG) as $id => $_tags) {
            $failureProviders[$id] = new Reference($id);
        }
        ksort($failureProviders);

        $operationProviders = [];
        foreach ($container->findTaggedServiceIds(FailingExtension::OPERATION_INVENTORY_PROVIDER_TAG) as $id => $_tags) {
            $operationProviders[$id] = new Reference($id);
        }
        ksort($operationProviders);

        $container->getDefinition(FailureRegistry::class)
            ->setArgument(0, array_values($failureProviders));

        $container->getDefinition(FailureOperationInventory::class)
            ->setArgument(0, array_values($operationProviders));
    }
}
