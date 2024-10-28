#!/bin/bash

if [ ! -d "/var/www/html/uploads" ]; then
  mkdir -p /var/www/html/uploads
  chown -R root:root /var/www/html/uploads
  chmod -R 755 /var/www/html/uploads
  echo "Dossier 'uploads' créé avec succès et permissions configurées."
else
  echo "Dossier 'uploads' existe déjà, saut de la création."
fi

composer require phpmailer/phpmailer google/apiclient

exec "$@"
