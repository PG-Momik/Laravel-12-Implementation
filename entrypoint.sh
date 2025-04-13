#!/bin/bash

set -e

if [ -f /var/www/html/artisan ]; then
    echo "Laravel is already installed. Starting the Laravel development server..."
    php artisan migrate
    php artisan serve --host=0.0.0.0
else
    echo "Laravel not found. Installing Laravel..."

    mkdir temp
    cd temp

    composer create-project --prefer-dist laravel/laravel .

    shopt -s dotglob
    mv * ../
    cd ..

    rm -rf temp

    echo "Laravel installation complete. Starting the Laravel development server..."
    php artisan migrate
    php artisan serve --host=0.0.0.0
fi
