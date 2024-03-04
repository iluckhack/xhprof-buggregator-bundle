<?php

declare(strict_types=1);

namespace Iluckhack\XhprofBuggregatorBundle\EventListener;

use SpiralPackages\Profiler\Profiler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @internal
 */
final class RequestProfilerListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly Profiler $profiler,
        private readonly bool $isEnabled,
        private readonly ?string $enabledHeader = null,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['start', 4096],
            KernelEvents::TERMINATE => ['end', -4096],
        ];
    }

    public function start(RequestEvent $event): void
    {
        if (!$this->isProfilingEnabled($event)) {
            return;
        }

        $this->profiler->start();
    }

    public function end(TerminateEvent $event): void
    {
        if (!$this->isProfilingEnabled($event)) {
            return;
        }

        $this->profiler->end();
    }

    private function isProfilingEnabled(KernelEvent $event): bool
    {
        if (!$event->isMainRequest()) {
            return false;
        }

        if ($this->enabledHeader === null || !$event->getRequest()->headers->has($this->enabledHeader)) {
            return $this->isEnabled;
        }

        return filter_var(
            $event->getRequest()->headers->get($this->enabledHeader),
            FILTER_VALIDATE_BOOLEAN,
        );
    }
}
