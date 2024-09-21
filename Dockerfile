# PHP 8.2 Apache imajını temel alalım
FROM php:8.2-apache

# Uygulama dosyalarını konteyner içine kopyalayalım
COPY . /var/www/html/

# Gerekli PHP modüllerini kurmak için komutlar (isteğe bağlı)
RUN docker-php-ext-install pdo pdo_mysql

# Apache'nin 80 portunu açalım
EXPOSE 80
