docker-compose up -d
docker-compose run composer update

echo "Creating table(s)"
docker-compose run --rm artisan migrate

echo "Feeding db"
docker-compose run --rm artisan db:seed
