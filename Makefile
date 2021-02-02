include .env
export $(shell sed 's/=.*//' .env)

up:
	docker-compose pull
	docker-compose up -d --build --remove-orphans

open: build
	open "http://$(PROJECT_BASE_URL):${PROJECT_PORT}"

build: up
	docker exec -t ${PROJECT_NAME}_app sh -c 'if [ ! -e "config.php" ]; then cp config.sample.php config.php; fi'
	docker exec -t ${PROJECT_NAME}_app sh -c 'composer install'

stop:
	docker-compose stop

stop-all-containers:
	ids=$$(docker ps -a -q) && if [ "$${ids}" != "" ]; then docker stop $${ids}; fi

in: up
	docker exec -it ${PROJECT_NAME}_app bash

crawl: build
	docker exec -t ${PROJECT_NAME}_app bash -c 'php bin/visual_regression_bot.php -v bot:crawl-sites sample-sites.csv'
