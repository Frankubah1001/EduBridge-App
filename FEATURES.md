# EduBridge Feature Catalogue — What a Complete Learning Platform Needs

> Gap analysis: your v1 covered **offline PWA + sync + IRT + mentorship**. Below adds the missing 80%.

## P0 — MVP Must-have (shipped in this prototype)
- [x] Course catalogue (WAEC/NECO/JAMB mapped) + lesson player + notes/bookmarks
- [x] Past-question bank + timed CBT mock + auto-grading + solution review
- [x] IRT diagnostic (`theta` ability) + mastery bars + weak-topic detector
- [x] AI Tutor (rule-based offline + API-ready for LLM) + Study Planner + Spaced repetition queue
- [x] Gamification: XP, streaks, badges, leaderboard, daily challenge
- [x] Mentorship list + messaging (local + WS-ready) + Community forum seed
- [x] Teacher Studio (create course/quiz) + Grading view
- [x] Dashboards: Student / Teacher / Parent / Admin (analytics charts via canvas)
- [x] Offline bundle downloader + Low-data toggle + Delta-sync queue viewer
- [x] Auth + RBAC + NDPR consent + Anti-cheat (shuffle, timer, tab-blur flag)

## P1 — Next (API stubs included, UI todo)
- [ ] Live classes (WebRTC/Jitsi embed) + attendance + recordings
- [ ] Essay auto-feedback (LLM rubric) + voice input + TTS readout
- [ ] Multilingual content (Hausa/Yoruba/Igbo/Pidgin toggle — dictionary file in `data/i18n.json`)
- [ ] Parent SMS/USSD digest + push notifications
- [ ] Payments: Paystack/Flutterwave + vouchers + school licenses
- [ ] Certificates (PDF + Open Badges) + Career tracks

## P2 — Scale
- [ ] Predictive at-risk model + churn alerts
- [ ] Proctoring (webcam + lockdown) + plagiarism check
- [ ] Marketplace for tutors + internship board
- [ ] Mobile wrappers (Capacitor), SMS quiz via USSD, IVR audio lessons

## Curriculum mapping
`data/curriculum.json` maps: Primary → JSS → SSS → WAEC/NECO/JAMB subjects → topics → lessons.
Add new topics without code changes.

## Suggested roadmap (8 weeks)
Wk1-2 P0 polish + seed 500 real past questions · Wk3 Live + payments sandbox
Wk4 i18n + TTS + low-data · Wk5-6 Authoring + analytics · Wk7 Pilot in 2 schools · Wk8 Hardening + NDPR audit
