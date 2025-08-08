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

## Installation - Production
1. Clone this repository
2. Install dependencies ``npm install && composer install``
3. Build assets ``npm run build``
4. Create database ``php bin/console doctrine:database:create --if-not-exists``
5. Migrate database ``php bin/console doctrine:migrations:migrate --no-interaction``
6. Configure website host [apache-site.conf](./docker/configs/apache-site.conf)

## Installation - Docker
You can build a docker image using the production docker file or use the image available here in github packages.
```yaml
php:
    image: ghcr.io/lukasbecvar/code-paste:latest
    container_name: code_paste_webserver
    restart: always
    depends_on:
        - mysql
    environment:
        - DATABASE_HOST=127.0.0.1
        - DATABASE_PORT=3306
        - DATABASE_NAME=code_paste
        - DATABASE_USERNAME=root
        - DATABASE_PASSWORD=root
        ...
    volumes:
        - ./.docker/services/log:/var/log
    mem_limit: 1g
    ports:
        - "80:80"
    networks:
        - main
```

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
