FROM nginx:stable-alpine

ARG USER_ID
ARG GROUP_ID

RUN delgroup dialout

RUN addgroup -g ${GROUP_ID} --system petshopGroup
RUN adduser -G petshopGroup --system -D -s /bin/sh -u ${USER_ID} petshopAdmin
RUN sed -i "s/user  nginx petshopGroup/user petshopAdmin petshopGroup/g" /etc/nginx/nginx.conf

