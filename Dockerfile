# Utiliser l'image PHP 8.3 FPM
FROM php:8.3-fpm

# Installer Nginx et TOUTES les dépendances système nécessaires
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libgmp-dev \
    libsodium-dev \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    supervisor \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installer TOUTES les extensions PHP requises (dont sodium pour web-push/JWT)
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    sodium

# Configurer les limites PHP pour l'upload de vidéos
COPY php.ini /usr/local/etc/php/conf.d/uploads.ini

# Installer Composer depuis l'image officielle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurer le répertoire de travail
WORKDIR /var/www/html

# Copier UNIQUEMENT les fichiers composer en premier (cache Docker optimal)
COPY composer.json composer.lock ./

# Installer les dépendances PHP SANS copier tout le projet (pour le cache Docker)
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-autoloader

# Copier le reste des fichiers du projet
COPY . .

# Générer l'autoloader après avoir copié le code source
RUN composer dump-autoload --optimize --no-dev

# Copier la configuration Nginx
COPY docker/nginx.conf /etc/nginx/sites-available/default

# Ajuster les permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exposer le port de Render
EXPOSE 80

# Script de démarrage (corrigé avec printf pour éviter les problèmes de syntaxe shell)
RUN printf '#!/bin/sh\n\
php artisan storage:link --force\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
php artisan migrate --force\n\
service nginx start\n\
php-fpm\n' > /start.sh
RUN chmod +x /start.sh

# Lancer Nginx et PHP-FPM
CMD ["/start.sh"]
