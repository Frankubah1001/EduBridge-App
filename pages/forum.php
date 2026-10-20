<?php
if(!setting_on('features.forum',true)){ echo "<div class='card'><h2>💬 Forum disabled</h2><p class='muted'>Admin turned this feature off.</p></div>"; return; }
$posts=[];
try{ if(db_ok()){ $posts=db()->query("SELECT t.id, t.title AS topic, t.tag, (SELECT name FROM users u WHERE u.id=t.author_id) AS author, 0 AS replies FROM forum_threads t WHERE t.status='open' ORDER BY t.id DESC LIMIT 20")->fetchAll();
  foreach($posts as &$p){ if(empty($p['author']))$p['author']='Community'; } } }catch(Exception $e){}
if(!$posts) $posts=json_data('forum.json');
$subjects=db_subjects_all();
?>
<h2>💬 Community Forum</h2><div class="grid g2"><div>
<?php foreach($posts as $p):?><div class="card"><span class="pill"><?=htmlspecialchars($p['tag']??'General')?></span>
<h3 style="margin:.3em 0"><?=htmlspecialchars($p['topic'])?></h3><p class="muted">by <?=htmlspecialchars($p['author'])?></p>
<button class="btn btn-ghost" onclick="replyDB(<?=$p['id']??0?>)">Reply (+3 XP)</button></div><?php endforeach;?></div>
<div class="card"><h3>✍️ Start a discussion</h3><input id="ft" placeholder="e.g. How to pass JAMB physics?">
<select id="ft_sub"><option value="">General</option><?= dd_options(array_map(fn($s)=>['name'=>$s['name']],$subjects),'name','name') ?></select>
<button class="btn btn-primary" onclick="post()">Post</button><div id="mine"></div>
<script>
async function replyDB(id){ addXP(3,'Forum reply'); if(id){ try{ await fetch('api/forum.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({thread_id:id,body:'Thanks for sharing!'})}); }catch(e){} } toast('💬 Reply saved — +3 XP'); }
async function post(){ const v=document.getElementById('ft').value; if(!v.trim())return;
 const tag=document.getElementById('ft_sub').value||'General';
 try{ const r=await (await fetch('api/forum.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({title:v,tag})})).json();
  document.getElementById('mine').innerHTML+=`<div class="opt">🆕 ${v} <span class="pill">${tag}</span></div>`;
 }catch(e){ document.getElementById('mine').innerHTML+=`<div class="opt">🆕 ${v} <span class="pill">you</span></div>`; }
 document.getElementById('ft').value='';
 queueSync('forum',{body:v}); const b=await store.getKV('badges',[]); if(!b.includes('helper')){b.push('helper');await store.putKV('badges',b);toast('🏅 Badge: Community Helper!');} addXP(5,'Forum post'); }</script>
</div></div>
