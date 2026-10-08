FROM node:22-bookworm-slim AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY resources ./resources
COPY vite.config.js ./vite.config.js
COPY public ./public
RUN npm run build
RUN npm prune --omit=dev

FROM php:8.5-apache-bookworm AS runtime
RUN apt-get update && apt-get install -y --no-install-recommends \
    libfreetype6-dev libicu-dev libjpeg62-turbo-dev libpng-dev libpq-dev \
    libwebp-dev libzip-dev unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install bcmath exif gd intl pdo_pgsql zip \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . .
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --no-scripts --optimize-autoloader \
    && mkdir -p storage/app/public storage/app/private storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && php artisan package:discover --no-interaction \
    && chown -R www-data:www-data storage bootstrap/cache \
    && sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && printf 'ServerName localhost\n' > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername \
    && printf 'upload_max_filesize=12M\npost_max_size=128M\nmemory_limit=256M\nopcache.enable=1\n' > /usr/local/etc/php/conf.d/portfolio.ini
COPY --from=frontend /app/public/build ./public/build
COPY --from=frontend /app/bootstrap/ssr ./bootstrap/ssr
COPY --from=frontend /app/node_modules ./node_modules
COPY --from=frontend /usr/local/bin/node /usr/local/bin/node
COPY docker/start.sh /usr/local/bin/portfolio-start
RUN chmod +x /usr/local/bin/portfolio-start
EXPOSE 80
ENTRYPOINT ["portfolio-start"]
