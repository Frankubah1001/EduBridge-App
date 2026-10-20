/* EduBridge App — IndexedDB offline store, XP engine, Delta-Sync queue, planner */
const DB='edubridge', V=2;
const store = {
  db:null,
  open(){ return new Promise(res=>{ const r=indexedDB.open(DB,V);
    r.onupgradeneeded=e=>{ const d=e.target.result;
      if(!d.objectStoreNames.contains('kv'))d.createObjectStore('kv');
      if(!d.objectStoreNames.contains('queue'))d.createObjectStore('queue',{autoIncrement:true});
      if(!d.objectStoreNames.contains('progress'))d.createObjectStore('progress',{keyPath:'id'});
      if(!d.objectStoreNames.contains('msgs'))d.createObjectStore('msgs',{autoIncrement:true}); };
    r.onsuccess=e=>{store.db=e.target.result;res();}; r.onerror=()=>res(); }); },
  putKV(k,v){ return new Promise(r=>{ try{const t=store.db.transaction('kv','readwrite');t.objectStore('kv').put(v,k);t.oncomplete=r;}catch(e){localStorage.setItem('eb_'+k,JSON.stringify(v));r();}}); },
  getKV(k,fb){ return new Promise(r=>{ try{const t=store.db.transaction('kv').objectStore('kv').get(k);t.onsuccess=()=>r(t.result??JSON.parse(localStorage.getItem('eb_'+k)||'null')??fb);}catch(e){r(JSON.parse(localStorage.getItem('eb_'+k)||'null')??fb);} }); },
  enqueue(op){ return new Promise(r=>{ try{const t=store.db.transaction('queue','readwrite');t.objectStore('queue').add({...op,ts:Date.now()});t.oncomplete=()=>{updateSyncBadge();r();};}catch(e){r();}}); },
  queueAll(){ return new Promise(r=>{ try{const t=store.db.transaction('queue').objectStore('queue').getAll();t.onsuccess=()=>r(t.result||[]);}catch(e){r([]);} }); },
  clearQueue(){ return new Promise(r=>{ try{const t=store.db.transaction('queue','readwrite');t.objectStore('queue').clear();t.oncomplete=r;}catch(e){r();}}); }
};
function toast(m){ const t=document.getElementById('toast'); t.textContent=m; t.style.display='block'; setTimeout(()=>t.style.display='none',2600); }
async function addXP(n,reason){
  let xp=await store.getKV('xp',0); xp+=n; await store.putKV('xp',xp);
  const day=new Date().toDateString(); let st=await store.getKV('streak',{count:1,last:day});
  if(st.last!==day){ const y=new Date(Date.now()-864e5).toDateString(); st.count=(st.last===y)?st.count+1:1; st.last=day; await store.putKV('streak',st); }
  queueSync('xp',{xp: n, reason}); checkBadges(xp, st.count); renderXP(); return xp;
}
async function checkBadges(xp, streak){
  let have=await store.getKV('badges',[]);
  const give=(slug)=>{ if(!have.includes(slug)){have.push(slug);store.putKV('badges',have);toast('🏅 New badge: '+slug);} };
  if(xp>=10)give('first-steps'); if(xp>=50)give('quiz-rookie'); if(streak>=3)give('streak-3'); if(xp>=500)give('marathon');
}
async function renderXP(){ const xp=await store.getKV('xp',0); document.querySelectorAll('[data-xp]').forEach(e=>e.textContent=xp+' XP');
  const st=await store.getKV('streak',{count:1}); document.querySelectorAll('[data-streak]').forEach(e=>e.textContent='🔥 '+st.count); }
function queueSync(op,payload){ store.enqueue({op,payload}); if(navigator.onLine) flushQueue(); }
async function flushQueue(){
  const q=await store.queueAll(); if(!q.length||!navigator.onLine) {updateSyncBadge();return;}
  try{ await fetch('api/sync.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({events:q})});
    await store.clearQueue(); toast('☁️ Synced '+q.length+' offline actions'); }catch(e){}
  updateSyncBadge();
}
async function updateSyncBadge(){ const q=await store.queueAll(); const s=document.getElementById('syncStatus');
  if(s){ s.textContent = navigator.onLine ? (q.length?('● Online · '+q.length+' pending'):'● Online · synced') : '● Offline · '+q.length+' queued'; } }
function speak(t){ if(!('speechSynthesis'in window))return toast('TTS not supported'); speechSynthesis.cancel(); speechSynthesis.speak(new SpeechSynthesisUtterance(t)); }
window.addEventListener('online',()=>{document.getElementById('offlineBar').style.display='none';flushQueue();updateSyncBadge();});
window.addEventListener('offline',()=>{document.getElementById('offlineBar').style.display='block';updateSyncBadge();});
document.addEventListener('DOMContentLoaded',async()=>{ await store.open(); renderXP(); updateSyncBadge(); flushQueue();
  if(!navigator.onLine)document.getElementById('offlineBar').style.display='block'; });
