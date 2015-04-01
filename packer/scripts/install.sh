#!/bin/bash

# Name: install.sh
# Description: Install the api site.

mv /tmp/api /var/www/api
cd /var/www/api && phing build
chown -R www-data:www-data /var/www/api
