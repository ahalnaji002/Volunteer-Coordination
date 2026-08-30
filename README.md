# Volunteer Coordination API

A Laravel REST API for coordinating volunteers across tasks and work locations. Administrators manage locations, tasks, volunteers, and assignments; volunteers can view shared reference data, maintain their own profile, and view only their own assignments.

## Technologies

- PHP 8.2+
- Laravel 12 and Eloquent ORM
- Laravel Sanctum token authentication
- PostgreSQL
- PHPUnit
- React 19, Vite 7, Tailwind CSS 4, React Router, Axios, and Lucide icons (optional frontend)

## Installation

```bash
git clone https://github.com/ahalnaji002/Volunteer-Coordination.git
cd Volunteer-Coordination
composer install
cp .env.example .env
php artisan key:generate
```

On Windows PowerShell, use `Copy-Item .env.example .env` instead of `cp`.

Create a PostgreSQL database, then configure `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=volunteer_coordination
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

Prepare and run the application:

```bash
php artisan migrate
php artisan db:seed
php artisan serve
```

The default local base URL is `http://127.0.0.1:8000/api`.

## Frontend Setup

Install and start the optional frontend in a second terminal:

```bash
cd frontend
npm install
npm run dev
```

Open `http://localhost:5173`. The Vite development server proxies `/api` requests to Laravel at `http://127.0.0.1:8000`. To use a different API URL, copy `frontend/.env.example` to `frontend/.env` and set `VITE_API_URL`.

Create a production frontend bundle with `npm run build`. The output is written to `frontend/dist`.

## Sample Accounts

`php artisan db:seed` creates or refreshes these reusable accounts without duplicating them:

| Role      | Email                   | Password      |
| --------- | ----------------------- | ------------- |
| Admin     | `admin@example.com`     | `password123` |
| Volunteer | `volunteer@example.com` | `password123` |

These credentials are for local review only and must be changed in a real deployment.

## API Headers

All requests should send:

```http
Accept: application/json
Content-Type: application/json
```

Except for login, every endpoint also requires the Sanctum token returned by `POST /api/login`:

```http
Authorization: Bearer YOUR_TOKEN
```

## Endpoints

| Method    | Route                                 | Access           | Description                                                 |
| --------- | ------------------------------------- | ---------------- | ----------------------------------------------------------- |
| POST      | `/api/login`                          | Public           | Log in as an admin or volunteer.                            |
| POST      | `/api/logout`                         | Authenticated    | Revoke the current access token.                            |
| GET       | `/api/user`                           | Authenticated    | Return the authenticated account.                           |
| GET       | `/api/work-locations`                 | Admin, Volunteer | List work locations.                                        |
| GET       | `/api/work-locations/{work_location}` | Admin, Volunteer | View a work location.                                       |
| POST      | `/api/work-locations`                 | Admin            | Create a work location.                                     |
| PUT/PATCH | `/api/work-locations/{work_location}` | Admin            | Update a work location.                                     |
| DELETE    | `/api/work-locations/{work_location}` | Admin            | Delete a work location.                                     |
| GET       | `/api/tasks`                          | Admin, Volunteer | List tasks.                                                 |
| GET       | `/api/tasks/{task}`                   | Admin, Volunteer | View a task.                                                |
| POST      | `/api/tasks`                          | Admin            | Create a task.                                              |
| PUT/PATCH | `/api/tasks/{task}`                   | Admin            | Update a task.                                              |
| DELETE    | `/api/tasks/{task}`                   | Admin            | Delete a task.                                              |
| GET       | `/api/volunteers`                     | Admin            | List volunteers.                                            |
| POST      | `/api/volunteers`                     | Admin            | Create a volunteer and linked account.                      |
| GET       | `/api/volunteers/{volunteer}`         | Admin            | View a volunteer.                                           |
| PUT/PATCH | `/api/volunteers/{volunteer}`         | Admin            | Update a volunteer.                                         |
| DELETE    | `/api/volunteers/{volunteer}`         | Admin            | Delete an unassigned volunteer.                             |
| GET       | `/api/assignments`                    | Admin            | List assignments.                                           |
| POST      | `/api/assignments`                    | Admin            | Assign a volunteer to a task and location.                  |
| GET       | `/api/assignments/{assignment}`       | Admin            | View an assignment.                                         |
| PUT/PATCH | `/api/assignments/{assignment}`       | Admin            | Update an assignment.                                       |
| DELETE    | `/api/assignments/{assignment}`       | Admin            | Delete an assignment.                                       |
| GET       | `/api/me`                             | Volunteer        | View the authenticated volunteer's profile.                 |
| PUT/PATCH | `/api/me`                             | Volunteer        | Update the authenticated volunteer's name, email, or phone. |
| GET       | `/api/my-assignments`                 | Volunteer        | View only the authenticated volunteer's assignments.        |

Volunteer accounts are created by an administrator; there is no public registration endpoint. Self-service endpoints derive the volunteer from the authenticated user and do not accept a client-supplied `volunteer_id`.

## Frontend Features

- Sanctum Bearer-token login, session restoration, logout, protected routes, and role-based navigation
- Admin dashboard and CRUD screens for work locations, tasks, volunteers, and assignments
- Volunteer dashboard, editable profile, personal assignments, and read-only task and location lists
- Laravel validation messages, loading and empty states, success/error feedback, and delete confirmation

