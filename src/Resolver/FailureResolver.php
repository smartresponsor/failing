<?php

declare(strict_types=1);

namespace App\Failing\Resolver;

use App\Failing\DTO\FailureDefinitionDTO;
use App\Failing\Registry\FailureRegistry;

/**
 * Resolves already-declared consumer failures without replacing Symfony exception semantics.
 */
final readonly class FailureResolver
{
    public function __construct(private FailureRegistry $registry) {}

    public function resolve(\Throwable $throwable): ?FailureDefinitionDTO
    {
        return $this->registry->byException($throwable);
    }
}
