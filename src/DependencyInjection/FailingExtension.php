<?php

declare(strict_types=1);

namespace App\Failing\DependencyInjection;

use App\Failing\Contract\FailureProviderInterface;
use App\Failing\Contract\FailureOperationInventoryProviderInterface;
use App\Failing\EventSubscriber\FailureExceptionSubscriber;
use App\Failing\Renderer\FailureProblemResponseRenderer;
use App\Failing\Inventory\FailureOperationInventory;
use App\Failing\Inventory\FailureOperationInventoryExporter;
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
        $container->registerForAutoconfiguration(FailureOperationInventoryProviderInterface::class)
            ->addTag(self::OPERATION_INVENTORY_PROVIDER_TAG);

        $container->setDefinition(FailureRegistry::class, new Definition(FailureRegistry::class, [[]]));
        $container->setDefinition(
            FailureOperationInventory::class,
            new Definition(FailureOperationInventory::class, [[], new Reference(FailureRegistry::class)]),
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
            FailureOperationInventoryExporter::class,
            new Definition(FailureOperationInventoryExporter::class, [new Reference(FailureOperationInventory::class)]),
        );
    }

    public function getAlias(): string
    {
        return 'failing';
    }
}
