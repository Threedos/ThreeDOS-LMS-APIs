# Functional Requirements Document (FRD)
# ThreeDOS Management System

---

| Field | Value |
|---|---|
| **Document Version** | 1.0 |
| **Status** | Final |
| **Last Updated** | 2026-07-27 |
| **System** | ThreeDOS-APIs |
| **Scope** | Backend REST API for council operations |

## 1. Purpose

This document captures the functional behavior implemented in the ThreeDOS API. It is derived from the current Laravel controllers, request validators, policies, services, and route definitions.

## 2. Business Summary

ThreeDOS manages council-based student and training workflows. It supports:

- Authentication and profile management
- Council management
- Council session planning
- Task lifecycle management
- Task submission and grading
- Team creation and member assignment
- Attendance tracking and bulk import
- AI mentor chat for guidance
- Redis cache inspection and invalidation

The core business rule is council isolation: most data is restricted to the authenticated user’s council unless the user has a global leadership role.

## 3. Actors and Access

| Actor | Primary Needs | Access Pattern |
|---|---|---|
| Delegate | View own tasks, submit work, see attendance, use AI mentor | Own data, council-scoped read access |
| Instructor | Create and manage council work | Council-scoped read/write |
| Head | Full operational control in assigned council | Council-scoped read/write |
| HR | Manage users and attendance-related operations | Administrative read/write within rules |
| VicePresident | Global oversight across councils | Global read/write |
| President | Highest authority | Global read/write |

## 4. Functional Requirements

### 4.1 Authentication

- Users shall log in with email and password.
- Successful login shall return a JWT access token and the authenticated user’s basic profile metadata.
- Users shall log out and revoke the current token.
- Users shall request a password reset link by email.
- Users shall reset their password using a token, email, and confirmed password.
- The system shall expose a lightweight `/me` endpoint for the authenticated profile.

### 4.2 Council Management

- Authorized users shall create councils with a required name and description.
- Authorized users shall list councils visible to their role and council context.
- Authorized users shall view, update, and delete a council by identifier.
- Council writes shall invalidate related caches for councils, sessions, tasks, and teams.

### 4.3 User Management

- Authorized users shall create users with name, email, password, role, and council.
- Authorized users shall update user accounts.
- Authorized users shall delete user accounts.
- Authorized users shall bulk import users from `.xlsx`, `.xls`, or `.csv` files.
- User listing shall support pagination and role-based filtering.
- Delegate users shall only see council-scoped user data, while Vice President and President can see global users.
- User write actions shall clear user caches.

### 4.4 Council Session Management

- Authorized users shall create sessions with title, date, description, material, and council identifier.
- Users shall list sessions paginated and filtered by search text.
- Users shall view, update, and delete sessions by identifier.
- Session writes shall clear session, task, and task-submission caches.

### 4.5 Task Management

- Authorized users shall create tasks attached to a council session.
- Tasks shall include title, description, council session, optional due date, and status.
- Users shall list tasks with pagination, search, and optional filters.
- Users shall view, update, and delete tasks by identifier.
- Task status values shall be limited to `Pending`, `In Progress`, and `Completed`.
- Task writes shall clear task and task-submission caches.
- Task creation shall be blocked unless the task session belongs to the user’s council, unless the user has a global executive role.

### 4.6 Task Submissions

- Delegates shall submit files for a task.
- Task submissions shall include task identifier, file path or reference, and optional user identifier.
- Delegate users shall only submit for themselves.
- Non-global users shall only submit for tasks and users in their own council.
- Instructors, Heads, and HR users may view council submissions.
- Delegates shall view only their own submissions.
- Authorized users shall update and delete submissions.
- Submission updates may include file, grade, comment, and status.
- Submission creation shall default status to `submitted`.
- Submission writes shall clear task-submission caches.

### 4.7 Team Management

- Authorized users shall create teams with a team number and council identifier.
- Users shall list teams within their accessible scope.
- Users shall view, update, and delete teams by identifier.
- Team creation and updates shall remain council-bound unless performed by global leadership.
- Team writes shall clear team caches.

### 4.8 Team Member Management

- Authorized users shall add a user to a team.
- Authorized users shall bulk add multiple team members in one request.
- Team member records shall support role, rate, and task assignment.
- Users shall list, view, update, and remove team members.
- Team members shall be visible to their own user record and to council administrators within the same council.
- Team-member writes shall clear team-member and team caches.

### 4.9 Attendance Management

- Authorized users shall create attendance records per session.
- Attendance status shall be limited to `present`, `absent`, or `late`.
- Authorized users shall bulk import attendance from Excel files.
- Users shall list attendance records with pagination and optional filters.
- Users shall view, update, and delete attendance records.
- Attendance writes shall clear attendance caches.

### 4.10 AI Mentor

- Authenticated users shall send a message to the AI mentor.
- The AI mentor shall answer in a guided, instructional style.
- The AI mentor shall not provide complete task solutions or ready-to-submit deliverables.
- The AI mentor shall remain focused on council/session learning content.

### 4.11 Cache Operations

- Authorized users shall inspect cache statistics.
- Authorized users shall clear endpoint cache.
- Authorized users shall clear cache for a named resource.
- Authorized users shall clear cache for a named user.

## 5. Business Rules

### 5.1 Role Rules

- Delegate users are limited to their own records plus council-scoped read access where policies allow it.
- Head and Instructor users may manage most operational records inside their council.
- HR users participate in user, attendance, and team-member workflows.
- Vice President and President users have the broadest access and can act across councils.

### 5.2 Council Isolation

- Council-scoped data shall not cross council boundaries for non-global users.
- Write requests that target another council shall be rejected.
- Listing endpoints shall automatically scope results according to the authenticated user role.

### 5.3 Import Rules

- User import accepts spreadsheet and CSV uploads.
- Attendance import accepts Excel files only.
- Team-member bulk import accepts an array payload called `members`.

### 5.4 Caching Rules

- Standard GET resources are cached for 30 minutes.
- Team and team-member resources are cached for 60 minutes.
- Write actions invalidate the relevant resource caches.
- Cache keys use resource-oriented prefixes such as `endpoint_cache`, `user`, and resource names.

## 6. Success Criteria

- Users can authenticate and retrieve their profile.
- Council leadership can manage councils, sessions, tasks, teams, attendance, and submissions.
- Delegates can submit work and review their own progress.
- Global leaders can monitor across all councils.
- AI mentor requests are available to authenticated users.
- Cache operations are inspectable and invalidatable.
