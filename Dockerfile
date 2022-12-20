FROM php:8.1-apache

<<<<<<< HEAD
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug
=======
WORKDIR /src/osekai

COPY . .
>>>>>>> d0e8b05 (Add Docker support)

RUN if command -v a2enmod >/dev/null 2>&1; then \
        a2enmod rewrite headers expires \
    ;fi

RUN docker-php-ext-install mysqli
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
RUN echo "ServerName 0.0.0.0" >> /etc/apache2/apache2.conf

<<<<<<< HEAD
WORKDIR /src/osekai

COPY ./docker ./docker

CMD ["bash", "./docker/docker_run.sh"]
=======
RUN cat $PHP_INI_DIR/php.ini

RUN cp -r ./404 /var/www/html/404 \
&& cp -r ./.git /var/www/html/.git \
&& cp -r ./admin /var/www/html/admin \
&& cp -r ./api /var/www/html/api \
&& cp -r ./authenticator /var/www/html/authenticator \
&& cp -r ./azelia /var/www/html/azelia \
&& cp -r ./badges /var/www/html/bages \
&& cp -r ./donate /var/www/html/donate \
&& cp -r ./global /var/www/html/global \
&& cp -r ./home /var/www/html/home \
&& cp -r ./medals /var/www/html/medals \
&& cp -r ./misc /var/www/html/misc \
&& cp -r ./omh /var/www/html/omh \
&& cp -r ./profiles /var/www/html/profiles \
&& cp -r ./rankings /var/www/html/rankings \
&& cp -r ./tools /var/www/html/tools \
&& cp -r ./.htaccess /var/www/html/.htaccess \
&& cp -r ./busted.html /var/www/html/busted.html \
&& cp -r ./expon.php /var/www/html/expon.php \
&& cp -r ./index.php /var/www/html/index.php \
&& cp -r ./maintenance.html /var/www/html/maintenance.html \
&& cp -r ./offline-application.html /var/www/html/offline-application.html \
&& cp -r ./offline-difficulties.html /var/www/html/offline-difficulties.html \
&& cp -r ./offline-issues.html /var/www/html/offline-issues.html \
&& cp -r ./offline-upgrade.html /var/www/html/offline-upgrade.html \
&& cp -r ./offline-work.html /var/www/html/offline-work.html \
&& cp -r ./proot.html /var/www/html/proot.html

CMD ["bash", "./cli_utils/docker_run.sh"]
>>>>>>> d0e8b05 (Add Docker support)
