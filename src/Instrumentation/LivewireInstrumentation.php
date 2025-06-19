<?php

namespace Keepsuit\LaravelOpenTelemetry\Instrumentation;

use Keepsuit\LaravelOpenTelemetry\Facades\Tracer;
use Livewire\Component;
use Livewire\EventBus;
use Livewire\LivewireManager;

class LivewireInstrumentation implements Instrumentation
{
    protected \WeakMap $components;

    public function register(array $options): void
    {
        $this->components = new \WeakMap;

        if (! class_exists(LivewireManager::class)) {
            return;
        }

        if (class_exists(EventBus::class)) {
            if (app()->resolved(LivewireManager::class)) {
                $this->registerLivewireV3(app()->make(LivewireManager::class));
            } else {
                app()->afterResolving(LivewireManager::class, $this->registerLivewireV3(...));
            }
        }
    }

    protected function registerLivewireV3(LivewireManager $livewireManager): void
    {
        $livewireManager->listen('mount', function (Component $component) {
            if (! Tracer::traceStarted()) {
                return;
            }

            $this->traceComponent($component);
        });

        $livewireManager->listen('hydrate', function (Component $component) {
            if (! Tracer::traceStarted()) {
                return;
            }

            $this->traceComponent($component);
        });

        $livewireManager->listen('dehydrate', function (Component $component) {
            $trace = $this->components[$component] ?? null;

            if ($trace === null) {
                return;
            }

            [$span, $scope] = $trace;

            $scope->detach();
            $span->end();
        });
    }

    protected function traceComponent(Component $component): void
    {
        $span = Tracer::newSpan('livewire component')
            ->setAttributes([
                'component.name' => $component->getName(),
                'component.id' => $component->getId(),
            ])
            ->start();
        $scope = $span->activate();

        $this->components[$component] = [$span, $scope];
    }
}
