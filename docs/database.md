# Database

## Migrations
```bash
php artisan migrate
php artisan migrate:rollback
```

## Seeders
```bash
php artisan db:seed
# or a specific seeder
php artisan db:seed --class=Database\\Seeders\\DatabaseSeeder
```

## Sample data
This project includes seeders for users, projects, rate cards, and timesheets if present.

## Factories
Use model factories for generating test data.
