
## Install

- clone repository
- run `git checkout task`
- enter into laradock dir `cd ./test-vuejs/laradock && cp env-example .env`
- build containers `docker-compose up -d nignx`
- enter into container `docker exec -it testtask_workspace_1 bash`
- run into workspace container `touch /var/www/database/database.sqlite && cp .env.example .env && composer install && php artisan key:generate && php artisan migrate:install && php artisan migrate && app:populate_subscribers`
- (optional) run `yarn install`
