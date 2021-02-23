include .env
export $(shell sed 's/=.*//' .env)
SITES_CSV=sample-sites.csv
SITES_JSON=sample-sites.json
INCLUDE_SITEMAPS=no
FORCE_SITEMAPS=no
export SITES_CSV
export SITES_JSON
export INCLUDE_SITEMAPS

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
	echo "Crawling with ${SITES_CSV}"
	docker exec -t ${PROJECT_NAME}_app bash -c 'php bin/visual_regression_bot.php -v bot:crawl-sites ${SITES_CSV} --include_sitemaps ${INCLUDE_SITEMAPS} --force_sitemaps ${FORCE_SITEMAPS}'

crawl-acquia: build
	echo "Crawling with ${SITES_JSON}"
	docker exec -t ${PROJECT_NAME}_app bash -c 'php bin/visual_regression_bot.php -v acquia:acsf-crawl-sites ${SITES_JSON} --include_sitemaps ${INCLUDE_SITEMAPS} --force_sitemaps ${FORCE_SITEMAPS}'
