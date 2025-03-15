FROM php:8.2-apache

# Enable Apache modules
RUN a2enmod rewrite

# Install needed extensions & tools
RUN apt-get update && apt-get install -y \
    zip unzip git curl \
    && docker-php-ext-install pdo pdo_mysql mysqli

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

 # Installer msmtp pour envoyer les mails
RUN apt-get update && apt-get install -y msmtp msmtp-mta

# Copier le fichier de conf msmtp
COPY ./msmtprc /etc/msmtprc
RUN chmod 600 /etc/msmtprc

COPY ./php.ini /usr/local/etc/php/conf.d/php.ini

WORKDIR /var/www/html
COPY ./www/ /var/www/html/
COPY ./msmtprc /var/www/.msmtprc
