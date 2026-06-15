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
    exif \
    pcntl \
    sodium

# ─── 3. Limites PHP (upload vidéo) ───────────────────────────────────────────
COPY php.ini /usr/local/etc/php/conf.d/uploads.ini

# ─── 4. Répertoire de travail ────────────────────────────────────────────────
WORKDIR /var/www/html

# ─── 5. Copie du projet complet (vendor inclus dans git) ─────────────────────
COPY . .

# ─── 6. Configuration Nginx ──────────────────────────────────────────────────
COPY docker/nginx.conf /etc/nginx/sites-available/default

# ─── 7. Permissions ──────────────────────────────────────────────────────────
RUN mkdir -p /var/www/html/storage/app/public \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/testing \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ─── 8. Port et démarrage ────────────────────────────────────────────────────
EXPOSE 80

RUN printf '#!/bin/sh\nphp artisan storage:link --force\nphp artisan config:cache\nphp artisan route:cache\nphp artisan view:cache\nphp artisan migrate --force\nservice nginx start\nexec php-fpm\n' > /start.sh && chmod +x /start.sh

CMD ["/start.sh"]
