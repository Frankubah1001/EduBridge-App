<?php $subjects=db_subjects_all(); $XPL=setting_int('xp.lesson',10); ?>
<h2>🗓️ My Study Planner</h2><div class="card"><p class="muted">Pomodoro blocks · streak-safe · syncs offline.</p>
<div class="grid g3"><div><label>Subject</label><select id="s"><?= dd_options(array_map(fn($x)=>['name'=>$x['name']],$subjects),'name','name') ?></select></div>
<div><label>Focus (min)</label><input id="m" type="number" value="25"></div><div><label>Blocks</label><input id="b" type="number" value="4"></div></div>
<button class="btn btn-primary" onclick="mk()">Build today's sessions</button><div id="out" style="margin-top:12px"></div></div>
<script>const XP_LESSON=<?=$XPL?>;
function mk(){ const s=document.getElementById('s').value,m=document.getElementById('m').value,b=+document.getElementById('b').value;
 document.getElementById('out').innerHTML=Array.from({length:b},(_,i)=>`<div class="opt">🍅 Block ${i+1}: ${s} · ${m} min focus + 5 min break ${i===b-1?`· then 🎉 ${XP_LESSON} XP`:''}</div>`).join('')+`<button class="btn btn-amber" onclick="addXP(XP_LESSON,'Finished study blocks');toast('🎉 Session complete +'+XP_LESSON+' XP')">Finish all (+${XP_LESSON} XP)</button>`; }</script>
