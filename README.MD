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
#### Environment variables
The environment variables are loaded from .env file. You can create .env file in root directory with your configuration.
| Variable | Description | Example value |
| --- | --- | --- |
| APP_SECRET | Secret key for application security (used for hashing, encryption, etc.) | 6514635062ba34f218535f9f106fd20b |
| SSL_ONLY | Allow access only via HTTPS protocol | false |
| MAINTENANCE_MODE | Enable or disable maintenance mode | false |
| ENCRYPTION_MODE | Enable encryption for pasted content | true |
| CONTACT_EMAIL | Administrator contact email | lukas@becvar.xyz |
| EXTERNAL_LOG_ENABLED | Enable external log sending | false |
| EXTERNAL_LOG_URL | External log API endpoint | https://admin.becvar.xyz/api/external/log |
| EXTERNAL_LOG_TOKEN | Authentication token for external log API | cbd8b08271wefg |
| METRICS_EXPORTER_ENABLED | Enable metrics exporter | true |
| METRICS_EXPORTER_ALLOWED_IP | IP address allowed to access metrics (`%` = all) | % |
| VISITOR_TRACKING_ENABLED | Enable visitor tracking | false |
| VISITOR_TRACKING_SERVICE_NAME | Service name for visitor tracking | paste.becvar.xyz |
| VISITOR_TRACKING_URL | API endpoint for visitor tracking | https://admin.becvar.xyz/api/monitoring/visitor/tracking |
| DATABASE_DRIVER | Database driver type | pdo_mysql |
| DATABASE_VERSION | Database version | 8.0.43 |
| DATABASE_HOST | Database host address | 127.0.0.1 |
| DATABASE_PORT | Database port | 3306 |
| DATABASE_NAME | Database name | code_paste |
| DATABASE_USERNAME | Database username | root |
| DATABASE_PASSWORD | Database password | root |

## Requirements
- Apache or Nginx web server with PHP support
- NodeJS with npm
- Composer
- PHP 8.4
- MySQL

## License
This software is licensed under the [MIT license](LICENSE).
