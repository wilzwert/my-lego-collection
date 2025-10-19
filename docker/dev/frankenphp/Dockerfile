# Dockerfile.dev
FROM dunglas/frankenphp:php8.4

# Arguments pour user/group
ARG UID=1000
ARG GID=1000

# Crée un utilisateur pour éviter de travailler en root
RUN addgroup --gid $GID symfony \
 && adduser --uid $UID --gid $GID --disabled-password --gecos "" symfony
 
# allows startup even if running as non-root user
# otherwise caddy will e.g. try to create a certificate in /data/caddy which is not writable as non-root by default
RUN mkdir -p /data/caddy \
    && chown -R symfony:symfony /data/caddy
 
# Installe les extensions nécessaires pour Symfony + PostgreSQL + Redis
RUN install-php-extensions \
    pdo_pgsql \
    redis \
    opcache \
    intl \
    zip
	
# Installe Xdebug
RUN install-php-extensions xdebug

# Configure PHP
COPY docker/dev/php.ini /usr/local/etc/php/php.ini
COPY docker/dev/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

WORKDIR /app

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Installer dépendances Symfony
COPY back/composer.json back/composer.lock ./
RUN composer install --no-scripts --no-interaction --no-progress

# Copier le code
COPY back/ ./

# Utiliser FrankenPHP comme serveur web
CMD php bin/console cache:clear --no-warmup && \
    exec frankenphp run --config /etc/caddy/Caddyfile --adapter caddyfile

# Changer ownership
RUN chown -R symfony:symfony /app

# Clear + warmup cache dev
RUN php bin/console cache:clear --env=dev --no-warmup \
 && php bin/console cache:warmup --env=dev

USER symfony

# Copier le Caddyfile dans le conteneur
COPY docker/dev/frankenphp/Caddyfile /etc/caddy/Caddyfile

# Lancer FrankenPHP via Caddy
CMD frankenphp run --config /etc/caddy/Caddyfile --adapter caddyfile