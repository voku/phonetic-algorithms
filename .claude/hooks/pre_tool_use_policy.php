<?php

declare(strict_types=1);

use voku\AgentLoop\AgentGuidance\AgentDisciplineHook;

$repositoryRoot = dirname(__DIR__, 2);
$autoload = $repositoryRoot . '/vendor/autoload.php';
$sourceRoot = $repositoryRoot . '/src';
if (is_file($autoload)) {
    require $autoload;
} elseif (is_dir($sourceRoot)) {
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
} else {
    fwrite(STDERR, "agent-loop hook runtime is unavailable.\n");
    exit(1);
}

$rawPayload = stream_get_contents(STDIN, 1_048_577);
if (!is_string($rawPayload)) {
    fwrite(STDERR, "Unable to read hook payload.\n");
    exit(1);
}

try {
    echo json_encode(
        (new AgentDisciplineHook($repositoryRoot))->preToolUseOutput($rawPayload),
        JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES,
    ) . "\n";
} catch (Throwable $throwable) {
    fwrite(STDERR, 'agent-loop Claude PreToolUse hook failed: ' . $throwable->getMessage() . "\n");
    exit(1);
}
