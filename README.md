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
