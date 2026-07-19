FROM php:7.4-apache

# Install git dan unzip
RUN apt-get update && apt-get install -y git unzip && rm -rf /var/lib/apt/lists/*

# Menyalin semua file Mikhmon dari repositori Anda ini langsung ke dalam folder web server Docker
COPY . /var/www/html/

# Atur hak akses folder agar web server bisa berjalan lancar
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
