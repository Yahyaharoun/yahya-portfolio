# Utiliser l'image PHP 8.3 FPM
FROM php:8.3-fpm

# Installer Nginx et les dépendances système
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    supervisor

# Installer les extensions PHP (dont pgsql pour Supabase)
RUN docker-php-ext-install pdo pdo_pgsql pgsql exif pcntl bcmath gd zip

# Configurer les limites PHP pour l'upload de vidéos
COPY php.ini /usr/local/etc/php/conf.d/uploads.ini

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurer le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers du projet
COPY . .

# Installer les dépendances PHP
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copier la configuration Nginx
COPY docker/nginx.conf /etc/nginx/sites-available/default

# Ajuster les permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exposer le port de Render
EXPOSE 80

# Script de démarrage
RUN echo "#!/bin/sh\n" \
    "php artisan storage:link\n" \
    "php artisan config:cache\n" \
    "php artisan route:cache\n" \
    "php artisan view:cache\n" \
    "php artisan migrate --force\n" \
    "service nginx start\n" \
    "php-fpm" > /start.sh
RUN chmod +x /start.sh

# Lancer Nginx et PHP-FPM
CMD ["/start.sh"]
