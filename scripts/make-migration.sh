#!/bin/bash

# check if environment is dev
[ -f .env ] && export $(grep -v '^#' .env | xargs)
[ "$APP_ENV" != "dev" ] && echo "This script is only for development environment" && exit 1

# create new migration version
docker-compose run php php bin/console make:migration --no-interaction
