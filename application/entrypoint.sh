#!/usr/bin/env sh

set -euxo pipefail;

php artisan config:cache;
php artisan event:cache;
php artisan route:cache;
php artisan view:cache;

exec php artisan "$@";
