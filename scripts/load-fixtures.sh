#!/bin/bash

# check if environment is dev
[ -f .env ] && export $(grep -v '^#' .env | xargs)
[ "$APP_ENV" != "dev" ] && echo "This script is only for development environment" && exit 1

# drop database
sh scripts/drop-database.sh

# migrate database to latest version
sh scripts/migrate.sh

# load testing datafixtures
docker-compose run php bash -c "
    php bin/console doctrine:fixtures:load --no-interaction &&
    php bin/console doctrine:fixtures:load --no-interaction --env=test
"
