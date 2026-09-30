<?php

declare(strict_types=1);

namespace App\Failing\Tests\Unit;

use App\Failing\Contract\FailureProviderInterface;
use App\Failing\Contract\FailureOperationInventoryProviderInterface;
use App\Failing\DTO\FailureDefinitionDTO;
use App\Failing\DTO\FailureOperationInventoryDTO;
use App\Failing\Inventory\FailureOperationInventory;
use App\Failing\Inventory\FailureOperationInventoryExporter;
use App\Failing\Registry\FailureRegistry;
use App\Failing\ValueObject\FailureCode;
use App\Failing\ValueObject\FailureType;
use PHPUnit\Framework\TestCase;

final class FailureOperationInventoryExporterTest extends TestCase
{
    public function testExporterProducesVersionedDeterministicallySortedEvidence(): void
    {
        $failureProvider = new class implements FailureProviderInterface {
            public function definitions(): iterable
            {
                yield new FailureDefinitionDTO(
                    new FailureCode('resource_missing'),
                    new FailureType('urn:test:resource-missing'),
                    404,
                    'Resource missing',
                );

                yield new FailureDefinitionDTO(
                    new FailureCode('invalid_request'),
                    new FailureType('urn:test:invalid-request'),
                    400,
                    'Invalid request',
                );
            }
        };

        $inventoryProvider = new class implements FailureOperationInventoryProviderInterface {
            public function inventories(): iterable
            {
                yield new FailureOperationInventoryDTO(
                    'POST',
                    '/api/v1/resources',
                    [new FailureCode('invalid_request')],
                );

                yield new FailureOperationInventoryDTO(
                    'GET',
                    '/api/v1/resources/{id}',
                    [new FailureCode('resource_missing')],
                    true,
                );

                yield new FailureOperationInventoryDTO(
                    'DELETE',
                    '/api/v1/resources/{id}/archive',
                    [],
                    true,
                );
            }
        };

        $registry = new FailureRegistry([$failureProvider]);
        $inventory = new FailureOperationInventory([$inventoryProvider], $registry);
        $exporter = new FailureOperationInventoryExporter($inventory);

        self::assertSame([
            'schemaVersion' => 2,
            'coverage' => [
                [
                    'method' => 'POST',
                    'path' => '/api/v1/resources',
                    'complete' => false,
                ],
                [
                    'method' => 'GET',
                    'path' => '/api/v1/resources/{id}',
                    'complete' => true,
                ],
                [
                    'method' => 'DELETE',
                    'path' => '/api/v1/resources/{id}/archive',
                    'complete' => true,
                ],
            ],
            'operations' => [
                [
                    'method' => 'POST',
                    'path' => '/api/v1/resources',
                    'code' => 'invalid_request',
                    'status' => 400,
                    'complete' => false,
                ],
                [
                    'method' => 'GET',
                    'path' => '/api/v1/resources/{id}',
                    'code' => 'resource_missing',
                    'status' => 404,
                    'complete' => true,
                ],
            ],
        ], $exporter->export());

        self::assertJson($exporter->exportJson());
    }
}
