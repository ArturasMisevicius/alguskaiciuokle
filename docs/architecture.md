# Architecture

- MVC using Laravel:
  - Models in `app/Models`
  - Controllers in `app/Http/Controllers`
  - Views in `resources/views` (Blade templates)
- Domain services in `app/Services` (business logic)
- Jobs/Queues in `app/Jobs`
- Events/Listeners in `app/Events` and `app/Listeners`
- Routes in `routes/web.php` (no API layer)
- Config in `config/*`
- Migrations/Seeders in `database/*`

Follow single-responsibility and keep controllers thin; push logic to services. Use Blade layouts/components for UI composition and Filament for admin/UI scaffolding where appropriate.
