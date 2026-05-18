FROM php:8.3-fpm-alpine

# Dépendances système nécessaires aux extensions PHP utilisées par Laravel + PostgreSQL.
RUN apk add --no-cache \
        bash \
        git \
        unzip \
        icu-dev \
        libzip-dev \
        postgresql-dev \
        oniguruma-dev \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
        intl \
        zip \
        bcmath \
    && rm -rf /var/cache/apk/*

# Composer installé depuis l'image officielle.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Les sources sont montées via bind-mount en dev (cf. docker-compose.yml).
# composer install est exécuté à la première utilisation via le entrypoint.
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
