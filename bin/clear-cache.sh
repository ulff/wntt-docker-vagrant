#!/usr/bin/env bash

mkdir -p /tmp/sf2
chown www:www -R /tmp/sf2

if [ -z "$1" ]
  then
    php /var/www/app/console cache:clear --no-warmup
  else
    php /var/www/app/console cache:clear --no-warmup --env=$1
fi
