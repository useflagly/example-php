<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use UseFlagly\UseFlaglyClient;
use UseFlagly\ValidateBody;

$apiKey = getenv('FLAGLY_API_KEY');
if (!$apiKey) {
    fwrite(STDERR, "FLAGLY_API_KEY não definida\n");
    exit(1);
}

$client = new UseFlaglyClient(
    baseUrl: 'https://api.useflagly.com.br', // opcional, é o padrão
    token: $apiKey,
);

// --- Health Check ---
$health = $client->healthCheck();
echo "Health: " . json_encode($health, JSON_PRETTY_PRINT) . "\n\n";

// --- Validar Feature Flag ---
$flagResult = $client->validateFlag(
    'minha-feature',
    new ValidateBody(identifier: 'user-123', context: ['plano' => 'premium', 'pais' => 'BR']),
    'production',
);
echo "Flag resultado: " . json_encode($flagResult, JSON_PRETTY_PRINT) . "\n\n";

// --- Validar Flow ---
$flowResult = $client->validateFlow(
    'meu-fluxo',
    new ValidateBody(identifier: 'user-123'),
    'production',
);
echo "Flow resultado: " . json_encode($flowResult, JSON_PRETTY_PRINT) . "\n\n";

// --- Validar Cenário ---
$scenarioResult = $client->validateScenario(
    'meu-cenario',
    new ValidateBody(identifier: 'user-123', context: ['plano' => 'free']),
);
echo "Cenário resultado: " . json_encode($scenarioResult, JSON_PRETTY_PRINT) . "\n\n";

// --- Validar parte de Flow ---
$flowPartResult = $client->validateFlowPart(
    'meu-fluxo-parte',
    new ValidateBody(identifier: 'user-123'),
);
echo "FlowPart resultado: " . json_encode($flowPartResult, JSON_PRETTY_PRINT) . "\n\n";

// --- Cache do flag ---
$cached = $client->getFlagCache('minha-feature', 'user-123');
echo "Cache do flag: " . json_encode($cached, JSON_PRETTY_PRINT) . "\n";
