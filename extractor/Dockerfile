FROM python:3.12-alpine AS build

RUN apk upgrade; \
    apk add gcc python3-dev libc-dev; \
    addgroup -g 9999 app; \
    adduser -G app -u 9999 -D app; \
    mkdir /wheels; \
    chown app:app /wheels; \
    rm -rf /tmp/**; \
    rm -rf /var/cache/apk/**;

WORKDIR /wheels
USER app

COPY --chown=app:app requirements.txt .

RUN pip wheel -r requirements.txt; \
    rm requirements.txt;


FROM python:3.12-alpine AS final

LABEL org.opencontainers.image.title="Optimizely Demo (Extractor)"
LABEL org.opencontainers.image.authors="xoxo@saifmahmud.name"

RUN apk upgrade; \
    apk add tini; \
    addgroup -g 9999 app; \
    adduser -G app -u 9999 -D app; \
    mkdir /app; \
    chown app:app /app; \
    rm -rf /tmp/**; \
    rm -rf /var/cache/apk/**;

WORKDIR /app
USER app

COPY --chown=app:app . .

RUN --mount=type=bind,from=build,source=/wheels,target=/wheels \
    pip install --no-cache-dir -r requirements.txt -f /wheels;

STOPSIGNAL SIGINT

ENTRYPOINT ["/sbin/tini", "--", "python"]
CMD ["-m", "uvicorn", "main:app", "--host", "0.0.0.0", "--port", "9000"]
