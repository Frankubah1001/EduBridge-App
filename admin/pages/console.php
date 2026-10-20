<?php
// Admin console shell — staff-only chrome, no learner navigation.
$u=admin_user();
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Console · EduBridge</title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="assets/admin.css"></head>
<body class="admin-body">
<header class="admin-bar"><div class="admin-bar-inner">
  <a class="admin-brand" href="index.php?r=console"><span class="admin-mark">🛡️</span><span>EduBridge <small>Admin Console</small></span></a>
  <nav class="admin-nav">
    <span class="admin-who"><?=htmlspecialchars($u['name']??'')?> <span class="pill navy"><?=htmlspecialchars($u['role']??'')?></span></span>
    <a href="index.php?r=logout">Sign out</a>
  </nav>
</div></header>
<main class="wrap admin-wrap">
  <h2 class="admin-title">🛠️ Admin Console <span class="pill">DB-connected</span></h2>
  <div class="grid g4" id="statCards"><div class="card" style="text-align:center"><div class="stat-num">…</div><div class="muted">Loading…</div></div></div><br>
  <div class="tabs" id="adminTabs">
    <button class="tab active" data-t="overview">📊 Overview</button>
    <button class="tab" data-t="taxonomy">🗂️ Subjects/Topics</button>
    <button class="tab" data-t="courses">📚 Courses+Lessons</button>
    <button class="tab" data-t="questions">❓ Question Bank</button>
    <button class="tab" data-t="papers">📝 Exam Papers</button>
    <button class="tab" data-t="moderation">🛡️ Moderation</button>
    <button class="tab" data-t="features">⚙️ Features+Branding</button>
    <button class="tab" data-t="schools">🏫 Schools</button>
    <button class="tab" data-t="plans">💳 Plans</button>
    <button class="tab" data-t="mentors">🧑‍🏫 Mentors</button>
    <button class="tab" data-t="users">👥 Users</button>
  </div>
  <?php require __DIR__.'/tab_core.php'; ?>
  <?php require __DIR__.'/tab_manage.php'; ?>
</main>
<div class="toast" id="toast"></div>
<script src="assets/admin.js"></script>
</body></html>
