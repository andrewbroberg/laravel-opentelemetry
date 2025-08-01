# Changelog

All notable changes to `laravel-opentelemetry` will be documented in this file.

## v1.8.0 - 2025-06-29

### What's Changed

* Console commands tracing by @cappuc in https://github.com/keepsuit/laravel-opentelemetry/pull/39

#### Upgrade guide

Add the `ConsoleInstrumentation` to the config `instrumentation` array:

```php
Instrumentation\ConsoleInstrumentation::class => [
    'enabled' => env('OTEL_INSTRUMENTATION_CONSOLE', true),
    'excluded' => [],
],

```
**Full Changelog**: https://github.com/keepsuit/laravel-opentelemetry/compare/1.7.0...1.8.0

## v1.7.0 - 2025-06-20

### What's Changed

* Http client global by @cappuc in https://github.com/keepsuit/laravel-opentelemetry/pull/38

#### Upgrade guide

In order to disable http client global tracing and keep the old behaviour, add `'manual' => true` to the instrumentation config

**Full Changelog**: https://github.com/keepsuit/laravel-opentelemetry/compare/1.6.0...1.7.0

## v1.6.0 - 2025-06-19

### What's Changed

* Bump stefanzweifel/git-auto-commit-action from 5 to 6 by @dependabot in https://github.com/keepsuit/laravel-opentelemetry/pull/35
* Livewire v3 instrumentation by @cappuc in https://github.com/keepsuit/laravel-opentelemetry/pull/36
* added app boostrap span by @cappuc in https://github.com/keepsuit/laravel-opentelemetry/pull/37

#### Upgrade guide

If you have published the config file, add `Instrumentation\LivewireInstrumentation::class => env('OTEL_INSTRUMENTATION_LIVEWIRE', true)` to instrumentation array of the config.

**Full Changelog**: https://github.com/keepsuit/laravel-opentelemetry/compare/1.5.0...1.6.0

## v1.5.0 - 2025-06-16

### What's Changed

* view tracing by @cappuc in https://github.com/keepsuit/laravel-opentelemetry/pull/34

#### Upgrade guide

If you have published the config file, add `Instrumentation\ViewInstrumentation::class => env('OTEL_INSTRUMENTATION_VIEW', true)` to `instrumentation` array of the config.

**Full Changelog**: https://github.com/keepsuit/laravel-opentelemetry/compare/1.4.0...1.5.0

## v1.4.0 - 2025-05-23

### What's Changed

* Skip recording of internal spans when trace is not started by @cappuc in https://github.com/keepsuit/laravel-opentelemetry/pull/33

**Full Changelog**: https://github.com/keepsuit/laravel-opentelemetry/compare/1.3.0...1.4.0

## v1.3.0 - 2025-03-22

### What's Changed

* allow to add custom headers to otlp exporter by @cappuc in https://github.com/keepsuit/laravel-opentelemetry/pull/32

**Full Changelog**: https://github.com/keepsuit/laravel-opentelemetry/compare/1.2.0...1.3.0

## v1.2.0 - 2025-02-23

### What's Changed

* replaced deprecated `db.system` with `db.system.name`
* Support laravel 12
* Bump aglipanci/laravel-pint-action from 2.4 to 2.5 by @dependabot in https://github.com/keepsuit/laravel-opentelemetry/pull/30

**Full Changelog**: https://github.com/keepsuit/laravel-opentelemetry/compare/1.1.0...1.2.0

## v1.1.0 - 2025-01-16

### What's Changed

* Add support for timeout and maxRetries by @ilicmilan in https://github.com/keepsuit/laravel-opentelemetry/pull/28

### New Contributors

* @ilicmilan made their first contribution in https://github.com/keepsuit/laravel-opentelemetry/pull/28

**Full Changelog**: https://github.com/keepsuit/laravel-opentelemetry/compare/1.0.0...1.1.0

## v1.0.0 - 2024-10-06

### Breaking changes

This release contains a lot of breaking changes

