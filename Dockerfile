# ══════════════════════════════════════════════
# Stage 1: Install PHP dependencies
# ══════════════════════════════════════════════
FROM php:8.3-fpm AS php-base

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Node.js 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html

# ══════════════════════════════════════════════
# Stage 2: Install dependencies & build assets
# ══════════════════════════════════════════════
FROM php-base AS builder

# Copy composer files first for better caching
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy package files and install Node modules
COPY package.json package-lock.json* ./
RUN npm ci --production=false

# Copy the full application
COPY . .

# Generate optimised autoloader
RUN composer dump-autoload --optimize

# Build frontend assets
RUN npm run build

# ══════════════════════════════════════════════
# Stage 3: Production image
# ══════════════════════════════════════════════
FROM php-base AS production

# Copy application from builder
COPY --from=builder /var/www/html /var/www/html

# Remove dev files
RUN rm -rf node_modules tests .env.example *.md

# Configure PHP-FPM
RUN sed -i 's/listen = .*/listen = 127.0.0.1:9000/' /usr/local/etc/php-fpm.d/www.conf

# Configure Nginx
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
RUN rm -f /etc/nginx/sites-enabled/default

# Configure Supervisor to run Nginx + PHP-FPM
RUN mkdir -p /var/log/supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Health check
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD curl -f http://localhost:${APP_PORT:-9094}/api/health || exit 1

EXPOSE ${APP_PORT:-9094}

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
