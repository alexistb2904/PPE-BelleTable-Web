PROJECT_NAME=quizDB
PHP_SERVICE=php

build:
	docker-compose up -d --build

stop:
	docker-compose down

restart:
	docker-compose down && docker-compose up -d --build

logs:
	docker-compose logs -f

php:
	docker-compose exec $(PHP_SERVICE) bash

composer-install:
	docker-compose exec $(PHP_SERVICE) composer install

composer-update:
	docker-compose exec $(PHP_SERVICE) composer update

xdebug-on:
	docker-compose exec $(PHP_SERVICE) bash -c "echo 'xdebug.mode=debug' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini"

xdebug-off:
	docker-compose exec $(PHP_SERVICE) bash -c "sed -i '/xdebug.mode=debug/d' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini"

db-reset:
	docker-compose down -v && docker-compose up -d --build

migrate:
	docker-compose exec db mysql -uuser -ppassword mydb < /docker-entrypoint-initdb.d/init-db.sql

open-mailpit:
	open http://localhost:1080

open-db:
	open http://localhost:8080

open-app:
	open http://localhost:8000

