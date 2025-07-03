FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk update && apk add --no-cache \
    zip \
    unzip \
    curl \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    nodejs \
    npm \
    openssl \
    icu-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libwebp-dev \
    imagemagick-dev \
    imagemagick \
    pkgconf

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql zip gd exif soap intl opcache

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Set working directory
WORKDIR /var/www/html

COPY . .

# Install project dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

RUN npm install

RUN npm run build

# Expose port 9000 for FPM
EXPOSE 9000

# Set user (optional, but recommended for security)
RUN addgroup -g 1000 -S user && adduser -u 1000 -S user -G user
USER user

# Start PHP-FPM
CMD ["php-fpm"]
