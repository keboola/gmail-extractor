FROM php:7.0.3
MAINTAINER Vladimír Kriška <vlado@keboola.com>
ENV DEBIAN_FRONTEND noninteractive

ADD . /code

RUN apt-get update && apt-get install unzip git -y
RUN cd && curl -sS https://getcomposer.org/installer | php && ln -s /root/composer.phar /usr/local/bin/composer
RUN cd /code && composer install --prefer-dist --no-interaction

WORKDIR /code

CMD php ./src/run.php --data=/data