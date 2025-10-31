# Alguskaiciuokle - Cloud Initialization Guide

## Project Overview
**Project Name:** Alguskaiciuokle
**Framework:** Laravel 12
**CSS Framework:** Tailwind CSS with Custom Components
**Icons:** FontAwesome (via npm)
**Build Tool:** Vite
**Authentication:** Laravel Breeze

## Technology Stack

### Backend
- **PHP:** ^8.2
- **Laravel Framework:** ^12.0
- **Laravel Tinker:** ^2.10.1

### Frontend
- **Tailwind CSS:** ^3.1.0
- **Alpine.js:** ^3.4.2
- **FontAwesome:** @fortawesome/fontawesome-free
- **Vite:** ^7.0.7
- **Axios:** ^1.11.0

### Development Tools
- **Laravel Breeze:** ^2.3
- **Laravel Pint:** ^1.24 (Code Style)
- **Pest PHP:** ^4.1 (Testing)
- **Laravel Sail:** ^1.41 (Docker environment)

## Project Structure

```
alguskaiciuokle/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   └── DashboardController.php (Admin panel controller)
│   │   │   ├── User/
│   │   │   │   └── DashboardController.php (User dashboard controller)
│   │   │   └── Auth/ (Authentication controllers)
│   │   └── Middleware/
│   │       ├── CheckRole.php
│   │       └── RoleMiddleware.php
│   ├── Models/
│   │   ├── User.php (with roles relationship)
│   │   └── Role.php
│   └── View/Components/ (Blade components)
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 2024_01_01_000000_create_roles_table.php
│   │   ├── 2024_01_01_000001_create_role_user_table.php
│   │   └── 2025_10_31_000000_add_initial_password_to_users_table.php
│   └── seeders/
│       ├── RoleSeeder.php (Creates admin & user roles)
│       └── UserSeeder.php (Creates admin, user, and 10 workers)
├── resources/
│   ├── css/
│   │   └── app.css (Tailwind + FontAwesome + Custom components)
│   ├── js/
│   │   └── app.js (Alpine.js + Bootstrap)
│   └── views/
│       ├── admin/ (Admin panel views with modern styling)
│       ├── user/ (User dashboard views)
│       ├── auth/ (Authentication views with FontAwesome icons)
│       ├── layouts/ (App and guest layouts)
│       └── components/ (Reusable Blade components)
├── routes/
│   ├── web.php (Application routes)
│   └── auth.php (Authentication routes)
├── public/ (Compiled assets)
├── package.json (NPM dependencies)
├── composer.json (PHP dependencies)
├── tailwind.config.js (Tailwind configuration)
├── vite.config.js (Vite configuration)
└── .env.example (Environment configuration template)
```

## Features Implemented

### Authentication & Authorization
- ✅ Role-based access control (Admin/User)
- ✅ Laravel Breeze authentication scaffolding
- ✅ Custom role middleware
- ✅ User model with role relationships

### Admin Features
- ✅ Admin Dashboard with statistics (Total Users, Admins, Regular Users)
- ✅ User Management CRUD operations
- ✅ View user passwords (stored in `initial_password` field)
- ✅ Reset user passwords
- ✅ Modern UI with FontAwesome icons
- ✅ Gradient headers and stat cards

### User Features
- ✅ User Dashboard with profile information
- ✅ Profile management
- ✅ Quick actions panel
- ✅ Modern card-based interface

### UI/UX Enhancements
- ✅ FontAwesome icons throughout the application
- ✅ Custom Tailwind CSS component classes
- ✅ Gradient card headers
- ✅ Responsive design
- ✅ Modern color scheme (Indigo primary color)
- ✅ Hover effects and transitions
- ✅ Icon-enhanced navigation
- ✅ Form inputs with icon prefixes

### Database Seeding
- ✅ Admin user (admin@admin.com / admin123)
- ✅ Regular user (user@user.com / user123)
- ✅ 10 Worker accounts (worker1-10@algus.com / worker{n}pass)

## Custom CSS Components

