# Tests

## Running tests
```bash
php artisan test
# or
./vendor/bin/phpunit
```

## Linting
Format your code before commit:
```bash
./vendor/bin/pint
```

## Testing guidelines
- Use Laravel's built-in testing tools for unit and feature tests
- Use database transactions or refresh database
- Prefer factories and seeders
- Keep tests deterministic and isolated
