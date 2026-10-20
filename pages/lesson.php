<?php
$id=(int)($_GET['id']??1); $course=null;
try{ if(db_ok()) $course=db_course($id); }catch(Exception $e){ $course=null; }
if(!$course){ $courses=json_data('courses.json');
  foreach($courses as $c) if($c['id']==$id) $course=$c; if(!$course)$course=$courses[0]; }
$lid=(int)($_GET['lid']??$course['lessons'][0]['id']); $lesson=$course['lessons'][0];
foreach($course['lessons'] as $l) if($l['id']==$lid)$lesson=$l; ?>
<p><a href="?p=courses">← All courses</a></p>
<div class="grid g2"><div class="card"><span class="pill"><?=$course['subject']?></span>
<h2><?=$lesson['title']?></h2><p class="muted"><?=$course['title']?> · ⏱ <?=$lesson['duration_min']?> min</p>
<p style="line-height:1.8;font-size:1.05rem"><?=$lesson['body']?></p>
<p><button class="btn btn-ghost" onclick="speak(document.getElementById('lessonBody').innerText)">🔊 Read aloud (TTS)</button>
<button class="btn btn-amber" onclick="bookmark(<?=$lesson['id']?>)">🔖 Bookmark</button></p>
<p id="lessonBody" style="display:none"><?=$lesson['body']?></p>
<div class="tabs"><?php foreach($course['lessons'] as $l):?><a class="tab <?= $l['id']==$lid?'active':'' ?>" href="?p=lesson&id=<?=$course['id']?>&lid=<?=$l['id']?>"><?=$l['title']?></a><?php endforeach;?></div>
<p><button class="btn btn-primary" onclick="completeLesson(<?=$course['id']?>,<?=$lesson['id']?>)">✅ Mark complete (+10 XP)</button>
<a class="btn btn-navy" href="?p=quiz&subject=<?=$course['subject']?>">Test me on this →</a></p></div>
<div class="card"><h3>📝 My notes</h3><textarea id="note" rows="6" placeholder="Type revision notes… they save offline"></textarea>
<button class="btn btn-ghost" onclick="saveNote(<?=$lesson['id']?>)">💾 Save note</button>
<h3>🔖 Bookmarks</h3><div id="bmBox"><p class="muted">None yet.</p></div>
<h3>↔️ Related past questions</h3><p><a href="?p=quiz&subject=<?=$course['subject']?>">Practice <?=$course['subject']?> CBT →</a></p></div></div>
<?php $XPL=setting_int('xp.lesson',10); ?>
<script>
(async()=>{ const n=await store.getKV('note_<?=$lid?>',''); document.getElementById('note').value=n||'';
 const b=await store.getKV('bookmarks',[]); if(b.length)document.getElementById('bmBox').innerHTML=b.map(x=>'<span class="pill amber">🔖 Lesson '+x+'</span>').join(' '); })();
async function saveNote(id){ const v=document.getElementById('note').value; await store.putKV('note_'+id,v); queueSync('note',{lesson:id}); toast('💾 Note saved offline'); }
async function bookmark(id){ let b=await store.getKV('bookmarks',[]); if(!b.includes(id)){b.push(id);await store.putKV('bookmarks',b);} addXP(2,'Bookmark'); toast('🔖 Bookmarked'); }
async function completeLesson(cid,lid){ let d=await store.getKV('lessonsDone',[]); if(!d.includes(lid)){d.push(lid);await store.putKV('lessonsDone',d);} addXP(<?=$XPL?>,'Lesson '+lid); queueSync('progress',{course:cid,lesson:lid}); toast('✅ +<?=$XPL?> XP!'); }
</script>
