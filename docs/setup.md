# Setup

## 1) Clone and install
```bash
git clone <repo-url>
cd alguskaiciuokle
composer install --no-interaction --prefer-dist --no-ansi
npm install
```

## 2) Environment
```bash
cp .env.example .env
php artisan key:generate
```
Set database credentials in `.env`. Configure locales (APP_LOCALE), mail, and storage (S3) as needed.

## 3) Database
```bash
php artisan migrate --seed
```

## 4) UI dependencies (Filament CSS/Tailwind)
```bash
composer require filament/filament
php artisan filament:install
npm run build # or npm run dev
```

## 5) Authentication (Fortify via Breeze, Blade views)
- Auth is preconfigured using Laravel Fortify (scaffolded via Breeze) with Blade views.
- Routes: `/login`, `/register`, `/forgot-password`, `/reset-password/{token}`, `/verify-email`.
- After seeding, you can log in with:
  - Admin: `admin@admin.com` / `admin123`
  - User: `user@user.com` / `user123`
- Password reset emails require mail to be configured in `.env` (see Environment docs).

## 5) Queues (optional but recommended)
- Set `QUEUE_CONNECTION=database` (or redis)
```bash
php artisan queue:table && php artisan migrate
```
Run worker in dev:
```bash
php artisan queue:work
```

## 6) Serve (Blade views)
```bash
php artisan serve
```

Optional integrations: configure Stripe keys for billing and S3 for media uploads.
