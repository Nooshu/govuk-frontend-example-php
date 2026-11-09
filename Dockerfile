# Multi-stage image for Google Cloud Run.
# Stage 1: Node — install the Frontend pin and compile Sass.
# Stage 2: Composer — production PHP dependencies.
# Stage 3: Caddy with the Brotli encoder (absent from the stock caddy binary).
# Stage 4: Runtime — Caddy + PHP-FPM on $PORT (Cloud Run default 8080).

FROM node:22-bookworm AS styles
WORKDIR /build
COPY package.json package-lock.json ./
RUN npm ci
COPY styles ./styles
COPY scripts ./scripts
RUN npm run build:styles

FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts --optimize-autoloader

FROM caddy:2.11.4-builder AS caddy
RUN xcaddy build \
      --output /usr/bin/caddy \
      --with github.com/ueffel/caddy-brotli@v1.7.0

FROM php:8.4-fpm-bookworm
WORKDIR /app

RUN apt-get update && apt-get install -y --no-install-recommends \
      libzip-dev unzip \
    && docker-php-ext-install zip opcache \
    && rm -rf /var/lib/apt/lists/*

COPY --from=caddy /usr/bin/caddy /usr/bin/caddy
COPY docker/Caddyfile /etc/caddy/Caddyfile
COPY docker/php.ini /usr/local/etc/php/conf.d/cloud-run.ini
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zzz-cloud-run.conf
COPY docker/entrypoint.sh /usr/local/bin/cloud-run-entrypoint.sh

COPY --from=vendor /app/vendor ./vendor
COPY --from=styles /build/node_modules/govuk-frontend ./node_modules/govuk-frontend
COPY --from=styles /build/dist ./dist
COPY . .

ENV APP_ENV=production \
    APP_DEBUG=false \
    DEMOS_ENABLED=true \
    LOG_CHANNEL=stderr \
    LOG_LEVEL=info \
    SESSION_DRIVER=cookie \
    SESSION_SECURE_COOKIE=true \
    PORT=8080

RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod +x /usr/local/bin/cloud-run-entrypoint.sh \
    && APP_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')" php artisan package:discover --ansi \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8080

HEALTHCHECK --interval=10s --timeout=3s --start-period=20s --retries=3 \
  CMD php -r '$p=getenv("PORT")?:8080; $b=@file_get_contents("http://127.0.0.1:".$p."/health"); exit($b==="ok"?0:1);'

ENTRYPOINT ["/usr/local/bin/cloud-run-entrypoint.sh"]
