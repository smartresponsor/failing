<?php

declare(strict_types=1);

namespace App\Failing\Inventory;

/**
 * Produces a deterministic machine-readable runtime failure inventory for external consumers such as Gating.
 */
final readonly class OperationFailureInventoryExporter
{
    public const int SCHEMA_VERSION = 1;

    public function __construct(private OperationFailureInventory $inventory) {}

    /**
     * @return array{
     *     schemaVersion: int,
     *     operations: list<array{method:string,path:string,code:string,status:int}>
     * }
     */
    public function export(): array
    {
        $operations = $this->inventory->evidence();

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
