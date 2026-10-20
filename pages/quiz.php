<?php
$subj=$_GET['subject']??''; $exam=$_GET['exam']??''; $topic=$_GET['topic']??'';
$QN=setting_int('features.quiz_n',10); $QMIN=setting_int('features.quiz_minutes',20);
if(!setting_on('features.quiz',true)){ echo "<div class='card'><h2>⏱️ CBT Mock disabled</h2><p class='muted'>Admin turned this feature off. Check back soon.</p></div>"; return; }
$subjects=db_subjects_all(); $exams=db_exams_all();
$topics=[]; try{ if($subj){ $sid=db()->query('SELECT id FROM subjects WHERE name='.db()->quote($subj).' LIMIT 1')->fetchColumn(); if($sid)$topics=db_topics_by_subject($sid); } }catch(Exception $e){}
?>
<div class="card"><div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
<h2 style="margin:0">⏱️ CBT Mock — <?= $subj?htmlspecialchars($subj):'Mixed' ?></h2>
<span class="quiz-timer" id="timer"><?= sprintf('%02d:00',$QMIN) ?></span>
<span class="pill" id="qcount">Q 1/<?=$QN?></span><span class="pill amber" id="antiCheat">🛡️ Proctored</span></div>
<form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;margin:10px 0"><input type="hidden" name="p" value="quiz">
<select name="subject"><option value="">All subjects</option><?= dd_options(array_map(fn($s)=>['name'=>$s['name']],$subjects),'name','name',$subj) ?></select>
<select name="exam"><option value="">All exams</option><?= dd_options(array_map(fn($e)=>['name'=>strtoupper($e['slug']),'slug'=>$e['slug']],$exams),'slug','name',$exam) ?></select>
<?php if($topics): ?><select name="topic"><option value="">All topics</option><?= dd_options(array_map(fn($t)=>['name'=>$t['name']],$topics),'name','name',$topic) ?></select><?php endif; ?>
<button class="btn btn-navy">Start (<?=$QN?> Qs · <?=$QMIN?> min)</button></form>
<p class="muted"><?=$QN?> questions · <?=$QMIN?> min · auto-graded · answers shuffled · tab-switching is flagged.</p>
<div class="progress"><div id="qbar" style="width:10%"></div></div><br>
<div id="quizBox"><p>Loading questions…</p></div>
<p><button class="btn btn-ghost" id="prevBtn">← Prev</button> <button class="btn btn-primary" id="nextBtn">Next →</button>
<button class="btn btn-navy" id="submitBtn" style="display:none">Submit exam</button></p>
<div id="resultBox"></div></div>
<?php
// Quiz connected to settings: XP values from admin
$XP_H=setting_int('xp.quiz_high',50); $XP_M=setting_int('xp.quiz_mid',25); $XP_L=setting_int('xp.quiz_low',10);
?>
<script>
let BANK=[],ORDER=[],ANS={},IDX=0,TLEFT=<?=$QMIN?>*60,FLAGS=0,T0=Date.now();
document.addEventListener('visibilitychange',()=>{ if(document.hidden){FLAGS++;document.getElementById('antiCheat').textContent='🛡️ Flags: '+FLAGS;queueSync('flag',{n:FLAGS});} });
setInterval(()=>{ if(TLEFT<=0)return submit(); TLEFT--; const m=String(Math.floor(TLEFT/60)).padStart(2,'0'),s=String(TLEFT%60).padStart(2,'0'); document.getElementById('timer').textContent=m+':'+s; },1000);
(async()=>{ const qs=new URLSearchParams({resource:'questions',limit:<?=$QN?><?php if($subj){?>,subject:'<?=addslashes($subj)?>'<?php }?><?php if($exam){?>,exam:'<?=addslashes($exam)?>'<?php }?><?php if($topic){?>,topic:'<?=addslashes($topic)?>'<?php }?>});
 const r=await fetch('api/data.php?'+qs); BANK=await r.json();
 let pool=BANK;
 pool=pool.sort(()=>Math.random()-.5).slice(0,<?=$QN?>);
 ORDER=pool.map(q=>({...q,opts:q.options.map((t,i)=>({t,ok:i===q.answer})).sort(()=>Math.random()-.5)}));
 render(); })();
