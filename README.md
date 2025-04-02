docker-compose up -d
docker exec -it laravel-vnpay-api-app-1 bash
php artisan migrate
php artisan queue:work