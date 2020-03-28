FROM php:7.4.2-apache

ENV DEBIAN_FRONTEND noninteractive
ENV DEBCONF_NOWARNINGS yes

RUN apt-get update && apt-get install -y \
    locales \
    libfreetype6-dev libjpeg62-turbo-dev libwebp-dev libpng-dev libmcrypt-dev libzip-dev libicu-dev libpq-dev \
  && docker-php-ext-configure gd \
    --with-freetype=/usr/include/ \
    --with-jpeg=/usr/include/ \
    --with-webp=/usr/include/ \
  && docker-php-ext-install gd \
  && docker-php-ext-install intl zip pgsql pdo_pgsql opcache \
  && pecl install mcrypt \
  && docker-php-ext-enable mcrypt \
  && pecl install xdebug \
  #&& docker-php-ext-enable xdebug \
  && a2enmod rewrite headers remoteip auth_digest

RUN sed -i -e "s/# \(en_US.UTF-8 UTF-8\)/\1/g" -e "s/# \(ja_JP.UTF-8 UTF-8\)/\1/g" /etc/locale.gen && locale-gen

ENV SHELL bash
ENV LANG en_US.UTF-8
ENV TZ Asia/Tokyo





RUN mkdir /var/log/apache2/fluent \
  && chown www-data:www-data /var/log/apache2/fluent

# jq install
RUN curl -qL -o /usr/local/bin/jq https://github.com/stedolan/jq/releases/download/jq-1.5/jq-linux64 && chmod +x /usr/local/bin/jq


# awscli install
RUN apt-get install -y python python-pip
RUN pip install awscli


# composer install
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


# cache vendor
WORKDIR /tmp
COPY composer.json .
COPY composer.lock .
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_PROCESS_TIMEOUT 3000
RUN cat composer.json | jq -r 'select(.repositories != null) | .repositories[].url' | xargs mkdir -p
RUN composer install --no-scripts --no-autoloader --working-dir .


# entrypoint
WORKDIR /usr/local/bin
COPY --chown=www-data:www-data .continuous/integration/docker/app/entrypoint.sh .
RUN chmod +x entrypoint.sh
ENV PATH $PATH:/usr/local/bin
ENTRYPOINT ["entrypoint.sh"]


# copy application files
RUN mkdir /usr/src/app
WORKDIR /usr/src/app
COPY --chown=www-data:www-data . .
COPY .continuous/integration/docker/app/php.ini $PHP_INI_DIR/conf.d
COPY .continuous/integration/docker/app/opcache.ini $PHP_INI_DIR/conf.d
COPY .continuous/integration/docker/app/sites-enabled $APACHE_CONFDIR/sites-enabled

RUN chmod -R 2775 storage bootstrap/cache

# vendor move cache from /tmp/vendor
RUN mv -n /tmp/vendor ./ \
    && composer dump-autoload --optimize --classmap-authoritative


CMD ["apache2-foreground"]
