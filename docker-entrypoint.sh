#!/bin/bash

# Attendre que la base de données soit prête
echo "Waiting for database to be ready..."
if [ "$DB_CONNECTION" = "pgsql" ]; then
    while ! pg_isready -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME"; do
        echo "PostgreSQL not ready, waiting..."
        sleep 2
    done
else
    while ! mysqladmin ping -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" --silent; do
        echo "MySQL not ready, waiting..."
        sleep 2
    done
fi

echo "Database is ready!"

# Exécuter les migrations si nécessaire
php artisan migrate --force

# Générer la documentation Swagger
php artisan l5-swagger:generate

# Démarrer Apache
apache2-foreground