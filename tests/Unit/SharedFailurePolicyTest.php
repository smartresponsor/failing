<?php

declare(strict_types=1);

namespace App\Failing\Tests\Unit;

use App\Failing\Contract\FailureProviderInterface;
use App\Failing\Contract\OperationFailureInventoryProviderInterface;
use App\Failing\DTO\FailureDefinitionDTO;
use App\Failing\DTO\OperationFailureInventoryDTO;
use App\Failing\Inventory\OperationFailureInventory;
use App\Failing\Registry\FailureRegistry;
use App\Failing\Resolver\FailureResolver;
use App\Failing\ValueObject\FailureCode;
use App\Failing\ValueObject\FailureType;
use PHPUnit\Framework\TestCase;

final class SharedFailurePolicyTest extends TestCase
{
    public function testRegisteredSharedFailureIsNotImplicitlyAttachedToOperations(): void
    {
        $failureProvider = new class implements FailureProviderInterface {
            public function definitions(): iterable
            {
                yield new FailureDefinitionDTO(
                    new FailureCode('forbidden'),
                    new FailureType('urn:test:forbidden'),
                    403,
                    'Forbidden',
                );

                yield new FailureDefinitionDTO(
                    new FailureCode('resource_missing'),
                    new FailureType('urn:test:resource-missing'),
                    404,
                    'Resource missing',
                );
            }
        };

        $inventoryProvider = new class implements OperationFailureInventoryProviderInterface {
            public function inventories(): iterable
            {
                yield new OperationFailureInventoryDTO(
                    'GET',
                    '/api/v1/resources/{id}',
                    [new FailureCode('resource_missing')],
                );
            }
        };

        $registry = new FailureRegistry([$failureProvider]);
        $inventory = new OperationFailureInventory([$inventoryProvider], $registry);

        self::assertSame([[
            'method' => 'GET',
            'path' => '/api/v1/resources/{id}',
            'code' => 'resource_missing',
            'status' => 404,
        ]], $inventory->evidence());
    }

    public function testUnknownThrowableDoesNotBecomeImplicitInternalError(): void
    {
        $registry = new FailureRegistry([]);

        self::assertNull((new FailureResolver($registry))->resolve(new \RuntimeException('unexpected')));
    }
}
