# Software Requirements Specification (SRS) - ThreeDOS Management System

**Version:** 3.0
**Status:** Final
**Last Updated:** 2026-07-27

## 1. Purpose

This document summarizes the implemented backend requirements for ThreeDOS-APIs. It focuses on the functional, security, and interface requirements exposed by the Laravel application.

## 2. System Overview

ThreeDOS-APIs is a RESTful backend for council-based operations. The application uses JWT authentication, Redis caching, Laravel policies, request validation, service/repository layering, and JSON responses.

## 3. External Interfaces

### 3.1 Primary API Base

- Base URL: `https://threedos-apis-production.up.railway.app/api`

### 3.2 Auth Header

- Protected routes require `Authorization: Bearer <token>`.

### 3.3 Common Response Shape

```json
{
  "status": "success",
  "message": "...",
  "data": null
}
```

## 4. Functional Requirements

### 4.1 Authentication

- Users shall log in with email and password.
- Users shall receive a JWT token on successful login.
- Users shall log out and revoke the token.
- Users shall request password reset emails.
- Users shall reset passwords with token confirmation.
- Users shall read their profile through `/me`.

### 4.2 Council Management

- Authorized users shall create, list, view, update, and delete councils.
- Council access shall be scope-aware by role.

### 4.3 User Management

- Authorized users shall create, list, view, update, delete, and bulk import users.
- Dashboard data shall be available through a dedicated users dashboard route.

### 4.4 Session Management

- Authorized users shall create, list, view, update, and delete sessions.
- Session creation and update shall require a matching council for non-global users.

### 4.5 Task Management

- Authorized users shall create, list, view, update, and delete tasks.
- Task status shall be constrained to the lifecycle states defined by the application.

### 4.6 Task Submissions

- Delegates shall create submissions for tasks.
- Council admins shall view council submissions.
- Submission records shall support grading and comments.

### 4.7 Teams and Team Members

- Authorized users shall create, list, view, update, and delete teams.
- Authorized users shall create, list, view, update, delete, and bulk import team members.

### 4.8 Attendance

- Authorized users shall create, list, view, update, delete, and bulk import attendance.

### 4.9 AI Mentor

- Authenticated users shall send prompts to the Gemini-backed mentor.
- The mentor shall guide learning instead of generating final task solutions.

### 4.10 Cache Administration

- Authorized users shall inspect cache stats.
- Authorized users shall clear endpoint, resource, and user cache scopes.

## 5. Requirements by Role

| Role | Scope |
|---|---|
| Delegate | Own submissions and council-scoped reads where allowed |
| Instructor | Council operational control |
| Head | Council operational control with broader authority |
| HR | User, attendance, and team-member workflows |
| VicePresident | Global access across councils |
| President | Global access across councils |

## 6. Security Requirements

- JWT shall protect private API routes.
- Policies shall enforce authorization at the resource level.
- Request classes shall reject invalid payloads and unsafe council mismatches.
- Council-scoped data shall not be writable across council boundaries for non-global users.
- The AI mentor shall refuse full task completion requests.

## 7. Data and Validation Requirements

- User creation requires name, email, password, role, and council.
- Council creation requires name and description.
- Session creation requires title, date, and council.
- Task creation requires title, description, and council session.
- Task submission requires task and file.
- Attendance requires user, session, and status.

## 8. Performance and Caching Requirements

- Standard GET endpoints shall use response caching.
- Teams and team-members shall use longer-lived caches.
- Write operations shall invalidate related cache keys.
- Redis shall support cache statistics and targeted invalidation.

## 9. Error Handling Requirements

- The API shall return `401` for authentication failures.
- The API shall return `403` for authorization failures.
- The API shall return `404` when a resource is missing.
- The API shall return `422` when validation fails.
- The API shall return `200` or `201` for successful requests.

## 10. Traceability Notes

- PRD describes the product intent.
- FRD describes the functional behavior.
- SDD describes the implementation design.
- API documentation describes the request and response surface.
