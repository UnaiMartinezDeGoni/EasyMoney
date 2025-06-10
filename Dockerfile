FROM php:8.2-cli

RUN apt-get update \
 && apt-get install -y \
      libonig-dev \
      libzip-dev \
      zip \
      unzip \
      default-mysql-client \
 && docker-php-ext-install \
      zip \
      mysqli \
      pdo_mysql \
 && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --optimize-autoloader

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
