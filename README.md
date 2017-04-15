# Virtual machines management dashboard

 master | [![SensioLabsInsight](https://insight.sensiolabs.com/projects/417a7fe1-ac9e-4fb9-a535-280784eca245/mini.png)](https://insight.sensiolabs.com/projects/417a7fe1-ac9e-4fb9-a535-280784eca245) | [![Scrutinizer](https://img.shields.io/scrutinizer/g/GMaissa/VmDashboard/master.svg)](https://scrutinizer-ci.com/g/GMaissa/VmDashboard/?branch=master) | [![Build Status](https://travis-ci.org/GMaissa/VmDashboard.svg?branch=master)](https://travis-ci.org/GMaissa/VmDashboard) | [![Code Coverage](https://scrutinizer-ci.com/g/GMaissa/VmDashboard/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/GMaissa/VmDashboard/?branch=master)
--------|---------|-------------|--------|----------

## About

This repository provides the backend part to a virtual machine management dashboard.

## Initial setup instructions

Create your .env file from .env.dist and configure it :

Variable             | Default value                             | Description
-------------------- | ----------------------------------------- | ----------------
COMPOSE_PROJECT_NAME | dashboard                                 | Docker Compose project name, used to prefix container names
COMPOSE_FILE         | docker-compose.yml:docker-compose-dev.yml | Docker Compose configuration files. If using Docker for Mac, you better use docker-sync for IO perf issues (see https://goo.gl/6XWF7b). Therefore add the docker-compose-sync.yml configuration file to the list
PROJECT_PORT_PREFIX  |                                           | Used to prefix exposed ports to avoid port collision when running multiple projects at once
DB_ROOT_PASSWORD     | dashboard                                 | MySQL password for root
XDEBUG_REMOTE_HOST   |                                           | XDebug remote host address. If set, will trigger XDebug installation in the engine container. When using Docker for Mac you can set it to 10.254.254.254 with solution provided on https://goo.gl/sLmuRU
XDEBUG_IDEKEY        |                                           | The IDE key used for debugging

## Launch instructions

Install the application dependencies :

    php composer.phar install

If using [Docker Sync](http://docker-sync.io), launch de sync daemon :

    docker-sync-daemon start
    
Start the Docker Compose stack :

    docker-compose up --build -d

Update the database schema :

    docker-compose exec --user www-data engine php bin/console doctrine:schema:create
    
Import the dev / test fixtures :

    docker-compose exec --user www-data engine php bin/console doctrine:fixtures:load
