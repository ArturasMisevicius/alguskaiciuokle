# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 application with role-based access control (RBAC) implementing separate admin and user dashboards. The application uses Laravel Breeze for authentication, Tailwind CSS for styling, Alpine.js for interactivity, and FontAwesome for icons.

## Development Commands

### Initial Setup
```bash
composer setup
# Runs: composer install, creates .env, generates key, migrates database, installs npm packages, builds assets
```

### Development Server
```bash
composer dev
# Runs concurrently: php artisan serve, queue listener, and vite dev server
```

### Testing
```bash
composer test
# Runs Pest tests with cleared config cache
```

```bash
# Run single test file
cmd /c "php artisan test --filter TestClassName"
```

### Database Commands
```bash
cmd /c "php artisan migrate"
cmd /c "php artisan migrate:fresh --seed"
cmd /c "php artisan db:seed --class=UserSeeder"
```

### Asset Building
```bash
npm run dev      # Development mode with HMR
npm run build    # Production build
```

### Code Quality
```bash
cmd /c "php artisan pint"  # Format code with Laravel Pint
```

## Architecture

### Role-Based Access Control (RBAC)

The application implements a custom RBAC system with two roles: `admin` and `user`.

**Key Components:**
- **Models:** `User` and `Role` with many-to-many relationship via `role_user` pivot table
- **Middleware:** `CheckRole` middleware (aliased as `role`) protects routes requiring specific roles
- **User Methods:**
  - `hasRole(string $roleName): bool` - Check single role
  - `hasAnyRole(array $roles): bool` - Check multiple roles
  - `isAdmin(): bool` - Convenience method for admin check
  - `assignRole(string $roleName): void` - Assign role to user
  - `removeRole(string $roleName): void` - Remove role from user

**Route Structure:**
- Routes are grouped by role with prefixes: `/admin/*` and `/user/*`
- All role-protected routes use `role:admin` or `role:user` middleware
- Main `/dashboard` route redirects based on user's role (see routes/web.php:13-18)

### Controller Organization

Controllers are namespaced by role to maintain separation of concerns:

- `App\Http\Controllers\Admin\DashboardController` - Admin dashboard and user CRUD operations
- `App\Http\Controllers\User\DashboardController` - User dashboard and profile management
- `App\Http\Controllers\ProfileController` - Breeze profile management (available to all authenticated users)

### Database Seeding

The application uses a seeding chain defined in `DatabaseSeeder`:
1. `RoleSeeder` - Creates `admin` and `user` roles
2. `UserSeeder` - Creates initial users with assigned roles

When seeding, always run in order: `RoleSeeder` first, then `UserSeeder`.

### User Management Features

Admins have full CRUD capabilities for users:
- View all users with pagination (admin/users)
- Create new users with initial password assignment
- Edit user details including role changes
- Delete users (with self-deletion protection)
- Password updates store both hashed password and initial_password field

The `initial_password` field in users table stores the original password for reference.

### Frontend Stack

- **Tailwind CSS v3** - Utility-first styling
- **Alpine.js v3** - Lightweight JavaScript framework for interactivity
- **Vite** - Asset bundling and HMR
- **FontAwesome v7** - Icon library
- **Blade Templates** - Server-side templating

Views are organized by role:
- `resources/views/admin/*` - Admin views
- `resources/views/user/*` - User views
- `resources/views/auth/*` - Breeze authentication views
- `resources/views/layouts/*` - Shared layouts (app, guest, navigation)

### Database Configuration

Default configuration uses SQLite (`DB_CONNECTION=sqlite`). The database file should be at `database/database.sqlite`.

For MySQL/PostgreSQL, update `.env` with appropriate connection details.

### Queue and Cache

- **Queue Connection:** Database-backed (`QUEUE_CONNECTION=database`)
- **Cache Store:** Database (`CACHE_STORE=database`)
- **Session Driver:** Database (`SESSION_DRIVER=database`)

Ensure queue worker is running in development: `php artisan queue:listen --tries=1`

## Common Patterns

### Adding New Role-Protected Routes

1. Add route to appropriate group in `routes/web.php`
2. Ensure middleware includes `role:admin` or `role:user`
3. Create controller method in namespaced controller
4. Create corresponding Blade view in role-specific directory

### Working with Roles

```php
// Check if user has role
if (auth()->user()->hasRole('admin')) { }

// Assign role to user
$user->assignRole('admin');

// Remove role from user
$user->removeRole('user');
```

### Middleware Registration

Middleware aliases are registered in `bootstrap/app.php`:
- `role` -> `App\Http\Middleware\CheckRole`

Use in routes: `->middleware('role:admin')`

## Testing with Pest

The project uses Pest PHP for testing. Test files are in `tests/` directory.

```bash
# Run all tests
composer test

# Run specific test
cmd /c "php artisan test --filter TestName"
```