function render(){ const q=ORDER[IDX]; if(!q)return;
 document.getElementById('qcount').textContent=`Q ${IDX+1}/${ORDER.length}`;
 document.getElementById('qbar').style.width=((IDX+1)/ORDER.length*100)+'%';
 document.getElementById('prevBtn').style.display=IDX?'':'none';
 document.getElementById('nextBtn').style.display=IDX<ORDER.length-1?'':'none';
 document.getElementById('submitBtn').style.display=IDX===ORDER.length-1?'':'none';
 document.getElementById('quizBox').innerHTML=`<span class="pill navy">${q.exam} ${q.year}</span> <span class="pill">${q.topic}</span>
  <h3>Q${IDX+1}. ${q.stem}</h3>`+q.opts.map((o,i)=>`<div class="opt ${ANS[q.id]===o.t?'sel':''}" onclick="pick(${q.id},'${o.t.replace(/'/g,"\\'")}')">${String.fromCharCode(65+i)}. ${o.t}</div>`).join('');
}
window.pick=(id,t)=>{ANS[id]=t;render();};
document.getElementById('nextBtn').onclick=()=>{IDX=Math.min(ORDER.length-1,IDX+1);render();};
document.getElementById('prevBtn').onclick=()=>{IDX=Math.max(0,IDX-1);render();};
document.getElementById('submitBtn').onclick=submit;
async function submit(){ let correct=0,resp=[],missed=[];
 ORDER.forEach(q=>{ const right=q.options[q.answer]; const ok=ANS[q.id]===right; if(ok)correct++;
  resp.push({correct:ok,a:q.discrimination,b:q.difficulty,topic:q.topic}); if(!ok)missed.push({q:q.id,topic:q.topic}); });
 const score=Math.round(correct/ORDER.length*100);
 let theta=IRT.estimate(resp); await store.putKV('theta',theta);
 let m=await store.getKV('mastery',{}); ORDER.forEach(q=>{ const k=q.topic; m[k]=m[k]==null?(ANS[q.id]===q.options[q.answer]?1:0):Math.round(((m[k]+(ANS[q.id]===q.options[q.answer]?1:0))/2)*100)/100; }); await store.putKV('mastery',m);
 let sr=await store.getKV('sr',[]); await store.putKV('sr',[...missed,...sr].slice(0,20));
 const secs=Math.round((Date.now()-T0)/1000);
 queueSync('attempt',{score,total:ORDER.length,theta,secs,flags:FLAGS});
 const xp=await addXP(score>=80?<?=$XP_H?>:score>=50?<?=$XP_M?>:<?=$XP_L?>,'Mock '+score+'%');
 document.getElementById('resultBox').innerHTML=`<div class="card" style="border:2px solid var(--emerald)"><h3>${score>=80?'🌟 Excellent!':score>=50?'👍 Good progress!':'💪 Keep pushing!'}</h3>
  <p>Score: <b>${score}%</b> (${correct}/${ORDER.length}) · θ=${theta} (${IRT.masteryLabel(theta)}) · ⏱ ${Math.floor(secs/60)}m ${secs%60}s · Flags: ${FLAGS} · +XP earned</p>
  <h4>Solutions</h4>`+ORDER.map((q,i)=>{const right=q.options[q.answer];const ok=ANS[q.id]===right;
   return `<div class="opt ${ok?'correct':'wrong'}">Q${i+1}. ${q.stem}<br>Your answer: <b>${ANS[q.id]||'—'}</b> · Correct: <b>${right}</b><br><span class="muted">💡 ${q.explanation}</span></div>`;}).join('')+`</div>`;
 window.scrollTo(0,document.getElementById('resultBox').offsetTop);
 try{ await fetch('api/diagnostic.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({responses:resp})}); }catch(e){}
 toast('Exam submitted: '+score+'%'); }
</script>
