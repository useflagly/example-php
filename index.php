<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/useflagly/sdk-php/src/Models.php';

use UseFlagly\UseFlaglyClient;
use UseFlagly\ValidateBody;
use UseFlagly\ReceiveMessage;

function loadDotEnv(string ...$paths): void
{
    foreach ($paths as $path) {
        if (!file_exists($path)) continue;
        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) continue;
            $idx = strpos($line, '=');
            if ($idx === false) continue;
            $key = trim(substr($line, 0, $idx));
            $val = trim(substr($line, $idx + 1));
            if ($key !== '' && getenv($key) === false) {
                putenv("$key=$val");
            }
        }
        return;
    }
}

loadDotEnv(__DIR__ . '/.env', __DIR__ . '/../.env');

$apiKey = getenv('FLAGLY_API_KEY') ?: '';
if ($apiKey === '') {
    fwrite(STDERR, "FLAGLY_API_KEY não definida\n");
    exit(1);
}

$identifier = getenv('FLAGLY_IDENTIFIER') ?: '';
if ($identifier === '') {
    fwrite(STDERR, "FLAGLY_IDENTIFIER não definida\n");
    exit(1);
}

$slug        = getenv('FLAGLY_SLUG')        ?: 'teste-1';
$environment = getenv('FLAGLY_ENVIRONMENT') ?: 'HML';

$client = new UseFlaglyClient(token: $apiKey);

// --- Health Check ---
$health = $client->healthCheck();
echo "Health: " . json_encode($health, JSON_PRETTY_PRINT) . "\n\n";

// --- Initialize ---
try {
    $initResult = $client->initialize(
        new ReceiveMessage(identifier: $identifier, slug: $slug),
        $environment
    );
    echo "Initialize: " . json_encode($initResult) . "\n\n";
} catch (\Throwable $e) {
    // initialize retorna um número (session id), não um JSON object
    echo "Initialize: ok\n\n";
}

// --- Result ---
$result = $client->getResult($identifier);
echo "Result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";

$data = $result['data'] ?? [];
if (empty($data)) {
    exit(0);
}

// --- Validar usando os slugs do resultado ---
foreach ($data as $flowSlug => $flowVal) {
    if (!is_array($flowVal)) continue; // ignora entradas não-objeto

    $flowResult = $client->validateFlow(
        $flowSlug,
        new ValidateBody(identifier: $identifier),
        $environment
    );
    echo "ValidateFlow ($flowSlug): " . json_encode($flowResult, JSON_PRETTY_PRINT) . "\n\n";

    foreach ($flowVal as $fpSlug => $fpVal) {
        if (!is_array($fpVal)) continue;

        $fpResult = $client->validateFlowPart(
            $fpSlug,
            new ValidateBody(identifier: $identifier),
            $environment
        );
        echo "ValidateFlowPart ($fpSlug): " . json_encode($fpResult, JSON_PRETTY_PRINT) . "\n\n";

        foreach (array_keys($fpVal) as $flagSlug) {
            $flagResult = $client->validateFlag(
                $flagSlug,
                new ValidateBody(identifier: $identifier),
                $environment
            );
            echo "ValidateFlag ($flagSlug): " . json_encode($flagResult, JSON_PRETTY_PRINT) . "\n\n";
        }
    }
}
