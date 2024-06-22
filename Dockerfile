#
# PHP Dependencies
#
FROM composer:2 as vendor
COPY database/ database/
COPY composer.json composer.json
COPY composer.lock composer.lock
RUN composer config --global --auth http-basic.kahasolusi.repo.repman.io token af63ffe944af2916f08d34b21694a2e2a6d7ea0ccf94def9fc0c6b4c45013a43
RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

#
# Frontend
#
FROM node:20.8.1-alpine3.18 as frontend
RUN mkdir -p /app/public

COPY . /app/
COPY resources/ /app/resources/

WORKDIR /app

ENV PNPM_HOME="/pnpm"
ENV PATH="$PNPM_HOME:$PATH"
RUN corepack enable
RUN --mount=type=cache,id=pnpm,target=/pnpm/store pnpm install --frozen-lockfile
RUN pnpm build

#
# Application
#
FROM agungkes/php8.2-base

LABEL maintainer="Agung Kurniawan Eka S <agungkes95@gmail.com>"

WORKDIR /var/www/html

COPY . /var/www/html
COPY --from=vendor /app/vendor/ /var/www/html/vendor/
COPY --from=frontend /app/public/ /var/www/html/public/
COPY --from=frontend /app/public/build/ /var/www/html/public/build/
COPY --from=frontend /app/public/build/assets/ /var/www/html/public/build/assets/

RUN php artisan storage:link

RUN mkdir -p /var/www/html/storage/logs
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html/storage
RUN chmod -R 755 /var/www/html/storage/logs