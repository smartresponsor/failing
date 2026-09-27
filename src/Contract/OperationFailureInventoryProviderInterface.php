<?php

declare(strict_types=1);

namespace App\Failing\Contract;

use App\Failing\DTO\OperationFailureInventoryDTO;

/**
 * Supplies deterministic operation-to-failure membership declared by a consumer.
 */
interface OperationFailureInventoryProviderInterface
{
    /** @return iterable<OperationFailureInventoryDTO> */
    public function inventories(): iterable;
}
