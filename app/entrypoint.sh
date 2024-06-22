#!/bin/sh
set -e

# Exécutez composer install si composer.json existe
if [ -f "composer.json" ]; then
    composer install
fi

# Démarrez PHP-FPM
php-fpm