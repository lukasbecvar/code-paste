#!/bin/bash

# clear console history
clear

# run test process
docker-compose run --no-deps php bash -c "
    php bin/console doctrine:database:drop --env=test --force &&
    php bin/console doctrine:database:create --if-not-exists --env=test &&
    php bin/console doctrine:migrations:migrate --no-interaction --env=test &&
    php bin/console doctrine:fixtures:load --no-interaction --env=test &&
    php vendor/bin/phpcbf &&
    php vendor/bin/phpcs &&
    php vendor/bin/phpstan analyze &&
    php bin/phpunit
"
