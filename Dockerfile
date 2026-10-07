# ==============================================================================
# Dockerfile - Sistem Manajemen Inventaris & Aset Kantor (Laravel 13)
# ==============================================================================
FROM php:8.3-fpm-alpine

# Label Metadata
LABEL maintainer="Tim Pengembang Sistem Inventaris"
LABEL description="Container Laravel 13 untuk Sistem Inventaris & Aset Kantor"

# Set working directory
WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    curl \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    oniguruma-dev \
    netcat-openbsd \
    shadow \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        opcache

# Configure PHP production / performance settings
RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable_cli=1'; \
    echo 'upload_max_filesize=64M'; \
    echo 'post_max_size=64M'; \
    echo 'memory_limit=256M'; \
} > /usr/local/etc/php/conf.d/custom-laravel.ini

# Install Composer binary from official image
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# Copy composer manifest first for Docker layer caching
COPY composer.json ./

# Install dependencies (fallback if packages are offline or ignore platform reqs)
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts \
    || composer install --no-dev --ignore-platform-reqs --no-scripts || true

# Copy application files
COPY . .

# Run composer autoload dump and discovery
RUN composer dump-autoload --optimize --no-interaction || true

# Prepare storage and cache directories with proper permissions
RUN mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && cp docker/entrypoint.sh /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

# Expose PHP-FPM FastCGI port
EXPOSE 9000

# Set entrypoint and default command
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
