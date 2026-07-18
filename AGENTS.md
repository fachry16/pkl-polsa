# PIKOBE Polsa — Agent Guide

## Tech stack
- Laravel 12, PHP 8.2+, Blade + Alpine.js, Tailwind (v3), Vite
- MySQL via Laragon (`.env`), SQLite as default (`.env.example`)

## Key commands
| Command | What it does |
|---|---|
| `composer run dev` | Starts server + queue + logs + Vite concurrently |
| `composer run test` | Runs `config:clear` then PHPUnit (all tests) |
| `composer run setup` | Full first-time setup (composer install, .env, key:generate, migrate, npm install, npm build) |
| `npm run build` | Vite production build |
| `php artisan test` | Runs PHPUnit (bypasses config:clear) |
| `php artisan migrate` | Run migrations |

## Testing
- PHPUnit (`phpunit.xml`): Unit + Feature suites
- SQLite `:memory:` in tests (always)
- Run a single test: `php artisan test --filter=TestName`
- Run a suite: `php artisan test --testsuite=Feature`

## Auth & roles
- Role-based middleware in `bootstrap/app.php`:
  - `role:admin`, `role:direktur`
  - `Kaprodi` (admin OR dosen with `jabatan === 'Kaprodi'`)
- User model has `role` field (admin, dosen, direktur) + `isKaprodi()` helper

## Architecture
- Domain models in `app/Models/` (17 models: RPS, CPL, CPMK, MataKuliah, Kurikulum, etc.)
- Controllers in `app/Http/Controllers/` (resourceful + custom action methods)
- Views in `resources/views/` as Blade templates
- Routes: `routes/web.php` (all web routes), `routes/auth.php` (Breeze auth), `routes/console.php` (Artisan commands)
- No API routes

## Database
- 28 migrations (created 2026-06-10 through 2026-06-16)
- Session, cache, queue all use `database` driver
- Seeders in `database/seeders/` (basic User factory)

## Code style
- PSR-4: `App\` → `app/`, `Database\` → `database/`, `Tests\` → `tests/`
- Indent: 4 spaces, LF line endings (per `.editorconfig`)
- Laravel Pint (`laravel/pint`) available for formatting
