# Multi-stage build for Render.com free tier.
# Stage 1: Node — install Frontend pin and compile Sass
# Stage 2: Composer — PHP dependencies
# Stage 3: Runtime — PHP built-in server (demo) on $PORT

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

FROM php:8.4-cli-bookworm
WORKDIR /app
RUN apt-get update && apt-get install -y --no-install-recommends \
      libzip-dev unzip \
    && docker-php-ext-install zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=vendor /app/vendor ./vendor
COPY --from=styles /build/node_modules/govuk-frontend ./node_modules/govuk-frontend
COPY --from=styles /build/dist ./dist
COPY . .

ENV APP_ENV=production \
    APP_DEBUG=false \
    DEMOS_ENABLED=true \
    LOG_CHANNEL=stderr

RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache \
    && php artisan package:discover --ansi || true \
    && chmod +x scripts/docker-entrypoint.sh

EXPOSE 10000
CMD ["./scripts/docker-entrypoint.sh"]
