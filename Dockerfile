FROM php:8.1-apache

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN if command -v a2enmod >/dev/null 2>&1; then \
        a2enmod rewrite headers expires \
    ;fi

RUN docker-php-ext-install mysqli
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
RUN echo "ServerName 0.0.0.0" >> /etc/apache2/apache2.conf

WORKDIR /src/osekai

COPY ./docker ./docker

CMD ["bash", "./docker/docker_run.sh"]