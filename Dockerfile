#
# monitoring
# Dockerfile used to build the production container
#

#### Step 1 : composer

FROM cylab/php:7.4 AS composer

COPY . /var/www/html
WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader

#### Step 2 : node

FROM node:16.15.0-alpine AS node

COPY . /var/www/html
WORKDIR /var/www/html
RUN npm --version && npm install && npm run prod

#### Step 3 : the actual docker image

FROM cylab/laravel74

# Custom logs : request time, laravel session
COPY ./docker/logs.conf /etc/apache2/conf-available/logs.conf
RUN a2enconf logs

# Increase upload limit
RUN sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 20M/g' /usr/local/etc/php/php.ini && \
    sed -i 's/post_max_size = 8M/post_max_size = 20M/g' /usr/local/etc/php/php.ini

COPY . /var/www/html
COPY ./docker/env.default /var/www/html/.env

COPY --from=composer /var/www/html/vendor /var/www/html/vendor

COPY --from=node /var/www/html/public/css /var/www/html/public/css
COPY --from=node /var/www/html/public/js /var/www/html/public/js
COPY --from=node /var/www/html/public/fonts /var/www/html/public/fonts

