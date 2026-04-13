FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    libpq-dev

# Install PHP extensions (PostgreSQL included)
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . .

# Install dependencies (NO cache tricks here to avoid 500 errors)
RUN composer install --no-dev --optimize-autoloader

# Fix storage permissions (important for Laravel)
RUN chmod -R 775 storage bootstrap/cache || true

# Expose Render port
EXPOSE 10000

# Start Laravel using correct public folder
CMD php -S 0.0.0.0:$PORT -t public