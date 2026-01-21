# API Documentation

## Overview

**Base URL**: `https://threedos-apis-production.up.railway.app/api`

**Authentication**:
All protected routes require a Bearer Token in the `Authorization` header.
Header format: `Authorization: Bearer <your_access_token>`

**Response Format**:
Responses are generally in JSON format.
Success responses typically have a `200` or `201` status code.
Error responses typically have `401`, `403`, `404`, `422` (validation), or `500` status codes.

---

## Authentication

### Login
Authenticate a user and retrieve an access token.

- **URL**: `/login`
- **Method**: `POST`
- **Auth Required**: No
- **Body Parameters**:
  - `email` (string, required): User's email address.
  - `password` (string, required): User's password.
- **Success Response** (200):
  ```json
  {
    "status": "success",
    "message": "Login successfully",
    "data": {
      "user_name": "User Name",
      "role": "RoleName",
      "access_token": "eyJ0eX...",
      "expires_in": 3600
    }
  }
  ```
- **Error Response** (401): INVALID_CREDENTIALS

### Logout
Invalidate the current access token.

- **URL**: `/logout`
- **Method**: `POST`
- **Auth Required**: Yes
- **Success Response** (200):
  ```json
  {
    "status": "success",
    "message": "Logout successfully",
    "data": null
  }
  ```

### Get My Profile
Retrieve the authenticated user's profile information.

- **URL**: `/me`
- **Method**: `GET`
- **Auth Required**: Yes
- **Success Response** (200):
  ```json
  {
    "status": "success",
    "message": "User retrieved successfully",
    "data": {
      "name": "User Name",
      "email": "user@example.com",
      "role": "RoleName",
      "council": "CouncilName",
      "status": "active",
      "last_active": "2026-01-22 00:00:00"
    }
  }
  ```

### Forget Password
Send a password reset link.

- **URL**: `/forget-password`
- **Method**: `POST`
- **Auth Required**: No
- **Body Parameters**:
  - `email` (string, required)

### Get Current Instance
Check which instance is serving the request (Hostname).

- **URL**: `/instance`
- **Method**: `GET`
- **Auth Required**: No
- **Success Response** (200):
  ```json
  "instance-hostname"
  ```

---

## Councils

### List Councils
Retrieve a paginated list of councils.

- **URL**: `/councils`
- **Method**: `GET`
- **Auth Required**: Yes (Cached response)
- **Query Parameters**:
  - `pageIndex` (integer, optional, default: 1): Page number.
  - `pageSize` (integer, optional, default: 10): Items per page.
  - `search` (string, optional): Search term for council name.
- **Success Response** (200):
  ```json
  {
    "status": "success",
    "message": "Councils retrieved successfully",
    "data": [
      {
        "id": "uuid",
        "name": "Council Name",
        "description": "Description..."
      }
    ],
    "links": { ... },
    "meta": { ... }
  }
  ```

### Create Council
Create a new council.

- **URL**: `/councils`
- **Method**: `POST`
- **Auth Required**: Yes (Permissions: 'create' Council)
- **Body Parameters**:
  - `name` (string, required, max: 255): Name of the council.
  - `description` (string, required): Description of the council.
- **Success Response** (201):
  ```json
  {
    "status": "success",
    "message": "Council created successfully",
    "data": null
  }
  ```

### Get Council
Retrieve a specific council by ID.

- **URL**: `/councils/{id}`
- **Method**: `GET`
- **Auth Required**: Yes
- **Success Response** (200): Council object.

### Update Council
Update an existing council.

- **URL**: `/councils/{id}`
- **Method**: `PUT` / `PATCH`
- **Auth Required**: Yes
- **Body Parameters**:
  - `name` (string, optional)
  - `description` (string, optional)
- **Success Response** (200):
  ```json
  {
    "status": "success",
    "message": "Success",
    "data": null
  }
  ```

### Delete Council
Delete a council.

- **URL**: `/councils/{id}`
- **Method**: `DELETE`
- **Auth Required**: Yes
- **Success Response** (200):
  ```json
  {
    "status": "success",
    "message": "Success",
    "data": null
  }
  ```

---

## Users

