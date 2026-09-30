<?php

declare(strict_types=1);

namespace App\Failing\Tests\Unit\DependencyInjection;

use App\Failing\Contract\FailureProviderInterface;
use App\Failing\Contract\FailureOperationInventoryProviderInterface;
use App\Failing\DependencyInjection\FailingExtension;
use App\Failing\DTO\FailureDefinitionDTO;
use App\Failing\DTO\FailureOperationInventoryDTO;
use App\Failing\FailingBundle;
use App\Failing\Inventory\FailureOperationInventory;
use App\Failing\Inventory\FailureOperationInventoryExporter;
use App\Failing\Registry\FailureRegistry;
use App\Failing\ValueObject\FailureCode;
use App\Failing\ValueObject\FailureType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class FailingBundleTest extends TestCase
{
    public function testBundleAutoconfiguresConsumerProvidersAndBuildsRuntimeInventory(): void
    {
        $container = new ContainerBuilder();
        $bundle = new FailingBundle();
        $bundle->getContainerExtension()->load([], $container);
        $bundle->build($container);

        $container->setDefinition(
            ExampleFailureProvider::class,
            (new Definition(ExampleFailureProvider::class))
                ->setAutoconfigured(true)
                ->setPublic(true),
        );
        $container->setDefinition(
            ExampleFailureOperationInventoryProvider::class,
            (new Definition(ExampleFailureOperationInventoryProvider::class))
                ->setAutoconfigured(true)
                ->setPublic(true),
        );

        foreach ([
            FailureRegistry::class,
            FailureOperationInventory::class,
            FailureOperationInventoryExporter::class,
        ] as $serviceId) {
            $container->getDefinition($serviceId)->setPublic(true);
        }

        $container->compile();

        self::assertTrue($container->getDefinition(ExampleFailureProvider::class)->hasTag(FailingExtension::FAILURE_PROVIDER_TAG));
        self::assertTrue($container->getDefinition(ExampleFailureOperationInventoryProvider::class)->hasTag(FailingExtension::OPERATION_INVENTORY_PROVIDER_TAG));

        $exporter = $container->get(FailureOperationInventoryExporter::class);
        self::assertInstanceOf(FailureOperationInventoryExporter::class, $exporter);
        self::assertSame([
            'schemaVersion' => 2,
            'coverage' => [[
                'method' => 'GET',
                'path' => '/api/example/{id}',
                'complete' => true,
            ]],
            'operations' => [[
                'method' => 'GET',
                'path' => '/api/example/{id}',
                'code' => 'example_not_found',
                'status' => 404,
                'complete' => true,
            ]],
        ], $exporter->export());
    }
}

final class ExampleFailureProvider implements FailureProviderInterface
{
    public function definitions(): iterable
    {
        yield new FailureDefinitionDTO(
            new FailureCode('example_not_found'),
            new FailureType('urn:example:not-found'),
            404,
            'Example not found',
        );
    }
}

final class ExampleFailureOperationInventoryProvider implements FailureOperationInventoryProviderInterface
{
    public function inventories(): iterable
    {
        yield new FailureOperationInventoryDTO(
            'GET',
            '/api/example/{id}',
            [new FailureCode('example_not_found')],
            true,
        );
    }
}
