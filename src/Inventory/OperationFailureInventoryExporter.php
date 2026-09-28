<?php

declare(strict_types=1);

namespace App\Failing\Inventory;

/**
 * Produces a deterministic machine-readable runtime failure inventory for external consumers such as Gating.
 */
final readonly class OperationFailureInventoryExporter
{
    public const int SCHEMA_VERSION = 2;

    public function __construct(private OperationFailureInventory $inventory) {}

    /**
     * @return array{
     *     schemaVersion: int,
     *     coverage: list<array{method:string,path:string,complete:bool}>,
     *     operations: list<array{method:string,path:string,code:string,status:int,complete:bool}>
     * }
     */
    public function export(): array
    {
        $coverage = $this->inventory->coverage();
        $coverageByOperation = [];
        foreach ($coverage as $operationCoverage) {
            $coverageByOperation[$operationCoverage['method'] . ' ' . $operationCoverage['path']] = $operationCoverage['complete'];
        }

        $operations = array_map(
            static fn(array $operation): array => $operation + [
                'complete' => $coverageByOperation[$operation['method'] . ' ' . $operation['path']] ?? false,
            ],
            $this->inventory->evidence(),
        );

        usort(
            $operations,
            static fn(array $left, array $right): int => [
                $left['path'],
                $left['method'],
                $left['code'],
                $left['status'],
            ] <=> [
                $right['path'],
                $right['method'],
                $right['code'],
                $right['status'],
            ],
        );

        return [
            'schemaVersion' => self::SCHEMA_VERSION,
            'coverage' => $coverage,
            'operations' => $operations,
        ];
    }

    public function exportJson(): string
    {
        return json_encode(
            $this->export(),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
        ) . PHP_EOL;
    }
}
