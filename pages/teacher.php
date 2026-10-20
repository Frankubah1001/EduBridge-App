<?php
$u=current_user(); if(!$u){ header('Location: ?p=login'); exit; }
$courses=[]; $subjects=db_subjects_all(); $levels=db_levels();
try{ if(db_ok()){ $courses=db()->query('SELECT c.*, s.name AS subject FROM courses c LEFT JOIN subjects s ON s.id=c.subject_id ORDER BY c.id DESC LIMIT 50')->fetchAll(); } }catch(Exception $e){}
$mystats=['students'=>0,'lessons'=>0,'questions'=>0];
try{ if(db_ok()&&!empty($u['school_id'])){ $mystats['students']=(int)db()->query('SELECT COUNT(*) c FROM users WHERE school_id='.(int)$u['school_id'])->fetch()['c']; }
 if(db_ok()){ $mystats['lessons']=(int)db()->query('SELECT COUNT(*) c FROM lessons')->fetch()['c']; $mystats['questions']=(int)db()->query('SELECT COUNT(*) c FROM questions')->fetch()['c']; } }catch(Exception $e){}
?>
<h2>👩‍🏫 Teacher Studio</h2>
<div class="grid g4">
<div class="card" style="text-align:center"><div class="stat-num"><?=$mystats['students']?></div><div class="muted">My school learners</div></div>
<div class="card" style="text-align:center"><div class="stat-num"><?=$mystats['lessons']?></div><div class="muted">Lessons in DB</div></div>
<div class="card" style="text-align:center"><div class="stat-num"><?=$mystats['questions']?></div><div class="muted">Questions in DB</div></div>
<div class="card" style="text-align:center"><div class="stat-num"><?=count($courses)?></div><div class="muted">Courses</div></div></div><br>
<div class="grid g2">
<div class="card"><h3>➕ Create micro-lesson (saves to DB)</h3>
<label>Course</label><select id="tc"><?= dd_options(array_map(fn($c)=>['id'=>$c['id'],'name'=>$c['title']],$courses),'id','name') ?></select>
<input id="t1" placeholder="Lesson title"><textarea id="t2" rows="4" placeholder="Lesson body…"></textarea>
<label>Status</label><select id="t3"><option>draft</option><option selected>published</option><option>review</option></select>
<button class="btn btn-primary" onclick="tSaveLesson()">Publish lesson → DB</button><div id="tOut"></div>
<h3>❓ Create question (saves to DB)</h3>
<div style="display:flex;gap:8px"><div style="flex:1"><label>Subject</label><select id="tq_sub"><?= dd_options(array_map(fn($s)=>['id'=>$s['id'],'name'=>$s['name']],$subjects),'id','name') ?></select></div>
<div style="flex:1"><label>Exam</label><select id="tq_exam"><?php foreach(db_exams_all() as $e): ?><option value="<?=$e['id']?>"><?=$e['name']?></option><?php endforeach; ?></select></div></div>
<input id="q1" placeholder="Question stem"><input id="q2" placeholder="Options comma-separated (4)"><input id="q3" placeholder="Correct option number (1-4)">
<button class="btn btn-navy" onclick="tSaveQ()">Add question → DB</button><div id="tqOut"></div></div>
<script>
async function apiPost(action,body){ const r=await fetch('api/teacher.php?action='+action,{method:'POST',body:new URLSearchParams(body)}); return r.json(); }
async function tSaveLesson(){ const r=await apiPost('save_lesson',{course_id:tc.value,title:t1.value,body:t2.value,status:t3.value});
 tOut.innerHTML=r.ok?`<p class="pill">✅ Lesson #${r.id} saved</p>`:`<p class="pill amber">${r.error||'Error'}</p>`; }
async function tSaveQ(){ const opts=q2.value.split(',').map(s=>s.trim()).filter(Boolean);
 const n=+q3.value||1; const r=await apiPost('save_question',{subject_id:tq_sub.value,exam_id:tq_exam.value,stem:q1.value,explanation:'',options_json:JSON.stringify(opts.map((b,i)=>({body:b,correct:i===(n-1)})))});
 tqOut.innerHTML=r.ok?`<p class="pill">✅ Question #${r.id} saved</p>`:`<p class="pill amber">${r.error||'Error'}</p>`; }
</script>
<div class="card"><h3>📊 My school at a glance</h3><div id="tStats"><p class="muted">Loading live stats…</p></div>
<h3>✅ Pending review</h3><div id="tQueue"><p class="muted">Loading…</p></div></div>
<script>(async()=>{ const r=await fetch('api/teacher.php?action=stats').then(r=>r.json()).catch(()=>null); if(!r)return;
 tStats.innerHTML=`<div class="grid g3"><div><div class="stat-num">${r.students}</div><div class="muted">Learners</div></div><div><div class="stat-num">${r.avg_xp}</div><div class="muted">Avg XP</div></div><div><div class="stat-num">${r.attempts}</div><div class="muted">Attempts</div></div></div>`;
 tQueue.innerHTML=(r.queue||[]).map(q=>`<div class="opt">#${q.id} [${q.exam}] ${q.subject} — ${(q.stem||'').slice(0,90)}</div>`).join('')||'<p class="muted">Review queue empty 🎉</p>'; })();</script>
</div></div>
