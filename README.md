# EduBridge AI Learning Platform — Enhanced Edition

Offline-first hybrid PWA for Nigeria — runs on XAMPP (PHP 8 + MySQL) with zero build step.

## Quick Start (XAMPP)

1. Ensure XAMPP Apache is running.
2. Open: `http://localhost/EduBridge/`
3. No DB required for demo — uses `data/*.json` + IndexedDB. Optional MySQL import: `database/schema.sql`.

## Structure

```
index.php               → Landing + App Shell router
config.php              → Brand, constants, helpers
manifest.json + sw.js   → PWA offline engine
assets/css/style.css    → Brand theme (#0A2540 / #00A86B / #FFC107 / #F4F6F9)
assets/js/app.js        → SPA router, IndexedDB, Delta-Sync, Gamification
assets/js/irt.js        → Client IRT engine (theta estimation)
api/*.php               → REST micro-API (diagnostic, courses, quiz, sync, gamification, mentor, ai-tutor)
data/*.json             → Seed curriculum, questions, badges
pages/*.php             → dashboard, courses, lesson, quiz, diagnostics, ai-tutor, mentor, leaderboard, parent, teacher, admin
database/schema.sql     → MySQL/Postgres schema for production
ARCHITECTURE.md         → Enhanced system architecture
FEATURES.md             → Full feature catalogue & roadmap
```

## Demo Accounts
Any email works — pick a role on login. Stored locally (offline-first).

## Brand
- Primary Deep Navy `#0A2540` · Secondary Emerald `#00A86B` · Accent Amber `#FFC107`
- BG Slate `#F4F6F9` · Text Charcoal `#1E293B` · Card `#FFFFFF`
# EduBridge-App
