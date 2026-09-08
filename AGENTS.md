# Eduva — Agent Guide & Code Standards

## Anti-AI Slop & Ponytail Rule (MANDATORY FOR AGENT)
- **Ponytail Active Always**: Enforce the Ponytail methodology on EVERY task.
  - **YAGNI First**: Ask if code needs to exist at all before writing. No speculative features, interfaces with one implementation, or unused boilerplate.
  - **Use Native Laravel & PHP**: Reach for standard Eloquent, Collections, Form Requests, Blade, Carbon, Str/Arr, native HTML5/CSS before adding dependencies or custom helper abstractions.
  - **Shortest Working Diff**: Fix root causes cleanly at the single source of truth. Avoid symptom patching across multiple callers.
  - **No Prose Slop**: Code first, followed by at most 2-3 concise lines. Do not add redundant code comments that restate what the code clearly does.
  - **No Swallowed Exceptions**: Never mask runtime errors or return dummy fallback arrays to hide broken code logic.

## Tech Stack
- **Framework**: Laravel 12, PHP 8.2+
- **Frontend**: Blade Templates + Alpine.js, Tailwind CSS (v3), Vite
- **Database**: MySQL via Laragon (`.env`), SQLite `:memory:` for testing
- **Code Formatter**: Laravel Pint (`laravel/pint`) / PSR-12

## Key Commands
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

## Auth & Roles
- Role-based middleware in `bootstrap/app.php`:
  - `role:admin`, `role:direktur`
  - `Kaprodi` (admin OR dosen with `jabatan === 'Kaprodi'`)
- User model has `role` field (admin, dosen, direktur, mahasiswa) + `isKaprodi()` helper

## Architecture & Code Style
- Domain models in `app/Models/` (RPS, CPL, CPMK, MataKuliah, Kurikulum, Assessment, etc.)
- Controllers in `app/Http/Controllers/` (resourceful + domain-oriented intent methods)
- Views in `resources/views/` as clean Blade templates
- Routes: `routes/web.php` (web routes), `routes/auth.php` (Breeze auth), `routes/console.php` (Artisan commands)
- No unnecessary API layers or over-engineered repositories. Idiomatic Laravel MVC.
