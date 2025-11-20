FROM dunglas/frankenphp:php8.4

# user/group
ARG UID=1000
ARG GID=1000
ARG DOCKER_GID=1001
ARG APP_USER=symfony

# Install sudo to allow our entrypoint to set ownership on docker.sock
# Create a symfony user to avoid working as root
RUN addgroup --gid $GID $APP_USER \
 && adduser --uid $UID --gid $GID --disabled-password --gecos "" $APP_USER

# Create a docker group and add our symfony user to it
# This is useful in systems where you are able to pass the right docker group id
# as it will allow testcontainers to use docker.sock
# otherwise it has no effect
RUN groupadd -g ${DOCKER_GID} docker && usermod -aG docker $APP_USER

# allows startup even if running as non-root user
# otherwise caddy will e.g. try to create a certificate in /data/caddy which is not writable as non-root by default
RUN mkdir -p /data/caddy \
    && chown -R $APP_USER:$APP_USER /data/caddy

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


WORKDIR /app

RUN chown $APP_USER:$APP_USER /app

USER $APP_USER

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install dependencies
COPY back/composer.json back/composer.lock ./
COPY back/packages ./packages

RUN composer install --no-scripts --no-interaction --no-progress --optimize-autoloader

# Copy app code
COPY back ./

USER root
# Change app ownership
RUN chown $APP_USER:$APP_USER /app

# Clear + warmup cache dev
RUN php -d display_errors=1 -d error_reporting=E_ALL bin/console cache:clear --env=dev --no-warmup -vvv
RUN php bin/console cache:warmup --env=dev


# Copy Caddyfile to container
COPY docker/dev/frankenphp/Caddyfile /etc/caddy/Caddyfile

# Copy entrypoint script
COPY docker/dev/frankenphp/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

USER $APP_USER

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]