* Compare your published config and update it accordingly.
* ENV variables prefix is changed from `OT` to `OTEL` and some env variables are changed.
* Tracer has been completely refactored, manual traces must been updated with the new methods.

`Tracer::start`, `Tracer::measure` and `Tracer::measureAsync` has been removed and replaced with:

```php
Tracer::newSpan('name')->start(); // same as old start
Tracer::newSpan('name')->measure(callback); // same as old measure
Tracer::newSpan('name')->setSpanKind(SpanKind::KIND_PRODUCER)->measure(callback); // same as old measureAsync









```
`Tracer::recordExceptionToSpan` has been removed and exception should be recorded directly to span: `$span->recordException($exception)`

`Tracer::setRootSpan($span)` has been removed, it was only used to share traceId with log context. This has been replaced with `Tracer::updateLogContext()`

##### Logging

This release introduce a custom log channel for laravel `otlp` that allows to collect laravel logs with OpenTelemetry.
This is the injected `otlp` channel:

```php
// config/logging.php
'channels' => [
    // injected channel config, you can override it adding an `otlp` channel in your config
    'otlp' => [
        'driver' => 'monolog',
        'handler' => \Keepsuit\LaravelOpenTelemetry\Support\OpenTelemetryMonologHandler::class,
        'level' => 'debug',
    ]
]









```
### What's Changed

* Refactoring tracer api by @cappuc in https://github.com/keepsuit/laravel-opentelemetry/pull/12
* Track http headers by @cappuc in https://github.com/keepsuit/laravel-opentelemetry/pull/13
* Improved http client tests by @cappuc in https://github.com/keepsuit/laravel-opentelemetry/pull/15
* Drop laravel 9 by @cappuc in https://github.com/keepsuit/laravel-opentelemetry/pull/16
* Auto queue instrumentation by @cappuc in https://github.com/keepsuit/laravel-opentelemetry/pull/14
* Follow OTEL env variables specifications by @cappuc in https://github.com/keepsuit/laravel-opentelemetry/pull/17
* Add support for logs by @cappuc in https://github.com/keepsuit/laravel-opentelemetry/pull/20

**Full Changelog**: https://github.com/keepsuit/laravel-opentelemetry/compare/0.4.0...1.0.0

## v0.4.1 - 2024-10-06

### What's changed

* Fix #27

**Full Changelog**: https://github.com/keepsuit/laravel-opentelemetry/compare/0.4.0...0.4.1

## v0.4.0 - 2024-01-05

### What's Changed

* Replaced deprecated trace attributes
* Increased phpstan level to 7
* Required stable versions of opentelemetry
* Bump actions/checkout from 3 to 4 by @dependabot in https://github.com/keepsuit/laravel-opentelemetry/pull/6
* Bump stefanzweifel/git-auto-commit-action from 4 to 5 by @dependabot in https://github.com/keepsuit/laravel-opentelemetry/pull/7
* Bump aglipanci/laravel-pint-action from 2.3.0 to 2.3.1 by @dependabot in https://github.com/keepsuit/laravel-opentelemetry/pull/8

**Full Changelog**: https://github.com/keepsuit/laravel-opentelemetry/compare/0.3.2...0.4.0

## v0.3.1 - 2023-07-10

### What's Changed

- Support for latest open-temetry sdk (which contains some namespace changes)

**Full Changelog**: https://github.com/keepsuit/laravel-opentelemetry/compare/0.3.0...0.3.1

## v0.3.0 - 2023-04-13

### What's Changed

- Changed some span names and attributes values to better respect specs
- Added `CacheInstrumentation` which records cache events
- Added `EventInstrumentation` which records dispatched events

**Full Changelog**: https://github.com/keepsuit/laravel-opentelemetry/compare/0.2.0...0.3.0

## v0.2.0 - 2023-03-02

- Removed deprecated `open-telemetry/sdk-contrib` and use `open-telemetry/exporter-*` packages

## v0.1.0 - 2023-01-20

- First release
