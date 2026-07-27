# API Documentation

## Overview

**Base URL**: `https://threedos-apis-production.up.railway.app/api`

**Authentication**: Protected routes require `Authorization: Bearer <token>`.

**Common envelope**:

```json
{
  "status": "success",
  "message": "...",
  "data": null
}
```

## Authentication

### POST `/login`
Authenticate with email and password.

Request body:

```json
{
  "email": "user@example.com",
  "password": "secret"
}
```

Success response:

```json
{
  "status": "success",
  "message": "Login successfully",
  "data": {
    "user_name": "John Doe",
    "role": "Delegate",
    "access_token": "eyJ...",
    "expires_in": 3600
  }
}
```

### POST `/logout`
Revoke the current token.

### POST `/forget-password`
Send a password reset link.

### POST `/reset-password`
Reset a password using token, email, password, and confirmation.

### GET `/me`
Return the authenticated user profile.

### GET `/instance`
Return the current host name.

## Users

### GET `/users`
List users with pagination and filtering.

Query parameters:

- `pageIndex`
- `pageSize`
- `search`
- `sort`
- `role_id`
- `filter=council`

### POST `/users`
Create a user.

Required fields:

- `name`
- `email`
- `password`
- `role_id`
- `council_id`

### POST `/users/bulk`
Bulk import users from `.xlsx`, `.xls`, or `.csv`.

### GET `/users/{id}`
Fetch a user.

### PUT/PATCH `/users/{id}`
Update a user.

### DELETE `/users/{id}`
Delete a user.

### GET `/users/dashboard`
Return dashboard metrics.

## Councils

### GET `/councils`
List councils visible to the authenticated user.

### POST `/councils`
Create a council.

Required fields:

- `name`
- `description`

### GET `/councils/{id}`
Fetch a council.

### PUT/PATCH `/councils/{id}`
Update a council.

### DELETE `/councils/{id}`
Delete a council.

## Roles

### GET `/roles`
List all roles.

### GET `/roles/{id}`
Fetch a role.

## Sessions

### GET `/sessions`
List council sessions.

Query parameters:

- `pageIndex`
- `pageSize`
- `search`

### POST `/sessions`
Create a session.

Required fields:

- `title`
- `date`
- `council_id`

Optional fields:

- `description`
- `material`

### GET `/sessions/{id}`
Fetch a session.

### PUT/PATCH `/sessions/{id}`
Update a session.

### DELETE `/sessions/{id}`
Delete a session.

## Tasks

### GET `/tasks`
List tasks.

Query parameters:

- `pageIndex`
- `pageSize`
- `search`
- `filter`

### POST `/tasks`
Create a task.

Required fields typically include:

- `title`
- `description`
- `council_session_id`

### GET `/tasks/{id}`
Fetch a task.

### PUT/PATCH `/tasks/{id}`
Update a task.

### DELETE `/tasks/{id}`
Delete a task.

## Task Submissions

### GET `/task-submissions`
List submissions.

Query parameters:

- `pageIndex`
- `pageSize`
- `search`
- `filter`
- `sort`
- `task_id`
- `user_id`
- `status`

### POST `/task-submissions`
Create a submission.

Required fields:

- `task_id`
- `file`

Optional field:

- `user_id`

### GET `/task-submissions/{id}`
Fetch a submission.

### PUT/PATCH `/task-submissions/{id}`
Update a submission.

### DELETE `/task-submissions/{id}`
Delete a submission.

## Teams

### GET `/teams`
List teams.

### POST `/teams`
Create a team.

Required fields:

- `team_number`
- `council_id`

### GET `/teams/{id}`
Fetch a team.

### PUT/PATCH `/teams/{id}`
Update a team.

### DELETE `/teams/{id}`
Delete a team.

## Team Members

### GET `/team-members`
List team members.

### POST `/team-members`
Create one team member.

Required fields:

- `team_id`
- `user_id`

Optional fields:

- `rate`
- `role`
- `task`

### POST `/team-members/bulk`
Bulk add team members.

Request body:

```json
{
  "members": [
    {
      "team_id": "uuid",
      "user_id": "uuid",
      "role": "member"
    }
  ]
}
```

### GET `/team-members/{id}`
Fetch a team member.

### PUT/PATCH `/team-members/{id}`
Update a team member.

### DELETE `/team-members/{id}`
Delete a team member.

## Attendances

### GET `/attendances`
List attendance records.

Query parameters:

- `pageIndex`
- `pageSize`
- `search`
- `user_id`
- `council_id`

### POST `/attendances`
Create an attendance record.

Required fields:

- `user_id`
- `council_session_id`
- `status` with `present`, `absent`, or `late`

### POST `/attendances/bulk`
Bulk import attendance from an Excel file.

### GET `/attendances/{id}`
Fetch an attendance record.

### PUT/PATCH `/attendances/{id}`
Update an attendance record.

### DELETE `/attendances/{id}`
Delete an attendance record.

## AI Chat

### POST `/ai-chat`
Send a message to the Gemini mentor.

Request body:

```json
{ "message": "Explain this topic" }
```

## Cache

### GET `/cache/stats`
Return cache statistics.

### DELETE `/cache/endpoint`
Clear endpoint cache entries.

### DELETE `/cache/resource`
Clear cache for a named resource.

### DELETE `/cache/user/{userId}`
Clear cache for one user.

## Notifications

### GET `/notifications`
Return the authenticated user’s notifications.

## Notes

- Most GET routes use `cache.response:1800`.
- `teams` and `team-members` use `cache.response:3600`.
- Access control is enforced by policies and form request authorization.
