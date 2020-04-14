test:
	docker-compose exec php bin/phpunit

prod-self-update:
	docker-compose stop nginx
	docker-compose exec php composer i -o --no-dev
	docker-compose exec php bin/console console doctrine:migrations:migrate -n
	docker-compose up node
	docker-compose up -d
