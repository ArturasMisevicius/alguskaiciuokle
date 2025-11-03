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
Set database credentials in `.env`.

## 3) Database
```bash
php artisan migrate --seed
```

## 4) UI dependencies (Filament)
```bash
composer require filament/filament
php artisan filament:install
npm run build # or npm run dev
```

## 5) Serve (Blade views)
```bash
php artisan serve
```

See [Blade Views](./blade.md) and [UI (Filament)](./ui-filament.md) for details.
