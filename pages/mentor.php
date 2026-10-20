<?php
if(!setting_on('features.mentor_chat',true)){ echo "<div class='card'><h2>👩‍🏫 Mentors disabled</h2><p class='muted'>Admin turned this feature off.</p></div>"; return; }
$mentors=[];
try{ if(db_ok()){ $mentors=db()->query("SELECT m.*, (SELECT name FROM subjects s WHERE s.id=m.subject_id) AS subject FROM mentors m WHERE m.verified=1 ORDER BY rating DESC")->fetchAll(); } }catch(Exception $e){}
if(!$mentors) $mentors=json_data('mentors.json');
foreach($mentors as &$mm){ if(empty($mm['subject']))$mm['subject']='General'; }
$sess=[]; try{ if(db_ok()) $sess=db()->query("SELECT s.*, (SELECT name FROM mentors m WHERE m.id=s.mentor_id) AS who FROM sessions s WHERE s.status IN ('scheduled','live') ORDER BY starts_at LIMIT 5")->fetchAll(); }catch(Exception $e){}
?>
<h2>👩‍🏫 Mentors</h2><div class="grid g2"><div>
<?php foreach($mentors as $m):?><div class="card" style="display:flex;gap:12px;align-items:center">
<div style="font-size:2.2rem">🧑‍🏫</div><div style="flex:1"><b><?=$m['name']?></b> <span class="pill <?= $m['status']=='online'?'':'amber' ?>"><?= $m['status']=='online'?'🟢 online':'🌙 offline' ?></span>
<br><span class="muted"><?=$m['subject']?> · <?=$m['school']?> · ⭐ <?=$m['rating']?></span></div>
<button class="btn btn-primary" onclick="chatWith('<?=$m['name']?>')">Chat</button></div><?php endforeach;?>
<div class="card"><h3>📹 Upcoming live classes</h3>
<?php if($sess): foreach($sess as $s): ?><div class="opt">📅 <?=htmlspecialchars($s['starts_at'])?> — <?=htmlspecialchars($s['title'])?> (<?=htmlspecialchars($s['who']??'')?>) <?php if($s['join_url']): ?><a class="btn btn-ghost" href="<?=htmlspecialchars($s['join_url'])?>" target="_blank">Join</a><?php endif; ?></div><?php endforeach; ?>
<?php else: ?><div class="opt">📅 Sat 10:00 — WAEC Maths Clinic <button class="btn btn-ghost" onclick="toast('Link sent when live')">Remind me</button></div>
<div class="opt">📅 Sun 16:00 — JAMB English Sprint</div>
<p class="muted">Admin schedules real sessions in the console (mentorship → sessions).</p><?php endif; ?></div></div>
<div class="card"><h3>💬 <span id="chatName">Chat</span></h3><div class="chat-box" id="mchat"><div class="chat-msg them">👋 Hello! Ask me any question on my subject.</div></div><br>
<div style="display:flex;gap:8px"><input id="mi" placeholder="Type a message…" style="margin:0"><button class="btn btn-primary" onclick="send()">Send</button></div>
<p class="muted" style="font-size:.8rem">Stored in IndexedDB offline · WebSocket/Redis in production.</p></div></div>
<script>
let WHO='Mentor';
async function chatWith(n){ WHO=n; document.getElementById('chatName').textContent='Chat with '+n;
 const h=await store.getKV('chat_'+n,[]); const box=document.getElementById('mchat');
 box.innerHTML=h.map(m=>`<div class="chat-msg ${m.me?'me':'them'}">${m.t}</div>`).join('')||'<div class="chat-msg them">👋 Hello! Ask me anything.</div>'; }
async function send(){ const i=document.getElementById('mi'); if(!i.value.trim())return;
 const box=document.getElementById('mchat'); box.innerHTML+=`<div class="chat-msg me">${i.value}</div>`;
 let h=await store.getKV('chat_'+WHO,[]); h.push({me:1,t:i.value}); const q=i.value; i.value='';
 queueSync('message',{to:WHO,body:q});
 setTimeout(async()=>{ const r='Great question! Here is how to approach it step-by-step. Try 3 similar past questions and send me your answers — I will review. 💪';
  box.innerHTML+=`<div class="chat-msg them">${r}</div>`; box.scrollTop=1e6; h.push({me:0,t:r}); await store.putKV('chat_'+WHO,h); },900);
 await store.putKV('chat_'+WHO,h); box.scrollTop=1e6; addXP(2,'Messaged mentor'); }
chatWith('Mrs. Adaeze Okafor');
</script>
