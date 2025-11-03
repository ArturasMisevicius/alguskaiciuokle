# Tests

## Running tests
```bash
php artisan test
# or
./vendor/bin/phpunit
```

## Coverage (if Xdebug available)
```bash
XDEBUG_MODE=coverage php artisan test --coverage --min=85
```

## Testing guidelines
- Use Pest or PHPUnit for feature/unit tests
- Use database transactions or refresh database
- Prefer factories and seeders
- Keep tests deterministic and isolated
