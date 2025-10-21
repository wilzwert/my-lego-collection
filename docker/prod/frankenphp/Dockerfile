# Stage 1 : build PHP deps
FROM dunglas/frankenphp:latest-php8.4 AS build

RUN install-php-extensions \
    pdo_pgsql \
    redis \
    opcache \
    intl \
    zip

COPY composer.json composer.lock /app/
WORKDIR /app
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

COPY back/ /app

RUN php bin/console cache:clear --env=prod && \
    php bin/console cache:warmup --env=prod

# Stage 2 : image finale
FROM dunglas/frankenphp:php8.4 AS prod

RUN install-php-extensions \
    pdo_pgsql \
    redis \
    opcache \
    intl \
    zip

COPY docker/php.ini /usr/local/etc/php/php.ini
COPY docker/frankenphp/Caddyfile /etc/caddy/Caddyfile

WORKDIR /app
COPY --from=build /app /app

EXPOSE 80 443
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile", "--adapter", "caddyfile"]
