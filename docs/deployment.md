# Deployment

## Build & optimize
```bash
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

## Migrate
```bash
php artisan migrate --force
```


## Env
Ensure correct `.env` and file permissions on `storage/` and `bootstrap/cache/`.
