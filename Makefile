DOCKER_COMPOSE?=docker-compose
EXEC?=$(DOCKER_COMPOSE) exec php
COMPOSER=$(EXEC) composer
CONSOLE=bin/console


.DEFAULT_GOAL := help

.PHONY: tests

help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'


start: up vendor db perm   ## Install and start the project

stop:                                                                                                  ## Remove docker containers
	$(DOCKER_COMPOSE) kill
	$(DOCKER_COMPOSE) rm -v --force

reset: stop start

clear: perm                                                                                            ## Remove all the cache, the logs and the sessions
	-$(EXEC) rm -rf var/cache/*
	-$(EXEC) rm -rf var/sessions/*
	-$(EXEC) rm -rf var/logs/*

clean: clear                                                                                           ## Clear and remove dependencies
	$(EXEC) rm -rf vendor

cc:                                                                                                    ## Clear the cache in dev and prod env
	$(EXEC) $(CONSOLE) cache:clear
	$(EXEC) $(CONSOLE) cache:clear --env=prod

tty:                                                                                                   ## Run app container in interactive mode
	$(EXEC) /bin/bash


db: vendor wait-for-db                                                                           ## Reset the database and load fixtures
	$(EXEC) $(CONSOLE) doctrine:database:drop --force --if-exists
	$(EXEC) $(CONSOLE) doctrine:database:create --if-not-exists
	$(EXEC) $(CONSOLE) doctrine:schema:create
	$(EXEC) $(CONSOLE) doctrine:fixtures:load -n

# Internal rules

wait-for-db:
	$(EXEC) php -r "set_time_limit(60);for(;;){if(@fsockopen('mysql',3306)){break;}echo \"Waiting for MySQL\n\";sleep(1);}"

build:
	$(DOCKER_COMPOSE) pull --ignore-pull-failures
	$(DOCKER_COMPOSE) build --force-rm

up:
	$(DOCKER_COMPOSE) up -d --remove-orphans

perm:
	$(EXEC) chown -R www-data:root var



# Rules from files

vendor: composer.lock
	$(COMPOSER) install -n

composer.lock: composer.json
	@echo compose.lock is not up to date.