### List Users
Retrieve a paginated list of users.

- **URL**: `/users`
- **Method**: `GET`
- **Auth Required**: Yes (Cached response)
- **Query Parameters**:
  - `pageIndex` (integer, optional, default: 1)
  - `pageSize` (integer, optional, default: 10)
  - `search` (string, optional): Search by name or email.
- **Success Response** (200): Standard paginated response.

### Create User
Create a new user manually.

- **URL**: `/users`
- **Method**: `POST`
- **Auth Required**: Yes (Permissions: 'create' User)
- **Body Parameters**:
  - `name` (string, required, max: 255)
  - `email` (string, required, email, max: 255)
  - `password` (string, required, min: 8)
  - `role_id` (uuid, required)
  - `council_id` (uuid, required)
- **Success Response** (201):
  ```json
  {
    "status": "success",
    "message": "User created successfully",
    "data": null
  }
  ```

### Bulk Create Users
Import users from an Excel/CSV file.
Columns expected: `name`, `email`, `password` (optional), `role` (role name), `council` (council name).

- **URL**: `/users/bulk`
- **Method**: `POST`
- **Auth Required**: Yes
- **Body Parameters**:
  - `file` (file, required): Multipart form-data.
- **Success Response** (200):
  ```json
  {
    "status": "success",
    "message": "Users imported successfully",
    "data": null
  }
  ```

### Get User
Retrieve a specific user by ID.

- **URL**: `/users/{id}`
- **Method**: `GET`
- **Auth Required**: Yes
- **Success Response** (200): User Object.

### Update User
Update an existing user.

- **URL**: `/users/{id}`
- **Method**: `PUT` / `PATCH`
- **Auth Required**: Yes
- **Body Parameters**: Any user field (`name`, `email`, `password`, `role_id`, `council_id`).
- **Success Response** (200):
  ```json
  {
    "status": "success",
    "message": "User updated successfully",
    "data": null
  }
  ```

### Delete User
Delete a user.

- **URL**: `/users/{id}`
- **Method**: `DELETE`
- **Auth Required**: Yes
- **Success Response** (200):
  ```json
  {
    "status": "success",
    "message": "User deleted successfully",
    "data": null
  }
  ```

---

## Roles

### List Roles
Retrieve all roles.

- **URL**: `/roles`
- **Method**: `GET`
- **Auth Required**: Yes
- **Success Response** (200): List of roles.

### Create Role
Create a new role.

- **URL**: `/roles`
- **Method**: `POST`
- **Auth Required**: Yes
- **Body Parameters**:
  - `name` (string, required)
- **Success Response** (201): Role Object.

### Get/Update/Delete Role
Standard resource routes:
- `GET /roles/{id}`
- `PUT /roles/{id}`
- `DELETE /roles/{id}`

---

## Tasks

### List Tasks
Retrieve tasks. Scoped to the user's council.

- **URL**: `/tasks`
- **Method**: `GET`
- **Auth Required**: Yes
- **Query Parameters**:
  - `pageIndex`, `pageSize`
  - `search` (string): Search title.
  - `filter` (string): Optional council_id filter.
- **Success Response** (200): Paginated tasks.

### Create Task
Create a new task.

- **URL**: `/tasks`
- **Method**: `POST`
- **Auth Required**: Yes
- **Body Parameters**:
  - `title` (string, required)
  - `description` (string, required)
  - `due_date` (datetime, optional)
  - `status` (string, optional)
  - `council_session_id` (uuid, required)
- **Success Response** (201): Task Object.

### Get/Update/Delete Task
Standard resource routes:
- `GET /tasks/{id}`
- `PUT /tasks/{id}`
- `DELETE /tasks/{id}`

---

## Task Submissions (Assignments)

### List Submissions
Retrieve submissions. 
- Instructors/Heads see submissions for their council.
- Delegates see their own submissions.

- **URL**: `/task-submissions`
- **Method**: `GET`
- **Auth Required**: Yes
- **Query Parameters**: `pageIndex`, `pageSize`
- **Success Response** (200): Paginated submissions.

### Create Submission
Submit a task.

