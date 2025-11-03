# UI with Filament

Filament provides an admin panel and UI components built on Tailwind CSS.

## Install
```bash
composer require filament/filament
php artisan filament:install
```
This publishes config, views, and sets up assets.

## Create a panel/resource (v3 style)
```bash
php artisan make:filament-user
# or
php artisan make:filament-resource Project
```

## Serve assets
Ensure Vite builds assets:
```bash
npm install
npm run dev
# or
npm run build
```

## Auth
By default, Filament uses your Laravel auth. Configure access in `config/filament.php`.

## Styling
Filament uses Tailwind. Customize via your Tailwind config and app CSS.
