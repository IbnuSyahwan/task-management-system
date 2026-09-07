# Task Management System

A Laravel and Livewire task management application built for the assessment brief. Authenticated users can create, edit, delete, search, filter, and paginate their own tasks.

## Requirements

- PHP 8.3+
- Composer
- Node.js and npm
- SQLite (included with PHP)

## SQLite Setup

SQLite does not require XAMPP, Apache, MySQL, or phpMyAdmin. The project uses `database/database.sqlite`. Confirm these values in `.env`:

```dotenv
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

## Installation

```bash
composer install
copy .env.example .env
php artisan key:generate
if not exist database\database.sqlite type nul > database\database.sqlite
php artisan migrate
npm install
npm run build
php artisan serve
```

To load the included demo account and sample tasks, run:

```bash
php artisan db:seed
```

Demo login:

```text
Email: demo@example.com
Password: password
```

Open `http://localhost:8000`, sign in with the demo account, and use the Tasks item in the sidebar. The seeder is repeatable and updates the same demo records instead of creating duplicates.

## Development

Run the application and Vite together with:

```bash
composer run dev
```

## Verification

```bash
php artisan test --compact
vendor/bin/pint.bat --format agent
php artisan route:list
```

The test suite covers authentication, task ownership, validation, CRUD behavior, search, and status/priority filtering.