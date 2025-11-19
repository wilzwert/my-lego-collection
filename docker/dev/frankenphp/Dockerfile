FROM dunglas/frankenphp:php8.4

# user/group
ARG UID=1000
ARG GID=1000
ARG DOCKER_GID=999

# Create a user to avoir working as root
RUN addgroup --gid $GID symfony \
 && adduser --uid $UID --gid $GID --disabled-password --gecos "" symfony

# Create a docker group and add our user to it
RUN groupadd -g ${DOCKER_GID} docker && usermod -aG docker symfony
# RUN groupadd -for -g ${DOCKER_GID} docker \
#    && usermod -aG docker symfony
 
# allows startup even if running as non-root user
# otherwise caddy will e.g. try to create a certificate in /data/caddy which is not writable as non-root by default
RUN mkdir -p /data/caddy \
    && chown -R symfony:symfony /data/caddy
 
# Install PHP extensions for Symfony + PostgreSQL + Redis
RUN install-php-extensions \
    pdo_pgsql \
    redis \
    opcache \
    intl \
    zip
	
# Install Xdebug
RUN install-php-extensions xdebug

# Configure PHP
COPY docker/dev/php.ini /usr/local/etc/php/php.ini
# COPY docker/dev/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

WORKDIR /app

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install dependencies
COPY back/composer.json back/composer.lock ./
RUN composer install --no-scripts --no-interaction --no-progress

# Copy app code
COPY back/ ./

# Use FrankenPHP as web server
CMD php bin/console cache:clear --no-warmup && \
    exec frankenphp run --config /etc/caddy/Caddyfile --adapter caddyfile

# Change app ownership
RUN chown -R symfony:symfony /app

# Clear + warmup cache dev
RUN php bin/console cache:clear --env=dev --no-warmup \
 && php bin/console cache:warmup --env=dev

USER symfony

# Copy Caddyfile to container
COPY docker/dev/frankenphp/Caddyfile /etc/caddy/Caddyfile

# Launch FrankenPHP
CMD frankenphp run --config /etc/caddy/Caddyfile --adapter caddyfile