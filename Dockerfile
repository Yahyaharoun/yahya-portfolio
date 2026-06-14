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
    supervisor

# Installer les extensions PHP (dont pgsql pour Supabase)
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Installer Node.js (pour build Vite/Inertia)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Configurer le répertoire de travail
WORKDIR /var/www/html

# Autoriser Composer à utiliser toute la mémoire disponible pendant l'installation
ENV COMPOSER_MEMORY_LIMIT=-1

# Copier les fichiers du projet
COPY . .

# Copier la configuration Nginx
COPY docker/nginx.conf /etc/nginx/sites-available/default

# Installer les dépendances PHP et Node
RUN composer install --no-interaction --no-progress --no-dev --optimize-autoloader
RUN npm install
RUN npm run build

# Ajuster les permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exposer le port de Render
EXPOSE 80

# Script de démarrage
RUN echo "#!/bin/sh\n" \
    "php artisan config:cache\n" \
    "php artisan route:cache\n" \
    "php artisan view:cache\n" \
    "php artisan migrate --force\n" \
    "service nginx start\n" \
    "php-fpm" > /start.sh
RUN chmod +x /start.sh

# Lancer Nginx et PHP-FPM
CMD ["/start.sh"]
