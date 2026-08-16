<?php

declare(strict_types=1);

use voku\AgentLoop\AgentGuidance\AgentDisciplineHook;

$repositoryRoot = dirname(__DIR__, 2);
$rootAutoload = $repositoryRoot . '/vendor/autoload.php';
$autoloadCandidates = [];
if (is_dir($repositoryRoot . '/vendor/voku/agent-loop')) {
    $autoloadCandidates[] = $rootAutoload;
}
$isolatedAutoloads = glob($repositoryRoot . '/tools/*/vendor/autoload.php');
if (is_array($isolatedAutoloads)) {
    sort($isolatedAutoloads, SORT_STRING);
    foreach ($isolatedAutoloads as $isolatedAutoload) {
        $autoloadCandidates[] = $isolatedAutoload;
    }
}
if (!in_array($rootAutoload, $autoloadCandidates, true)) {
    $autoloadCandidates[] = $rootAutoload;
}

foreach ($autoloadCandidates as $autoload) {
    if (!is_file($autoload)) {
        continue;
    }

    require_once $autoload;
    if (class_exists(AgentDisciplineHook::class)) {
        break;
    }
}

$sourceRoot = $repositoryRoot . '/src';
if (!class_exists(AgentDisciplineHook::class) && is_dir($sourceRoot)) {
    // Running from a checkout with no installed dependencies. Register the
    // package's own PSR-4 root rather than requiring one class by hand: a
    // hand-maintained list silently breaks the hook the moment the hook gains
    // a collaborator, and the hook is what injects context into every session.
    spl_autoload_register(static function (string $class) use ($sourceRoot): void {
        $prefix = 'voku\\AgentLoop\\';
        if (!str_starts_with($class, $prefix)) {
            return;
        }
        $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
        $file = $sourceRoot . '/' . $relative . '.php';
        if (is_file($file)) {
            require $file;
        }
    });
}

if (!class_exists(AgentDisciplineHook::class)) {
    fwrite(STDERR, "agent-loop hook runtime is unavailable.\n");
    exit(1);
}

$event = 'SessionStart';
foreach ($argv as $argument) {
    if (str_starts_with($argument, '--event=')) {
        $event = substr($argument, strlen('--event='));
    }
}

$rawPayload = stream_get_contents(STDIN, 1_048_577);
if (!is_string($rawPayload)) {
    fwrite(STDERR, "Unable to read hook payload.\n");
    exit(1);
}

try {
    echo json_encode(
        (new AgentDisciplineHook($repositoryRoot))->claudeContextOutput($event, $rawPayload),
        JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES,
    ) . "\n";
} catch (Throwable $throwable) {
    fwrite(STDERR, 'agent-loop Claude context hook failed: ' . $throwable->getMessage() . "\n");
    exit(1);
}
