#!/bin/bash

echo "#### Checking Vendor folder..."

if [ ! -d ./vendor ]; then
    echo "#### Running Composer Install"
    composer install
    chmod 777 -R storage/
else
    echo "#### Vendor folder OK"
fi

# register cronjob
echo "#### Registering Cronjob Service..."
touch /var/log/cron.log
crontab /etc/cron.d/container_cronjob

service cron reload
service cron start
