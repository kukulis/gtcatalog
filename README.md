# install


## docker

Create network if it is not created yet

    docker network create catalog-network


stop composer and remove all related images (?)

    docker compose down --rmi=all


# initial database

    INSERT INTO user (id, email, roles, password, enabled, name) VALUES (1, 'admin@unishop.lt', '["ROLE_ADMIN", "ROLE_BUH", "ROLE_EDITOR", "ROLE_USER"]', '$2y$13$seZOjYrMPTGoV0m2jYPx2eUENsyWUUCch.nG5EdOYk7B7PUxwsAki', 1, 'Adminas');

The hashed password here is 'Labas123'.

