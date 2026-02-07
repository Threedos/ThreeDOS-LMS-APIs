# Product Requirements Document (PRD) - ThreeDOS Management System

**Version:** 2.0
**Status:** Draft
**Last Updated:** 2026-02-07

---

## 1. Executive Summary
The ThreeDOS Management System is a comprehensive backend platform designed to digitalize and streamline the operations of student activities and educational councils. It serves as a centralized hub for managing users, councils, teams, tasks, and attendance, replacing manual tracking methods with a secure, role-based API ecosystem.

## 2. Product Scope
### In Scope
- **User Management**: Authentication, profile management, and role assignment.
- **Council Operations**: Creation and management of councils and their sessions.
- **Task Lifecycle**: Assignment, submission, grading, and feedback.
- **Team Dynamics**: Grouping delegates into teams with internal hierarchies (Leader/Member).
- **Attendance Tracking**: Logging presence for sessions with status (Present/Absent/Late).
- **Performance**: High-performance data retrieval using Redis caching.

### Out of Scope
- Frontend Interface (This is a Backend-only API project).
- Real-time chat messaging system (Notifications are supported, but not chat).
- Payment processing.

## 3. User Personas & Roles

### 3.1 The Delegate
* **Description**: A regular member of a specific council.
* **Needs**: To know what tasks are due, submit their work, and check their attendance record.
* **Pain Points**: Missing deadlines due to lack of notifications, uncertainty about submission status.

### 3.2 The Instructor
* **Description**: A mentor or supervisor responsible for a group of delegates.
* **Needs**: To create tasks, grade submissions, log attendance for sessions, and manage teams.
* **Pain Points**: Grading via spreadsheets is error-prone; creating teams manually is tedious.

### 3.3 The Head
* **Description**: The leader of a specific Council.
* **Needs**: High-level view of their council's performance, ability to manage instructors and delegates within their council.
* **Pain Points**: Difficulty in tracking the overall progress of the council.

### 3.4 The Vice President (VP) & President
* **Description**: Top-level executives with organization-wide oversight.
* **Needs**: Access to data across *all* councils, ability to create/delete councils and manage high-level settings.
* **Pain Points**: Fragmented data across different councils making "big picture" analysis hard.

## 4. User Stories

### 4.1 Authentication & Security
*   **US-1.1**: As a user, I want to log in using my email and password so that I can access my account securely.
*   **US-1.2**: As a user, I want to reset my password via email if I forget it.
*   **US-1.3**: As the system, I want to block excessive login attempts (throttling) to prevent brute-force attacks.

### 4.2 Council & Session Management
*   **US-2.1 (VP/President)**: I want to create a new Council with a name and description.
*   **US-2.2 (Head/Instructor)**: I want to create a "Session" (meeting) for my council to track when we meet.
*   **US-2.3**: I want sessions to be automatically viewable only by members of that council.

### 4.3 Task Management
*   **US-3.1 (Head/Instructor)**: I want to create a task linked to a specific session, with a title, description, and due date.
*   **US-3.2 (Head/Instructor)**: I want to edit a task's details if the requirements change.
*   **US-3.3 (Delegate)**: I want to view a list of all tasks assigned to my council.

### 4.4 Submissions & Grading
*   **US-4.1 (Delegate)**: I want to upload a file as my submission for a task.
*   **US-4.2 (Instructor)**: I want to view all submissions for a task.
*   **US-4.3 (Instructor)**: I want to grade a submission and leave a text comment for feedback.

### 4.5 Team Management
*   **US-5.1 (Instructor)**: I want to create teams and assign specific users to them.
*   **US-5.2 (Instructor)**: I want to designate a "Leader" and "Co-Leader" for each team.
*   **US-5.3 (Instructor)**: I want to bulk-import team members from a file to save time.

### 4.6 Attendance
*   **US-6.1 (Instructor)**: I want to log attendance for a session, marking users as Present, Absent, or Late.
*   **US-6.2 (Delegate)**: I want to see my own attendance history to know my standing.

## 5. Functional Requirements

### 5.1 Dashboard Logic
*   **Delegates**: See only their own stats (tasks completed, attendance %).
*   **Instructors/Heads**: See stats for their specific Council.
*   **VP/President**: See global stats across the entire organization.

### 5.2 Data Isolation
*   Users must strictly be limited to accessing data within their own `council_id`, unless they are VP/President.
*   Middleware must enforce this check on every request.

## 6. Non-Functional Requirements
*   **Performance**: API response time should be under 700ms for cached read operations.
*   **Scalability**: System should handle 500+ concurrent users (optimized via Redis).
*   **Maintainability**: Code must follow PSR-12 standards and strictly adhere to the Service-Repository pattern.
*   **Availability**: 99.9% uptime target.

## 7. Roadmap & Future Scope
*   **Phase 2**: Notification system integration (Email/Push).
*   **Phase 3**: Mobile App utilizing these APIs.
*   **Phase 4**: Advanced Analytics & Reporting Module.
