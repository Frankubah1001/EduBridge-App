<?php
$u=current_user(); if(!$u){header('Location: ?p=login');exit;}
$school=''; $dbxp=0; $dbstreak=0;
try{ if(db_ok()&&!empty($u['id'])){ $row=db()->query('SELECT u.xp,u.streak,(SELECT name FROM schools s WHERE s.id=u.school_id) AS school FROM users u WHERE u.id='.(int)$u['id'])->fetch();
 if($row){ $dbxp=(int)$row['xp']; $dbstreak=(int)$row['streak']; $school=$row['school']??''; } } }catch(Exception $e){}
?>
<h2>👋 Hello, <?=htmlspecialchars($u['name'])?> <span class="pill"><?=htmlspecialchars($u['role'])?></span> <?php if($school): ?><span class="pill navy">🏫 <?=htmlspecialchars($school)?></span><?php endif; ?></h2>
<div class="grid g4">
<div class="card" style="text-align:center"><div class="xp-ring" style="margin:auto" id="xpRing"><span data-xp>0 XP</span></div><p class="muted">Total XP</p></div>
<div class="card" style="text-align:center"><div class="stat-num" data-streak>🔥 1</div><p class="muted">Day streak</p></div>
<div class="card" style="text-align:center"><div class="stat-num" id="thetaDash">θ 0.0</div><p class="muted">Ability estimate</p></div>
<div class="card" style="text-align:center"><div class="stat-num" id="doneDash">0</div><p class="muted">Lessons done</p></div></div><br>
<div class="grid g2"><div class="card"><h3>📊 Mastery by topic</h3><div id="masteryBox"><p class="muted">Take a diagnostic or quiz to populate this.</p></div>
<canvas id="chart" width="400" height="160" style="width:100%"></canvas></div>
<div class="card"><h3>⚡ Quick actions</h3><p><a class="btn btn-primary" href="?p=courses">Continue learning</a> <a class="btn btn-amber" href="?p=quiz">Mock exam</a></p>
<p><a class="btn btn-ghost" href="?p=ai-tutor">AI Tutor</a> <a class="btn btn-ghost" href="?p=planner">Study Planner</a> <a class="btn btn-ghost" href="?p=sync">Offline bundles</a></p>
<h3>🏅 My badges</h3><div id="badgeBox"><p class="muted">Earn badges by studying.</p></div>
<h3>🔁 Spaced repetition due</h3><div id="srBox"><p class="muted">Missed questions reappear here tomorrow.</p></div></div></div>
<script>
(async()=>{ const xp=await store.getKV('xp',0); document.getElementById('xpRing').style.setProperty('--p',Math.min(100,xp/5)+'%');
 const th=await store.getKV('theta',0); document.getElementById('thetaDash').textContent='θ '+(+th).toFixed(2);
 const done=await store.getKV('lessonsDone',[]); document.getElementById('doneDash').textContent=done.length;
 const m=await store.getKV('mastery',{}); const box=document.getElementById('masteryBox');
 if(Object.keys(m).length){ box.innerHTML=Object.entries(m).map(([k,v])=>`<div class="mastery-row"><span>${k}</span><div class="progress" style="flex:1"><div style="width:${Math.round(v*100)}%"></div></div><b>${Math.round(v*100)}%</b></div>`).join('');
  const c=document.getElementById('chart').getContext('2d'); const ks=Object.keys(m),vs=Object.values(m);
  c.fillStyle='#00A86B'; ks.forEach((k,i)=>{ const h=vs[i]*130; c.fillRect(20+i*60,140-h,40,h); c.fillStyle='#0A2540'; c.fillText(k.slice(0,8),20+i*60,155); c.fillStyle='#00A86B'; }); }
 const b=await store.getKV('badges',[]); if(b.length)document.getElementById('badgeBox').innerHTML=b.map(x=>`<span class="badge-chip">🏅 ${x}</span>`).join('');
 const sr=await store.getKV('sr',[]); if(sr.length)document.getElementById('srBox').innerHTML=sr.slice(0,5).map(x=>`<div class="opt">🔁 ${x.topic}: review Q#${x.q}</div>`).join('');
})();
</script>
