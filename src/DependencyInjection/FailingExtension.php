<?php

declare(strict_types=1);

namespace App\Failing\DependencyInjection;

use App\Failing\Contract\FailureProviderInterface;
use App\Failing\Contract\OperationFailureInventoryProviderInterface;
use App\Failing\Http\FailureExceptionSubscriber;
use App\Failing\Http\FailureProblemResponseRenderer;
use App\Failing\Inventory\OperationFailureInventory;
use App\Failing\Inventory\OperationFailureInventoryExporter;
use App\Failing\Registry\FailureRegistry;
use App\Failing\Resolver\FailureResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registers the generic Failing services and consumer-provider autoconfiguration.
 */
final class FailingExtension extends Extension
{
    public const string FAILURE_PROVIDER_TAG = 'failing.failure_provider';
    public const string OPERATION_INVENTORY_PROVIDER_TAG = 'failing.operation_inventory_provider';

    /** @param list<array<string, mixed>> $configs */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(FailureProviderInterface::class)
            ->addTag(self::FAILURE_PROVIDER_TAG);
        $container->registerForAutoconfiguration(OperationFailureInventoryProviderInterface::class)
            ->addTag(self::OPERATION_INVENTORY_PROVIDER_TAG);

        $container->setDefinition(FailureRegistry::class, new Definition(FailureRegistry::class, [[]]));
        $container->setDefinition(
            OperationFailureInventory::class,
            new Definition(OperationFailureInventory::class, [[], new Reference(FailureRegistry::class)]),
        );
        $container->setDefinition(
            FailureResolver::class,
            new Definition(FailureResolver::class, [new Reference(FailureRegistry::class)]),
        );
        $container->setDefinition(FailureProblemResponseRenderer::class, new Definition(FailureProblemResponseRenderer::class));
        $container->setDefinition(
            FailureExceptionSubscriber::class,
            (new Definition(FailureExceptionSubscriber::class, [
                new Reference(FailureResolver::class),
                new Reference(FailureProblemResponseRenderer::class),
            ]))->addTag('kernel.event_subscriber'),
        );
        $container->setDefinition(
            OperationFailureInventoryExporter::class,
            new Definition(OperationFailureInventoryExporter::class, [new Reference(OperationFailureInventory::class)]),
        );
    }

    public function getAlias(): string
    {
        return 'failing';
    }
}
