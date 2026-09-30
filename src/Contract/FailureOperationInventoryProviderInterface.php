<?php

declare(strict_types=1);

namespace App\Failing\Contract;

use App\Failing\DTO\FailureOperationInventoryDTO;

/**
 * Supplies deterministic operation-to-failure membership declared by a consumer.
 */
interface FailureOperationInventoryProviderInterface
{
    /** @return iterable<FailureOperationInventoryDTO> */
    public function inventories(): iterable;
}
