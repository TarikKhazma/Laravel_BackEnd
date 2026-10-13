# Task Manager API

A REST API for managing tasks, built with **Laravel 13** and **Laravel Sanctum**.
Each user logs in with a token, manages their own tasks, categorizes them, adds them to favorites, and has a profile with an image.

---

## Table of Contents

- [Tech Stack](#tech-stack)
- [Features](#features)
- [Database & Relationships](#database--relationships)
- [Installation](#installation)
- [Authentication](#authentication)
- [Endpoints](#endpoints)
- [Request Examples](#request-examples)
- [Response Codes](#response-codes)
- [Project Structure](#project-structure)
- [Notes](#notes)

---

## Tech Stack

| Technology | Purpose |
|---|---|
| PHP 8.5 | Language |
| Laravel 13 | Framework |
| Laravel Sanctum 4 | Token authentication |
| MySQL (XAMPP) | Database |
| Postman | API testing |

---

## Features

- **Register, login, and logout** with Sanctum tokens.
- **Task management (CRUD)**: create, read, update, and delete tasks.
- **Users can only see and modify their own tasks**:
  - `user_id` comes from the token, not from the request.
  - Accessing another user's task returns `403 Unauthorized`.
- **Task priority** is an `enum`: `high`, `medium`, or `low`.
- **Sort tasks by priority**, ascending or descending.
- **Task categories** (Many to Many relationship).
- **Favorites**: add a task to favorites, remove it, and list favorites (Many to Many).
- **One profile per user** with a **required image** uploaded to `storage`.
- **Role-based access control (RBAC)**: only admins can view all users' tasks, via the `CheckUser` middleware.
- **Validation** with custom error messages (in Arabic).
- **All errors are returned as JSON**: `401` instead of a redirect to a login page.

---

## Database & Relationships

### Tables

| Table | Key Columns |
|---|---|
| `users` | `name`, `email`, `password`, `role` (default `user`) |
| `tasks` | `user_id`, `title`, `description`, `priority` (`high`/`medium`/`low`) |
| `profiles` | `user_id` (unique), `phone`, `address`, `date_of_birth`, `bio`, `image` |
| `categories` | `name` |
| `category_task` | Pivot table: `task_id`, `category_id` (composite unique) |
| `favorites` | Pivot table: `user_id`, `task_id` |
| `personal_access_tokens` | Sanctum tokens |

### Relationships

| Relationship | Type | In the Models |
|---|---|---|
| User ↔ Profile | One to One | `User::profile()` → `hasOne` / `Profile::user()` → `belongsTo` |
| User ↔ Task | One to Many | `User::tasks()` → `hasMany` / `Task::user()` → `belongsTo` |
| Task ↔ Category | Many to Many | `Task::categories()` → `belongsToMany` (table `category_task`) |
| User ↔ Task (favorites) | Many to Many | `User::favoriteTasks()` / `Task::favoriteByUser()` → `belongsToMany` (table `favorites`) |

---

## Installation

```bash
# 1. Install dependencies
composer install

# 2. Environment file
cp .env.example .env
php artisan key:generate
```

Update the database settings in `.env` (create the database in phpMyAdmin first):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_manager
DB_USERNAME=root
DB_PASSWORD=
```

```bash
# 3. Create the tables
php artisan migrate

# 4. Seed default categories (Work, Personal, Projects, ...)
php artisan db:seed --class=CategorySeeder

# 5. Link the storage folder so images are publicly accessible
php artisan storage:link

# 6. Start the server
php artisan serve
```

Base API URL: `http://127.0.0.1:8000/api`

> **Admin account:** every new user gets `role = user`. To make a user an admin, change their `role` to `admin` in phpMyAdmin.

---

## Authentication

1. Create an account with `POST /api/register`.
2. Log in with `POST /api/login`. The response includes a `Token`.
3. Send the token with every protected request 🔒:

```http
Authorization: Bearer YOUR_TOKEN
Accept: application/json
```

---

## Endpoints

🔓 = public  ·  🔒 = requires token  ·  👑 = admin only

### User

| Method | URL | Description | |
|---|---|---|---|
| POST | `/api/register` | Register a new account | 🔓 |
| POST | `/api/login` | Log in and get a token | 🔓 |
| POST | `/api/logout` | Log out (revokes the current token) | 🔒 |
| GET | `/api/user` | Get the authenticated user | 🔒 |
| GET | `/api/user/{id}/tasks` | Get a user's tasks (`{id}` must be your own, otherwise 403) | 🔒 |
| GET | `/api/user/{id}/profile` | Get a user's profile | 🔒 |

### Tasks

| Method | URL | Description | |
|---|---|---|---|
| GET | `/api/tasks` | List my tasks | 🔒 |
| POST | `/api/tasks` | Create a task | 🔒 |
| GET | `/api/tasks/{taskId}` | Show a task | 🔒 |
| PUT | `/api/tasks/{taskId}` | Update a task | 🔒 |
| DELETE | `/api/tasks/{taskId}` | Delete a task | 🔒 |
| GET | `/api/task/{taskId}/user` | Get the task's owner | 🔒 |
| GET | `/api/task/ordered` | My tasks sorted descending (high → low) | 🔒 |
| GET | `/api/task/ordered/desc` | My tasks sorted descending (high → low) | 🔒 |
| GET | `/api/task/ordered/asc` | My tasks sorted ascending (low → high) | 🔒 |
| GET | `/api/task/all` | All users' tasks | 👑 |

### Categories

| Method | URL | Description | |
|---|---|---|---|
| POST | `/api/tasks/{taskId}/categories` | Attach categories to a task (no duplicates) | 🔒 |

### Favorites

| Method | URL | Description | |
|---|---|---|---|
| GET | `/api/task/favorites` | List my favorite tasks | 🔒 |
| POST | `/api/task/{taskId}/favorite` | Add a task to favorites (no duplicates) | 🔒 |
| DELETE | `/api/task/{taskId}/favorite` | Remove a task from favorites | 🔒 |

### Profile

| Method | URL | Description | |
|---|---|---|---|
| POST | `/api/profile` | Create a profile with an image (form-data) | 🔒 |
| GET | `/api/profile/{id}` | Show a profile (`{id}` = **user** id) | 🔒 |
| PUT | `/api/profile/{id}` | Update a profile (`{id}` = **profile** id) | 🔒 |

---

## Request Examples

### Register

```http
POST /api/register
Content-Type: application/json

{
  "name": "Tarek",
  "email": "tarek@gmail.com",
  "password": "123456789",
  "password_confirmation": "123456789"
}
```

### Login

```http
POST /api/login
Content-Type: application/json

{
  "email": "tarek@gmail.com",
  "password": "123456789"
}
```

Response:

```json
{
  "message": "Login Successflly",
  "User": { "id": 1, "name": "Tarek", "email": "tarek@gmail.com", "role": "user" },
  "Token": "1|abc123..."
}
```

### Create a Task

Don't send `user_id`. It is set automatically from the token.

```http
POST /api/tasks
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "title": "learn laravel",
  "description": "learn relationships (many to many)",
  "priority": "high"
}
```

| Field | Rules |
|---|---|
| `title` | Required, string, max 40 characters |
| `description` | Optional |
| `priority` | Required, `high`, `medium`, or `low` |

### Update a Task

Send only the fields you want to change:

```http
PUT /api/tasks/1
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "priority": "low"
}
```

### Attach Categories to a Task

```http
POST /api/tasks/1/categories
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "category_id": [1, 2]
}
```

### Create a Profile with an Image

The request must be **form-data** (not JSON), and the `image` field must be of type **File**:

| Field | Type | Rules |
|---|---|---|
| `image` | File | **Required**, `jpg`, `jpeg`, `png`, or `webp`, max 2MB |
| `phone` | Text | Optional, max 20 characters |
| `address` | Text | Optional |
| `date_of_birth` | Text | Optional, a date before today (`2000-05-20`) |
| `bio` | Text | Optional, max 1000 characters |

Images are stored in `storage/app/public/my photo/` and are accessible at:

```
http://127.0.0.1:8000/storage/my photo/image-name.png
```

---

## Response Codes

| Code | Meaning |
|---|---|
| `200` | OK |
| `201` | Created |
| `204` | Deleted (empty response) |
| `401` | Unauthenticated: missing or invalid token |
| `403` | Unauthorized: the resource belongs to another user, or you're not an admin |
| `404` | Not found, or an invalid value in the URL (e.g. `ordered/abc`) |
| `405` | Wrong HTTP method (e.g. GET instead of POST) |
| `422` | Validation error. Check `errors` in the response |

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── UserController.php       # register, login, logout, getProfile, getUserTasks
│   │   ├── TaskController.php       # CRUD, categories, favorites, sorting, getAllTasks
│   │   └── ProfileController.php    # store (with image), show, update
│   ├── Middleware/
│   │   └── CheckUserRole.php        # allows admins only (role = admin)
│   └── Requests/
│       ├── StoreTaskRequest.php
│       ├── UpdateTaskRequest.php
│       ├── StoreProfileRequest.php
│       └── UpdateProfileRequest.php
└── Models/
    ├── User.php                     # profile, tasks, favoriteTasks
    ├── Task.php                     # user, categories, favoriteByUser
    ├── Profile.php                  # user
    └── Category.php                 # tasks

bootstrap/app.php                    # CheckUser alias + API errors return JSON and 401
routes/api.php                       # all routes
database/migrations/                 # tables
database/seeders/CategorySeeder.php  # default categories
```

---

## Notes

- **Profile id in the URL:** `GET /api/profile/{id}` takes the **user** id, while `PUT /api/profile/{id}` takes the **profile** id.
- **Updating a profile** does not change the image yet. To send files on update, note that PHP doesn't parse form-data with `PUT`. Send it as `POST` with a `_method=PUT` field instead.
- **`/api/user/{id}/profile`** returns any user's profile to anyone with a valid token.
- **Favorites and categories** only accept your own tasks. You can't add another user's task to your favorites.
