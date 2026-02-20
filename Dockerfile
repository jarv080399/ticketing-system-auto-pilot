# ===========================================================================
# Multi-Stage Dockerfile: Laravel + Vue SPA
# ===========================================================================
# Stage 1: Node.js for frontend build
# ===========================================================================

# ==========================================================================
# STAGE 1: FRONTEND BUILD (Node.js)
# ==========================================================================
FROM node:20-alpine AS frontend-build

WORKDIR /app

# Copy package files for better layer caching
COPY package*.json ./

# Install dependencies
RUN npm ci --quiet

# Copy frontend source files
COPY resources/ ./resources/
COPY vite.config.js ./
COPY tailwind.config.js ./
COPY postcss.config.js ./

# Build the Vue/Vite frontend
RUN npm run build

# ===========================================================================
# STAGE 2: PHP RUNTIME (Production)
# ===========================================================================
FROM php:8.3-fpm-alpine AS runtime

# Arguments for user/group mapping
ARG PUID=1000
ARG PGID=1000

# Add extension installer helper
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# ---------------------------------------------------------------------------
# System Dependencies
# ---------------------------------------------------------------------------
RUN apk add --no-cache \
    bash curl gettext \
    icu-dev libzip-dev oniguruma-dev libxml2-dev \
    freetype-dev libjpeg-turbo-dev libpng-dev \
    nginx \
    shadow

# ---------------------------------------------------------------------------
# PHP Extensions
# ---------------------------------------------------------------------------
RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    autoconf g++ make

# Configure and install GD
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Install core PHP extensions
RUN docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    bcmath \
    intl \
    zip \
    mbstring \
    xml \
    simplexml \
    dom \
    opcache

# Install Redis
RUN pecl channel-update pecl.php.net \
    && printf "\n" | pecl install redis \
    && docker-php-ext-enable redis

# Install MSSQL Drivers via helper
RUN install-php-extensions pdo_sqlsrv sqlsrv

# Remove build dependencies
RUN apk del .build-deps

# ---------------------------------------------------------------------------
# Composer
# ---------------------------------------------------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ---------------------------------------------------------------------------
# User Configuration
# ---------------------------------------------------------------------------
RUN groupmod -g ${PGID} www-data \
    && usermod -u ${PUID} -g ${PGID} www-data

# ---------------------------------------------------------------------------
# PHP Configuration
# ---------------------------------------------------------------------------
RUN { \
    echo "opcache.enable=1"; \
    echo "opcache.validate_timestamps=1"; \
    echo "opcache.memory_consumption=128"; \
    echo "opcache.max_accelerated_files=10000"; \
    echo "memory_limit=256M"; \
    echo "upload_max_filesize=20M"; \
    echo "post_max_size=20M"; \
    echo "max_execution_time=30"; \
    echo "expose_php=Off"; \
    } > /usr/local/etc/php/conf.d/zz-custom.ini

# ---------------------------------------------------------------------------
# Nginx Configuration
# ---------------------------------------------------------------------------
RUN sed -i 's/user nginx;/user www-data;/' /etc/nginx/nginx.conf || true \
    && sed -i 's/^pid \/run\/nginx.pid;/pid \/tmp\/nginx.pid;/' /etc/nginx/nginx.conf \
    && sed -i '/http {/a \    client_body_temp_path /tmp/client_temp;\n    proxy_temp_path       /tmp/proxy_temp_path;\n    fastcgi_temp_path     /tmp/fastcgi_temp;\n    uwsgi_temp_path       /tmp/uwsgi_temp;\n    scgi_temp_path        /tmp/scgi_temp;' /etc/nginx/nginx.conf

# Point nginx to use configs from /tmp (writable location)
RUN sed -i 's|include /etc/nginx/http.d/*.conf;|include /tmp/nginx/conf.d/*.conf;|' /etc/nginx/nginx.conf