The frontend stores the training-project access token in browser `localStorage`. Use the seeded sample accounts above to test both roles.

## Response Standard

Model data is transformed through API Resources. Passwords, remember tokens, and other sensitive authentication fields are never returned.

Successful response:

```json
{
    "status": "success",
    "message": "Tasks retrieved successfully.",
    "data": [
        {
            "id": 1,
            "name": "First aid",
            "description": null,
            "created_at": "2026-08-30T10:00:00.000000Z",
            "updated_at": "2026-08-30T10:00:00.000000Z"
        }
    ]
}
```

Error response:

```json
{
    "status": "error",
    "message": "Forbidden.",
    "data": null
}
```

Validation errors also include an `errors` object:

```json
{
    "status": "error",
    "message": "Validation failed.",
    "data": null,
    "errors": {
        "name": ["The name field is required."]
    }
}
```

Central exception handling in `bootstrap/app.php` standardizes `401`, `403`, resource and endpoint `404`, `405`, `422`, `429`, and `500` API errors.

## Testing and Review

Run the automated suite and inspect the effective API routes:

```bash
php artisan test
php artisan route:list --path=api
```

The feature suite covers core response envelopes, authentication, authorization, validation, missing resources/endpoints, and method errors.

Postman files are included in the `postman/` directory:

- `postman/Volunteer-Coordination.postman_collection.json`
- `postman/Volunteer-Coordination-Local.postman_environment.json`

The collection uses one shared `token` variable. Log in with the desired role before testing role-specific endpoints; the login script automatically updates the token in the environment.

Manual Postman verification has been completed for the main CRUD flows, role-based access, token revocation, duplicate assignment validation, and volunteer self-service.

## Production Safety

Use `APP_ENV=production` and `APP_DEBUG=false`, replace the sample passwords, and run `php artisan optimize:clear` after environment changes. Never commit `.env` or live access tokens.

## Deployment on Render

The production layout uses three Render resources defined in `render.yaml`:

- `volunteer-coordination-api`: Docker Web Service running Laravel with Apache and PHP 8.2
- `volunteer-coordination-frontend`: Static Site built from `frontend/`
- `volunteer-coordination-db`: managed PostgreSQL database connected to Laravel over Render's private network

### PostgreSQL

Create the database through the Blueprint or in the Render dashboard. Keep the database in the same region as the backend and use its internal connection URL. The Blueprint maps that URL to `DATABASE_URL`; Laravel also continues to support the individual `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` variables.

Do not run seeders automatically in production. Run `php artisan db:seed` manually only if production sample accounts are explicitly wanted.

### Laravel Web Service

Use these service settings when configuring the backend manually instead of using the Blueprint:

| Setting | Value |
| ------- | ----- |
| Runtime | Docker |
| Root directory | Repository root (leave blank) |
| Dockerfile | `./Dockerfile` |
| Docker build context | `.` |
| Health check path | `/up` |

Set these backend environment variables in Render:

| Variable | Production value |
| -------- | ---------------- |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | Output from `php artisan key:generate --show` |
| `APP_URL` | The backend origin, such as `https://BACKEND-SERVICE.onrender.com` |
| `DB_CONNECTION` | `pgsql` |
| `DATABASE_URL` | Render PostgreSQL internal connection URL |
| `RUN_MIGRATIONS` | `true` to run pending migrations before Apache starts |
| `CORS_ALLOWED_ORIGINS` | The frontend origin, such as `https://FRONTEND-SITE.onrender.com` |
| `LOG_CHANNEL` | `stderr` |
| `LOG_LEVEL` | `info` |

Generate the Laravel key locally and paste the complete output into Render; never add it to a repository file:

```bash
php artisan key:generate --show
```

The Blueprint sets `RUN_MIGRATIONS=true`, which runs this command before Apache starts:

```bash
php artisan migrate --force
```

This runs only pending migrations and is safe to repeat after a restart. It does not use `migrate:fresh` and does not seed data. Set `RUN_MIGRATIONS=false` if migrations will instead be run manually or through a paid Render pre-deploy command.

### React Static Site

Use these frontend settings when configuring the Static Site manually:

| Setting | Value |
| ------- | ----- |
| Root directory | `frontend` |
| Build command | `npm ci && npm run build` |
| Publish directory | `dist` |
| SPA rewrite | `/*` to `/index.html` |

Set the frontend build environment variable to the deployed backend API URL:

```env
VITE_API_URL=https://BACKEND-SERVICE.onrender.com/api
```

The value is intentionally not hard-coded because Render assigns the final service hostname. Local development remains unchanged: the Vite server uses `/api` and proxies it to `http://127.0.0.1:8000`.

`CORS_ALLOWED_ORIGINS` on the backend must exactly include the deployed frontend origin without the `/api` suffix. Multiple allowed origins can be supplied as a comma-separated list. The default configuration continues to allow `http://localhost:5173` and `http://127.0.0.1:5173` for local development.

### Verification

After both services are available:

1. Open `https://BACKEND-SERVICE.onrender.com/up` and confirm a successful response.
2. Open the frontend URL and sign in with an existing account.
3. Verify authenticated API requests, role-specific navigation, and one read operation for each role.
4. Refresh a nested frontend route and confirm the SPA loads instead of returning a 404.
5. Review Render logs to confirm migrations completed and no CORS, database, or application errors are present.
