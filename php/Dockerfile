FROM php:8.2-fpm

ARG MSMTPRC_MAIL
ARG MSMTPRC_PASSWORD

# Mise à jour et installation des dépendances
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    msmtp \
    msmtp-mta \
    nano \
    telnet \
    ca-certificates \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql

# Configuration de msmtp
RUN echo "account         default" > /etc/msmtprc && \
    echo "host            smtp.office365.com" >> /etc/msmtprc && \
    echo "port            587" >> /etc/msmtprc && \
    echo "auth            on" >> /etc/msmtprc && \
    echo "tls             on" >> /etc/msmtprc && \
    echo "tls_starttls    on" >> /etc/msmtprc && \
    echo "from            placeholder@mail.com" >> /etc/msmtprc && \
    echo "user            placeholder@mail.com" >> /etc/msmtprc && \
    echo "password        placeholderpassword" >> /etc/msmtprc && \
    echo "logfile         /var/log/msmtp.log" >> /etc/msmtprc

RUN sed -i "s|from            placeholder@mail.com|from            ${MSMTPRC_MAIL}|g" /etc/msmtprc && \
    sed -i "s|user            placeholder@mail.com|user            ${MSMTPRC_MAIL}|g" /etc/msmtprc && \
    sed -i "s|password        placeholderpassword|password        ${MSMTPRC_PASSWORD}|g" /etc/msmtprc

RUN chmod 755 /etc/msmtprc

RUN sed -i "/sendmail_path =/c\sendmail_path = /usr/bin/msmtp -t" /usr/local/etc/php/php.ini-development
RUN sed -i "/sendmail_path =/c\sendmail_path = /usr/bin/msmtp -t" /usr/local/etc/php/php.ini-production

RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

# Script d'initialisation pour créer le répertoire des uploads avec les bonnes permissions
COPY init.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/init.sh

# Commande par défaut
CMD ["/usr/local/bin/init.sh", "php-fpm"]

EXPOSE 1025
