FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libpq-dev \
    libssl-dev \
    librabbitmq-dev \
    libzstd-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_pgsql

RUN pecl install redis \
    && docker-php-ext-enable redis

RUN pecl install amqp \
    && docker-php-ext-enable amqp

RUN docker-php-ext-install intl

WORKDIR /app