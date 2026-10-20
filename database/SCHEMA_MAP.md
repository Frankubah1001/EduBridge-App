# EduBridge DB Schema Map (MariaDB `edubridge` — 30 tables)

## Auth separation
The admin module is now a **standalone application** in `admin/`, with its own
session namespace (`$_SESSION['admin_user']`) that is fully isolated from the
learner session (`$_SESSION['user']`).

- Learners (`student | parent | mentor`) log in at **`?p=login`** via `api/auth.php` → `is_admin=false`.
- Staff (`admin | teacher | school_admin`) log in at **`admin/index.php?r=login`** via `admin/login.php`
  (bcrypt + 5-attempt lockout + audit + 2h idle timeout → `admin_user` session key).
- `admin/inc/api.php` enforces `admin_is_in()` — a learner session gets **403**.
- Legacy routes still work and delegate to the new app:
  - `?p=admin` → `admin/index.php?r=console`
  - `?p=admin-login` → `admin/index.php?r=login`
  - `api/admin_auth.php` → `admin/login.php`
  - `api/admin_content.php` → `admin/inc/api.php`

## Admin application layout
```
admin/
├── index.php          # router: ?r=login | console | logout
├── login.php          # staff login endpoint (POST)
├── inc/
│   ├── boot.php       # ADMIN_ROOT/APP_ROOT, admin_user(), admin_is_in(), admin_url(), admin_api_out()
│   ├── auth.php       # admin_attempt_login() — bcrypt, lockout, audited
│   ├── api.php        # dispatcher: auth gate + action routing
│   ├── api1.php       # stats, taxonomy, courses, lessons
│   ├── api2.php       # questions
│   ├── api3.php       # papers, moderation, audit
│   ├── api4.php       # settings, schools, plans, vouchers
│   └── api5.php       # mentors, sessions, messages, users
├── pages/             # console shell, login page, tab markup
└── assets/            # admin.css + admin.js (talks only to inc/api.php)
```

## Table groups
```
IDENTITY:  roles, users, schools, guardians, consents
TAXONOMY:  levels → subjects → topics        (admin: Subjects/Topics tab)
CONTENT:   courses → lessons                 (admin: Courses+Lessons tab)
EXAMS:     exams, questions → options, papers → paper_items, attempts, certificates
ENGAGE:    xp_events, badges → user_badges
SOCIAL:    mentors, sessions, messages, forum_threads
COMMERCE:  plans, payments, vouchers
GROWTH:    notifications, bundles, sync_queue, study_plans, audit_log
```

## Key relations
- `courses(level_id, subject_id)`, `lessons(course_id, topic_id)`
- `questions(subject_id, topic_id, exam_id)` → `options(question_id)`
- `papers(exam_id, subject_id)` → `paper_items(paper_id, question_id)`
- `attempts(user_id, paper_id)` · `study_plans(user_id, subject_id)`
- `mentors.user_id → users.id` (nullable) · `vouchers.plan_id → plans.id`
- Only `status='published'` rows reach learners; drafts stay in admin.

## Seeded now
499 questions · 5 courses · 9 lessons · 4 plans (free/medium/pro/school) · 2 active
vouchers (`SCHOLAR-2026` → Pro, `EDUBRIDGE-MEDIUM-2026` → Medium) · 3 mentors ·
6 schools · admin `admin@edubridge.ng / admin123`

## Maintenance scripts
- `php database/migrate_admin_seed.php` — adds the Medium plan, plan feature copy, starter vouchers, drops the stray `mentors.user_id2` column.
- `php database/restore_seed_rows.php` — idempotently restores any seed rows missing from the DB.
- `php database/verify_seed.php` — compares live data against `data/*.json` and checks for orphan rows.
- `php database/verify_api_fields.php` — asserts every admin API response exposes the fields `admin.js` renders.
- Re-seed: `php database/seed.php` · Re-import: `mysql -u root -p < database/schema.sql`
