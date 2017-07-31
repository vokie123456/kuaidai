#!/bin/sh

basepath=$(cd `dirname $0`; pwd)
cd ${basepath}

chmod -R 777 ./storage
chmod -R 777 ./bootstrap/cache

/usr/local/php/bin/php artisan optimize --force
/usr/local/php/bin/php artisan config:cache
/usr/local/php/bin/php artisan route:cache

chown -R www:www ${basepath}

/etc/init.d/php-fpm restart