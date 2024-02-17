ARG environment=development

FROM php:8.3-cli-alpine AS first-base

RUN apk upgrade; \
    apk add --no-cache curl tini; \
    addgroup -g 9999 app; \
    adduser -G app -u 9999 -D app; \
    mkdir /app; \
    chown app:app /app; \
    rm -rf /tmp/**; \
    rm -rf /var/cache/apk/**;

COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/bin

ARG environment=development

RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini-staging; \
    cp "/usr/local/etc/php/php.ini-${environment}" /usr/local/etc/php/php.ini;

RUN IPE_GD_WITHOUTAVIF=1 install-php-extensions bcmath bz2 \
    gd igbinary intl lzf msgpack pcntl \
    pdo_mysql sockets openswoole uv xsl zip zstd redis;

RUN sed -i 's/^variables_order = "GPCS"$/variables_order = "EGPCS"/g' /usr/local/etc/php/php.ini; \
    sed -i 's/^memory_limit = 128M$/memory_limit = 256M/g' /usr/local/etc/php/php.ini; \
    sed -i 's/^post_max_size = 8M$/post_max_size = 192M/g' /usr/local/etc/php/php.ini; \
    sed -i 's/^upload_max_filesize = 2M$/upload_max_filesize = 128M/g' /usr/local/etc/php/php.ini; \
    sed -i 's/^;opcache.enable=1$/opcache.enable=1/g' /usr/local/etc/php/php.ini; \
    sed -i 's/^;opcache.enable_cli=0$/opcache.enable_cli=1/g' /usr/local/etc/php/php.ini;


FROM first-base AS extensions-development

RUN install-php-extensions excimer xdebug;


FROM first-base AS extensions-staging

RUN install-php-extensions excimer opcache;


FROM first-base AS extensions-production

RUN install-php-extensions opcache;


FROM extensions-${environment} AS second-base

COPY --from=composer:latest /usr/bin/composer /usr/bin

USER app
WORKDIR /app

COPY --chown=app:app composer.json composer.lock ./


FROM second-base AS packages-development

RUN composer install --ansi --no-scripts --no-progress --no-cache;


FROM second-base AS packages-staging

RUN composer install --optimize-autoloader --ansi --no-scripts --no-progress --no-cache;


FROM second-base AS packages-production

RUN composer install --no-dev --optimize-autoloader --ansi --no-scripts --no-progress --no-cache;


FROM packages-${environment} AS third-base

COPY --chown=app:app . .

RUN composer run post-autoload-dump --ansi


FROM third-base AS home-run

LABEL org.opencontainers.image.title="Optimizely Demo (Application)"
LABEL org.opencontainers.image.authors="xoxo@saifmahmud.name"

RUN php artisan config:clear; \
    php artisan event:clear; \
    php artisan route:clear; \
    php artisan structure-scouts:clear; \
    php artisan view:clear;

STOPSIGNAL SIGINT

ENTRYPOINT ["/sbin/tini", "--", "/bin/sh", "/app/entrypoint.sh"]
CMD ["tinker"]
