#!/usr/bin/env bash

php /var/www/app/console doctrine:mongodb:fixtures:load
php /var/www/app/console fos:user:create admin admin@wntt admin --super-admin
php /var/www/app/console fos:user:create user user@wntt user
php /var/www/app/console sysla:oauth:create-client --name=client-name --redirect-uri=http://wntt/redirect/uri --grant-type=client_credentials --grant-type=password --grant-type=refresh_token
