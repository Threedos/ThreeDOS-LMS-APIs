# API Documentation

## Overview
This documentation details the API endpoints for the ThreeDOS application.
All protected routes require a Bearer Token in the `Authorization` header.

**Base URL**: `http://localhost:8000/api` (or your deployed domain)

## Authentication

### Login
Authenticates a user and returns a JWT token.

- **URL**: `/login`
- **Method**: `POST`
- **Auth**: None
- **Body Parameters**:
  - `email` (string, required): User's email
  - `password` (string, required): User's password
- **Returns**:
  - `200 OK`: 
    ```json
    {
      "access_token": "eyJ0eX...",
      "expires_in": 3600
    }
    ```
  - `401 Unauthorized`: `{"error": "Invalid credentials"}`

### Logout
Revokes the current user's token.

- **URL**: `/logout`
- **Method**: `POST`
- **Auth**: Bearer Token
- **Returns**:
  - `200 OK`: `{"message": "Token revoked"}`


---

## Users

### List Users
Get a list of all users.

- **URL**: `/users`
- **Method**: `GET`
- **Auth**: Bearer Token
- **Returns**: `200 OK` (JSON array of user objects)

### Create User
Create a new user.

- **URL**: `/users`
- **Method**: `POST`
- **Auth**: Bearer Token
- **Body Parameters**:
  - `name` (string)
  - `email` (string, unique)
  - `password` (string)
  - `role_id` (uuid)
  - `council_id` (uuid)
- **Returns**: `201 Created` - `"User created successfully"`

### Get User
Get a specific user by ID.

- **URL**: `/users/{id}`
- **Method**: `GET`
- **Auth**: Bearer Token
- **Returns**: `200 OK` (User object)

### Update User
Update an existing user.

- **URL**: `/users/{id}`
- **Method**: `PUT` / `PATCH`
- **Auth**: Bearer Token
- **Body Parameters** (Any of the following):
  - `name`, `email`, `password`, `role_id`, `council_id`
- **Returns**: `200 OK` - `{"message": "User updated successfully"}`

### Delete User
Delete a user.

- **URL**: `/users/{id}`
- **Method**: `DELETE`
- **Auth**: Bearer Token
- **Returns**: `200 OK` - `{"message": "User deleted successfully"}`

---

## Roles

### List Roles
Get a list of all roles.

- **URL**: `/roles`
- **Method**: `GET`
- **Auth**: Bearer Token
- **Returns**: `200 OK` (JSON array of roles)

### Create Role
Create a new role.

- **URL**: `/roles`
- **Method**: `POST`
- **Auth**: Bearer Token
- **Body Parameters**:
  - `name` (string, required)
- **Returns**: `201 Created` (Role object)

### Get Role
Get a specific role.

- **URL**: `/roles/{id}`
- **Method**: `GET`
- **Auth**: Bearer Token
- **Returns**: `200 OK` (Role object)

### Update Role
Update a role.

- **URL**: `/roles/{id}`
- **Method**: `PUT` / `PATCH`
- **Auth**: Bearer Token
- **Body Parameters**:
  - `name` (string)
- **Returns**: `200 OK` - `{"message": "Role updated successfully"}`

### Delete Role
Delete a role.

- **URL**: `/roles/{id}`
- **Method**: `DELETE`
- **Auth**: Bearer Token
- **Returns**: `200 OK` - `{"message": "Role deleted successfully"}`

---

## Councils

### List Councils
Get a list of councils.

- **URL**: `/councils`
- **Method**: `GET`
- **Auth**: Public (for `index`)
- **Query Parameters**:
  - `pageIndex` (integer, required)
  - `pageSize` (integer, required)
  - `search` (string, optional)
- **Returns**: `200 OK` (JSON list)

### Get Council
Get a specific council.

- **URL**: `/councils/{id}`
- **Method**: `GET`
- **Auth**: Public
- **Returns**: `200 OK` (Council object)

### Create Council
Create a new council.

- **URL**: `/councils`
- **Method**: `POST`
- **Auth**: Bearer Token
- **Body Parameters**:
  - `name` (string, required, max:255)
  - `description` (string, required)
- **Returns**: `201 Created` - `{"message": "Council created successfully"}`

### Update Council
Update a council.

- **URL**: `/councils/{id}`
- **Method**: `PUT` / `PATCH`
- **Auth**: Bearer Token
- **Body Parameters**:
  - `name`, `description`
- **Returns**: `200 OK` - `{"message": "Council updated successfully"}`

### Delete Council
Delete a council.

- **URL**: `/councils/{id}`
- **Method**: `DELETE`
- **Auth**: Bearer Token
- **Returns**: `200 OK` - `{"message": "Council deleted successfully"}`

---

## Tasks

### List Tasks
Get a list of tasks.

- **URL**: `/tasks`
- **Method**: `GET`
- **Auth**: Bearer Token
- **Returns**: `200 OK` (JSON list)

### Create Task
Create a new task.

- **URL**: `/tasks`
- **Method**: `POST`
- **Auth**: Bearer Token
- **Body Parameters**:
  - `title` (string)
  - `description` (text)
  - `due_date` (datetime)
  - `status` (string)
  - `council_id` (uuid)
- **Returns**: `201 Created` (Task object)

### Get Task
Get a specific task.

- **URL**: `/tasks/{id}`
- **Method**: `GET`
- **Auth**: Bearer Token
- **Returns**: `200 OK` (Task object)

### Update Task
Update a task.

- **URL**: `/tasks/{id}`
- **Method**: `PUT` / `PATCH`
- **Auth**: Bearer Token
- **Body Parameters**:
  - `title`, `description`, `due_date`, `status`, `council_id`
- **Returns**: `200 OK` - `{"message": "Task updated successfully"}`

### Delete Task
Delete a task.

- **URL**: `/tasks/{id}`
- **Method**: `DELETE`
- **Auth**: Bearer Token
- **Returns**: `200 OK` - `{"message": "Task deleted successfully"}`

---

## Task Submissions

### List Submissions
Get a list of task submissions.

- **URL**: `/task-submissions`
- **Method**: `GET`
- **Auth**: Bearer Token
- **Returns**: `200 OK` (JSON list)

### Create Submission
Create a new task submission.

- **URL**: `/task-submissions`
- **Method**: `POST`
- **Auth**: Bearer Token
- **Body Parameters**:
  - `task_id` (uuid)
  - `user_id` (uuid)
  - `file` (string/path, or upload depending on implementation, likely string based on fillable)
  - `status` (string)
  - `grade` (string/int)
  - `comment` (text)
  - `council_id` (uuid)
- **Returns**: `201 Created` (Submission object)

### Get Submission
Get a specific submission.

- **URL**: `/task-submissions/{id}`
- **Method**: `GET`
- **Auth**: Bearer Token
- **Returns**: `200 OK` (Submission object)

### Update Submission
Update a submission.

- **URL**: `/task-submissions/{id}`
- **Method**: `PUT` / `PATCH`
- **Auth**: Bearer Token
- **Body Parameters**:
  - `grade`, `comment`, `status`, `file`, etc.
- **Returns**: `200 OK` - `{"message": "TaskSubmission updated successfully"}`

### Delete Submission
Delete a submission.

- **URL**: `/task-submissions/{id}`
- **Method**: `DELETE`
- **Auth**: Bearer Token
- **Returns**: `200 OK` - `{"message": "TaskSubmission deleted successfully"}`
