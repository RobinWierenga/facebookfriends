# facebookfriends example project

![Alt text](example.png?raw=true "Example")

Example project for facebookfriend finder. This demonstrates how you can determine if a friend is connected through
another friend based on a local data structure. This is not connected to facebook so it can be used in any application.

Based on setup provided [here](https://dev.to/aschmelyun/the-beauty-of-docker-for-local-laravel-development-13c0).

## Usage

To get started, make sure you have [Docker installed](https://docs.docker.com/docker-for-mac/install/) on your system, and then clone this repository.

Then execute run.sh from the local folder.

You can open the application at http://localhost:8090/

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

## Code

Main breath-first-search logic is located in FacebookFriendRepository.php.

## Persistent MySQL Storage

By default, whenever you bring down the Docker network, your MySQL data will be removed after the containers are destroyed. If you would like to have persistent data that remains after bringing containers down and back up, do the following:

1. Create a `mysql` folder in the project root, alongside the `nginx` and `src` folders.
2. Under the mysql service in your `docker-compose.yml` file, add the following lines:


```
volumes:
  - ./mysql:/var/lib/mysql
```
