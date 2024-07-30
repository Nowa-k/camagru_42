#!/bin/sh

# Créer le répertoire des uploads avec les permissions correctes
mkdir -p /var/www/html/uploads
chown -R www-data:www-data /var/www/html/uploads
chmod -R 755 /var/www/html/uploads

# Démarrer PHP-FPM
php-fpm
