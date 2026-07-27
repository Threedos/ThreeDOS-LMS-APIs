# Product Requirements Document (PRD)
# ThreeDOS Management System

---

| Field | Value |
|---|---|
| **Document Version** | 4.0 |
| **Status** | Final |
| **Last Updated** | 2026-07-27 |
| **Owner** | ThreeDOS Product Team |
| **Base URL** | `https://threedos-apis-production.up.railway.app/api` |

## 1. Product Summary

ThreeDOS is a backend-first council management API for student organizations and training programs. It centralizes the operational work that is usually spread across chat apps, spreadsheets, and manual approvals.

The product exists to answer three operational questions:

- What happened in a council session?
- Who was assigned work, who submitted, and who was graded?
- Who can see or modify the data across councils?

## 2. Why the Product Exists

The codebase is designed around recurring pain points in council operations:

- Manual tracking of tasks, attendance, and grades is slow and error-prone.
- Council data needs to be isolated from other councils.
- Leadership needs both council-level and global visibility.
- Delegates need a guided assistant that helps them learn without solving the work for them.

## 3. What Users Use It For

| Role | Main Usage |
|---|---|
| Delegate | Review own tasks, submit work, check attendance, ask the AI mentor questions |
| Instructor | Create and manage sessions, tasks, teams, attendance, and submissions |
| Head | Lead a council and manage the operational workflow |
| HR | Manage people records and support attendance/team workflows |
| VicePresident | Oversee all councils and perform global operations |
| President | Full global authority |

## 4. Product Scope

### 4.1 In Scope

- JWT authentication and profile retrieval
- Password reset workflow
- Council CRUD
- User CRUD and bulk import
- Council session CRUD
- Task CRUD and status updates
- Task submission CRUD and grading
- Team CRUD and member management
- Attendance CRUD and bulk import
- AI mentor chat
- Cache statistics and invalidation endpoints

### 4.2 Out of Scope

- Frontend applications
- Real-time chat between users
- Push notification infrastructure
- Payroll, finance, and payment workflows

## 5. Product Goals

| Goal | Outcome |
|---|---|
| Reduce manual council work | Replace spreadsheets and informal tracking with API records |
| Improve accountability | Every submission, attendance record, and task has a traceable owner and timestamp |
| Enforce data boundaries | Council data stays inside the correct council unless global access is intended |
| Support scale | Bulk import and caching reduce admin overhead |
| Help delegates learn | The AI mentor guides instead of solving tasks |

## 6. Personas

### Delegate

Needs a simple way to see responsibilities and submit work. Delegates should only access their own submissions and other council data when policy allows.

### Instructor

Needs to run the day-to-day workflow for a council, including sessions, tasks, submissions, teams, and attendance.

### Head

Needs the same council operations as an instructor with broader authority over the council.

### HR

Needs operational access to people management and attendance-related flows.

### VicePresident and President

Need global visibility and write access across councils.

## 7. Core User Journeys

### 7.1 Authentication Journey

1. User logs in with email and password.
2. API returns a JWT token, user name, role, and expiry.
3. Authenticated user can load `/me` for profile data.
4. User can log out and revoke the token.

### 7.2 Council Operations Journey

1. A privileged user creates a council.
2. The council becomes the parent for sessions, teams, users, tasks, and attendance.
3. Council writes invalidate related caches.

### 7.3 Session-to-Submission Journey

1. A session is created for a council.
2. Tasks are attached to the session.
3. Delegates submit files for tasks.
4. Instructors and council leaders grade or review submissions.

### 7.4 Team and Attendance Journey

1. A team is created inside a council.
2. Members are added individually or in bulk.
3. Attendance is logged by session and can be imported from Excel.

### 7.5 AI Mentor Journey

1. A delegate sends a message to the chat endpoint.
2. Gemini responds with a guided explanation.
3. The mentor avoids providing a complete answer or final deliverable.

## 8. Business Rules

### 8.1 Council Isolation

- Non-global users are scoped to their own council.
- Users cannot write records into another council unless the logic explicitly allows it.
- VP and President are the only roles with intended cross-council access.

### 8.2 RBAC

- Head and Instructor can manage most operational data in their council.
- HR can participate in user, attendance, and team-member flows.
- Delegate is mostly read-own / submit-own.
- Global executives can override council boundaries.

### 8.3 Caching

- Standard GET endpoints are cached for 30 minutes.
- Teams and team-members are cached for 60 minutes.
- Write operations clear the impacted resource caches.

### 8.4 Imports

- Users import accepts `.xlsx`, `.xls`, and `.csv`.
- Attendance import accepts Excel files.
- Team-member import accepts an array of member objects.

## 9. Success Metrics

- 100 percent of task activity is stored in the API.
- Attendance can be tracked without spreadsheets.
- Users can work within council boundaries without cross-contamination.
- Leadership can inspect the system using dashboards and cache stats.

## 10. Product Notes

- The backend exposes a small admin cache surface for debugging and support.
- The dashboard endpoint is role-aware and returns either own stats or council/global stats depending on the user role.
- The AI mentor is intentionally constrained to guidance.

| Phase | Feature | Status |
|---|---|---|
| Phase 1 | Core API (Auth, Councils, Tasks, Submissions, Teams, Attendance) | Done |
| Phase 2 | AI Mentor (Gemini 2.5 Flash integration) | Done |
| Phase 3 | Email Notifications (MailerSend) | Done / Removed |
| Phase 4 | Advanced Analytics and Reporting Module | Done / Removed |
---

*Document maintained by the ThreeDOS Backend Development Team.*
