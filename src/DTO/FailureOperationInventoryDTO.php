<?php

declare(strict_types=1);

namespace App\Failing\DTO;

use App\Failing\ValueObject\FailureCode;

/**
 * Deterministic membership of public failures in one external HTTP operation.
 */
final readonly class FailureOperationInventoryDTO
{
    /** @param list<FailureCode> $failures */
    public function __construct(
        public string $method,
        public string $path,
        public array $failures,
        public bool $complete = false,
    ) {
        if ($method !== strtoupper($method) || 1 !== preg_match('/^[A-Z]+$/', $method)) {
            throw new \InvalidArgumentException('Operation method must be an uppercase HTTP token.');
        }

        if (!str_starts_with($path, '/')) {
            throw new \InvalidArgumentException('Operation path must start with "/".');
        }
    }

    public function key(): string
    {
        return $this->method . ' ' . $this->path;
    }
}
