<div class="grid g2"><div class="card"><h2>Welcome back 👋</h2>
<?php if(!empty($_GET['e'])): ?><div class="opt wrong"><?=htmlspecialchars($_GET['e'])?></div><?php endif; ?>
<div class="tabs"><button class="tab active" onclick="showL('quick',this)">Quick demo</button><button class="tab" onclick="showL('pass',this)">Password login</button></div>
<div id="l-quick">
<p class="muted">Learner quick view — no password. For a real account, <a href="?p=register">register →</a></p>
<form method="POST" action="api/auth.php"><label>Full name</label><input name="name" value="Adaeze Learner" required>
<label>Email</label><input name="email" type="email" value="adaeze@edubridge.ng" required>
<label>Role</label><select name="role"><option value="student">Student</option><option value="teacher">Teacher (demo view)</option><option value="parent">Parent</option></select>
<label class="muted" style="font-weight:400"><input type="checkbox" required style="width:auto"> I consent to NDPR data processing for learning analytics</label><br><br>
<button class="btn btn-primary" style="width:100%">Login →</button></form></div>
<div id="l-pass" style="display:none">
<p class="muted">Log in with the email + password you registered with.</p>
<form method="POST" action="api/auth.php?action=password"><label>Email</label><input name="email" type="email" required>
<label>Password</label><input name="password" type="password" required>
<button class="btn btn-navy" style="width:100%">Log in securely →</button></form></div>
<script>function showL(w,b){ document.querySelectorAll('#l-quick,#l-pass').forEach(()=>{}); document.querySelectorAll('.tab').forEach(x=>x.classList.remove('active')); b.classList.add('active');
 document.getElementById('l-quick').style.display=w=='quick'?'':'none'; document.getElementById('l-pass').style.display=w=='pass'?'':'none'; }</script>
<p class="muted" style="font-size:.82rem">New here? <a href="?p=register">Create account →</a> · 🔒 Staff? <a href="admin/index.php?r=login">admin console →</a></p></div>
<div class="card" style="background:var(--navy);color:#fff;border:0"><h3 style="color:#FFC107">Why EduBridge?</h3><ul style="line-height:2"><li>🧠 IRT adaptive engine finds your exact level</li><li>📴 Full offline — syncs when data returns</li><li>🤖 AI tutor + study planner + TTS</li><li>🏆 XP, streaks, badges, leaderboards</li><li>👩‍🏫 Verified mentors & teacher studio</li><li>🔒 Anti-cheat CBT + NDPR-safe</li></ul></div></div>
