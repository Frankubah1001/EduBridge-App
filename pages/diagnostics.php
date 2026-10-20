<?php
$DN=setting_int('features.diagnostic_n',6); $DXP=setting_int('xp.diagnostic',20);
if(!setting_on('features.diagnostic',true)){ echo "<div class='card'><h2>🧠 Diagnostic disabled</h2><p class='muted'>Admin turned this feature off.</p></div>"; return; }
$subjects=db_subjects_all();
?>
<div class="card"><h2>🧠 Adaptive Diagnostic (IRT)</h2>
<p class="muted">The engine picks each next question by <b>Fisher information</b> — hardest useful question for your current θ. Watch θ move live.</p>
<form method="GET" style="display:flex;gap:8px;flex-wrap:wrap"><input type="hidden" name="p" value="diagnostics">
<select name="subject"><option value="">All subjects (mixed)</option><?= dd_options(array_map(fn($s)=>['name'=>$s['name']],$subjects),'name','name',$_GET['subject']??'') ?></select>
<button class="btn btn-navy">Set subject</button></form>
<p>θ ability: <b id="liveTheta">0.00</b> <span class="pill" id="liveLabel">Developing</span></p>
<div class="progress"><div id="dbar" style="width:0%"></div></div><br>
<div id="dBox"><button class="btn btn-primary" id="startD">▶ Start <?=$DN?>-question diagnostic (+<?=$DXP?> XP)</button></div>
<div id="dResult"></div></div>
<script>
let DBANK=[],D_ANS=[],D_CUR=null,D_THETA=0,D_N=0;const D_MAX=<?=$DN?>;
document.getElementById('startD').onclick=async()=>{ const r=await fetch('api/data.php?resource=questions&limit=60<?= ($_GET['subject']??'')?'&subject='.urlencode($_GET['subject']):'' ?>'); DBANK=await r.json();
 D_ANS=[];D_THETA=0;D_N=0;next(); };
function next(){ if(D_N>=D_MAX)return finish();
 D_CUR=IRT.nextQuestion(D_THETA,DBANK,D_ANS.map(x=>x.id)); if(!D_CUR)return finish();
 document.getElementById('dbar').style.width=(D_N/D_MAX*100)+'%';
 document.getElementById('dBox').innerHTML=`<span class="pill">${D_CUR.subject} · ${D_CUR.topic}</span>
 <h3>${D_CUR.stem}</h3>`+D_CUR.options.map((o,i)=>`<div class="opt" onclick="dans(${i})">${String.fromCharCode(65+i)}. ${o}</div>`).join(''); }
window.dans=async(i)=>{ const ok=i===D_CUR.answer;
 D_THETA=IRT.updateTheta(D_THETA,ok,D_CUR.discrimination,D_CUR.difficulty);
 D_ANS.push({id:D_CUR.id,correct:ok,a:D_CUR.discrimination,b:D_CUR.difficulty,topic:D_CUR.topic}); D_N++;
 document.getElementById('liveTheta').textContent=D_THETA.toFixed(2);
 document.getElementById('liveLabel').textContent=IRT.masteryLabel(D_THETA);
 toast(ok?'✅ Correct! θ → '+D_THETA.toFixed(2):'❌ Review this topic. θ → '+D_THETA.toFixed(2)); next(); };
async function finish(){ await store.putKV('theta',D_THETA); addXP(<?=$DXP?>,'Diagnostic');
 const r=await (await fetch('api/diagnostic.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({responses:D_ANS})})).json().catch(()=>({weak_topics:[...new Set(D_ANS.filter(x=>!x.correct).map(x=>x.topic))],recommendation:'Keep practising weak topics'}));
 document.getElementById('dbar').style.width='100%';
 document.getElementById('dBox').innerHTML=`<p><a class="btn btn-ghost" href="?p=diagnostics">↻ Retake</a> <a class="btn btn-navy" href="?p=quiz">Take full mock →</a></p>`;
 document.getElementById('dResult').innerHTML=`<div class="card" style="border:2px solid var(--amber)"><h3>Result: θ = ${D_THETA.toFixed(2)} (${IRT.masteryLabel(D_THETA)})</h3>
 <p>🎯 Weak topics: <b>${(r.weak_topics||[]).join(', ')||'None — solid!'}</b></p><p>💡 ${r.recommendation||''}</p></div>`; }
</script>
