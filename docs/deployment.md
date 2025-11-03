# Deployment

## Build & optimize
```bash
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm ci && npm run build
```

## Migrate
```bash
php artisan migrate --force
```

## Supervisor/Queues
Configure a Supervisor program to run `php artisan queue:work`.

## Cron
Run scheduler every minute:
```
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```

## Env
Ensure correct `.env` and file permissions on `storage/` and `bootstrap/cache/`.
