#!/bin/bash

/entrypoint.sh apache2

cd /var/www/html

wp core install --url="$WEBSITE_IP" --title=My-Only-Store --admin_user=$ADMIN_USER --admin_password=$ADMIN_PASSWORD --admin_email=$ADMIN_EMAIL
wp plugin install woocommerce --activate
wp plugin install rest-api --activate
wp theme install ridizain --activate
wp theme install woodizain --activate

wp plugin install /scripts/plugins/woo_integration.zip --activate

echo "#######################################"
echo "####       Have fun!               ####"
echo "#######################################"

tail -f /var/log/apache2/error.log
