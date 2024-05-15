# Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

## Installation

**Clone the repository**

If you have Git installed, you can clone the repository by running:

```shell
git clone https://github.com/Siomachkin/InvoiceService
```

Alternatively, you can download the ZIP file and extract it.

**Environment Variables**

Before starting the services, you need to set up your environment variables. Create a .env file in the root directory of the project and add the following variables:

```shell
    MONGO_HOST=mongo
    MONGO_PORT=27017
    MONGO_DATABASE=your_database_name
    REDIS_SCHEME=redis
    REDIS_HOST=redis
    REDIS_PORT=6379
    REDIS_BRPOP_TIMEOUT=5
    GOOGLE_BUCKET_NAME=your_google_bucket_name
    MAIL_KEY=your_mail_key
    MAIL_DOMAIN=your_mail_domain
    MAIL_FROM_EMAIL=your_email@example.com
```

Replace the placeholder values with your actual data.

**Build and Run Docker Containers**

Navigate to the project directory and run the following command to build and start the containers:

```shell
    docker-compose up --build
```    

This command will start all the services defined in your docker-compose.yml file.

## Accessing the Application

The main application will be accessible at [http://localhost:8000](http://localhost:8000)

Swagger UI for API documentation will be available at [http://localhost:8080](http://localhost:8080)

MongoDB is exposed on port 27017, and Redis is on port 6379. These services are for internal use by the application and workers.

## Stopping the Services

To stop the services, you can run:

```shell
docker-compose down
```

This command stops and removes all the running containers.

## Additional Services

**Mailgun**

The application uses the Mailgun service for sending emails. You will need to set up and configure this service separately.

**Google Cloud**

The application uses Google Cloud for invoice storage. This service also requires separate setup and configuration.

## Populating a Database with Test Data Using Docker

To execute the PHP script inside the Docker container, use the `docker exec` command to run the script inside the `invoiceservice-app-1` container:

```shell
docker exec invoiceservice-app-1 php /var/www/html/docker/php/DatabaseSeeder.php
```