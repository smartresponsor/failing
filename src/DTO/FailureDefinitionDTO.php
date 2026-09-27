<?php

declare(strict_types=1);

namespace App\Failing\DTO;

use App\Failing\ValueObject\FailureCode;
use App\Failing\ValueObject\FailureType;

/**
 * Immutable declaration of one public failure contract.
 *
 * Business vocabulary remains consumer-owned. Failing only standardizes this shape.
 */
final readonly class FailureDefinitionDTO
{
    /** @param class-string<\Throwable>|null $exceptionClass */
    public function __construct(
        public FailureCode $code,
        public FailureType $type,
        public int $httpStatus,
        public string $title,
        public ?string $exceptionClass = null,
    ) {
        if ($httpStatus < 400 || $httpStatus > 599) {
            throw new \InvalidArgumentException('Failure HTTP status must be between 400 and 599.');
        }

        if ('' === trim($title)) {
            throw new \InvalidArgumentException('Failure title must not be empty.');
        }
    }
}
