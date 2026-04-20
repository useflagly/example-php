# example-php

Exemplo de uso do SDK PHP do [UseFlagly](https://useflagly.com.br).

## Pré-requisitos

- PHP 8.1+ e [Composer](https://getcomposer.org) (ou Docker)

## Configuração

Crie um arquivo `.env` (ou copie o `.env` da raiz do repositório de exemplos):

```env
FLAGLY_API_KEY=sua-api-key-aqui
FLAGLY_IDENTIFIER=seu-identifier
FLAGLY_SLUG=seu-slug
FLAGLY_ENVIRONMENT=HML
```

## Executar com Docker

```bash
docker build -t example-php .
docker run --rm --env-file .env example-php
```

## Executar localmente

```bash
composer install
php index.php
```

## O que o exemplo demonstra

1. **Health check** da API
2. **Initialize** — registra o identifier+slug e inicia a avaliação assíncrona
3. **getResult** — obtém a árvore de resultados com todos os slugs avaliados
4. Itera o resultado chamando **validateFlow**, **validateFlowPart** e **validateFlag** com os slugs reais

## SDK

```bash
composer require useflagly/sdk-php
```

```php
use UseFlagly\UseFlaglyClient;
use UseFlagly\ValidateBody;
use UseFlagly\ReceiveMessage;

$client = new UseFlaglyClient(token: 'SUA_API_KEY');

// 1. Inicializar
$client->initialize(new ReceiveMessage(identifier: 'user-123', slug: 'meu-slug'), 'HML');

// 2. Obter resultado
$result = $client->getResult('user-123');

// 3. Validar flags
$flag = $client->validateFlag(
    'meu-flag',
    new ValidateBody(identifier: 'user-123'),
    'HML',
);
```
