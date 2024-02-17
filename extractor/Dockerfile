FROM python:3-alpine AS final

RUN apk upgrade; \
    apk add tini gcc python3-dev libc-dev; \
    addgroup -g 9999 app; \
    adduser -G app -u 9999 -D app; \
    mkdir /app; \
    chown app:app /app; \
    rm -rf /tmp/**; \
    rm -rf /var/cache/apk/**;

WORKDIR /app
USER app

COPY --chown=app:app requirements.txt .
RUN pip install -r requirements.txt

USER root

RUN apk delete gcc python3-dev libc-dev; \
    rm -rf /tmp/**; \
    rm -rf /var/cache/apk/**;

USER app
COPY --chown=app:app . .

LABEL org.opencontainers.image.title="Optimizely Demo (Extractor)"
LABEL org.opencontainers.image.authors="xoxo@saifmahmud.name"

STOPSIGNAL SIGINT

ENTRYPOINT ["/sbin/tini", "--"]
CMD ["python", "-m", "uvicorn", "main:app", "--host", "0.0.0.0", "--port", "9000"]
