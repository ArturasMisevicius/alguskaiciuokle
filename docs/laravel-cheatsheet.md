# Laravel Cheatsheet (Queues & Scheduling)

## Queues
```bash
php artisan queue:work
php artisan queue:retry all
php artisan queue:failed
```

Configure `QUEUE_CONNECTION` in `.env` (e.g., `database`, `redis`).

## Scheduler
```bash
php artisan schedule:run
```
Add tasks in `app/Console/Kernel.php`.

## Useful
```bash
php artisan tinker
php artisan route:list
php artisan config:cache
php artisan cache:clear
php artisan optimize:clear
```
