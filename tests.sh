#!/bin/bash

if [ -f ./set-env.sh ]; then
  source ./set-env.sh
fi

php --version \
  && composer --version \
  && ./vendor/bin/phpcs --standard=psr2 -n --ignore=vendor . \
  && ./vendor/bin/phpunit
