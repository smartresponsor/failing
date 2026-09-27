<?php

declare(strict_types=1);

namespace App\Failing\Contract;

use App\Failing\DTO\FailureDefinitionDTO;

/**
 * Supplies consumer-owned failure declarations to the generic Failing registry.
 */
interface FailureProviderInterface
{
    /** @return iterable<FailureDefinitionDTO> */
    public function definitions(): iterable;
}
