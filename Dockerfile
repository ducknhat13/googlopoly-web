FROM php:8.2-fpm

# Cài đặt các gói cần thiết
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    nginx \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql bcmath

# Cài đặt Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Tạo thư mục Composer cache và phân quyền
RUN mkdir -p /root/.composer && chown -R www-data:www-data /root/.composer

# Tăng giới hạn bộ nhớ PHP
RUN echo "memory_limit = 512M" > /usr/local/etc/php/conf.d/memory-limit.ini

# Tạo thư mục làm việc
WORKDIR /var/www/html

# Sao chép toàn bộ project vào container
COPY . .

# Sao chép file cấu hình Nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Dọn cache Composer trước khi cài đặt
RUN composer clear-cache && rm -rf /root/.composer/cache

# Cấu hình Composer để tránh timeout và giới hạn bộ nhớ
RUN composer config --global process-timeout 2000 && \
    composer config --global memory-limit -1

# Cài đặt các dependencies của Laravel với tối ưu hiệu suất
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader --prefer-dist --no-progress --no-scripts --no-interaction

# Phân quyền cho storage và bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose cổng 80 để Nginx phục vụ web
EXPOSE 80

# Lệnh chạy khi container khởi động
CMD ["sh", "-c", "service php8.2-fpm start && nginx -g 'daemon off;'" ]
