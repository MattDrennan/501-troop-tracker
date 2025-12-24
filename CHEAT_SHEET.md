# Laravel Artisan Cheat Sheet

This section provides a quick reference for common `php artisan` commands used during development.

## Generating Code (`make:*`)

Use the `make` commands to quickly scaffold new classes. Many commands can be combined with flags for convenience.

| Command | Description |
|---|---|
| `php artisan make:model Trooper -mf` | Creates a `Trooper` model (`-m` also creates a migration, `-f` creates a factory). |
| `php artisan make:controller LoginSubmitController --invokable` | Creates a single-action controller, perfect for the ADR pattern. |
| `php artisan make:request StoreTrooperRequest` | Creates a new form request class for validation in `app/Http/Requests`. |
| `php artisan make:migration create_squads_table` | Creates a new database migration file. |
| `php artisan make:factory SquadFactory` | Creates a new model factory. |
| `php artisan make:seeder UnitSeeder` | Creates a new database seeder class. |
| `php artisan make:test UserAuthenticationTest` | Creates a new feature test file in `tests/Feature`. |
| `php artisan make:test TrooperRepositoryTest --unit` | Creates a new unit test file in `tests/Unit`. |
| `php artisan code:models --table=units` | (From `reliese/laravel`) Generates a model from an existing `units` table. |

## Database Migrations & Seeding

These commands are used to manage your database schema and test data.

| Command | Description |
|---|---|
| `php artisan migrate` | Runs all outstanding database migrations. |
| `php artisan migrate:fresh` | Drops all tables and re-runs all migrations from scratch. |
| `php artisan migrate:fresh --seed` | Drops all tables, re-runs migrations, and then runs the database seeders. |
| `php artisan migrate:rollback` | Rolls back the last batch of migrations. |
| `php artisan migrate:rollback --step=3` | Rolls back the last 3 batches of migrations. |
| `php artisan db:seed` | Runs all database seeders. |
| `php artisan db:seed --class=TrooperSeeder` | Runs a specific seeder class. |

## Testing

Use these commands to run your PHPUnit test suite.

| Command | Description |
|---|---|
| `php artisan test` | Runs the entire test suite (feature and unit). |
| `php artisan test --filter=UserAuthenticationTest` | Runs all tests within a specific file. |
| `php artisan test --filter=test_user_can_log_in` | Runs a specific test method by name. |

## Development & Debugging

| Command | Description |
|---|---|
| `php artisan tinker` | Starts an interactive shell (REPL) to run arbitrary code in your application. |
| `php artisan route:list` | Lists all registered routes in your application. |
| `php artisan config:clear` | Clears the configuration cache. Run this if your `.env` changes aren't taking effect. |
| `php artisan cache:clear` | Flushes the application cache. |