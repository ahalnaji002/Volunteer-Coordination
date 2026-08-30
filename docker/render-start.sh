#!/bin/sh
set -eu

render_port="${PORT:-10000}"

sed "s/\${PORT}/${render_port}/g" docker/ports.conf.template > /etc/apache2/ports.conf
sed "s/\${PORT}/${render_port}/g" docker/apache-vhost.conf.template > /etc/apache2/sites-available/000-default.conf

php artisan config:cache
php artisan view:cache

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force
fi

if [ "${RUN_SEEDERS:-false}" = "true" ]; then
    php artisan db:seed --force
fi

exec apache2-foreground
