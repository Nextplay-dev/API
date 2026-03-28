FROM ghcr.io/nextplay-dev/infra:laravel-latest

COPY . /app

RUN composer install

ENTRYPOINT ["entrypoint.sh"]
