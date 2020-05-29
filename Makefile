test-fixtures:
	docker-compose exec php bin/console doctrine:fixtures:load -e test -q
test:
	docker-compose exec php bin/phpunit

prod-self-update:
	make pause
	git pull
	docker-compose exec php-workers composer i -o --no-dev
	docker-compose exec php-workers bin/console doctrine:migrations:migrate -n
	docker-compose up node
	make play
pause:
	touch .stopped
	docker-compose stop nginx
	docker-compose stop php
	docker-compose exec php bin/console messenger:stop-workers

play:
	if [ -f .stopped ]; then (rm .stopped) fi
	docker-compose up -d nginx
	docker-compose up -d php-workers
