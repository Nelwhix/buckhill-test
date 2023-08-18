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

## Setup
- Clone the repo
```bash
    git clone https://github.com/Nelwhix/buckhill-test.git
```

- Start Docker network, nginx is on port 8088, php on 9000 and mysql
on 4306, you can edit this by running 
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
    openssl genpkey -algorithm RSA -out app/storage/app/private_key.pem
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
- Larastan is set at Level 8:
```bash
    docker compose run composer composer larastan 
```
- I used the rules in your phpinsights:
```bash
    docker compose run composer composer insights 
```