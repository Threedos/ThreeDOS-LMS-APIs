# ThreeDOS APIs

## Overview
This is the backend API for the ThreeDOS system, built with Laravel. It follows a **3-Tier Architecture** to ensure separation of concerns, maintainability, and scalability.

## Architecture

The project is structured into three main layers:

1.  **Presentation Layer (Controllers)**
    *   Handles HTTP requests and responses.
    *   Validates input.
    *   Delegates business logic to Services.
    *   Located in: `app/Http/Controllers/api/`

2.  **Business Logic Layer (Services)**
    *   Contains the core business rules and logic.
    *   Orchestrates data flow between Controllers and Repositories.
    *   Located in: `app/Services/`

3.  **Data Access Layer (Repositories)**
    *   Handles direct interaction with the database (Eloquent models).
    *   Abstracted via Interfaces to allow for easy testing and swapping of implementations.
    *   Located in: `app/Repositories/` and `app/Interfaces/`

### Service Provider
*   **RepositoryServiceProvider**: Binds Interfaces to their concrete Repository implementations.

## Key Entities
*   **User**: System users (Students, Instructors, Heads).
*   **Role**: User roles/permissions.
*   **Council**: Student councils/groups.
*   **Task**: Assignments or duties.
*   **TaskSubmission**: Submissions for tasks.

## Installation

1.  **Clone the repository**
    ```bash
    git clone <repository-url>
    cd ThreeDOS-APIs
    ```

2.  **Install Dependencies**
    ```bash
    composer install
    ```

3.  **Environment Setup**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *   Update `.env` with your database credentials.

4.  **Database Migration**
    ```bash
    php artisan migrate
    ```

5.  **Run the Server**
    ```bash
    php artisan serve
    ```

## API Endpoints

The API provides endpoints for managing the entities listed above. Standard CRUD operations are supported via the respective Controllers.

*   `AuthController`: Login, Register, Logout.
*   `RoleController`
*   `CouncilController`
*   `UserController`
*   `TaskController`
*   `TaskSubmissionController`

## Error Handling

This project implements a **Global Exception Handler** for API routes to ensure consistent JSON responses for errors.

### Implementation Details:
*   **Handler Class**: `app/Exceptions/ApiExceptionHandler.php`
    *   This class intercepts exceptions thrown during request execution.
    *   It maps specific exceptions (e.g., `ModelNotFoundException`, `AuthenticationException`, `ValidationException`) to appropriate HTTP status codes and custom JSON messages.
*   **Registration**: The handler is registered in `bootstrap/app.php` using the `withExceptions` configuration method. It is configured to only handle exceptions for routes beginning with `api/*`.

### Error Response Format:
All API errors follow this JSON structure:

```json
{
    "status": "error",
    "message": "A descriptive error message",
    "errors": { ... } // Optional: Validation errors if applicable
}
```

### Supported Status Codes:
*   **404 Not Found**: For non-existent resources or routes.
*   **401 Unauthenticated**: For unauthorized access attempts.
*   **422 Validation Error**: For invalid input data (includes an `errors` object).
*   **500 Server Error**: For generic internal server errors (default).
