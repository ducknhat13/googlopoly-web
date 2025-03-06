FROM php:8.2-fpm

# Cài đặt các gói cần thiết
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql bcmath

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Tăng giới hạn bộ nhớ PHP
RUN echo "memory_limit = 512M" > /usr/local/etc/php/conf.d/memory-limit.ini

# Tạo thư mục làm việc
WORKDIR /var/www/html

# Sao chép toàn bộ project vào container
COPY . .

# Dọn cache Composer trước khi cài đặt
RUN composer clear-cache

# Cấu hình Composer để tránh timeout
RUN composer config --global process-timeout 2000

# Cài đặt các dependencies của Laravel với tối ưu hiệu suất
RUN composer install --no-dev --optimize-autoloader --prefer-dist

# Phân quyền cho storage và bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose cổng 9000 để Nginx kết nối
EXPOSE 9000

# Lệnh chạy khi container khởi động
CMD ["php-fpm"]
