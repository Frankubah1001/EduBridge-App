<?php
$HB=setting('home.hero_badge','WAEC · NECO · JAMB Ready');
$SQ=setting('home.stat_questions','500+'); $SS=setting('home.stat_subjects','12'); $SO=setting('home.stat_offline','100%');
$TG=setting('tagline',EDU_TAGLINE);
$feat=['quiz'=>setting_on('features.quiz',true),'diag'=>setting_on('features.diagnostic',true),'ai'=>setting_on('features.ai_tutor',true),'rank'=>setting_on('features.leaderboard',true),'mentor'=>setting_on('features.mentor_chat',true)||setting_on('features.forum',true),'sync'=>setting_on('features.offline_bundles',true),'pay'=>setting_on('features.payments',true)];
?>
<div class="hero">
<div><span class="pill amber">🇳🇬 <?=htmlspecialchars($HB)?></span>
<h1>Learn anywhere.<br>Even <span style="color:#FFC107">offline</span>.</h1>
<p><?=htmlspecialchars($TG)?> Adaptive diagnostics (IRT), past-question CBT mocks, AI tutor, mentors & gamified streaks — engineered for low-bandwidth Nigeria.</p>
<p><a class="btn btn-primary" href="?p=register">📚 Start Learning Free</a> <?php if($feat['quiz']): ?><a class="btn btn-amber" href="?p=quiz">⏱️ Take a Mock</a><?php endif; ?></p>
<p class="muted" style="color:#cbd5e1;font-size:.85rem">✔ Works offline · ✔ Low-data mode · ✔ Hausa/Yoruba/Igbo-ready · ✔ NDPR compliant</p></div>
<div class="hero-card"><h3>⚡ 60-second Diagnostic</h3><p class="muted">Discover your mastery level (θ) in any subject.</p>
<div class="grid g3"><div class="card" style="text-align:center"><div class="stat-num"><?=htmlspecialchars($SQ)?></div><div class="muted">Past questions</div></div>
<div class="card" style="text-align:center"><div class="stat-num"><?=htmlspecialchars($SS)?></div><div class="muted">Subjects</div></div>
<div class="card" style="text-align:center"><div class="stat-num"><?=htmlspecialchars($SO)?></div><div class="muted">Offline-ready</div></div></div>
<br><?php if($feat['diag']): ?><a class="btn btn-navy" href="?p=diagnostics" style="width:100%;text-align:center">Run Free Diagnostic →</a><?php endif; ?>
<p class="muted" style="font-size:.8rem">XP: <b data-xp>0 XP</b> · <span class="streak" data-streak>🔥 1</span></p></div>
</div><br>
<h2>Everything a learning platform needs — in one app</h2>
<div class="grid g4">
<div class="card"><h3>📖 Courses + Lessons</h3><p class="muted">Curriculum-mapped micro-lessons with notes & TTS readout.</p><a href="?p=courses">Browse →</a></div>
<?php if($feat['diag']): ?><div class="card"><h3>🧠 Adaptive IRT Tests</h3><p class="muted">Questions get harder/easier based on your θ ability live.</p><a href="?p=diagnostics">Try it →</a></div><?php endif; ?>
<?php if($feat['ai']): ?><div class="card"><h3>🤖 AI Tutor & Planner</h3><p class="muted">Offline-smart tutor + spaced-repetition study plan.</p><a href="?p=ai-tutor">Ask AI →</a></div><?php endif; ?>
<?php if($feat['rank']): ?><div class="card"><h3>🏆 XP, Streaks & Badges</h3><p class="muted">Gamified motivation with school leaderboards.</p><a href="?p=leaderboard">Ranks →</a></div><?php endif; ?>
<?php if($feat['mentor']): ?><div class="card"><h3>👩‍🏫 Mentors + Forum</h3><p class="muted">Verified teachers, 1:1 chat & community Q&A.</p><a href="?p=mentor">Meet mentors →</a></div><?php endif; ?>
<div class="card"><h3>👨‍👩‍👧 Parent & Teacher Views</h3><p class="muted">Progress digests, authoring studio & class analytics.</p><a href="?p=parent">Reports →</a></div>
<?php if($feat['sync']): ?><div class="card"><h3>📦 Offline Bundles</h3><p class="muted">Download a whole subject for zero-data revision.</p><a href="?p=sync">Sync center →</a></div><?php endif; ?>
<?php if($feat['pay']): ?><div class="card"><h3>💳 Freemium + Vouchers</h3><p class="muted">Free core, Pro unlocks, school licenses & scholarships.</p><a href="?p=pricing">Pricing →</a></div><?php endif; ?>
</div>
