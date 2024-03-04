<?php

declare(strict_types=1);

namespace Iluckhack\XhprofBuggregatorBundle\EventListener;

use SpiralPackages\Profiler\Profiler;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
final class ConsoleProfilerListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly Profiler $profiler,
        private readonly bool $isEnabled,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::COMMAND => ['start', 4096],
            ConsoleEvents::TERMINATE => ['end', -4096],
        ];
    }

    public function start(): void
    {
        if (!$this->isEnabled) {
            return;
        }

        $this->profiler->start();
    }

    public function end(): void
    {
        if (!$this->isEnabled) {
            return;
        }

        $this->profiler->end();
    }
}
