<?php

declare(strict_types=1);

namespace App\Failing\Tests\Unit;

use App\Failing\Contract\FailureProviderInterface;
use App\Failing\Contract\FailureOperationInventoryProviderInterface;
use App\Failing\DTO\FailureDefinitionDTO;
use App\Failing\DTO\FailureOperationInventoryDTO;
use App\Failing\Inventory\FailureOperationInventory;
use App\Failing\Registry\FailureRegistry;
use App\Failing\Resolver\FailureResolver;
use App\Failing\ValueObject\FailureCode;
use App\Failing\ValueObject\FailureType;
use PHPUnit\Framework\TestCase;

final class FailureContractTest extends TestCase
{
    public function testRegistryResolverAndOperationEvidenceShareOneDeclaration(): void
    {
        $provider = new class implements FailureProviderInterface {
            public function definitions(): iterable
            {
                yield new FailureDefinitionDTO(
                    new FailureCode('catalog.resource_not_found'),
                    new FailureType('/problems/catalog/resource-not-found'),
                    404,
                    'Resource not found',
                    \OutOfBoundsException::class,
                );
            }
        };

        $registry = new FailureRegistry([$provider]);
        $resolved = (new FailureResolver($registry))->resolve(new \OutOfBoundsException('missing'));

        self::assertNotNull($resolved);
        self::assertSame(404, $resolved->httpStatus);

        $inventoryProvider = new class implements FailureOperationInventoryProviderInterface {
            public function inventories(): iterable
            {
                yield new FailureOperationInventoryDTO(
                    'GET',
                    '/api/v1/catalog/{id}',
                    [new FailureCode('catalog.resource_not_found')],
                );
            }
        };

        $evidence = (new FailureOperationInventory([$inventoryProvider], $registry))->evidence();
        self::assertSame([[
            'method' => 'GET',
            'path' => '/api/v1/catalog/{id}',
            'code' => 'catalog.resource_not_found',
            'status' => 404,
        ]], $evidence);
    }
}