The application includes custom Tailwind components defined in `resources/css/app.css`:

- **Buttons:** `.btn-primary`, `.btn-secondary`, `.btn-danger`, `.btn-success`
- **Cards:** `.card`, `.card-header`, `.card-body`
- **Forms:** `.input-group`, `.form-input`
- **Badges:** `.badge-success`, `.badge-info`, `.badge-warning`, `.badge-danger`
- **Tables:** `.table`, `.table-header`, `.table-body`, `.table-cell`
- **Stats:** `.stat-card`, `.stat-card-body`, `.stat-icon`
- **Alerts:** `.alert-success`, `.alert-error`, `.alert-info`, `.alert-warning`

## Environment Setup

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL/PostgreSQL/SQLite database

### Installation Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/ArturasMisevicius/alguskaiciuokle.git
   cd alguskaiciuokle
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database** (edit `.env` file)
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=alguskaiciuokle
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run migrations and seeders**
   ```bash
   php artisan migrate:fresh --seed
   ```

7. **Build frontend assets**
   ```bash
   npm run build
   ```

8. **Start development server**
   ```bash
   php artisan serve
   ```

9. **Access the application**
   - URL: http://localhost:8000
   - Admin: admin@admin.com / admin123
   - User: user@user.com / user123

## User Accounts

### Default Admin
- **Email:** admin@admin.com
- **Password:** admin123
- **Role:** Admin

### Default User
- **Email:** user@user.com
- **Password:** user123
- **Role:** User

### Worker Accounts (1-10)
- **Email Pattern:** worker{n}@algus.com (e.g., worker1@algus.com)
- **Password Pattern:** worker{n}pass (e.g., worker1pass)
- **Role:** User

## Routes

### Public Routes
- `/` - Welcome page
- `/login` - Login page
- `/register` - Registration page

### Admin Routes (Prefix: `/admin`)
- `/admin/dashboard` - Admin dashboard
- `/admin/users` - User management list
- `/admin/users/create` - Create new user
- `/admin/users/{user}/edit` - Edit user
- `/admin/users/{user}` - Delete user

### User Routes (Prefix: `/user`)
- `/user/dashboard` - User dashboard
- `/user/profile` - User profile

## Development Commands

### Build Assets
```bash
# Development build (watch mode)
npm run dev

# Production build
npm run build
```

### Database
```bash
# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Fresh migrations with seeding
php artisan migrate:fresh --seed

# Seed database only
php artisan db:seed
```

### Code Quality
```bash
# Format code with Pint
./vendor/bin/pint

# Run tests
php artisan test
```

## Security Considerations

### Password Storage
- Passwords are hashed using Laravel's default bcrypt hashing
- `initial_password` field stores plaintext passwords for admin viewing
- **Important:** In production, consider removing or encrypting `initial_password` field

### Role-Based Access
- Middleware protects admin routes
- Role checking on user model
- Session-based authentication

## Deployment Checklist

### Pre-Deployment
- [ ] Update `.env` with production values
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate new `APP_KEY`
- [ ] Configure production database
- [ ] Set up proper file permissions
- [ ] Run `composer install --optimize-autoloader --no-dev`
- [ ] Run `npm run build`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`

### Post-Deployment
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Seed database: `php artisan db:seed --force`
- [ ] Set up SSL certificate
- [ ] Configure web server (Nginx/Apache)
- [ ] Set up queue workers if using
- [ ] Configure cron for scheduled tasks
- [ ] Set up backup system
- [ ] Monitor error logs

## Support & Documentation

### Laravel Resources
- Official Documentation: https://laravel.com/docs
- Laracasts: https://laracasts.com
- Laravel News: https://laravel-news.com

### Package Documentation
- Tailwind CSS: https://tailwindcss.com/docs
- FontAwesome: https://fontawesome.com/docs
- Alpine.js: https://alpinejs.dev/start-here
- Vite: https://vitejs.dev/guide

## License
MIT License

## Author
Arturas Misevicius

## Repository
https://github.com/ArturasMisevicius/alguskaiciuokle
