# Blade Views

## Structure
- Views live in `resources/views`
- Layouts in `resources/views/layouts`
- Partials/components in `resources/views/components`

## Layouts
Use a base layout and yield sections:
```php
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html>
  <head>
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    @stack('head')
  </head>
  <body class="min-h-screen">
    @include('partials.nav')
    <main class="container mx-auto p-4">
      @yield('content')
    </main>
    @stack('scripts')
  </body>
</html>
```

## Components
```bash
php artisan make:component Alert
```
Renders via `<x-alert type="success">Saved</x-alert>`.

## Useful
- `@vite`, `@csrf`, `@method`, `@error`
- Stacks: `@push('head')` / `@stack('head')`
- Conditionals/loops: `@if`, `@foreach`
