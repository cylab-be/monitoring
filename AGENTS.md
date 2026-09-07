# Agent Instructions for Monitoring Repo

## Development Environment
- Start dev environment with `docker compose up` (ensure `.env` is created from `env.dev`).
- UI available at `http://127.0.0.1:8080/`.
- Mailhog available at `http://127.0.0.1:8025`.

## Commands
- **Linting**: Use `vendor/bin/phpcs` (follows PSR2 + custom rules).
- **Typechecking**: Use `vendor/bin/phpstan` (level 5).
- **Testing**: Use `vendor/bin/phpunit`.
- **Frontend Build**: `npm run build` (uses `esbuild.mjs`).
- **Frontend Watch**: `npm run watch`.

## Architecture & Workflow
- **Framework**: Laravel 12.
- **Frontend**: Esbuild with Sass and jQuery.
- **Database/Migrations**: Managed via Laravel Artisan within the Docker container.
- **Architecture**: Monolithic Laravel application; servers "push" state to this interface.
