<?php
$u=current_user(); if(!$u){ header('Location: ?p=login'); exit; }
$kids=[]; $kid=$u;
try{ if(db_ok()){
  if(($u['role']??'')=='parent'){
    $kids=db()->query('SELECT u.*, (SELECT name FROM schools s WHERE s.id=u.school_id) AS school FROM users u JOIN guardians g ON g.student_user_id=u.id WHERE g.parent_user_id='.(int)($u['id']??0))->fetchAll();
    if($kids) $kid=$kids[0];
  }
}}catch(Exception $e){}
$smsOn=setting_on('features.parent_sms',false);
?>
<h2>👨‍👩‍👧 Parent Report</h2>
<?php if(($u['role']??'')=='parent' && !$kids): ?><div class="card"><p class="muted">No linked child yet — your child registers with their email, then you link it at <a href="?p=register&role=parent">Register →</a>.</p></div><?php endif; ?>
<div class="grid g2">
<div class="card"><h3><?=htmlspecialchars($kid['name']??'Learner')?> — <?=htmlspecialchars($kid['school']??'no school')?></h3>
<div class="grid g3"><div><div class="stat-num"><?= (int)($kid['xp']??0) ?></div><div class="muted">Total XP (DB)</div></div>
<div><div class="stat-num"><?= (int)($kid['streak']??0) ?></div><div class="muted">Day streak</div></div>
<div><div class="stat-num" id="pAvg">—</div><div class="muted">Avg score</div></div></div><br>
<div id="pMastery"><p class="muted">Loading mastery from attempts…</p></div>
<?php if($smsOn): ?><button class="btn btn-primary" onclick="toast('📩 Weekly SMS digest enabled')">Enable SMS digest</button>
<?php else: ?><p class="muted">📩 SMS digest is off (admin setting).</p><?php endif; ?></div>
<div class="card"><h3>🔔 Recent attempts</h3><div id="pAtt"><p class="muted">Loading…</p></div></div></div>
<script>(async()=>{ const r=await fetch('api/parent.php').then(r=>r.json()).catch(()=>null); if(!r)return;
 document.getElementById('pAvg').textContent=(r.avg??'—')+'%';
 document.getElementById('pMastery').innerHTML=(r.by_subject||[]).map(x=>`<div class="mastery-row"><span>${x.subject}</span><div class="progress" style="flex:1"><div style="width:${x.avg}%"></div></div><b>${x.avg}%</b></div>`).join('')||'<p class="muted">No attempts yet.</p>';
 document.getElementById('pAtt').innerHTML=(r.recent||[]).map(x=>`<div class="opt">${x.created_at||''} — <b>${x.score}%</b> (${x.total} Qs) θ=${x.theta}</div>`).join('')||'<p class="muted">None.</p>'; })();</script>
