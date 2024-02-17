FROM oven/bun:alpine AS packages

RUN apk upgrade; \
    apk add --no-cache bash; \
    addgroup -g 9999 app; \
    adduser -G app -u 9999 -D app; \
    mkdir /app; \
    chown app:app /app; \
    rm -rf /tmp/**; \
    rm -rf /var/cache/apk/**;

WORKDIR /app
USER app

COPY --chown=app:app package.json bun.lockb ./
COPY --chown=app:app .pro/ .pro/
RUN bun install --frozen-lockfile;


FROM node:20-alpine AS build

RUN apk upgrade; \
    addgroup -g 9999 app; \
    adduser -G app -u 9999 -D app; \
    mkdir /app; \
    chown app:app /app; \
    rm -rf /tmp/**; \
    rm -rf /var/cache/apk/**;

WORKDIR /app
USER app

COPY --chown=app:app . .
COPY --chown=app:app --from=packages /app/node_modules/ node_modules/

ARG environment=development
RUN mv .env.${environment} .env; \
    npm run generate;


FROM nginx:mainline-alpine AS final

LABEL org.opencontainers.image.title="Optimizely Demo (Panel)"
LABEL org.opencontainers.image.authors="xoxo@saifmahmud.name"

RUN apk upgrade; \
    apk add tini; \
    addgroup -g 9999 app; \
    adduser -G app -u 9999 -D app; \
    mkdir /app; \
    chown app:app /app; \
    rm -rf /tmp/**; \
    rm -rf /var/cache/apk/**;

COPY .nginx.conf /etc/nginx/nginx.conf

WORKDIR /app

COPY --chown=app:app --from=build /app/.output/public/ public/

ENTRYPOINT ["/sbin/tini", "--"]
CMD ["nginx", "-g", "daemon off;"]
