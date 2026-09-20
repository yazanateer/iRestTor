# Tech Stack

## Backend

- **PHP 8.3** / **Laravel 13**
- **Inertia.js** (`inertiajs/inertia-laravel ^2.0`) — server-side adapter; controllers return `Inertia::render(...)` instead of JSON or Blade views
- **Laravel Sanctum** — included but auth is session-based (Breeze scaffold); Sanctum is not actively used for API tokens
- **Tightenco Ziggy** — exposes named Laravel routes to the frontend via `ZiggyVue`
- **Laravel Breeze** (dev) — auth scaffolding source; generated controllers in `Auth/` remain in place
- **Laravel Pint** (dev) — PHP code style fixer (PSR-12 based)
- **PHPUnit 12** — test runner

## Frontend

- **Vue 3** (`^3.4`) with **TypeScript** (`^6.0`)
- **Inertia.js Vue 3 adapter** (`@inertiajs/vue3 ^2.0`)
- **Vite 8** with `laravel-vite-plugin` and `@vitejs/plugin-vue`
- **Tailwind CSS v3** with `@tailwindcss/forms` plugin; font is **Figtree**
- **Bootstrap 5.3** + **Bootstrap Icons 1.13** — imported globally in `app.ts`; both LTR and RTL CSS bundles are loaded (`bootstrap.min.css` + `bootstrap.rtl.min.css`)
- **vue-i18n v11** — three locales: `en`, `ar`, `he`. Arabic and Hebrew are RTL; the app sets `document.dir` automatically based on the active locale
- **axios** — HTTP client (configured in `bootstrap.js`)
- **qs-esm** — query string serialization

## Database & Queue

- **SQLite** locally (`database/database.sqlite`)
- **MySQL** in production
- **DB-driven queue** — `QUEUE_CONNECTION=database`. No Redis. Queue is consumed by `php artisan queue:listen`.

## External Services

- **SMS** — custom HTTP provider via `SmsService`. Config keys: `services.sms.url`, `services.sms.token`, `services.sms.api_key`.
- **WhatsApp** — Twilio API via `WhatsappService`. Config keys: `services.twilio.sid`, `services.twilio.token`, `services.twilio.whatsapp_from`.

## Common Commands

```bash
# Full dev environment (server + queue + log tail + vite, concurrently)
composer dev

# First-time setup
composer setup

# Run tests
composer test
# or
php artisan test

# Database migrations
php artisan migrate

# Build frontend assets
npm run build

# Frontend dev server (Vite only — use composer dev to run everything together)
npm run dev

# Send WhatsApp reminders for tomorrow's appointments (meant to be scheduled)
php artisan appointments:send-whatsapp-reminders

# Format PHP code
./vendor/bin/pint
```

## Key Config Conventions

- Middleware aliases registered in `bootstrap/app.php`: `admin` → `EnsureUserIsAdmin`, `manager` → `EnsureUserIsManager`
- Shared Inertia props (all pages receive): `auth.user`, `flash.success`, `flash.error`
- Asset versioning handled by `laravel-vite-plugin`; single entry point: `resources/js/app.ts`
- File uploads (logos, cover images) stored under `storage/app/public/business-branding/` via the `public` disk