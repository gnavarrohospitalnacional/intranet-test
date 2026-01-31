#FROM php:8.4-fpm
FROM php:8.3-fpm-bullseye

# Install dependencies
RUN apt-get update && apt-get install -y \
    libaio1 \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    gnupg \
    libldap2-dev \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /opt/oracle

COPY instantclient-basic-linux.x64-21.20.0.0.0dbru.zip .
COPY instantclient-sdk-linux.x64-21.20.0.0.0dbru.zip .

RUN unzip -o instantclient-basic-linux.x64-21.20.0.0.0dbru.zip \
 && unzip -o instantclient-sdk-linux.x64-21.20.0.0.0dbru.zip \
 && rm *.zip \
 && ln -s /opt/oracle/instantclient_21_20 /opt/oracle/instantclient

# Set environment variables
ENV LD_LIBRARY_PATH="/opt/oracle/instantclient:${LD_LIBRARY_PATH}"
ENV ORACLE_HOME="/opt/oracle/instantclient"

# Configure dynamic library loading
RUN echo /opt/oracle/instantclient > /etc/ld.so.conf.d/oracle.conf && ldconfig

# Install the OCI8 PHP extension via PECL (CONFIGURACIÓN EXPLÍCITA)
RUN echo "instantclient,/opt/oracle/instantclient" | pecl install oci8-3.3.0 \
    && docker-php-ext-enable oci8

# You may also need PDO_OCI if using PDO
RUN docker-php-ext-configure pdo_oci --with-pdo-oci=instantclient,/opt/oracle/instantclient \
    && docker-php-ext-install pdo_oci  

# Copy composer.lock and composer.json
COPY composer.lock composer.json artisan /var/www/

# Set working directory
WORKDIR /var/www  

# Install Node.js 22+ LTS and npm
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

# Verify Node.js and npm installation
RUN node --version && npm --version

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo_mysql zip mbstring exif pcntl bcmath gd opcache ldap

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory contents
COPY . /var/www

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install

# Copy existing application directory permissions
COPY --chown=www:www . /var/www

# Change current user to www
USER www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]