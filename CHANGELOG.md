# Changelog

All notable changes to the Alguskaiciuokle project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [Unreleased]

### Added - 2025-11-03
- Admin Tariff management (CRUD) and pricing fallback by time bands

### Added - 2025-10-31

#### User Management Features
- **Created 10 worker accounts** with unique credentials
  - Email pattern: worker{1-10}@algus.com
  - Password pattern: worker{1-10}pass
  - All workers assigned 'user' role
  - Location: `database/seeders/UserSeeder.php:48-65`

- **Admin password viewing feature**
  - Added `initial_password` field to users table
  - Migration: `database/migrations/2025_10_31_000000_add_initial_password_to_users_table.php`
  - Admins can view user passwords in admin panel
  - Password display in users table at `resources/views/admin/users/index.blade.php:83-87`
  - Password display in edit form at `resources/views/admin/users/edit.blade.php:65-72`

- **Admin password reset feature**
  - Admins can reset user passwords from edit page
  - New password field in edit form at `resources/views/admin/users/edit.blade.php:75-89`
  - Controller update logic at `app/Http/Controllers/Admin/DashboardController.php:80-107`
  - Updates both hashed and initial_password fields

#### UI/UX Enhancements

##### CSS Framework & Styling
- **Installed FontAwesome** package via npm (@fortawesome/fontawesome-free)
- **Enhanced Tailwind CSS** with custom component classes in `resources/css/app.css`:
  - Button components: `.btn-primary`, `.btn-secondary`, `.btn-danger`, `.btn-success`
  - Card components: `.card`, `.card-header`, `.card-body`
  - Form components: `.input-group`, `.form-input`
  - Badge components: `.badge-success`, `.badge-info`, `.badge-warning`, `.badge-danger`
  - Table components: `.table`, `.table-header`, `.table-body`, `.table-cell`
  - Stat components: `.stat-card`, `.stat-card-body`, `.stat-icon`
  - Alert components: `.alert-success`, `.alert-error`, `.alert-info`, `.alert-warning`

##### Admin Interface Updates
- **Admin Dashboard** (`resources/views/admin/dashboard.blade.php`)
  - Added FontAwesome icons to header and sections
  - Implemented gradient card headers (indigo gradient)
  - Redesigned statistics cards with icon badges
  - Added "Quick Actions" section with icon buttons
  - Enhanced visual hierarchy with modern card design

- **User Management List** (`resources/views/admin/users/index.blade.php`)
  - Added FontAwesome icons to table headers
  - User avatars with circular icon backgrounds
  - Password display with lock icon and styled box
  - Role badges with shield/user icons
  - Hover effects on table rows
  - Modern action buttons (Edit/Delete) with icons
  - Gradient table header

- **User Edit Form** (`resources/views/admin/users/edit.blade.php`)
  - Icon-prefixed form inputs (user, envelope, id-badge, lock, key icons)
  - Gradient card header
  - Modern button styling with icons
  - Enhanced password display section
  - Improved form layout and spacing

- **User Create Form** (`resources/views/admin/users/create.blade.php`)
  - Icon-prefixed form inputs
  - Gradient card header
  - Consistent button styling with icons
  - Modern form design matching edit form

##### User Interface Updates
- **User Dashboard** (`resources/views/user/dashboard.blade.php`)
  - Redesigned with modern card layout
  - Icon-enhanced information display
  - Stat cards with icon badges
  - Quick actions panel with icons
  - Improved visual hierarchy

##### Authentication Interface
- **Login Page** (`resources/views/auth/login.blade.php`)
  - Added welcome header with icon
  - Icon-prefixed input fields (envelope, lock icons)
  - Enhanced "Remember me" checkbox with icon
  - Modern button styling
  - Improved visual design and spacing

