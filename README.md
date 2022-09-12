### Project setup

1. Copy .env.dist to .env to setup docker variables

		HOST_USER=1000:1000
		DATABASE_USER=TEST
		DATABASE_PASSWORD=password
		DATABASE_NAME=tema
		DATABASE_CONTAINER_NAME=localdb
		
2. Copy app.env.dist to app/.env.local to setup application variables and set proper variables
3. Run 
		
		docker-compose build
		docker-compose up -d
		
4. Go inside php-fpm container and install composer dependencies
		
		docker-compose exec php-fpm bash
		composer install
	
5. Inside php-fpm container run
		
		php bin/console doctrine:migrations:migrate
