
# Laravel task-API

Notes API service to create, read, update & delete notes. User can sign up & login into the system, also he can add tags to notes and search his notes by a specific tags.




## Tech Stack


**Server:** Laravel 10.31.0, PHP 8.1, MySQL-server 8.0, Docker-compose

## Run Locally

Clone the project

```bash
  git clone https://github.com/OleGK4/task-api.git
```

Go to the project directory

```bash
  cd task-api
```

Install composer

```bash
  composer install
```

Set the alias

```bash
  alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
```

Start the project in detached mode

```bash
  sail up -d
```

Migrate your database

```bash
  sail artisan migrate:fresh
```

Seed the database

```bash
  sail artisan db:seed
```
## Tests

To test you can use Swagger documentation *api-docs* from *storage/api-docs/*.

 Also you can use Postman collection *api-testing.postman_collection* with environment *api-testing.postman_environment* from the same directory.



## Documentation

- **[MySQL Database building with docker-compose](https://blog.christian-schou.dk/creating-and-running-a-mysql-database-with-docker-compose/)**
- **[Setting up laravel project using docker](https://ianclemence.medium.com/setting-up-laravel-project-using-docker-step-by-step-guide-7c5720fbc2c8)**
- **[Official Laravel documentation 10.x](https://laravel.com/docs/10.x/installation)**
- **[Laravel Sail](https://laravel.com/docs/10.x/sail)**
- **[Swagger Integration](https://websolutionstuff.com/post/how-to-integrate-swagger-in-laravel-10)**
- **[Swagger documentation](https://zircote.github.io/swagger-php/guide/common-techniques.html)**

