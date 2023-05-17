# usa-php-slim-api

This is a REST API made with PHP, Slim Framework and PostgreSQL, documented with Swagger, in a dockerized environment. With this API, you can get information about USA states and countries. 

## Setup

Before usage, rename the *.env.example* file in the root directory to *.env* and set the following environment variables:

```bash
POSTGRES_USER=myuser
POSTGRES_PASSWORD=mypassword
POSTGRES_DB=slim_api
```

## Usage

Make sure you have *Docker* and *Docker Compose* installed.
Execute the following command in the root directory:

```bash
docker-compose up --build
```
