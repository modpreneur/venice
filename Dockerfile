FROM modpreneur/apache-framework:1.0.3

MAINTAINER Jakub Fajkus <fajkus@modpreneur.com>

# Install app
ADD . /var/app

WORKDIR /var/app

RUN composer install --no-scripts --optimize-autoloader

EXPOSE 80

RUN chmod +x entrypoint.sh
ENTRYPOINT ["sh", "entrypoint.sh"]