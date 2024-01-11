FROM php:7.4
ENV DEBIAN_FRONTEND noninteractive
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update -q \
  && apt-get install unzip git -y

WORKDIR /root

RUN curl -sS https://getcomposer.org/installer | php \
  && mv composer.phar /usr/local/bin/composer

COPY ./docker/php.ini /usr/local/etc/php/php.ini
COPY . /code

WORKDIR /code

RUN composer install --prefer-dist --no-interaction

CMD php ./src/run.php --data=/data
