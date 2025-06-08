FROM php:8.4-fpm
#FROM php:8.4-cli

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    curl \
    && docker-php-ext-install pdo_mysql zip mbstring gd

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Рабочая директория внутри контейнера
WORKDIR /var/www/html

# Копируем Laravel проект в контейнер
COPY ./my_api_project /var/www/html

# Установка зависимостей Laravel
RUN composer install --no-interaction --optimize-autoloader

# Права на storage и bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 9000

#CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=9000"]
CMD ["php-fpm"]
