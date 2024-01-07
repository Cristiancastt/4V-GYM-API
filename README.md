# 4V GYM API

This is the official specification for the 4V GYM API. The API is designed to manage activities within the GYM, including information about activity types, monitors, and individual activities.

## Table of Contents

- [Introduction](#introduction)
- [Server Information](#server-information)
- [API Endpoints](#api-endpoints)
  - [Activity Types](#activity-types)
  - [Monitors](#monitors)
  - [Activities](#activities)
- [Examples](#examples)

## Introduction

This API is built using Symfony. It provides functionalities related to managing activity types, monitors, and individual activities within the 4V GYM.
Important notes
> [!IMPORTANT]
> **PHP:** Make sure to have PHP installed on your machine. Symfony 5.3 (the version used in the specification) requires PHP 7.2.5 or higher. You can download PHP from [php.net](https://www.php.net/downloads).

> [!IMPORTANT]
> **Composer:** Composer is the PHP dependency manager and is used to install Symfony project dependencies. You can download Composer from [getcomposer.org](https://getcomposer.org/download/).

> [!IMPORTANT]
> **Symfony CLI:** Symfony CLI provides useful tools for working with Symfony applications. You can install it by following the instructions at [symfony.com/download](https://symfony.com/download).

> [!IMPORTANT]
> **MySQL or any other compatible database management system:** You need to have a database server installed and configured. Database configuration is done through the `.env` file in your Symfony project.

## Steps
### 1. Clone the Repository
```bash
git clone https://github.com/Cristiancastt/4V-GYM-API
cd 4V-GYM-API
```
### 2. Install all dependencies
```bash
composer install
```
### 3. Configure Database
Edit the .env file to set up the connection to your database.
# .env
DATABASE_URL=mysql://user:password@127.0.0.1:3306/database_name
### 3. Create Database
```bash
php bin/console doctrine:database:create
```
### 4. Appy Migrations
```bash
php bin/console doctrine:migrations:migrate
```
### 5. Start Server
```bash
symfony server:start
```








## Server Information

- Base URL: `https://localhost:8000/`
- Contact: [Cristian Castiñeiras](mailto:cristianaranacastineiras@gmail.com)
- License: [The Unlicense](https://es.wikipedia.org/wiki/Unlicense)


#### Get Activity Types

- **Endpoint:** `/activity-types`
- **Method:** `GET`
- **Summary:** Finds Activities
- **Description:** Retrieve a list of available activity types in the GYM.
- **Responses:**
  - `200`: Successful operation. Returns an array of [ActivityType](#activitytype).
  - `400`: Any problem in the server.

### Monitors

#### Get Monitors

- **Endpoint:** `/monitors`
- **Method:** `GET`
- **Summary:** Find the available monitors
- **Description:** Retrieve a list of available monitors in the GYM.
- **Responses:**
  - `200`: Successful operation. Returns an array of [Monitor](#monitor).
  - `400`: Any problem in the server.

#### Add Monitor

- **Endpoint:** `/monitors`
- **Method:** `POST`
- **Summary:** Add a new Monitor to the GYM
- **Description:** Add a new monitor to the GYM.
- **Request Body:** [Monitor](#monitor)
- **Responses:**
  - `200`: Successful operation. Returns the added [Monitor](#monitor).
  - `400`: Any error like validations. Returns an [Error](#error).

#### Update Monitor

- **Endpoint:** `/monitors/{monitorId}`
- **Method:** `PUT`
- **Summary:** Update an existing monitor
- **Description:** Update an existing monitor by ID.
- **Parameters:**
  - `monitorId` (path): Monitor ID to update.
- **Request Body:** [Monitor](#monitor)
- **Responses:**
  - `200`: Successful operation. Returns the updated [Monitor](#monitor).
  - `404`: Monitor not found.
  - `400`: Any other error, like validations. Returns an [Error](#error).

#### Delete Monitor

- **Endpoint:** `/monitors/{monitorId}`
- **Method:** `DELETE`
- **Summary:** Deletes a Monitor
- **Description:** Delete a monitor by ID.
- **Parameters:**
  - `monitorId` (path): Monitor ID to delete.
- **Responses:**
  - `404`: Monitor not found.
  - `400`: Any other error, like validations. Returns an [Error](#error).

### Activities

#### Get Activities

- **Endpoint:** `/activities`
- **Method:** `GET`
- **Summary:** Find the available activities
- **Parameters:**
  - `date` (query): Date to filter, the format is dd-MM-yyyy.
- **Responses:**
  - `200`: Successful operation. Returns an array of [Activity](#activity).
  - `400`: Any problem in the server.

#### Add Activity

- **Endpoint:** `/activities`
- **Method:** `POST`
- **Summary:** Add a new Activity to the GYM
- **Description:** Add a new activity to the GYM.
- **Request Body:** [ActivityNew](#activitynew)
- **Responses:**
  - `200`: Successful operation. Returns the added [Activity](#activity).
  - `400`: Any error like validations. Returns an [Error](#error).

#### Update Activity

- **Endpoint:** `/activities/{activityId}`
- **Method:** `PUT`
- **Summary:** Update an existing Activity
- **Description:** Update an existing activity by ID.
- **Parameters:**
  - `activityId` (path): Activity ID to update.
- **Request Body:** [ActivityNew](#activitynew)
- **Responses:**
  - `200`: Successful operation. Returns the updated [Activity](#activity).
  - `404`: Activity not found.
  - `400`: Any other error, like validations. Returns an [Error](#error).

#### Delete Activity

- **Endpoint:** `/activities/{activityId}`
- **Method:** `DELETE`
- **Summary:** Deletes an Activity
- **Description:** Delete an activity by ID.
- **Parameters:**
  - `activityId` (path): Activity ID to delete.
- **Responses:**
  - `404`: Activity not found.
  - `400`: Any other error, like validations. Returns an [Error](#error).

## Examples

### Activity Types

#### Get Activity Types

- **Example:** `GET: https://localhost:8000/activity-types`

### Monitors

#### Get Monitors

- **Example:** `GET: https://localhost:8000/monitors`

#### Add Monitor

- **Example:** `POST: https://localhost:8000/monitors`
  - Request Body:
    ```json
    {
      "name": "Miguel Goyena",
      "email": "miguel_goyena@cuatrovientos.org",
      "phone": "654767676",
      "photo": "http://foto.com/miguel.goyena"
    }
    ```

#### Update Monitor

- **Example:** `PUT: https://localhost:8000/monitors/{id}`
  - Request Body:
    ```json
    {
      "name": "Miguel Goyenaaaaaaa",
      "email": "miguel_goyena@cuatrovientos.org",
      "phone": "654767676",
      "photo": "http://foto.com/miguel.goyena"
    }
    ```

#### Delete Monitor

- **Example:** `DELETE: https://localhost:8000/monitors/{id}`

### Activities

#### Get Activities

- **Example:** `GET: https://localhost:8000/activities?date_param=19-12-2023`

#### Add Activity

- **Example:** `POST: https://localhost:8000/activities`
  - Request Body:
    ```json
    {
      "activity_type_id": 1,
      "date_start": "2023-01-01 09:00:00",
      "date_end": "2023-01-01 10:30:00",
      "monitors": [
        {"id": 2},
        {"id": 1}
      ]
    }
    ```

#### Update Activity

- **Example:** `PUT: https://localhost:8000/activities/{id}`
  - Request Body:
    ```json
    {
      "date_start": "2023-12-19 13:30:00",
      "date_end": "2023-12-19 15:00:00",
      "activity_type_id": 1,
      "monitors": [
        {"id": 1},
        {"id": 2}
      ]
    }
    ```

#### Delete Activity

- **Example:** `DELETE: https://myserver/v1/activities/{id}`

