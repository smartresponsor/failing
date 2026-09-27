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
        if (1 !== preg_match('/^[a-z][a-z0-9]*(?:[._-][a-z0-9]+)*$/', $value)) {
            throw new \InvalidArgumentException(
                'Failure code must be a stable lower-case machine token using letters, digits, dot, underscore, or hyphen.',
            );
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
