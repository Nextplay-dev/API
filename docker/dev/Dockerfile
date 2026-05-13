FROM ghcr.io/nextplay-dev/infra:laravel-latest

RUN apt-get update && apt-get install -y \
    nodejs \
    npm

RUN npm install --save-dev chokidar

RUN install-php-extensions xdebug

RUN echo "xdebug.mode=develop,debug,coverage" >> $PHP_INI_DIR/php.ini

COPY ./docker/dev/Caddyfile /etc/frankenphp/Caddyfile
COPY ./docker/dev/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY ./docker/dev/reverb.conf /etc/supervisor/conf.d/reverb.conf

RUN chmod +x /usr/local/bin/entrypoint.sh

COPY . /app

RUN composer install

ENTRYPOINT ["entrypoint.sh"]