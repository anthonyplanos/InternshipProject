<h1 align="center">ShoreTalks</h1>

<p align="center">
ShoreTalks is an internal company collaboration and social platform where employees can share ideas, opinions, and interests to foster healthy discussion and teamwork. It emphasizes safe, anonymous idea-sharing to reduce pressure and protect identity while encouraging honest participation.
</p>

---

## Features

- Authentication with Laravel Breeze
- Admin panel powered by Filament
- Roles and permissions with Spatie
- Activity and audit logging
- Soft deletes for posts

## Project Description

ShoreTalks is designed as an internal workplace platform to improve collaboration across teams. Employees can post suggestions, perspectives, and discussion points in a way that promotes respectful conversation and practical idea exchange.

The platform puts strong focus on anonymous participation so users can speak more openly without fear of social pressure. This supports safer communication, more inclusive feedback, and healthier decision-making within the company.

## Screenshots

### Admin View

![Admin Dashboard](docs/screenshots/admin-dashboard.png)
![Admin Dashboard](docs/screenshots/admin-dashboard-1.png)

### Staff View

![Staff Dashboard](docs/screenshots/staff-users-view.png)

### Employee View

![Employee Dashboard](docs/screenshots/employee-dashboard.png)

## Tech Stack

- PHP / Laravel
- MySQL
- Vite + NPM
- Tailwind CSS
- Livewire

## Prerequisites

Install these first:

- PHP 8.2+ (recommended)
- Composer 2+
- Node.js 18+ and NPM
- MySQL (XAMPP is supported)
- Git

Check your versions:

```bash
php -v
composer -V
node -v
npm -v
mysql --version
```

## Quick Start (Windows / XAMPP Friendly)

1. Clone the repository

```bash
git clone https://github.com/anthonyplanos/InternshipProject.git
cd InternshipProject
```

2. Install backend and frontend dependencies

```bash
composer install
npm install
```

3. Create your environment file

```bash
copy .env.example .env
```

4. Create a MySQL database

- Start Apache and MySQL in XAMPP
- Open phpMyAdmin: http://localhost/phpmyadmin
- Create a new database, for example: internship_project

5. Configure environment values in .env

Update these values to match your local database:

	DB_CONNECTION=mysql
	DB_HOST=127.0.0.1
	DB_PORT=3306
	DB_DATABASE=internship_project
	DB_USERNAME=root
	DB_PASSWORD=

6. Run Laravel setup

```bash
php artisan key:generate
php artisan migrate
```

7. Run the app

In terminal 1:

```bash
php artisan serve
```

In terminal 2:

```bash
npm run dev
```

Open: http://127.0.0.1:8000

## Quick Start (macOS / Linux)

```bash
git clone https://github.com/anthonyplanos/InternshipProject.git
cd InternshipProject
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Then configure database values in .env and run:

```bash
php artisan migrate
php artisan serve
npm run dev
```

## Package Install Commands (New Project Reference)

Run these from your project root:

```bash
# Livewire
composer require livewire/livewire

# Filament v3
composer require filament/filament:"^3.0" -W
php artisan filament:install --panels

# Spatie Permission
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate

# Spatie Activity Log
composer require spatie/laravel-activitylog
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan migrate
```

## Laravel Make Commands (Seeder, Model, Factory, and more)

```bash
# Model only
php artisan make:model Post

# Model + migration + factory + seeder
php artisan make:model Post -mfs

# Model + migration + factory + seeder + controller
php artisan make:model Post -mfsc

# Seeder
php artisan make:seeder PostSeeder

# Factory (linked to model)
php artisan make:factory PostFactory --model=Post

# Migration
php artisan make:migration create_posts_table

# Controller (resource)
php artisan make:controller PostController --resource

# Form request
php artisan make:request StorePostRequest

# Policy
php artisan make:policy PostPolicy --model=Post

# Filament resource (if Filament is installed)
php artisan make:filament-resource Post
```

Common database run commands:

```bash
php artisan migrate
php artisan db:seed
php artisan db:seed --class=PostSeeder
php artisan migrate:fresh --seed
```

## Common Issues

1. Vite manifest not found

Run:

```bash
npm run dev
```

2. SQLSTATE access denied

Recheck DB_DATABASE, DB_USERNAME, DB_PASSWORD, and DB_PORT in .env.

3. Class or cache issues after pulling changes

Run:

```bash
php artisan optimize:clear
```

4. APP_KEY missing

Run:

```bash
php artisan key:generate
```

## Useful Commands

```bash
php artisan migrate:fresh --seed
php artisan test
php artisan optimize:clear
```

## Notes

- All required packages are already defined in composer.json and package.json.
- Roles, permissions, Filament, and activity log dependencies are installed through Composer.
- Default MySQL port for XAMPP is 3306.

## License

This project is open-sourced software licensed under the MIT license.