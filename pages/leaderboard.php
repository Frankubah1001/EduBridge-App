<?php
if(!setting_on('features.leaderboard',true)){ echo "<div class='card'><h2>🏆 Leaderboard disabled</h2><p class='muted'>Admin turned this feature off.</p></div>"; return; }
$CN=setting_int('features.challenge_n',5); $CXP=setting_int('features.challenge_xp',30); $CHON=setting_on('features.challenge',true);
$mySchool=current_user()['school_id']??null;
$nat=[]; $sch=[];
try{ if(db_ok()){ $nat=db_leaderboard(null,10); if($mySchool)$sch=db_leaderboard($mySchool,10); } }catch(Exception $e){}
$medal=['🥇','🥈','🥉'];
?>
<h2>🏆 Leaderboard <span class="pill">live from DB</span></h2>
<div class="card"><div class="tabs"><button class="tab active" onclick="showB('nat',this)">National</button><button class="tab" onclick="showB('sch',this)">My school</button></div>
<div id="b-nat"><table><tr><th>#</th><th>Learner</th><th>School</th><th>XP</th><th>Streak</th></tr>
<?php $i=0; foreach($nat as $r): $i++; ?><tr><td><?= $medal[$i-1]??$i ?></td><td><b><?=htmlspecialchars($r['name'])?></b></td><td><?=htmlspecialchars($r['school']??'—')?></td><td><?= (int)$r['xp'] ?></td><td>🔥 <?= (int)$r['streak'] ?></td></tr><?php endforeach; ?>
<?php if(!$nat): ?><tr><td colspan="5" class="muted">No ranked learners yet — be the first! Your XP: <b data-xp>0 XP</b></td></tr><?php endif; ?></table></div>
<div id="b-sch" style="display:none"><table><tr><th>#</th><th>Learner</th><th>XP</th><th>Streak</th></tr>
<?php $i=0; foreach($sch as $r): $i++; ?><tr><td><?= $medal[$i-1]??$i ?></td><td><b><?=htmlspecialchars($r['name'])?></b></td><td><?= (int)$r['xp'] ?></td><td>🔥 <?= (int)$r['streak'] ?></td></tr><?php endforeach; ?>
<?php if(!$sch): ?><tr><td colspan="4" class="muted">Log in with your school account to see your school board. <a href="?p=register">Register →</a></td></tr><?php endif; ?></table></div>
<?php if($CHON): ?><h3>🎯 Daily challenge</h3><div class="opt">Answer <?=$CN?> mixed questions today → <b>+<?=$CXP?> XP bonus</b> <a class="btn btn-amber" href="?p=quiz">Play now</a></div><?php endif; ?></div>
<script>function showB(w,btn){ document.querySelectorAll('.tab').forEach(x=>x.classList.remove('active')); btn.classList.add('active');
 document.getElementById('b-nat').style.display=w=='nat'?'':'none'; document.getElementById('b-sch').style.display=w=='sch'?'':'none'; }</script>
