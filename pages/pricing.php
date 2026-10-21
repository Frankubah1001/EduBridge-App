<?php
$plans=db_plans(); $payOn=setting_on('features.payments',true);
function naira($k){ return '₦'.number_format(($k?:0)/100); }
?>
<h2>💳 Simple pricing, free core forever</h2><div class="grid g3">
<?php foreach($plans as $p): ?>
<div class="card" <?= $p['slug']=='pro'?'style="border:2.5px solid var(--emerald)"':'' ?>>
<?php if($p['slug']=='pro'): ?><span class="pill">Most popular</span><?php endif; ?>
<h3><?=htmlspecialchars($p['name'])?></h3>
<div class="stat-num price-naira"><?= $p['price_kobo']?naira($p['price_kobo']).'<span style="font-size:1rem">/'.$p['term'].'</span>':($p['slug']=='school'?'Custom':'₦0') ?></div>
<p class="muted"><?=htmlspecialchars($p['features']?:($p['slug']=='free'?'Courses · CBT mocks · AI tutor (daily limit) · offline bundles':($p['slug']=='pro'?'Unlimited AI · adaptive plans · mentors · certificates · parent SMS · no ads':'Teacher studio · class analytics · bulk vouchers · scholarships')))?></p>
<?php if($p['slug']=='free'): ?><a class="btn btn-ghost" href="?p=register">Start free</a>
<?php elseif($payOn): ?><button class="btn btn-primary" onclick="toast('Paystack checkout opens in production (sandbox)')"><?=$p['slug']=='school'?'Contact sales':'Upgrade (Paystack)'?></button>
<?php else: ?><span class="pill amber">Payments paused by admin</span><?php endif; ?>
</div><?php endforeach; ?>
<div class="card"><h3>🎟️ Voucher</h3><p class="muted">Have a school or scholarship code?</p><input id="vcode" placeholder="e.g. SCHOLAR-2026"><button class="btn btn-navy" onclick="redeem()">Apply voucher</button><div id="vout"></div></div></div>
<p class="muted">Payments: Paystack/Flutterwave + bank transfer + vouchers. No learner is locked out — scholarship pool covers indigent students.</p>
<script>async function redeem(){ const code=document.getElementById('vcode').value; if(!code.trim())return;
 const r=await (await fetch('api/voucher.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({code})})).json().catch(()=>({ok:false}));
 document.getElementById('vout').innerHTML=r.ok?`<p class="pill">✅ ${r.plan} activated</p>`:`<p class="pill amber">${r.error||'Invalid code'}</p>`; }</script>
