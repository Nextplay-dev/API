FROM ghcr.io/nextplay-dev/infra:laravel-latest

COPY ./docker/prod/Caddyfile /etc/frankenphp/Caddyfile
COPY ./docker/prod/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY ./docker/prod/worker.conf /etc/supervisor/conf.d/worker.conf
COPY ./docker/prod/schedule.conf /etc/supervisor/conf.d/schedule.conf
COPY ./docker/prod/reverb.conf /etc/supervisor/conf.d/reverb.conf
COPY ./docker/prod/schedule_entrypoint.sh /usr/local/bin/schedule_entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/schedule_entrypoint.sh

COPY . /app

RUN composer install

ENTRYPOINT ["entrypoint.sh"]
