init: docker-down-clear docker-pull docker-build docker-up-d composer-install db-migrate assets-install assets-watch
up: docker-up-d composer-install db-migrate assets-install assets-watch

docker-up:
	docker-compose up

docker-up-d:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build --build-arg UID=`id -u`

db-migrate:
	docker-compose run --rm php-cli php bin/console doctrine:migrations:migrate --no-interaction

db-migrate-diff:
	docker-compose run --rm php-cli php bin/console doctrine:migrations:diff

db-migrate-prev:
	docker-compose run --rm php-cli php bin/console doctrine:migrations:migrate prev

composer-install:
	docker-compose run --rm php-cli composer install

assets-install:
	docker-compose run --rm node yarn install

assets-build:
	docker-compose run node yarn build

assets-watch:
	docker-compose run node yarn watch