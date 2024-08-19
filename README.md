# Install

## Project files

Rename .env.example to .env

Create categories **var/log** and set permissions to **777**

## docker

Create network if it is not created yet

    docker network create catalog-network


stop composer and remove all related images (?)

    docker compose down --rmi=all

## Run Docker

    docker compose up -d

If getting errors **pull access denied for...**

Run this commands

    docker build -t gt/catalog:latest .docker/catalog
    docker build -t gt/catalog_web:latest .docker/catalog_web

Then **docker compose up -d** will work normally

## Initial database

Docker do not download DB automatically, you should ask one of your teammates to send you a DB copy and import it into **catalog_db** image.

Command to import DB
    
    docker exec -i catalog_db mysql -P23311 -ucat -pcat cat < DB.sql

Create new user into DB

    INSERT INTO user (id, email, roles, password, enabled, name) VALUES (1, 'admin@unishop.lt', '["ROLE_ADMIN", "ROLE_BUH", "ROLE_EDITOR", "ROLE_USER"]', '$2y$13$seZOjYrMPTGoV0m2jYPx2eUENsyWUUCch.nG5EdOYk7B7PUxwsAki', 1, 'Adminas');

The hashed password here is **Labas123**.

## Initial project files

Connect to catalog image

    docker exec -it catalog bash

Run this command to set up your home directory

    expot HOME=/var/www/html

Run composer installation command

    composere install

Updating DB scheme

    bin/console doctrine:schema:update --force

## xdebug

    cp xdebug.ini.example xdebug.ini

Edit xdebug.ini and change ip address in line:

    xdebug.client_host=172.27.0.1

to value equal to container 'catalog' ip address. To get ip address you may run:

    docker inspect catalog

You will get lots of info and in the end something like this:
    
    ...
    "Gateway": "172.25.0.1"
    ...

Running from command line, from inside docker container:

    export XDEBUG_SESSION=PHPSTORM
    export PHP_IDE_CONFIG="serverName=catalog.dv"

When debugging with rest testing tool,add header parameter Cookie:XDEBUG_SESSION=PHPSTORM

# GUI

    http://localhost:8003


#JWT

excerpt from documentation:

    mkdir config/jwt

    openssl genrsa -out config/jwt/private.pem -aes256 4096

Paprašė slaptažodžio, įvedžiau Labas123

    openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem

Paprašė slaptažodžio, įvedžiau tą patį.


# Migrations

    bin/console doctrine:migrations:migrate

rollbackas:

    bin/console doctrine:migrations:migrate prev