FROM dunglas/frankenphp:php8.4 AS frankenphp_upstream

FROM frankenphp_upstream AS frankenphp_base

WORKDIR /app

# user/group
ARG UID=1000
ARG GID=1000
ARG DOCKER_GID=1001
ARG APP_USER=symfony

ENV UID=${UID} \
    GID=${GID} \
    DOCKER_GID=${DOCKER_GID} \
    APP_USER=${APP_USER} \
    APP_ENV=dev \
    XDEBUG_MODE=off

# Install sudo to allow our entrypoint to set ownership on docker.sock
# Create a symfony user to avoid working as root
RUN addgroup --gid ${GID} ${APP_USER} \
 && adduser --uid ${UID} --gid ${GID} --disabled-password --gecos "" ${APP_USER}

RUN chown -R ${APP_USER}:${APP_USER} /app

RUN mkdir /app/var \
    && chown -R ${APP_USER}:${APP_USER} /app/var

# allows startup even if running as non-root user
# otherwise caddy will e.g. try to create a certificate in /data/caddy which is not writable as non-root by default
RUN mkdir -p /data/caddy \
    && chown -R ${APP_USER}:${APP_USER} /data/caddy /config/caddy

# Install PHP extensions for Symfony + PostgreSQL + Redis
RUN install-php-extensions \
    @composer \
    pdo_pgsql \
    redis \
    opcache \
    intl \
    zip \
    amqp

# entrypoint
COPY --link --chmod=755 docker/dev/frankenphp/docker-entrypoint.sh /usr/local/bin/docker-entrypoint

# Caddyfile
COPY --link docker/dev/frankenphp/Caddyfile /etc/caddy/Caddyfile

HEALTHCHECK --start-period=60s CMD curl -f http://localhost:2019/metrics || exit 1


# Dev
FROM frankenphp_base AS frankenphp_dev

# Create a docker group and add our symfony user to it
# This is useful in systems where you are able to pass the right docker group id
# as it will allow testcontainers to use docker.sock
# otherwise it has no effect
RUN groupadd -g ${DOCKER_GID} docker && usermod -aG docker ${APP_USER}

# Configure PHP
COPY --link docker/dev/php.ini-dev /usr/local/etc/php/php.ini

ENV APP_ENV=dev XDEBUG_MODE=off

VOLUME /app/var/

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN set -eux; \
	install-php-extensions \
		xdebug \
	;

COPY --link docker/dev/frankenphp/conf.d/app.dev.ini $PHP_INI_DIR/conf.d/

ENTRYPOINT ["docker-entrypoint"]

USER ${APP_USER}

CMD [ "frankenphp", "run", "--config", "/etc/caddy/Caddyfile", "--watch" ]