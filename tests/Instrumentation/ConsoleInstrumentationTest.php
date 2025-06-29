<?php

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Keepsuit\LaravelOpenTelemetry\Instrumentation\ConsoleInstrumentation;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;

test('trace console command', function () {
    registerInstrumentation(ConsoleInstrumentation::class);

    simulateTestConsoleCommand();

    $spans = getRecordedSpans();

    expect($spans)->toHaveCount(1);

    $consoleSpan = $spans->first();

    expect($consoleSpan)
        ->toBeInstanceOf(\OpenTelemetry\SDK\Trace\ImmutableSpan::class)
        ->getName()->toBe('test:command')
        ->getStatus()->getCode()->toBe(\OpenTelemetry\API\Trace\StatusCode::STATUS_OK);
});

test('trace console command with failing status', function () {
    registerInstrumentation(ConsoleInstrumentation::class);

    simulateTestConsoleCommand(exitCode: 1);

    $spans = getRecordedSpans();

    expect($spans)->toHaveCount(1);

    $consoleSpan = $spans->first();

    expect($consoleSpan)
        ->toBeInstanceOf(\OpenTelemetry\SDK\Trace\ImmutableSpan::class)
        ->getName()->toBe('test:command')
        ->getStatus()->getCode()->toBe(\OpenTelemetry\API\Trace\StatusCode::STATUS_ERROR);
});

test('exclude commands from tracing', function () {
    registerInstrumentation(ConsoleInstrumentation::class, [
        'excluded' => ['test:command'],
    ]);

    simulateTestConsoleCommand();

    $spans = getRecordedSpans();

    expect($spans)->toHaveCount(0);
});

test('exclude commands with class from tracing', function () {
    registerInstrumentation(ConsoleInstrumentation::class, [
        'excluded' => [\Keepsuit\LaravelOpenTelemetry\Tests\Support\TestCommand::class],
    ]);

    simulateTestConsoleCommand();

    $spans = getRecordedSpans();

    expect($spans)->toHaveCount(0);
});

function simulateTestConsoleCommand(int $exitCode = 0): void
{
    app('events')->dispatch(new CommandStarting(
        $command = 'test:command',
        $input = new ArgvInput(['artisan', 'test:command', '--foo=bar']),
        $output = new BufferedOutput
    ));

    app('events')->dispatch(new CommandFinished(
        $command,
        $input,
        $output,
        exitCode: $exitCode
    ));
}
