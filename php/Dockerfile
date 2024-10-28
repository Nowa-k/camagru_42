# Utilisation de l'image PHP 8.2
FROM php:8.2-fpm

# Installation des dépendances système nécessaires
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

RUN mkdir -p /var/www/html/upload && \
    chown -R www-data:www-data /var/www/html/upload && \
    chmod -R 755 /var/www/html/upload

WORKDIR /var/www/html

RUN composer require phpmailer/phpmailer google/apiclient

EXPOSE 1025

CMD ["php-fpm"]