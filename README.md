# Cloud Environments Management Backend

 master | [![SensioLabsInsight](https://insight.sensiolabs.com/projects/76b75baa-ce4b-434b-b7df-7adbd85c857b/mini.png)](https://insight.sensiolabs.com/projects/76b75baa-ce4b-434b-b7df-7adbd85c857b) | [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/GMaissa/cem-backend/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/GMaissa/cem-backend/?branch=master) | [![Build Status](https://travis-ci.org/GMaissa/cem-backend.svg?branch=master)](https://travis-ci.org/GMaissa/cem-backend) | [![Code Coverage](https://scrutinizer-ci.com/g/GMaissa/cem-backend/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/GMaissa/cem-backend/?branch=master)
--------|---------|-------------|--------|----------


## About

This repository provides the backend part to a Cloud Environments Management dashboard.

## Development 

### Development environmment setup

Create your .env file from .env.dist and configure it :

Variable             | Default value                             | Description
-------------------- | ----------------------------------------- | ----------------
COMPOSE_PROJECT_NAME | cem                                       | Docker Compose project name, used to prefix container names
COMPOSE_FILE         | docker-compose.yml:docker-compose-dev.yml | Docker Compose configuration files. If using Docker for Mac, you better use docker-sync for IO perf issues (see https://goo.gl/6XWF7b). Therefore add the docker-compose-sync.yml configuration file to the list
PROJECT_PORT_PREFIX  |                                           | Used to prefix exposed ports to avoid port collision when running multiple projects at once
DB_ROOT_PASSWORD     | dashboard                                 | MySQL password for root
XDEBUG_REMOTE_HOST   |                                           | XDebug remote host address. If set, will trigger XDebug installation in the engine container. When using Docker for Mac you can set it to 10.254.254.254 with solution provided on https://goo.gl/sLmuRU
XDEBUG_IDEKEY        |                                           | The IDE key used for debugging


### Develoment environment initialization

If using [Docker Sync](http://docker-sync.io), launch de sync daemon :

```bash
docker-sync-daemon start
```
    
Start the Docker Compose stack :

```bash
docker-compose up --build -d
```

Install the application dependencies :

```bash
docker-compose exec --user www-data engine php composer.phar install
```

Create the database schema :

```bash
docker-compose exec --user www-data engine php bin/console doctrine:schema:create
```
    
Import the dev / test fixtures :

```bash
docker-compose exec --user www-data engine php bin/console doctrine:fixtures:load
```


### Running tests

Install the dev dependencies :

```bash
docker-compose exec --user www-data engine php composer.phar install --dev
```

Run PHPUnit test suite :

```bash
docker-compose exec --user www-data engine php vendor/bin/phpunit
```

Run Behat tests :

```bash
docker-compose exec --user www-data engine php vendor/bin/behat
```

## Contributing

In order to be accepted, your contribution needs to pass a few controls : 

* PHP files should be valid
* PHP files should follow the [PSR-2](http://www.php-fig.org/psr/psr-2/) standard
* PHP files should be [phpmd](https://phpmd.org) and [phpcpd](https://github.com/sebastianbergmann/phpcpd)
warning/error free

To ease the validation process, install the [pre-commit framework](http://pre-commit.com)
and install the repository pre-commit hook :

```bash
pre-commit install
```

Finally, in order to homogenize commit messages across contributors (and to ease generation of the CHANGELOG),
please apply this [git commit message hook](https://gist.github.com/GMaissa/f008b2ffca417c09c7b8)
onto your local repository. 


## License

This bundle is released under the MIT license. See the complete license in the bundle:

```bash
LICENSE
```