# Create necessary directories
RUN mkdir -p /var/lib/nginx/tmp /var/run/nginx /var/run/php-fpm /tmp/nginx/conf.d \
    && chown -R www-data:www-data /var/lib/nginx /var/log/nginx /var/run/nginx /var/run/php-fpm /tmp/nginx

# ---------------------------------------------------------------------------
# Application Setup
# ---------------------------------------------------------------------------
WORKDIR /var/www/html

# Copy application files (excluding frontend source)
COPY --chown=www-data:www-data app/ ./app/
COPY --chown=www-data:www-data bootstrap/ ./bootstrap/
COPY --chown=www-data:www-data config/ ./config/
COPY --chown=www-data:www-data database/ ./database/
COPY --chown=www-data:www-data public/ ./public/
COPY --chown=www-data:www-data resources/views/ ./resources/views/
COPY --chown=www-data:www-data routes/ ./routes/
COPY --chown=www-data:www-data storage/ ./storage/
COPY --chown=www-data:www-data artisan composer.json composer.lock ./

# Copy Nginx configuration template to a permanent location
COPY docker/nginx/default.conf /etc/nginx/templates/default.conf.template

# Copy built frontend assets from Stage 1
COPY --from=frontend-build --chown=www-data:www-data /app/public/build/ ./public/build/

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ---------------------------------------------------------------------------
# Cleanup
# ---------------------------------------------------------------------------
RUN rm -rf /var/cache/apk/* /tmp/* /var/tmp/* ~/.composer/cache

# ---------------------------------------------------------------------------
# Runtime Configuration
# ---------------------------------------------------------------------------
EXPOSE 9093

# Default port (can be overridden)
ENV APP_PORT=9093

# Set ownership for all necessary directories
RUN chown -R www-data:www-data \
    /var/www/html \
    /var/run/nginx \
    /var/run/php-fpm \
    /var/lib/nginx \
    /var/log/nginx \
    /tmp

# Create necessary directories again after cleanup
RUN mkdir -p /tmp/nginx/conf.d \
    && chown -R www-data:www-data /tmp/nginx

HEALTHCHECK --interval=30s --timeout=3s --start-period=10s --retries=3 \
    CMD curl -f http://localhost:${APP_PORT}/ || exit 1

# Create entrypoint script
RUN mkdir -p /etc/nginx/templates && \
    echo '#!/bin/sh' > /entrypoint.sh && \
    echo 'set -e' >> /entrypoint.sh && \
    echo '' >> /entrypoint.sh && \
    echo 'echo "Starting Semantic Query API on port ${APP_PORT}..."' >> /entrypoint.sh && \
    echo '' >> /entrypoint.sh && \
    echo '# Create nginx conf directory' >> /entrypoint.sh && \
    echo 'mkdir -p /tmp/nginx/conf.d' >> /entrypoint.sh && \
    echo '' >> /entrypoint.sh && \
    echo '# Process nginx config template with envsubst' >> /entrypoint.sh && \
    echo "envsubst '$APP_PORT' < /etc/nginx/templates/default.conf.template > /tmp/nginx/conf.d/default.conf" >> /entrypoint.sh && \
    echo '' >> /entrypoint.sh && \
    echo '# Run Laravel optimizations' >> /entrypoint.sh && \
    echo 'php artisan config:cache' >> /entrypoint.sh && \
    echo 'php artisan route:cache' >> /entrypoint.sh && \
    echo 'php artisan view:cache' >> /entrypoint.sh && \
    echo '' >> /entrypoint.sh && \
    echo '# Run database migrations' >> /entrypoint.sh && \
    echo 'php artisan migrate --force' >> /entrypoint.sh && \
    echo '' >> /entrypoint.sh && \
    echo '# Start PHP-FPM and Nginx' >> /entrypoint.sh && \
    echo 'php-fpm -D' >> /entrypoint.sh && \
    echo 'exec nginx -g "daemon off;"' >> /entrypoint.sh && \
    chmod +x /entrypoint.sh

# Switch to non-root user for runtime
USER www-data

ENTRYPOINT ["/entrypoint.sh"]
