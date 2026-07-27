# ThreeDOS APIs

ThreeDOS APIs is a backend-only, API-first council management system built with Laravel 12. It centralizes council operations such as authentication, council and session management, tasks, submissions, teams, attendance, user management, caching, and the AI mentor chat workflow.

## What This Project Does

ThreeDOS is designed for student councils and training organizations that need:

- Role-based access control
- Council-scoped data isolation
- Task assignment and submission tracking
- Attendance management
- Team and team-member management
- Bulk imports for users, attendance, and team members
- Redis-backed cache inspection and invalidation
- A Gemini-powered mentor that guides users instead of solving tasks for them

## Tech Stack

- Laravel 12
- PHP 8.4+
- JWT authentication via `tymon/jwt-auth`
- Redis for caching and cache inspection
- Google Gemini integration via `google-gemini-php/laravel`
- MailerSend mail driver
- Laravel Telescope for observability
- Vite for frontend asset compilation

## Requirements

Before running the project locally, you need:

- PHP 8.4+
- Composer
- Node.js and npm
- A database engine supported by Laravel, such as MySQL
- Redis for cache features

## Quick Start

### 1. Install dependencies

```bash
composer install
npm install
```

### 2. Create your environment file

Copy `.env.example` to `.env` and update the values for your machine.

### 3. Generate the app key

```bash
php artisan key:generate
```

### 4. Set your app-specific secrets

Make sure these values are configured in `.env`:

- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `REDIS_HOST`, `REDIS_PORT`, `REDIS_PASSWORD`
- `JWT_SECRET`
- `GEMINI_API_KEY`
- `MAIL_*` values if you want email delivery to work

### 5. Run migrations

```bash
php artisan migrate
```

### 6. Seed demo data

```bash
php artisan db:seed
```

This seeds example councils, roles, users, sessions, tasks, teams, team members, submissions, and attendance records.

### 7. Build frontend assets

```bash
npm run build
```

## Recommended Local Workflow

The repository includes Composer scripts that combine the common setup and development commands.

### Full setup

```bash
composer setup
```

This runs:

- `composer install`
- `.env` creation if missing
- `php artisan key:generate`
- `php artisan migrate --force`
- `npm install`
- `npm run build`

### Development mode

```bash
composer dev
```

This starts:

- Laravel development server
- Queue listener
- Laravel Pail logs
- Vite dev server

### Run tests

```bash
composer test
```

## Example Demo Accounts

After running `php artisan db:seed`, you can log in with these sample accounts:

| Role | Email | Password |
|---|---|---|
| Vice President | `vp@threedos.local` | `password` |
| Head | `head.backend@threedos.local` | `password` |
| Instructor | `instructor.frontend@threedos.local` | `password` |
| HR | `hr@threedos.local` | `password` |
| Delegate | `delegate.frontend@threedos.local` | `password` |
| Delegate | `delegate.backend@threedos.local` | `password` |

## API Usage

### Base URL

Production:

```text
https://threedos-apis-production.up.railway.app/api
```

### Authentication

Most endpoints require a Bearer token in the `Authorization` header.

```http
Authorization: Bearer <your_access_token>
```

### Login Example

```bash
curl -X POST "https://threedos-apis-production.up.railway.app/api/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "vp@threedos.local",
    "password": "password"
  }'
```

### Using the Token

```bash
curl "https://threedos-apis-production.up.railway.app/api/me" \
  -H "Authorization: Bearer <your_access_token>"
```

## Main API Areas

- Authentication: `/login`, `/logout`, `/forget-password`, `/me`
- Councils: `/councils`
- Users: `/users`, `/users/bulk`, `/users/dashboard`
- Roles: `/roles`
- Sessions: `/sessions`
- Tasks: `/tasks`
- Task submissions: `/task-submissions`
- Teams: `/teams`
- Team members: `/team-members`, `/team-members/bulk`
- Attendances: `/attendances`, `/attendances/bulk`
- AI chat: `/ai-chat`
- Cache admin: `/cache/stats`, `/cache/endpoint`, `/cache/resource`, `/cache/user/{userId}`
- Notifications: `/notifications`

## Common Request Rules

### Council isolation

Most data is scoped to the authenticated user’s council. Vice President and President have global access, while other roles are limited by policies and request authorization.

### Status values

- Task status: `Pending`, `In Progress`, `Completed`
- Attendance status: `present`, `absent`, `late`
- Submission status is controlled by service logic and defaults to `submitted` on creation

### Bulk import formats

- Users: `.xlsx`, `.xls`, `.csv`
- Attendance: Excel file only
- Team members: JSON array in a `members` field

## Documentation

The detailed specifications live in the `docs` folder:

- [PRD](docs/PRD.md)
- [FRD](docs/FRD.md)
- [SDD](docs/SDD.md)
- [SRS](docs/SRS.md)
- [API Documentation](docs/API_DOCUMENTATION.md)

The Postman collection is available in [ThreeDOS_API.postman_collection.json](ThreeDOS_API.postman_collection.json).

## Troubleshooting

### Login fails

- Confirm the user exists in the seeded data or your database.
- Check that `APP_KEY` and `JWT_SECRET` are set.
- Verify the password is correct.

### Cache endpoints do not work

- Make sure Redis is running and reachable.
- Confirm the Redis settings in `.env`.

### Mail or password reset does not work

- Set the correct `MAIL_*` values.
- Verify your mail provider credentials.

### AI chat fails

- Set `GEMINI_API_KEY` in `.env`.
- Confirm outbound network access is allowed in your environment.

## Useful Commands

```bash
php artisan serve
php artisan migrate
php artisan db:seed
php artisan test
npm run dev
npm run build
```

## Project Structure Overview

- `app/Http/Controllers/Api` contains the API controllers
- `app/Http/Requests` contains validation and authorization rules
- `app/Services` contains business logic
- `app/Repositories` contains data access logic
- `app/Policies` contains access rules
- `app/Http/Resources` shapes API responses
- `database/seeders` contains demo seed data
- `docs` contains the PRD, FRD, SDD, SRS, and API reference

## Notes

- This repository is backend-only.
- The API uses JSON responses.
- Redis-backed cache invalidation is part of the design and should be enabled in deployed environments.
- The AI mentor is intentionally constrained to guidance and should not be used as a task-solving endpoint.
