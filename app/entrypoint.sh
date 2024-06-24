#!/bin/sh
set -e

# Exécutez composer install si composer.json existe
if [ -f "composer.json" ]; then
    composer install
fi

php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:schema:create


# Démarrez PHP-FPM
php-fpm