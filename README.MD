# Code paste
Self-hosted solution for sharing text and code snippets.

## Preview
![Paste view](.github/images/preview.png)

## Features
- Share text and code snippets
- Paste content encryption
- SSL only mode
- Maintenance mode
- Syntax highlighting

## Installation - Development
1. Clone the repository
2. Run with docker compose (scripts/docker-start-dev.sh)
3. Load testing database fixtures (scripts/load-fixtures.sh)

## Docker image
You can build a docker image using the production docker file or use the image available here in github packages.

## Installation - Production
1. Clone this repository
2. Install dependencies ``npm install && composer install``
3. Build assets ``npm run build``
4. Create database ``php bin/console doctrine:database:create --if-not-exists``
5. Migrate database ``php bin/console doctrine:migrations:migrate --no-interaction``
6. Configure website host [apache-site.conf](./docker/configs/apache-site.conf)

## Configuration
1. Create your own .env file with name .env.prod and change app env in main .env
2. Configure SSL_ONLY false or true if you want allow only SSL traffic
3. Configure MAINTENANCE_MODE true or false if you want to enable maintenance mode
4. ENCRYPTION_MODE true or false if you want to enable paste content encryption (key is APP_SECRET value in .env)
5. Configure DATABASE_DRIVER, DATABASE_HOST, DATABASE_PORT, DATABASE_NAME, DATABASE_USERNAME, DATABASE_PASSWORD

## Requirements
- Apache or Nginx web server with PHP support
- NodeJS with npm
- Composer
- PHP 8.4
- MySQL

## License
This software is licensed under the [MIT license](LICENSE).
