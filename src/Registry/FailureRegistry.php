<?php

declare(strict_types=1);

namespace App\Failing\Registry;

use App\Failing\Contract\FailureProviderInterface;
use App\Failing\DTO\FailureDefinitionDTO;

/**
 * Runtime index aggregated from consumer providers; never a source-code business catalogue.
 */
final class FailureRegistry
{
    /** @var array<string, FailureDefinitionDTO> */
    private array $byCode = [];

    /** @var array<class-string<\Throwable>, FailureDefinitionDTO> */
    private array $byException = [];

    /** @param iterable<FailureProviderInterface> $providers */
    public function __construct(iterable $providers)
    {
        foreach ($providers as $provider) {
            foreach ($provider->definitions() as $definition) {
                $this->register($definition);
            }
        }
    }

    public function byCode(string $code): ?FailureDefinitionDTO
    {
        return $this->byCode[$code] ?? null;
    }

    public function byException(\Throwable $throwable): ?FailureDefinitionDTO
    {
        foreach ($this->byException as $class => $definition) {
            if ($throwable instanceof $class) {
                return $definition;
            }
        }

        return null;
    }

    /** @return list<FailureDefinitionDTO> */
    public function all(): array
    {
        return array_values($this->byCode);
    }

    private function register(FailureDefinitionDTO $definition): void
    {
        $code = (string) $definition->code;
        if (isset($this->byCode[$code])) {
            throw new \LogicException(sprintf('Duplicate failure code "%s".', $code));
        }

        $this->byCode[$code] = $definition;

        if (null === $definition->exceptionClass) {
            return;
        }

        if (isset($this->byException[$definition->exceptionClass])) {
            throw new \LogicException(sprintf('Duplicate failure exception mapping "%s".', $definition->exceptionClass));
        }

        $this->byException[$definition->exceptionClass] = $definition;
    }
}
