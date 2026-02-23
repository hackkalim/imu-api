FROM php:8.2-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Enable Apache modules
RUN a2enmod rewrite

# Configure PHP for maximum error logging
RUN echo "error_log = /dev/stderr" >> /usr/local/etc/php/conf.d/error-logging.ini
RUN echo "log_errors = On" >> /usr/local/etc/php/conf.d/error-logging.ini
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/error-logging.ini
RUN echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/error-logging.ini
RUN echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/error-logging.ini

# Set working directory
WORKDIR /var/www/html

# Copy your application files
COPY api/ /var/www/html/

# Create a simple index.php to prevent 403 error
RUN echo "<?php echo json_encode(['status' => 'API is running']); ?>" > /var/www/html/index.php

# Ensure Apache logs to stdout/stderr
RUN ln -sf /dev/stdout /var/log/apache2/access.log && \
    ln -sf /dev/stderr /var/log/apache2/error.log

# Expose port 80
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]
