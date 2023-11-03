FROM php:8.2-fpm

RUN apt update && docker-php-ext-install pdo_mysql

RUN apt install -y git && apt install -y zip

## composer install
RUN curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
RUN php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer

COPY ./api/docker-entrypoint.sh /

RUN chmod +x /docker-entrypoint.sh

RUN apt-get update
RUN apt update && apt install -y vim

# Updating the packages and installing cron and vim editor
RUN apt-get install cron -y

# Crontab file copied to cron.d directory.
COPY ./crontab_file /etc/cron.d/container_cronjob

WORKDIR /www

COPY ./api .

RUN composer install && chmod 777 -R storage/

EXPOSE 80

## Add the wait script to the image
ADD https://github.com/ufoscout/docker-compose-wait/releases/download/2.10.0/wait /wait
RUN chmod +x /wait

# set timezone
RUN rm /etc/localtime
RUN ln -sf /usr/share/zoneinfo/America/Sao_Paulo /etc/localtime
RUN dpkg-reconfigure -f noninteractive tzdata

RUN /docker-entrypoint.sh
