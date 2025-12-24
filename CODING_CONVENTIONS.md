# Coding Conventions

## 1. Introduction

This document outlines the coding standards, conventions, and architectural patterns for the Troop Tracker project. Adhering to these guidelines is crucial for ensuring code consistency, readability, and long-term maintainability as we incrementally refactor the application.

## 2. General Principles

Our development philosophy is guided by a few core tenets:

- **Simplicity and Readability:** *Simple is better than complex*, and *readability counts*. Write code that is explicit, straightforward, and easy for others to understand. If an implementation is hard to explain, it's likely a bad idea.
- **Follow The "Obvious" Way:** *There should be one—and preferably only one—obvious way to do it.* When a specific rule is not defined here, default to the standard conventions and best practices of the Laravel framework.
- **Pragmatic Refactoring:** We follow the Strangler Fig pattern for modernization. This embraces the idea that *now is better than never*, allowing us to make steady, incremental progress. However, we also recognize that *never is often better than right now*, which is why we plan our refactoring with patterns like [ADR](#6-architecture) instead of rushing a full rewrite.
- **Handle Errors Explicitly:** *Errors should never pass silently.* All new and refactored code should include robust error handling and validation.
- **Embrace SOLID:** Our architectural choices are guided by the [SOLID principles](https://en.wikipedia.org/wiki/SOLID) to create maintainable and scalable code. You will see these principles reflected in the patterns below.

## 3. Naming Conventions

We enforce a strict set of casing rules to maintain consistency across the codebase.

| Element                                       | Convention   | Example                               |
| --------------------------------------------- | ------------ | ------------------------------------- |
| Classes (Controllers, Models, Services, etc.) | `PascalCase` | `LoginSubmitController`, `Trooper`    |
| Functions & Class Methods                     | `camelCase`  | `function handleRequest(Request $data)` |
| Function/Method Parameters                    | `snake_case` | `function myMethod(string $user_name)` |
| Local Variables                               | `snake_case` | `$local_variable = 'value';`          |
| Class Properties/Variables                    | `snake_case` | `private string $class_property;`     |

## 4. Coding Style

### 4.1. Function & Method Signatures

All function and method signatures **must** include explicit, scalar type hints for parameters and return types wherever possible.

```php
public function findUserById(int $user_id): ?User
{
    // ...
}
```

### 4.2. Line and Function Length

- **Line Length:** Aim to keep lines of code under **100 characters** for better readability.
- **Function/Method Length:** Functions and methods should be focused and concise, ideally not exceeding **30 lines**. If a method grows beyond this, consider refactoring it into smaller, private helper methods.

## 5. Database Conventions

To leverage Laravel's Eloquent ORM conventions and simplify relationship definitions, all database schema elements **must** follow these naming rules. Adhering to these conventions allows Eloquent to automatically infer relationships without requiring explicit key definitions in your models.

| Element | Convention | Example |
| :--- | :--- | :--- |
| **Table Names** | Plural, `snake_case` | `troopers`, `event_troopers` |
| **Column Names** | `snake_case` | `first_name`, `event_date` |
| **Primary Key** | `id` | An auto-incrementing integer named `id`. |
| **Foreign Keys** | Singular table name + `_id` | A `posts` table has a `user_id` column to link to the `users` table. |
| **Pivot Tables** | Singular table names, alphabetized, joined by `_` | `role_user` for a `roles` and `users` relationship. |
| **Timestamps** | `created_at`, `updated_at` | For Eloquent's automatic timestamping. |
| **Soft Deletes** | `deleted_at` | For Eloquent's `SoftDeletes` trait. |

## 6. Architecture

For new and refactored features, we will adopt the **Action-Domain-Responder (ADR)** pattern. This pattern helps to separate concerns and organize application logic cleanly.

This pattern is a practical application of several SOLID principles:

-   **Single Responsibility Principle (S):** Each component has one job.
    -   The **Action**'s responsibility is to interpret the HTTP request and orchestrate the call to the Domain. In Laravel, this is our invokable Controller.
    -   The **Domain**'s responsibility is to execute the core business logic. In our application, this layer consists of **Eloquent Models** and dedicated **Service classes** that are completely unaware of the web context.
    -   The **Responder**'s responsibility is to build the HTTP response from the data the Domain returns. This will typically be a **Blade view**, a **JSON response**, or a redirect.

-   **Dependency Inversion Principle (D):** It inverts the traditional flow of control.
    -   High-level components (Actions) depend on abstractions, not on low-level components (Domain). The Domain logic doesn't know or care that it was called by a web controller; it could just as easily be called from an Artisan command or a queue job. This decoupling makes our business logic (the most valuable part of our code) more reusable and easier to test in isolation.

## 7. Controllers

### 7.1. Invokable (Single-Action) Controllers

For controller actions that perform a single, specific task (e.g., submitting a form, displaying a page), prefer using invokable (single-action) controllers. This aligns with the Single Responsibility Principle and keeps our routing and controller logic clean and focused.

**Example:**

```php
// app/Http/Controllers/LoginSubmitController.php
class LoginSubmitController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        // Handle login submission...
    }
}

// routes/web.php
Route::post('/login', LoginSubmitController::class);
```

## 8. Testing (PHPUnit)

A robust test suite is essential for our refactoring efforts. All new features and refactored code must be accompanied by tests.

### 8.1. Test Method Naming

All test method names must be `snake_cased` and begin with the `test_` prefix. The name should clearly describe what the test is asserting.

```php
public function test_invoke_handles_unapproved_user(): void
{
    // ...
}
```

### 8.2. Subject Under Test

When instantiating the class being tested, the variable name **must** be `$subject`.

```php
public function test_something(): void
{
    $subject = new MyAwesomeService();
    $result = $subject->doSomething();
    $this->assertTrue($result);
}
```

### 8.3. Mocking

When creating mocks with PHPUnit's built-in mocking library, use the `expects()` and `method()` chain for setting up expectations. This provides a clear, readable format.

```php
// Example for a mock of Illuminate\Http\Request
$request_mock = $this->createMock(Request::class);

$request_mock->expects($this->once())
    ->method('getHeaderLine')
    ->with('Accept')
    ->willReturn('application/json');
```