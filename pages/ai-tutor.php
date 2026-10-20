<?php $subjects=db_subjects_all(); if(!setting_on('features.ai_tutor',true)){ echo "<div class='card'><h2>🤖 AI Tutor disabled</h2><p class='muted'>Admin turned this feature off.</p></div>"; return; } ?>
<div class="grid g2"><div class="card"><h2>🤖 AI Tutor</h2><p class="muted">Offline-smart now · connects to LLM API when online. Ask anything.</p>
<div class="chat-box" id="chat"><div class="chat-msg them">👋 Hi! I'm Bridge, your study buddy. Ask me e.g. <i>“explain quadratic equations”</i> or <i>“make me a timetable”</i>.</div></div><br>
<div style="display:flex;gap:8px"><input id="q" placeholder="Ask about any topic…" style="margin:0"><button class="btn btn-primary" onclick="ask()">Send</button>
<button class="btn btn-ghost" onclick="speak(document.getElementById('chat').innerText.slice(-300))">🔊</button></div>
<p class="muted" style="font-size:.8rem">Try: quadratic · photosynthesis · demand & supply · motion · JAMB English · timetable</p></div>
<div class="card"><h3>🗓️ Study Planner (spaced repetition)</h3>
<label>Subject</label><select id="pSub"><?= dd_options(array_map(fn($x)=>['name'=>$x['name']],$subjects),'name','name') ?></select>
<label>Minutes per day</label><input id="pMin" type="number" value="45">
<label>Exam date</label><input id="pDate" type="date">
<button class="btn btn-amber" onclick="genPlan()">Generate plan</button><div id="planOut" style="margin-top:10px"></div></div></div>
<script>
async function ask(q){ q=q||document.getElementById('q').value; if(!q.trim())return;
 const box=document.getElementById('chat'); box.innerHTML+=`<div class="chat-msg me">${q}</div><div class="chat-msg them" id="typing">…</div>`; box.scrollTop=1e6; document.getElementById('q').value='';
 try{ const r=await fetch('api/ai_tutor.php?format=json&q='+encodeURIComponent(q)); const j=await r.json();
  document.getElementById('typing').remove(); box.innerHTML+=`<div class="chat-msg them">${j.answer}</div>`; }catch(e){ document.getElementById('typing').remove(); box.innerHTML+=`<div class="chat-msg them">📴 Offline tutor: break the topic into definition → example → 5 past questions. Try again online for full AI.</div>`; }
 box.scrollTop=1e6; queueSync('tutor',{q}); addXP(2,'Asked tutor'); }
document.getElementById('q').addEventListener('keydown',e=>{if(e.key==='Enter')ask();});
function genPlan(){ const s=document.getElementById('pSub').value,m=+document.getElementById('pMin').value||45;
 const days=['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
 document.getElementById('planOut').innerHTML=days.map((d,i)=>`<div class="opt">📅 ${d}: ${s} — ${Math.round(m*0.5)} min lesson + ${Math.round(m*0.5)} min past questions ${i%3===2?' + 🔁 review day':''}</div>`).join('')+`<p><button class="btn btn-primary" onclick="addXP(5,'Planned week');toast('🗓️ Plan saved offline')">💾 Save plan (+5 XP)</button></p>`;
 queueSync('plan',{s,m}); }
</script>
