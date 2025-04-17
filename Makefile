PROJECT_NAME=quizDB
PHP_SERVICE=apache

build:
	docker-compose up -d --build
	@echo ""
	@echo "Build termine avec succes !"
	@echo "Application :        http://localhost:8080"
	@echo "phpMyAdmin :                  http://localhost:8081"
	@echo "Mailpit (boîte mail locale) : http://localhost:1080"
	@echo ""

start:
	docker-compose up -d
	@echo ""
	@echo "Demarrage termine avec succes !"
	@echo "Application :        http://localhost:8080"
	@echo "phpMyAdmin :                  http://localhost:8081"
	@echo "Mailpit (boîte mail locale) : http://localhost:1080"
	@echo ""

stop:
	docker-compose down
	@echo ""
	@echo "Arrêt termine avec succès !"
	@echo ""

restart:
	docker-compose down && docker-compose up -d --build
	@echo ""
	@echo "Redemarrage termine avec succes !"
	@echo "Application :        http://localhost:8080
	@echo "phpMyAdmin :                  http://localhost:8081
	@echo "Mailpit (boîte mail locale) : http://localhost:1080"

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

open-mailpit:
	open http://localhost:1080

open-db:
	open http://localhost:8080

open-app:
	open http://localhost:8000

