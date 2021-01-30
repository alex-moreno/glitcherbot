#!/usr/bin/env bash

# Install dependencies
add-apt-repository ppa:ondrej/php
apt-get update
apt-get install -y apache2 git curl php7.4 php7.4-bcmath php7.4-bz2 php7.4-cli php7.4-curl php7.4-intl php7.4-json php7.4-mbstring php7.4-opcache php7.4-soap php7.4-sqlite3 php7.4-xml php7.4-xsl php7.4-zip libapache2-mod-php7.4 sqlite3 php-pear composer
pear install HTML_Table

# Add Apache custom config.
cp /vagrant/config/000-default.conf /etc/apache2/sites-available/000-default.conf
a2enmod rewrite
apachectl -k graceful

