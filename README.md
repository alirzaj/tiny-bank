# tiny bank
This is a banking software that has the ability to transfer credit between two cards.

Using this application, you can see a list of users that had the most transactions in the past 10 minutes.

<!-- TOC -->
* [How to use this application?](#how-to-use-this-application)
    * [*Step 1: Copy .env.example*](#step-1-copy-envexample)
    * [*Step 2: Install the dependencies*](#step-2-install-the-dependencies)
    * [*Step 3: Generate your application's key*](#step-3-generate-your-applications-key)
    * [*Step 4: Start the necessary containers*](#step-4-start-the-necessary-containers)
    * [*Step 5: Migrate the tables*](#step-5-migrate-the-tables)
* [Database schema](#database-schema)
* [Run tests](#run-tests)
* [Useful links](#useful-links)
<!-- TOC -->

## How to use this application?
To run this application you need the following dependencies:

- `PHP 8.2+`
- `composer 2.x`
- `MySQL 8.0+`

This application is packaged with Docker out of the box. so if you have Docker installed on your machine, you can follow the below instructions to get things up and running.
### *Step 1: Copy .env.example*

Go to you terminal in the project's root directory and run the following command: (please note that **sensible defaults** for running the application using docker has been provided in .env.example. fell free to change them as you wish.)

``` bash
cp .env.example .env
```

### *Step 2: Install the dependencies*

Run this command to install all the dependencies using composer.

``` bash
docker compose run --rm composer install
```

### *Step 3: Generate your application's key*

In order to run this application, a key must be provided. this command will generate the key and put it in your .env file.

``` bash
docker compose run --rm artisan key:generate
```

### *Step 4: Start the necessary containers*

Using this command, you can start the necessary containers to run the application.

``` bash
docker compose up mysql app -d
```

### *Step 5: Migrate the tables*

At last, you need to run this command in order to create the tables in database.

``` bash
docker compose run --rm artisan migrate
```

## Database schema
Here is the schema of the application's database:

![schema](//TODO add link)

## Run tests

In order to run tests, execute the following command:

``` bash
docker compose run --rm artisan test
```

> **Note**
> It's better to have a separated database for testing.
> In order to keep things simple, this application provides you with only one database out of the box (checkout .env.example and docker-compose.yml for details).
> If you want to run tests with another database, it's recommended to create a new database (e.g: tiny-bank-testing) and set its credentials in .env file.

## Useful links

you may see a directory called Actions. This is a simple pattern to make our code more re-usable by encapsulating one unit of business' logic and to have better testability. Read more about it in [this article](https://freek.dev/1371-refactoring-to-actions).
