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

## Timesheet & Salary System

This application includes a comprehensive timesheet tracking and automatic salary calculation system.

### Overview

The system provides:
- **Manual time entry** with start/end times, breaks, and project assignment
- **Live timer** for clock-in/clock-out functionality
- **Weekly view** with submission workflow (draft → submitted → approved/rejected)
- **Automatic pricing** using a flexible rate card matrix
- **Admin approval workflow** for timesheets

### Database Tables

- `projects` - Projects that can be assigned to timesheets
- `timesheets` - Individual time entries with status workflow
- `rate_cards` - Pricing rules with precedence-based matching
- `timesheet_pricing_details` - Calculated pricing breakdown per entry

### Rate Card System ("Price Matrix")

Rate cards define how timesheet entries are priced. The system automatically selects the best matching rate card based on specificity (precedence).

**Scoping Options:**
- User-specific (applies to one user)
- Role-specific (applies to all users with a role)
- Project-specific (applies to specific project)
- Day-of-week mask (e.g., Mon-Fri only)
- Time bands (e.g., 08:00-18:00 for regular hours)
- Date ranges (effective from/until dates)

**Rate Types:**
- `fixed` - Fixed hourly rate (e.g., €15/hour)
- `multiplier` - Multiplier vs base rate (e.g., 1.5x for overtime)

**Precedence Rules:**
The system automatically calculates precedence scores:
- User-specific: +100 points
- Role-specific: +50 points
- Project-specific: +30 points
- Day-specific: +10 points
- Time-band specific: +10 points
- Date-range specific: +5 points

Higher precedence always wins. Most specific rule applies.

**Overtime Support:**
- Daily overtime (e.g., >8 hours/day)
- Weekly overtime (e.g., >40 hours/week)
- Separate rate cards for overtime with higher rates

### Pricing Engine

Location: `app/Services/PricingEngineService.php`

The pricing engine automatically:
1. Splits time entries across days (handles midnight crossings)
2. Splits entries into segments by time bands
3. Finds best-matching rate card for each segment
4. Calculates pricing and stores detailed breakdown
5. Updates timesheet with total hours and amount

**To recalculate pricing:**
```php
$pricingEngine = app(PricingEngineService::class);
$pricingEngine->calculatePricing($timesheet);
```

### User Workflow

1. **Create timesheet entry:**
   - Manual entry: specify date, start, end, break
   - Or use live timer: clock in/out
2. **Entry saved as draft** (can edit/delete)
3. **Submit for approval** (single entry or entire week)
4. **Admin reviews and approves/rejects**
5. **Approved entries** show calculated hours and earnings

### Admin Tasks

**Projects Management:**
- CRUD operations for projects
- Activate/deactivate projects
- Route: `admin.projects.index`

**Rate Cards Management:**
- Create complex pricing rules
- Set precedence manually or auto-calculate
- Duplicate existing rate cards for quick setup
- Route: `admin.rate-cards.index`

**Timesheet Approval:**
- View all timesheets with filtering
- Approve/reject individual entries
- Bulk approve multiple entries
- View detailed pricing breakdown
- Route: `admin.timesheets.index`

### Sample Data

The seeder creates:
- 4 sample projects (Internal Dev, Client A, Client B, Maintenance)
- 4 rate cards:
  - Standard: Mon-Fri, 08:00-18:00, €15/hour
  - Evening: Mon-Fri, 18:00-22:00, €20/hour
  - Weekend: Sat-Sun, all day, €25/hour
  - Daily Overtime: >8 hours, €22.50/hour

### Common Commands

```bash
# Seed projects and rate cards
cmd /c "php artisan db:seed --class=ProjectSeeder"
cmd /c "php artisan db:seed --class=RateCardSeeder"

# Refresh and reseed everything
cmd /c "php artisan migrate:fresh --seed"
```

## Testing with Pest

The project uses Pest PHP for testing. Test files are in `tests/` directory.

```bash
# Run all tests
composer test

# Run specific test
cmd /c "php artisan test --filter TestName"
```
