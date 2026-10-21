<?php require_once __DIR__.'/config.php';
$page = $_GET['p'] ?? 'home';
$allowed = ['home','login','register','admin-login','dashboard','courses','lesson','quiz','diagnostics','ai-tutor','planner','mentor','forum','leaderboard','teacher','parent','admin','sync','pricing'];
if(!in_array($page,$allowed)) $page='home';
$user = current_user();
$title = ucfirst(str_replace('-',' ',$page));
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= $title ?> · <?= EDU_NAME ?></title>
<link rel="manifest" href="manifest.json"><meta name="theme-color" content="#0A2540">
<link rel="stylesheet" href="assets/css/style.css">
<script>window.EDU_USER = <?= json_encode($user) ?>;</script>
<script src="assets/js/irt.js"></script>
<script src="assets/js/app.js"></script></head>
<body>
<div class="offline-bar" id="offlineBar">⚡ Offline mode — your work saves locally & syncs later</div>
<header class="topbar"><div class="topbar-inner">
<a class="brand" href="?p=home"><span class="brand-mark">🌉</span><span>EduBridge<small>AI Learning · NG</small></span></a>
<nav class="navlinks" id="mainNav">
<a href="?p=home" class="<?= $page=='home'?'active':'' ?>">Home</a>
<a href="?p=courses" class="<?= $page=='courses'?'active':'' ?>">Courses</a>
<a href="?p=quiz" class="<?= $page=='quiz'?'active':'' ?>">CBT Mock</a>
<a href="?p=diagnostics" class="<?= $page=='diagnostics'?'active':'' ?>">Diagnostics</a>
<a href="?p=ai-tutor" class="<?= $page=='ai-tutor'?'active':'' ?>">AI Tutor</a>
<a href="?p=mentor" class="<?= $page=='mentor'?'active':'' ?>">Mentors</a>
<a href="?p=leaderboard" class="<?= $page=='leaderboard'?'active':'' ?>">🏆 Ranks</a>
<?php if($user): ?><a href="?p=dashboard">Dashboard</a><a href="api/auth.php?action=logout">Logout (<?=htmlspecialchars(explode(' ',$user['name'])[0])?>)</a>
<?php else: ?><a href="?p=login">Login</a><a href="?p=register" class="active" style="background:var(--emerald);color:#fff">Register</a><?php endif; ?>
</nav></div></header>
<div class="wrap">
<?php
$f = __DIR__."/pages/$page.php";
if(file_exists($f)) include $f; else echo "<div class='card'>Page missing</div>";
?>
</div>
<footer class="footer">🌉 <b style="color:#fff">EduBridge AI</b> · Offline-first learning for Nigeria · WAEC · NECO · JAMB · <span id="syncStatus">● Online</span> · <a href="admin/index.php?r=login" style="color:#64748b;font-size:.78rem">Staff</a></footer>
<div class="toast" id="toast"></div>
<script>if('serviceWorker' in navigator){navigator.serviceWorker.register('sw.js').catch(()=>{});}</script>
</body></html>
