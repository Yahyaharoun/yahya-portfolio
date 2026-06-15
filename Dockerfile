FROM php:8.3-fpm

# ─── 1. Dépendances système ───────────────────────────────────────────────────
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    libgmp-dev \
    libsodium-dev \
    supervisor \
    && rm -rf /var/lib/apt/lists/*

# ─── 2. Extensions PHP ───────────────────────────────────────────────────────
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    zip \
    gd \
    bcmath \
    xml \
    exif \
    pcntl \
    sodium

# ─── 3. Limites PHP (upload vidéo) ───────────────────────────────────────────
COPY php.ini /usr/local/etc/php/conf.d/uploads.ini

# ─── 4. Composer ─────────────────────────────────────────────────────────────
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ─── 5. Répertoire de travail ────────────────────────────────────────────────
WORKDIR /var/www/html

# ─── 6. Copie composer.json en premier (cache Docker optimal) ────────────────
COPY composer.json composer.lock ./

# ─── 7. Installation des dépendances PHP ─────────────────────────────────────
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1
RUN composer install \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-dev \
    --no-scripts \
    && composer clear-cache

# ─── 8. Copie du projet complet ──────────────────────────────────────────────
COPY . .

# ─── 9. Génération autoloader final ──────────────────────────────────────────
RUN composer dump-autoload --optimize --no-dev --no-interaction

# ─── 10. Configuration Nginx ─────────────────────────────────────────────────
COPY docker/nginx.conf /etc/nginx/sites-available/default

# ─── 11. Permissions ─────────────────────────────────────────────────────────
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ─── 12. Script de démarrage ─────────────────────────────────────────────────
EXPOSE 80

RUN printf '#!/bin/sh\n\
php artisan storage:link --force\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
php artisan migrate --force\n\
service nginx start\n\
exec php-fpm\n' > /start.sh && chmod +x /start.sh

CMD ["/start.sh"]