- **URL**: `/task-submissions`
- **Method**: `POST`
- **Auth Required**: Yes
- **Body Parameters**:
  - `task_id` (uuid, required)
  - `file` (file, required): Multipart form-data.
- **Success Response** (201): Submission Object.

### Get/Update/Delete Submission
Standard resource routes:
- `GET /task-submissions/{id}`
- `PUT /task-submissions/{id}`
- `DELETE /task-submissions/{id}`

---

## Sessions (Council Sessions)

### List Sessions
Retrieve sessions for the user's council.

- **URL**: `/sessions`
- **Method**: `GET`
- **Auth Required**: Yes
- **Query Parameters**: `pageIndex`, `pageSize`, `search`
- **Success Response** (200): Paginated sessions (includes `attendance_count`).

### Create Session
Create a new session.

- **URL**: `/sessions`
- **Method**: `POST`
- **Auth Required**: Yes
- **Body Parameters**:
  - `title` (string, required)
  - `date` (date, required)
  - `description` (string, nullable)
  - `material` (string, nullable)
  - `council_id` (uuid, required)
- **Success Response** (201): Session Object.

### Get/Update/Delete Session
Standard resource routes:
- `GET /sessions/{id}`
- `PUT /sessions/{id}`
- `DELETE /sessions/{id}`

---

## Attendances

### List Attendances
Retrieve attendances for the council.

- **URL**: `/attendances`
- **Method**: `GET`
- **Auth Required**: Yes
- **Query Parameters**: `pageIndex`, `pageSize`
- **Success Response** (200): Paginated list.

### Create Attendance
Record a single attendance.

- **URL**: `/attendances`
- **Method**: `POST`
- **Auth Required**: Yes
- **Body Parameters**:
  - `user_id` (uuid, required)
  - `council_session_id` (uuid, required)
  - `status` (string, required, enum: `present`, `absent`, `late`)
- **Success Response** (201): Attendance Object.

### Bulk Create Attendance
Import attendance from file.
Columns expected: `email` (user email), `name` (session title), `date`, `status`.

- **URL**: `/attendances/bulk`
- **Method**: `POST`
- **Auth Required**: Yes
- **Body Parameters**:
  - `file` (file, required)
- **Success Response** (200):
  ```json
  {
    "status": "success",
    "message": "Success",
    "data": null
  }
  ```

### Get/Update/Delete Attendance
Standard resource routes:
- `GET /attendances/{id}`
- `PUT /attendances/{id}`
- `DELETE /attendances/{id}`

---

## Teams Management

### List Teams
- **URL**: `/teams`
- **Method**: `GET`
- **Auth Required**: Yes
- **Success Response** (200): List of teams.

### Create Team
- **URL**: `/teams`
- **Method**: `POST`
- **Auth Required**: Yes
- **Body Parameters**:
  - `team_number` (string, required)
  - `council_id` (uuid, required)
- **Success Response** (201): Team ID.

### Team Members Actions
Bulk add or manage team members.

- **POST** `/team-members`: Bulk add members.
  - **Body**: `members` (array of objects with `team_id`, `user_id`, `rate`, `role` (member/leader/co-leader), `task`).
- **GET/PUT/DELETE** `/team-members/{id}`: Resource management.

---

## Notifications

### Get Notifications
Get notifications for the current user.

- **URL**: `/notifications`
- **Method**: `GET`
- **Auth Required**: Yes
- **Success Response** (200): List of notifications (Cached for 30 mins).

---

## Cache Management (Admin)

### Get Cache Stats
- **URL**: `/cache/stats`
- **Method**: `GET`
- **Success Response** (200):
  ```json
  { "status": "success", "data": { ... } }
  ```

### Clear Endpoint Cache
- **URL**: `/cache/endpoint`
- **Method**: `DELETE`
- **Success Response** (200): Clears all endpoint caches.

### Clear Resource Cache
- **URL**: `/cache/resource`
- **Method**: `DELETE`
- **Body Parameters**:
  - `resource` (string, required): One of `users`, `councils`, `tasks`, `sessions`, `attendances`, `roles`, `task-submissions`.

### Clear User Cache
- **URL**: `/cache/user/{userId}`
- **Method**: `DELETE`
