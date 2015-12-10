FROM php:7-apache

MAINTAINER Jakub Fajkus <fajkus@modpreneur.com>

RUN apt-get update
RUN apt-get -y install \
    apt-utils \
    curl \
    git \
    libcurl4-openssl-dev \
    libpq-dev \
    libpq5 \
    zlib1g-dev \
    wget\
    libmcrypt-dev


RUN docker-php-ext-install curl json mbstring opcache pdo_mysql zip mcrypt

# Install apcu
RUN pecl install -o -f apcu-5.1.2 apcu_bc-beta \
    && rm -rf /tmp/pear \
    && echo "extension=apcu.so" > /usr/local/etc/php/conf.d/apcu.ini \
    && echo "extension=apc.so" >> /usr/local/etc/php/conf.d/apcu.ini


# prepare php and apache
RUN rm -rf /etc/apache2/sites-available/* /etc/apache2/sites-enabled/*

ENV APP_DOCUMENT_ROOT /var/app/web
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2

ADD docker/php.ini /usr/local/etc/php/
ADD docker/000-default.conf /etc/apache2/sites-available/000-default.conf

# Install node(npm)
RUN curl -sL https://deb.nodesource.com/setup_5.x | bash - \
    && apt-get install -y nodejs

## Phantomjs for frontend testing
#ADD docker/phantomjs /usr/local/bin/phantomjs
#
## Selenium for js test
#ADD docker/selenium-server-standalone-2.48.2.jar /opt/selenium-server-standalone.jar


WORKDIR /var/app


# Install composer
RUN curl -sS https://getcomposer.org/installer | php \
    && cp composer.phar /usr/bin/composer

# Install app
RUN rm -rf /var/app/*
ADD . /var/app


# Remove parameters.yml
RUN rm -rf /var/app/app/config/parameters.yml


# enable apache and mod rewrite
RUN a2ensite 000-default.conf \
    && a2enmod expires \
    && a2enmod rewrite \
    && service apache2 restart

RUN composer install --no-scripts --optimize-autoloader

#install js
RUN npm install -g jspm \
    && cd web/js \
    && jspm config registries.github.auth "dmxjZWs6NjgwOGM3MzVkZDkwMDZjNjBiOWRmM2RjYjc5MTI5OGUwMjkxNjgzZg==" \
    && jspm install \
    && nodejs ./scripts/buildBundles.js

EXPOSE 80

RUN chmod +x entrypoint.sh
ENTRYPOINT ["sh", "entrypoint.sh"]