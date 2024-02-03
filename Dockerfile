FROM php:8.1-apache

RUN apt-get -y update \
 && apt-get install -y sqlite3


################################################################################
# Set Up PHP INI
################################################################################
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
RUN sed -i '/^;include_path = ".:\/php\/includes"$/a include_path = ".:/var/www/vendor/app/src:\/usr\/share\/php"' "$PHP_INI_DIR/php.ini"
RUN sed -i '/^;date.timezone =$/a date.timezone = Europe\/London' "$PHP_INI_DIR/php.ini"

################################################################################
# Apache application configuration
################################################################################
RUN a2enmod rewrite actions
COPY docker/apache2.conf /etc/apache2/conf-available/app.conf
RUN cd /etc/apache2/conf-enabled && \
    ln -s ../conf-available/app.conf ../conf-enabled/app.conf
