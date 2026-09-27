<?php

declare(strict_types=1);

namespace App\Failing\ValueObject;

/**
 * RFC 9457 problem type URI-reference owned by the consumer declaration.
 */
final readonly class FailureType
{
    public function __construct(public string $value)
    {
        if ('' === trim($value) || str_contains($value, ' ')) {
            throw new \InvalidArgumentException('Failure type must be a non-empty URI-reference without spaces.');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
