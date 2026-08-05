# Usa imagem oficial do PHP 8.2 com Apache
FROM php:8.2-apache

# Instala dependências do sistema e extensões PHP necessárias para o Composer e MySQL
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli zip

# Força o Apache a utilizar UTF-8 em todas as respostas HTTP
RUN echo "AddDefaultCharset UTF-8" >> /etc/apache2/conf-enabled/charset.conf

# Instala o Composer globalmente
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia todos os arquivos do projeto para o container
COPY . /var/www/html/

# Instala as dependências do PHPMailer via Composer sem pacotes de dev
RUN composer install --no-dev --optimize-autoloader

# Habilita o módulo mod_rewrite do Apache
RUN a2enmod rewrite

# Dá permissão correta de arquivo para o Apache
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80