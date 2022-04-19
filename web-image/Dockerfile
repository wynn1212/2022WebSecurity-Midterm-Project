FROM php:7.0.30-apache
RUN apt-get update && apt-get install -y \
    # For php gd ext
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    # For php zip ext
    zlib1g-dev \
    libzip-dev
RUN docker-php-ext-install mysqli pdo pdo_mysql zip
RUN docker-php-ext-configure gd --with-freetype-dir --with-jpeg-dir
RUN docker-php-ext-install gd