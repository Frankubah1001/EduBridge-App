# EduBridge AI Learning Platform — Enhanced Edition

Offline-first hybrid PWA for Nigeria — runs on XAMPP (PHP 8 + MySQL) with zero build step.

## Quick Start (XAMPP)

1. Ensure XAMPP Apache is running.
2. Open: `http://localhost/EduBridge/`
3. No DB required for demo — uses `data/*.json` + IndexedDB. Optional MySQL import: `database/schema.sql`.

### Database setup (optional, unlocks the admin console)

The admin console needs MySQL. Configure your credentials first:

```bash
cp config.local.example.php config.local.php   # then edit your user/password
```

`config.local.php` is git-ignored — never commit real credentials.
Credentials resolve in this order: `config.local.php` -> `EDU_DB_*` env vars -> safe defaults.

Then import the schema and seed it:

```bash
mysql -u root -p < database/schema.sql
php database/seed.php
```

Staff login: `admin@edubridge.ng` / `admin123` at `http://localhost/EduBridge/admin/`.

## Structure

```
index.php               → Landing + App Shell router
config.php              → Brand, constants, helpers
config.local.php        → Local DB credentials (git-ignored)
admin/                  → Standalone admin app (own admin_user session)
  index.php               → Router: ?r=login | console | logout
  login.php               → Staff login (bcrypt + lockout + audit)
  inc/boot.php            → Admin bootstrap, admin_url(), admin_is_in()
  inc/auth.php            → admin_attempt_login()
  inc/api.php             → Admin API dispatcher (auth-gated)
  inc/api1-5.php          → Content / questions / papers / commerce / people
  pages/                  → Console shell + tab markup
  assets/                 → admin.css + admin.js
manifest.json + sw.js   → PWA offline engine
assets/css/style.css    → Brand theme (#0A2540 / #00A86B / #FFC107 / #F4F6F9)
assets/js/app.js        → SPA router, IndexedDB, Delta-Sync, Gamification
assets/js/irt.js        → Client IRT engine (theta estimation)
api/*.php               → REST micro-API (diagnostic, courses, quiz, sync, gamification, mentor, ai-tutor)
data/*.json             → Seed curriculum, questions, badges
pages/*.php             → dashboard, courses, lesson, quiz, diagnostics, ai-tutor, mentor, leaderboard, parent, teacher
database/schema.sql     → MySQL schema for production
database/seed.php       → Idempotent seeder
database/verify_seed.php → Seed-integrity + orphan check
database/verify_api_fields.php → Asserts admin API exposes the fields admin.js renders
ARCHITECTURE.md         → Enhanced system architecture
FEATURES.md             → Full feature catalogue & roadmap
```

## Admin Console

The admin module is a **standalone application** under `admin/` with its own session
namespace (`admin_user`), fully isolated from the learner session (`user`).

- Staff (`admin | teacher | school_admin`) sign in at `admin/index.php?r=login`.
- Learners sign in at `?p=login` and receive **403** from the admin API.
- Legacy routes still work: `?p=admin`, `?p=admin-login`, `api/admin_auth.php`,
  and `api/admin_content.php` all delegate to the new app.

## Demo Accounts
Any email works — pick a role on login. Stored locally (offline-first).
Staff: `admin@edubridge.ng` / `admin123`.

## Brand
- Primary Deep Navy `#0A2540` · Secondary Emerald `#00A86B` · Accent Amber `#FFC107`
- BG Slate `#F4F6F9` · Text Charcoal `#1E293B` · Card `#FFFFFF`
# EduBridge-App