##### Navigation Enhancements
- **Main Navigation** (`resources/views/layouts/navigation.blade.php`)
  - Added icons to navigation links (dashboard, users, profile)
  - User dropdown with user-circle icon
  - Replaced SVG chevron with FontAwesome icon
  - Profile and logout links with icons
  - Enhanced visual consistency

#### Documentation
- **Created cloud.md** - Comprehensive initialization and deployment guide
  - Project overview and technology stack
  - Detailed project structure
  - Feature documentation
  - Installation instructions
  - User account reference
  - Route documentation
  - Development commands
  - Security considerations
  - Deployment checklist

- **Created CHANGELOG.md** - This file for tracking all project changes

### Changed

#### Database Schema
- **User model** (`app/Models/User.php:21-26`)
  - Added `initial_password` to fillable fields
  - Maintains plaintext password for admin viewing

#### Seeders
- **UserSeeder** (`database/seeders/UserSeeder.php`)
  - Updated to store initial_password for admin, user, and all workers
  - Password handling improved for consistency

#### Controllers
- **Admin DashboardController** (`app/Http/Controllers/Admin/DashboardController.php`)
  - `storeUser` method: Now stores initial_password on user creation (line 61)
  - `updateUser` method: Added password reset functionality (lines 80-107)
  - Validates and updates initial_password when new password provided

#### Frontend Build
- **package.json** - Added @fortawesome/fontawesome-free dependency
- **Built assets** - Compiled CSS and JS with FontAwesome integration

### Technical Improvements

#### CSS Architecture
- Centralized component styling using Tailwind @layer components
- Consistent color scheme (Indigo primary, with supporting colors)
- Reusable utility classes for common patterns
- Gradient effects for enhanced visual appeal

#### Icon System
- FontAwesome integration via npm (no CDN dependency)
- Consistent icon usage across all views
- Semantic icon choices matching functionality
- Icon prefixes for form inputs

#### Component Design
- Card-based layouts for better content organization
- Stat cards with visual hierarchy
- Modern button system with consistent styling
- Enhanced table design with hover states

#### User Experience
- Visual feedback on interactive elements
- Consistent spacing and alignment
- Responsive design principles
- Improved accessibility with icon labels

## Git Repository

### Initial Commit - 2025-10-31
- Synchronized project with GitHub
- Repository: https://github.com/ArturasMisevicius/alguskaiciuokle.git
- Configured git user: artur (menokalve78@gmail.com)
- Committed 126 files with 18,369 lines of code

## Installation & Setup Notes

### Important Files to Read
- `/cloud.md` - Complete project documentation and setup guide
- `/CHANGELOG.md` - This file, tracks all changes
- `/.env.example` - Environment configuration template
- `/README.md` - Project README

### First-Time Setup Commands
```bash
# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate:fresh --seed

# Build assets
npm run build

# Start server
php artisan serve
```

### Default Credentials
- **Admin:** admin@admin.com / admin123
- **User:** user@user.com / user123
- **Workers:** worker{1-10}@algus.com / worker{1-10}pass

## Known Issues & Notes

### Security Considerations
- `initial_password` field stores passwords in plaintext for admin viewing
- In production environments, consider encrypting this field or implementing a more secure alternative
- Review password reset workflow for compliance with security policies

### Future Improvements
- Consider implementing password encryption for initial_password field
- Add email notifications for password resets
- Implement audit logging for password changes
- Add two-factor authentication
- Implement activity logs for admin actions

## Breaking Changes
None in this release.

## Deprecated Features
None in this release.

## Removed Features
- **Bootstrap CSS** - Project now uses Tailwind CSS exclusively (no Bootstrap was actually present, confirmed during implementation)

## Version Information
- **Laravel:** 12.0
- **PHP:** 8.2+
- **Tailwind CSS:** 3.1.0
- **FontAwesome:** Latest (via npm)
- **Alpine.js:** 3.4.2

---

**Note:** This changelog should be read at the start of each session to avoid repeating errors and actions. All significant changes must be documented here to maintain project history and prevent duplicate work.
