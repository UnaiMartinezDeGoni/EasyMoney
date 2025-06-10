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

# 3. Copiamos composer desde la imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Definimos el directorio de trabajo
WORKDIR /var/www/html

# 5. Copiamos el proyecto completo
COPY . .

# 6. Instalamos las dependencias PHP
RUN composer install --optimize-autoloader

# 7. Exponemos el puerto 8000
EXPOSE 8000

# 8. Arrancamos el servidor PHP embebido apuntando a public/
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
