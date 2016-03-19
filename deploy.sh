#!/bin/bash

git status -uall --ignored
docker build -t keboola/gmail-extractor .
docker login -e="." -u="$QUAY_USERNAME" -p="$QUAY_PASSWORD" quay.io
docker tag keboola/gmail-extractor quay.io/keboola/gmail-extractor:$TRAVIS_TAG
docker ps -a
docker push quay.io/keboola/gmail-extractor:$TRAVIS_TAG
