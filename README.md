# Volunteer Coordination API

A RESTful backend API built with Laravel for managing volunteers, tasks, work locations, and volunteer profiles.

The project was developed as part of practical backend training, with a focus on clean API architecture, authentication, authorization, validation, database relationships, and centralized error handling.

---

## Features

### Authentication

- Volunteer registration
- User login
- Laravel Sanctum authentication
- Token-based API access
- Logout and token revocation
- Role-based access:
  - `admin`
  - `volunteer`

### Work Locations

Authenticated users can:

- List work locations
- View a specific work location

Admins can additionally:

- Create work locations
- Update work locations
- Delete work locations

### Tasks

Authenticated users can:

- List tasks
- View a specific task

Admins can additionally:

- Create tasks
- Update tasks
- Delete tasks

### Volunteers

Admins have full CRUD access to volunteers:

- List volunteers
- Create volunteers
- View a volunteer
- Update a volunteer
- Delete a volunteer

When an Admin creates a volunteer, the linked user account is created inside a database transaction with:

```text
role = volunteer
```

The user account and volunteer profile are linked through `user_id`.

### Volunteer Self-Service

Authenticated volunteers can access their own profile using:

```http
GET /api/me
PUT /api/me
PATCH /api/me
```

A volunteer can update:

- Name
- Email
- Phone

A volunteer cannot update:

- Role
- `user_id`
- National ID

The profile is retrieved from the authenticated user relationship:

```php
$request->user()->volunteer
```

This prevents volunteers from accessing or modifying another volunteer's profile by supplying a different ID.

---

## Authorization

Admin-only endpoints are protected using custom middleware.

Volunteer profile ownership is handled using `VolunteerPolicy`.

A volunteer attempting to access Admin volunteer endpoints receives:

```json
{
    "message": "Forbidden."
}
```

with:

```text
403 Forbidden
```

---

## Validation

The project uses dedicated Laravel Form Requests:

```text
StoreVolunteerRequest
UpdateVolunteerRequest
UpdateOwnProfileRequest
```

This separates Admin validation rules from the fields volunteers are allowed to update themselves.

API responses for volunteers are formatted using:

```text
VolunteerResource
```

---

## Global API Error Handling

API exceptions are handled centrally in:

```text
bootstrap/app.php
```

The API returns clean JSON responses without exposing:

- Stack traces
- Internal file paths
- Line numbers
- Exception implementation details

Handled API responses include:

| Status | Meaning |
|---|---|
| 401 | Unauthenticated |
| 403 | Forbidden |
| 404 | Resource not found |
| 404 | Endpoint not found |
| 405 | Method not allowed |
| 422 | Validation failed |
| 429 | Too many requests |
| 500 | Server Error |

Example:

```json
{
    "message": "Resource not found."
}
```

---

## Database Design

Main application tables include:

```text
users
volunteers
tasks
work_locations
assignments
personal_access_tokens
```

### User and Volunteer Relationship

Each volunteer is linked to one user account.

```text
User 1 ───── 1 Volunteer
```

The `users` table stores account-related data:

```text
name
email
password
role
```

The `volunteers` table stores volunteer-specific data:

```text
user_id
phone
national_id
```

`name` and `email` are intentionally stored only in the `users` table.

This avoids duplicating the same information across `users` and `volunteers` and prevents inconsistencies between two copies of the same data.

The `volunteers.user_id` field is:

- Required
- Unique
- A foreign key to `users.id`

---

## API Endpoints

### Authentication

```http
POST /api/register
POST /api/login
POST /api/logout
GET  /api/user
```

### Work Locations

Authenticated users:

```http
GET /api/work-locations
GET /api/work-locations/{id}
```

Admin only:

```http
POST       /api/work-locations
PUT/PATCH  /api/work-locations/{id}
DELETE     /api/work-locations/{id}
```

### Tasks

Authenticated users:

```http
GET /api/tasks
GET /api/tasks/{id}
```

Admin only:

```http
POST       /api/tasks
PUT/PATCH  /api/tasks/{id}
DELETE     /api/tasks/{id}
```

### Volunteers

Admin only:

```http
GET        /api/volunteers
POST       /api/volunteers
GET        /api/volunteers/{id}
PUT/PATCH  /api/volunteers/{id}
DELETE     /api/volunteers/{id}
```

Volunteer self-service:

```http
GET        /api/me
PUT/PATCH  /api/me
```

---

## Technologies

- PHP
- Laravel
- Laravel Sanctum
- PostgreSQL
- Eloquent ORM
- REST API
- Postman
- Git
- GitHub

---

## Installation

Clone the repository:

```bash
git clone https://github.com/ahalnaji002/Volunteer-Coordination.git
```

Enter the project:

```bash
cd Volunteer-Coordination
```

Install dependencies:

```bash
composer install
```

Create the environment file:

```bash
cp .env.example .env
```

For Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Generate the Laravel application key:

```bash
php artisan key:generate
```

Configure PostgreSQL in `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=volunteer_coordination
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Run migrations:

```bash
php artisan migrate
```

Start the development server:

```bash
php artisan serve
```

The API will normally run at:

```text
http://127.0.0.1:8000
```

---

## API Headers

API clients should send:

```http
Accept: application/json
```

Protected endpoints require a Sanctum Bearer token:

```http
Authorization: Bearer YOUR_TOKEN
```

---

## Testing

Run the Laravel test suite:

```bash
php artisan test
```

API endpoints, authentication, authorization, validation, and error responses were also tested using Postman.

---

## Production Error Configuration

Production environments should disable Laravel debug output:

```env
APP_ENV=production
APP_DEBUG=false
```

After changing environment configuration:

```bash
php artisan optimize:clear
```

This prevents sensitive server information from being exposed through API error responses.

---

## Project Structure

```text
app/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   ├── Requests/
│   └── Resources/
├── Models/
└── Policies/

bootstrap/
└── app.php

database/
└── migrations/

routes/
└── api.php
```

---

## Repository

https://github.com/ahalnaji002/Volunteer-Coordination
