# Dockerfile.base
FROM php:8.2-fpm

###########################################################################
# User Data
###########################################################################
ARG user=laravel
ARG uid=1000

###########################################################################
# Linux Dependecies
###########################################################################
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libgd-dev \
    jpegoptim optipng pngquant gifsicle \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nano \
    cron \
    ca-certificates \
    supervisor

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*




###########################################################################
# PHP Dependecies & Composer
###########################################################################
RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets zip intl

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN groupadd -g $uid $user \
    && useradd -u $uid -g $user -m $user \
    && usermod -aG www-data $user \
    && mkdir -p /home/$user/.composer \
    && chown -R $user:$user /home/$user

###########################################################################
# Set default configs to bash, user and directory
###########################################################################
WORKDIR /var/www

# RUN rm -rf www

# RUN composer create-project laravel/laravel www

# WORKDIR /var/www

# Change ownership of work directory
RUN chown -R $user:$user /var/www

# change permissions of storage path
# RUN chmod -R 755 /var/www/storage

# storage link
# RUN php artisan storage:link

USER $user
