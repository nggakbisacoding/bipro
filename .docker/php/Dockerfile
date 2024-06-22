FROM php:8.2-fpm-alpine3.18

LABEL maintainer="Agung Kurniawan Eka S <agungkes95@gmail.com>"

# Install apk packages we want
RUN apk add -Uuv \
    bash freetype-dev libjpeg-turbo-dev libzip-dev \
    libpng-dev postgresql-dev curl-dev supervisor \
    && rm -rf /var/cache/apk/*

# # Install wait-for-it
RUN curl https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh > /opt/wait-for-it.sh \
    && chmod +x /opt/wait-for-it.sh \
    && ln -s /opt/wait-for-it.sh /usr/bin/wait-for-it

# Install PHP extensions
ENV PHPREDIS_VERSION 5.3.7
COPY .docker/php/install-extensions.sh /opt/install-extensions.sh
RUN /opt/install-extensions.sh
