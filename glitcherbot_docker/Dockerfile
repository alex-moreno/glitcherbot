FROM php:7.4-apache

RUN a2enmod rewrite

RUN curl -L https://getcomposer.org/composer-2.phar --output ~/composer-2.phar \
    && chmod +x ~/composer-2.phar \
    && mv ~/composer-2.phar /usr/local/bin/composer \
    && composer --version
