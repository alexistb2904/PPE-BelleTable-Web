FROM php:8.2-apache

# Enable Apache modules
RUN a2enmod rewrite

# Install needed extensions & tools
RUN apt-get update && apt-get install -y \
    zip unzip git curl msmtp msmtp-mta \
    && docker-php-ext-install pdo pdo_mysql mysqli opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Configure Xdebug
RUN echo "zend_extension=xdebug.so" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
 && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
 && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
 && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
 && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini


RUN echo "zend_extension=opcache" >> /usr/local/etc/php/conf.d/opcache-recommended.ini \
 && echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache-recommended.ini \
 && echo "opcache.enable_cli=1" >> /usr/local/etc/php/conf.d/opcache-recommended.ini \
 && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache-recommended.ini \
 && echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/opcache-recommended.ini \
 && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache-recommended.ini \
 && echo "opcache.validate_timestamps=1" >> /usr/local/etc/php/conf.d/opcache-recommended.ini \
 && echo "opcache.revalidate_freq=2" >> /usr/local/etc/php/conf.d/opcache-recommended.ini

COPY ./msmtprc /etc/msmtprc
RUN chmod 600 /etc/msmtprc

COPY ./php.dev.ini /usr/local/etc/php/conf.d/php.ini

# Copier le code
WORKDIR /var/www/html
COPY ./www/ /var/www/html/
COPY ./msmtprc /var/www/.msmtprc
