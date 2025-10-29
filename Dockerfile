# Use official PHP with Apache
FROM php:8.2-apache

# Install system dependencies and PHP extensions
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      libzip-dev \
      unzip \
      git \
      libpq-dev \
 && docker-php-ext-install pdo_pgsql pgsql zip \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite if your app needs it
RUN a2enmod rewrite

# Copy source code into Apache document root
COPY --chown=www-data:www-data . /var/www/html

WORKDIR /var/www/html

# If composer.phar exists in repo, use it to install dependencies (if composer.json present)
RUN if [ -f composer.phar ] && [ -f composer.json ]; then \
      php composer.phar install --no-dev --no-interaction --optimize-autoloader || true; \
    fi

# Uncomment these lines instead if you prefer to install Composer via the installer:
# RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
#  && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
#  && php -r "unlink('composer-setup.php');" \
#  && if [ -f composer.json ]; then composer install --no-dev --no-interaction --optimize-autoloader; fi

# Set file permissions
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html

# Copy entrypoint (adjusts Apache to honor $PORT)
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Use entrypoint that adjusts Apache listen port then starts Apache
ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]
