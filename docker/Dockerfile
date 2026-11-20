FROM php:8.5-cli-bookworm
RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libicu-dev libonig-dev libxml2-dev \
    && docker-php-ext-install intl mbstring pdo_mysql \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
WORKDIR /app
