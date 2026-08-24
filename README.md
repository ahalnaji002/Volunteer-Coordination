# Volunteer Coordination API

A Laravel-based REST API for managing volunteers, tasks, work locations, and volunteer assignments.

The project was developed as part of practical backend training and focuses on building a structured API using Laravel, PostgreSQL, authentication, authorization, validation, resources, policies, and centralized error handling.

---

## Features

### Authentication
- Register volunteer accounts
- Login using email and password
- Laravel Sanctum API authentication
- Logout and token revocation
- User roles:
  - Admin
  - Volunteer

### Work Locations
- List work locations
- View a specific work location
- Admin-only create, update, and delete operations

### Tasks
- List tasks
- View a specific task
- Admin-only create, update, and delete operations

### Volunteers
- Full Admin CRUD
- Each volunteer is linked to a user account
- User and volunteer creation handled inside a database transaction
- Volunteer profile data returned using `VolunteerResource`
- Volunteer ownership handled using `VolunteerPolicy`

### Volunteer Self-Service
Authenticated volunteers can manage their own profile through:

```http
GET /api/me
PUT /api/me
PATCH /api/me
