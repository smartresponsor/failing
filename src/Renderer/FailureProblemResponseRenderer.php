<?php

declare(strict_types=1);

namespace App\Failing\Renderer;

use App\Failing\DTO\FailureDefinitionDTO;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Renders an already-resolved failure using the RFC 9457 Problem Details shape.
 */
final readonly class FailureProblemResponseRenderer
{
    /** @param array<string, mixed> $extensions */
    public function render(
        FailureDefinitionDTO $definition,
        string $detail,
        array $extensions = [],
    ): JsonResponse {
        return new JsonResponse([
            'type' => (string) $definition->type,
            'title' => $definition->title,
            'status' => $definition->httpStatus,
            'detail' => $detail,
            'code' => (string) $definition->code,
        ] + $extensions, $definition->httpStatus, ['Content-Type' => 'application/problem+json']);
    }
}
