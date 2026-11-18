#!/bin/sh
set -eu
cd /var/www/html
if [ "${SKIP_COMPOSER_INSTALL:-0}" != "1" ] && [ -f composer.json ] && [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi
mkdir -p storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
exec docker-php-entrypoint "$@"
