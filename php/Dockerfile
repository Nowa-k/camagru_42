FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    nano \
    telnet \
    zip \
    unzip \
    ca-certificates \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

EXPOSE 1025

ENTRYPOINT ["/entrypoint.sh"]

CMD ["php-fpm"]