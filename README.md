# docker-compose-laravel

Example project for facebookfriend finder.
Based on setup provided by: [here](https://dev.to/aschmelyun/the-beauty-of-docker-for-local-laravel-development-13c0).

## Usage

To get started, make sure you have [Docker installed](https://docs.docker.com/docker-for-mac/install/) on your system, and then clone this repository.

Clone this project.

Then execute run.sh from the local folder.

Next, navigate in your terminal to the directory you cloned this, and spin up the containers for the web server by running `docker-compose up -d --build site`.

Bringing up the Docker Compose network with `site` instead of just using `up`, ensures that only our site's containers are brought up at the start, instead of all of the command containers as well. The following are built for our web server, with their exposed ports detailed:

## Mysql

Database can be reached as:
localhost
- db: homestead
- user: homestead
- pass: secret
- port: 3310

## ports used
- **nginx** - `:8090`
- **mysql** - `:3310`
- **php** - `:9000`
- **redis** - `:6380`

## Database data

The database is filled with 11.110 rows, this is equal to 5 levels of friends. To increase the amount of friends
change the MAX_DEPTH var in DatabaseSeeder.php and run docker-compose run --rm artisan db:seed. This will delete all data from the db
and increasing this value will take some time to fill the db (1 min for 1 million rows.. so around 10 mins for 10 million for a depth of 7). 

## Persistent MySQL Storage

By default, whenever you bring down the Docker network, your MySQL data will be removed after the containers are destroyed. If you would like to have persistent data that remains after bringing containers down and back up, do the following:

1. Create a `mysql` folder in the project root, alongside the `nginx` and `src` folders.
2. Under the mysql service in your `docker-compose.yml` file, add the following lines:

```
volumes:
  - ./mysql:/var/lib/mysql
```
