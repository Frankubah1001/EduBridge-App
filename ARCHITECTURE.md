# EduBridge — Enhanced System Architecture (v2.0)

## What changed vs v1
Kept your 4-layer offline-first core, added **5 new service groups** needed for a complete learning platform:
`AI Personalization` · `Content & Live` · `Engagement` · `Payments & Access` · `Analytics & Safety`.

```
+-----------------------------------------------------------------------------------+
|                         CLIENT LAYER — PWA / Web (PHP shell + JS)                 |
|  React-ready shell (current: vanilla SPA) | IndexedDB (courses/quiz/queue)        |
|  Service Worker (precache + offline bundles) | Low-data mode | TTS | i18n (EN/HA/YO/IG/Pidgin) |
+------------------------------------------|----------------------------------------+
                                           │ Delta-Sync (REST + retry queue) / WS
                                           ▼
+-----------------------------------------------------------------------------------+
|                        API GATEWAY (Apache/.htaccess + api/router.php)            |
|  TLS · Rate-limit · Router · Session+JWT · RBAC (student/teacher/parent/admin)    |
+------------------------------------------|----------------------------------------+
                                           ▼
+-----------------------------------------------------------------------------------+
|                              BACKEND SERVICES (PHP APIs → scale to Node/Go/Py)    |
|  [A] Diagnostic & Sync  | [B] IRT Assessment (PHP+JS) | [C] Mentorship & Chat     |
|  [D] NEW AI Tutor & Planner (recommend + spaced-repetition + essay assist)        |
|  [E] NEW Content & Live (courses, studio, WebRTC live, media bundles)             |
|  [F] NEW Engagement (XP/streaks/badges/leaderboard/challenges)                    |
|  [G] NEW Payments & Enrollment (Paystack/Flutterwave, vouchers, school licenses)  |
|  [H] NEW Analytics & Safety (dashboards, at-risk predictor, moderation, NDPR log) |
+------------------------------------------|----------------------------------------+
                                           ▼
+-----------------------------------------------------------------------------------+
|                        DATA (MySQL today → Postgres/Redis/R2 prod)                |
|  MySQL (users/courses/attempts/xp) | JSON seed bundles | IndexedDB mirror          |
|  Redis (later) | R2/S3 (media bundles) | Notification queue (mail/SMS/USSD)        |
+-----------------------------------------------------------------------------------+
```

## New Subsystems (12 feature domains)
1. LMS Core · 2. Question Bank & CBT · 3. Adaptive IRT · 4. AI Tutor/Planner
5. Gamification · 6. Live Class & Mentorship · 7. Community (forum/groups)
8. Authoring Studio (teacher) · 9. Analytics (student/teacher/parent/school)
10. Offline & Low-bandwidth kit · 11. Payments & Scholarships · 12. Safety & Compliance

See `FEATURES.md` for full matrix and `database/schema.sql` for data model.
