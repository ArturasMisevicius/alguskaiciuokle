# Laravel Boost

Laravel Boost enhances AI-assisted development by adding Laravel-specific guidelines and MCP tools to your IDE.

## Install

Already added as a dev dependency in `composer.json`.

Run the installer:

```bash
php artisan boost:install --no-interaction
```

## What it configures
- AI guidelines for Laravel, Pest, Tailwind, and project conventions
- MCP Boost server for supported IDEs (Cursor, VS Code, Claude Code)

## Notes
- Boost is dev-only and has no runtime impact.
- If you change IDEs, re-run the installer to set up integration.

