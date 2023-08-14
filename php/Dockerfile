FROM php:8.2-fpm-alpine

ARG USER_ID
ARG GROUP_ID

RUN delgroup dialout
RUN addgroup -g ${GROUP_ID} --system petshopGroup
RUN adduser -G petshopGroup --system -D -s /bin/sh -u ${USER_ID} petshopAdmin

RUN sed -i "s/user = www-data/user = petshopAdmin/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/group = www-data/group = petshopGroup/g" /usr/local/etc/php-fpm.d/www.conf

RUN docker-php-ext-install pdo pdo_mysql

USER petshopAdmin
