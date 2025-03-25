CONTAINER_NAME = nextplay-dev-laravel

start:
	docker compose up -d --build

stop:
	docker compose down

cli:
	docker exec -it $(CONTAINER_NAME) bash

reverb:
	docker exec -it $(CONTAINER_NAME) php artisan reverb:start

re: stop start

test:
	docker exec -it $(CONTAINER_NAME) php artisan test

paratest:
	docker exec -it $(CONTAINER_NAME) php artisan test --parallel

migrate:
	docker exec -it $(CONTAINER_NAME) php artisan migrate

lint: ide-helper
	docker exec -it $(CONTAINER_NAME) ./vendor/bin/pint --repair

ide-helper:
	docker exec -it $(CONTAINER_NAME) php artisan ide-helper:generate
	docker exec -it $(CONTAINER_NAME) php artisan ide-helper:models -F ./_ide_helper_models.php

prepare: ide-helper lint paratest

.PHONY: start stop cli test migrate lint ide-helper prepare re reverb
