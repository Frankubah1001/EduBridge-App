<?php
// Admin login page — staff only, no learner nav.
$err=$_GET['e']??'';
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Staff Sign In · EduBridge</title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="assets/admin.css"></head>
<body class="admin-body">
<header class="admin-bar"><div class="admin-bar-inner">
  <a class="admin-brand" href="index.php?r=console"><span class="admin-mark">🛡️</span><span>EduBridge <small>Admin Console</small></span></a>
  <nav class="admin-nav"><a href="../index.php?p=home">← Back to site</a></nav>
</div></header>
<main class="wrap admin-wrap">
<div class="grid g2" style="max-width:900px;margin:40px auto">
  <div class="card" style="border-top:5px solid var(--amber)"><h2>🛡️ Staff Sign In</h2>
  <p class="muted">Admins, teachers &amp; school managers only. Learners use the <a href="../index.php?p=login">student login</a>.</p>
  <?php if($err!==''): ?><div class="opt wrong"><b>⚠️ <?=htmlspecialchars($err)?></b></div><?php endif; ?>
  <form method="POST" action="login.php">
    <label>Staff email</label><input name="email" type="email" value="admin@edubridge.ng" required>
    <label>Password</label><input name="password" type="password" placeholder="••••••••" required>
    <button class="btn btn-navy" style="width:100%">Unlock Console →</button></form>
  <p class="muted" style="font-size:.82rem">Seeded: <b>admin@edubridge.ng / admin123</b> · Rate-limited · every attempt is audit-logged · sessions expire after 2h idle.</p></div>
  <div class="card" style="background:var(--navy);color:#fff;border:0"><h3 style="color:#FFC107">🔒 Separate by design</h3>
  <ul style="line-height:2"><li>Own URL + own session key</li><li>Real password check (bcrypt) — no role picker</li><li>Only staff roles pass the gate</li><li>Brute-force lockout after 5 failures</li><li>All actions audit-logged</li></ul></div>
</div>
</main></body></html>
