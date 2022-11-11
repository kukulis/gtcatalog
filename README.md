# install


## docker

Create network if it is not created yet

    docker network create catalog-network


stop composer and remove all related images (?)

    docker compose down --rmi=all


# initial database

    INSERT INTO user (id, email, roles, password, enabled, name) VALUES (1, 'admin@unishop.lt', '["ROLE_ADMIN", "ROLE_BUH", "ROLE_EDITOR", "ROLE_USER"]', '$2y$13$seZOjYrMPTGoV0m2jYPx2eUENsyWUUCch.nG5EdOYk7B7PUxwsAki', 1, 'Adminas');

The hashed password here is 'Labas123'.


# xdebug

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

    