FROM composer:2 AS deps

WORKDIR /app
COPY composer.json ./
RUN composer install --no-dev --optimize-autoloader --no-interaction && \
    sed -i "s/'Authorization: Bearer ' . \$this->token/'apiKey: ' . \$this->token/" \
    /app/vendor/useflagly/sdk-php/src/UseFlaglyClient.php

FROM php:8.3-cli-alpine

RUN docker-php-ext-install curl 2>/dev/null || true

WORKDIR /app
COPY --from=deps /app/vendor ./vendor
COPY . .

CMD ["php", "index.php"]
