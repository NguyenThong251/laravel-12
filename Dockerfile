FROM php:8.2-fpm

# Cài đặt các package và extension cần thiết
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libgd-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    xml \
    gd \
    zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Thiết lập thư mục làm việc
WORKDIR /var/www

# Copy mã nguồn
COPY . /var/www

# Cấp quyền
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

# Cài đặt dependencies
RUN composer install --optimize-autoloader --no-dev

# Expose port
EXPOSE 9000

# Khởi động PHP-FPM
CMD ["php-fpm"]