# Petshop-API
My submission for the coding task

## Specifications
- PHP 8.2 
- Laravel 10 
- Swagger Documentation 
- Feature and Unit Tests 
- JWT Authentication
- Migration and Seeders
- Eloquent Relationships
- Custom Middleware
- Docker Setup
- Larastan 
- PHP Insights
- IDE Helpers for Models

## Setup
- Clone the repo
```bash
    git clone https://github.com/Nelwhix/buckhill-test.git
```

- Start Docker network, nginx is on port 8088, php on 9000 and mysql
on 4306, you can edit manually in the `docker-compose.yaml` or if you have
'sed' cli installed(installed by default on most Unix-based systems)
run 
```bash
    sed -i 's/8088/${desired_nginx_port}/g; s/9000:9000/${desired_php_port}:9000/g; s/4306/${desired_php_port/g' docker-compose.yaml
    
    # so for example
    sed -i 's/8088/5173/g; s/9000:9000/9001:9000/g; s/4306/5306/g' docker-compose.yaml
```

then you can start docker
```bash 
    docker compose up -d
```

- Install Dependencies
```bash
    docker compose run composer composer install
```

- Generate JWT SECRET
```bash
   docker compose run php php artisan jwt:generate
```

- Generate App key
```bash
    docker compose run php php artisan key:generate
```

- Run Migrations and seeders
```bash
    docker compose run php php artisan migrate --seed
```

## Submission
- Swagger docs is at http://localhost:8088/api/v1/docs
- To run all tests:
```bash
    docker compose run php php artisan test
```
- Truncate and reseed Db cron is at app/app/Console/Kernel.php or 
you can test with
```bash
    docker compose run php php artisan db:truncate-reseed
```

- Larastan is set at Level 8:
```bash
    docker compose run composer composer larastan 
```
- I used the rules in your phpinsights:
```bash
    docker compose run composer composer insights 
```

## Level 3 Challenge: Currency Exchange Package
Package is at packages/currency-exchange