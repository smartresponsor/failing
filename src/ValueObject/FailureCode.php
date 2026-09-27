<?php

declare(strict_types=1);

namespace App\Failing\ValueObject;

/**
 * Stable machine-readable consumer failure identifier.
 */
final readonly class FailureCode
{
    public function __construct(public string $value)
    {
        if (1 !== preg_match('/^[a-z][a-z0-9_]*(?:\.[a-z][a-z0-9_]*)+$/', $value)) {
            throw new \InvalidArgumentException(
                'Failure code must use dotted lower-case subject vocabulary, e.g. billing.invoice_not_found.',
            );
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
