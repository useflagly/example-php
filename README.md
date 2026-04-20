# example-php

Exemplo de uso do SDK PHP do [UseFlagly](https://useflagly.com.br).

## Pré-requisitos

- PHP 8.1+
- [Composer](https://getcomposer.org)
- Extensão `curl` habilitada

## Instalação

```bash
composer install
```

## Configuração

```bash
export FLAGLY_API_KEY="sua-api-key-aqui"
```

## Executar

```bash
php index.php
```

## O que o exemplo demonstra

- Health check da API
- Validar um **Feature Flag** com identificador e contexto
- Validar um **Flow**
- Validar um **Cenário**
- Validar uma **parte de Flow**
- Obter o cache de um flag

## Instalação do SDK

```bash
composer require useflagly/sdk-php
```

### Uso básico

```php
use UseFlagly\UseFlaglyClient;
use UseFlagly\ValidateBody;

$client = new UseFlaglyClient(token: 'SUA_API_KEY');

$result = $client->validateFlag(
    'meu-flag',
    new ValidateBody(identifier: 'user-123', context: ['plano' => 'premium']),
    'production',
);
```
