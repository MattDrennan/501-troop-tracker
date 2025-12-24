# Composer Dependencies

This document outlines the Composer dependencies used in the Troop Tracker project. It is intended to help developers understand the purpose of each package.

## Production Dependencies (`require`)

These packages are required for the application to run in a production environment.

| Package | Purpose |
|---|---|
| `php` | The underlying programming language version for the project. |
| `benhall14/php-calendar` | A simple library used to generate HTML calendars for displaying events or schedules. |
| `google/apiclient` | The official Google API Client Library for PHP. It is likely used for integrating with Google services like Google Calendar, Sheets, or Drive. |
| `kreait/firebase-php` | The Firebase Admin SDK for PHP. This is used for server-side integration with Firebase services (e.g., Authentication, Firestore). |
| `laravel/framework` | The core of the application. Laravel is the PHP framework upon which the application is built. |
| `laravel/tinker` | An interactive REPL (Read-Eval-Print Loop) for Laravel. Allows developers to interact with the application and its objects via the command line. |
| `phpmailer/phpmailer` | A robust email sending library for PHP. Used for sending application emails and notifications. |
| `spatie/calendar-links` | A library to generate "Add to Calendar" links for various calendar services (Google, iCal, Outlook). This enhances user experience for events. |

---

## Development Dependencies (`require-dev`)

These packages are only used for local development and testing. They are not installed in the production environment.

| Package | Purpose |
|---|---|
| `fakerphp/faker` | A library for generating fake data. It is essential for populating the database with test data (database seeding) for development and automated testing. |
| `kitloong/laravel-migrations-generator` | A tool to generate Laravel migration files from an existing database schema. Useful for creating migrations from a database that was designed visually. |
| `laravel/pail` | A command-line tool for tailing Laravel application logs in real-time with helpful filtering capabilities. |
| `laravel/pint` | An opinionated PHP code style fixer built on top of PHP-CS-Fixer. It helps maintain a consistent coding style across the project. |
| `laravel/sail` | A command-line interface for managing Laravel's default Docker development environment, simplifying the local development setup. |
| `mockery/mockery` | A mock object framework used in unit tests. It allows for the creation of test doubles (mocks) to isolate code during testing. |
| `nunomaduro/collision` | Provides beautiful and detailed error reporting for command-line PHP applications, improving the debugging experience. |
| `phpunit/phpunit` | The standard testing framework for PHP. It is used to write and run unit, feature, and integration tests for the application. |
| `reliese/laravel` | A code generation tool that can create Eloquent Models and other related classes directly from the database schema, speeding up development. |

---

## Composer Scripts

The `composer.json` file also contains several scripts to automate common tasks:

*   **`setup`**: A convenient script to set up the project for a new developer. It installs Composer and NPM dependencies, creates the `.env` file, generates an application key, and runs database migrations.
*   **`dev`**: Starts all the necessary services for local development concurrently: the PHP server, the queue worker, the log trailer (`pail`), and the Vite asset bundler.
*   **`test`**: Runs the application's automated test suite using PHPUnit.

For more details on other scripts, refer to the `scripts` section in the `composer.json` file.

## A Note on `reliese/laravel`

You specifically highlighted the `reliese/laravel` package.

*   **Package**: `reliese/laravel`
*   **Purpose**: This is a development tool that helps accelerate the creation of boilerplate code. It can inspect your database schema and automatically generate corresponding Eloquent Models, including properties, relationships (`HasOne`, `BelongsTo`, etc.), and PHPDoc blocks. This saves significant time during development, especially when dealing with a large number of database tables. It is a `dev` dependency because it's not needed for the application to run in production.
