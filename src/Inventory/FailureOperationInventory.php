<?php

declare(strict_types=1);

namespace App\Failing\Inventory;

use App\Failing\Contract\FailureOperationInventoryProviderInterface;
use App\Failing\DTO\FailureOperationInventoryDTO;
use App\Failing\Registry\FailureRegistry;

/**
 * Aggregates operation membership and derives deterministic METHOD + path + status evidence.
 */
final class FailureOperationInventory
{
    /** @var array<string, FailureOperationInventoryDTO> */
    private array $operations = [];

    /** @param iterable<FailureOperationInventoryProviderInterface> $providers */
    public function __construct(
        iterable $providers,
        private readonly FailureRegistry $registry,
    ) {
        foreach ($providers as $provider) {
            foreach ($provider->inventories() as $inventory) {
                $key = $inventory->key();
                if (isset($this->operations[$key])) {
                    throw new \LogicException(sprintf('Duplicate operation failure inventory "%s".', $key));
                }

                $this->operations[$key] = $inventory;
            }
        }
    }

    /** @return list<array{method:string,path:string,complete:bool}> */
    public function coverage(): array
    {
        $coverage = [];
        foreach ($this->operations as $operation) {
            $coverage[] = [
                'method' => $operation->method,
                'path' => $operation->path,
                'complete' => $operation->complete,
            ];
        }

        usort(
            $coverage,
            static fn(array $left, array $right): int => [$left['path'], $left['method']] <=> [$right['path'], $right['method']],
        );

        return $coverage;
    }

    /** @return list<array{method:string,path:string,code:string,status:int}> */
    public function evidence(): array
    {
        $evidence = [];
        foreach ($this->operations as $operation) {
            foreach ($operation->failures as $failureCode) {
                $definition = $this->registry->byCode((string) $failureCode);
                if (null === $definition) {
                    throw new \LogicException(sprintf(
                        'Operation "%s" references unknown failure "%s".',
                        $operation->key(),
                        (string) $failureCode,
                    ));
                }

                $evidence[] = [
                    'method' => $operation->method,
                    'path' => $operation->path,
                    'code' => (string) $definition->code,
                    'status' => $definition->httpStatus,
                ];
            }
        }

        return $evidence;
    }
}
