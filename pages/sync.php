<?php
if(!setting_on('features.offline_bundles',true)){ echo "<div class='card'><h2>📦 Offline bundles disabled</h2><p class='muted'>Admin turned this feature off.</p></div>"; return; }
$subjects=db_subjects_all(); $lowOn=setting_on('features.lowdata',true);
?>
<h2>📦 Sync & Offline Center</h2><div class="grid g2">
<div class="card"><h3>📡 Delta-Sync queue</h3><p class="muted">Every offline action is queued with a timestamp, then batch-sent when online (server-authoritative).</p>
<div id="qBox"><p>Loading…</p></div><p><button class="btn btn-primary" onclick="flushQueue()">☁️ Sync now</button>
<button class="btn btn-ghost" onclick="store.clearQueue().then(()=>location.reload())">Clear queue</button></p></div>
<div class="card"><h3>⬇ Offline bundles <span class="pill">from DB subjects</span></h3>
<?php foreach(array_slice($subjects,0,8) as $s): ?><div class="opt">📕 <?=htmlspecialchars($s['name'])?> <button class="btn btn-ghost" onclick="dlB('<?=addslashes($s['name'])?>')">Download</button></div><?php endforeach; ?>
<?php if($lowOn): ?><h3>🐢 Low-data mode</h3><label><input type="checkbox" id="lowdata" style="width:auto" onchange="store.putKV('lowdata',this.checked);toast(this.checked?'Low-data ON: images off':'Low-data OFF')"> Disable images & auto-media</label><?php endif; ?>
<p class="muted">USSD/SMS quiz (*347*EDU#) + IVR audio lessons ship in P2 for zero-smartphone learners.</p></div></div>
<script>(async()=>{ const q=await store.queueAll();
 document.getElementById('qBox').innerHTML=q.length?q.map((e,i)=>`<div class="opt">#${i+1} <b>${e.op}</b> <span class="muted">${new Date(e.ts).toLocaleString()}</span></div>`).join(''):'<p class="muted">✅ All synced — nothing pending.</p>';
 document.getElementById('lowdata').checked=await store.getKV('lowdata',false); })();
async function dlB(s){ const r=await fetch('api/data.php?resource=questions'); const all=await r.json();
 await store.putKV('bundle_'+s,all.filter(q=>q.subject===s)); queueSync('download',{subject:s}); addXP(5,'Bundle '+s); toast('📦 '+s+' bundle saved offline'); }</script>
