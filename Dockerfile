FROM modpreneur/apache-framework

MAINTAINER Jakub Fajkus <fajkus@modpreneur.com>

# Install node(npm)
RUN curl -sL https://deb.nodesource.com/setup_6.x | bash - \
        && apt-get install -y nodejs \
        && npm install -g less \
        && npm install -g webpack  --save-dev

# Install app
RUN rm -rf /var/app/*
ADD . /var/app

WORKDIR /var/app

RUN composer install --no-scripts --optimize-autoloader

#install js
RUN cd web/js \
    && npm install

EXPOSE 80

RUN chmod +x entrypoint.sh
ENTRYPOINT ["sh", "entrypoint.sh"]