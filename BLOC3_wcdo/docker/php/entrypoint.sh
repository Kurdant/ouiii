#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

# Installation des dépendances PHP si vendor/ absent (premier démarrage).
if [ ! -d vendor ] || [ ! -f vendor/autoload.php ]; then
    echo ">>> [entrypoint] composer install (premier démarrage)…"
    composer install --no-interaction --prefer-dist --no-progress
fi

# Copie .env si absent.
if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
    echo ">>> [entrypoint] .env créé depuis .env.example"
fi

# Génération APP_KEY si vide.
if [ -f .env ] && ! grep -q "^APP_KEY=base64:" .env; then
    php artisan key:generate --force || true
fi

# Permissions storage/bootstrap pour PHP-FPM (utilisateur www-data).
mkdir -p storage/framework/{cache,sessions,testing,views} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R ug+rwX storage bootstrap/cache || true

# Attente active de PostgreSQL (jusqu'à 30s) puis migrations + seeders.
echo ">>> [entrypoint] attente de PostgreSQL…"
for i in $(seq 1 30); do
    if php -r "exit(@fsockopen(getenv('DB_HOST') ?: 'db', (int)(getenv('DB_PORT') ?: 5432)) ? 0 : 1);"; then
        echo ">>> [entrypoint] PostgreSQL est joignable."
        break
    fi
    sleep 1
done

echo ">>> [entrypoint] migrate + seed…"
php artisan migrate --force || true
php artisan db:seed --force || true

exec "$@"
