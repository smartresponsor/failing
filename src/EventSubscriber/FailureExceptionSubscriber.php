<?php

declare(strict_types=1);

namespace App\Failing\EventSubscriber;

use App\Failing\Renderer\FailureProblemResponseRenderer;
use App\Failing\Resolver\FailureResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Thin Symfony adapter: resolve a declared failure and render it; otherwise leave Symfony untouched.
 */
final readonly class FailureExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private FailureResolver $resolver,
        private FailureProblemResponseRenderer $renderer,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => 'onKernelException'];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();
        $definition = $this->resolver->resolve($throwable);
        if (null === $definition) {
            return;
        }

        $event->setResponse($this->renderer->render($definition, $throwable->getMessage()));
    }
}
