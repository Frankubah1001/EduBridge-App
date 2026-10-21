/* EduBridge App — IndexedDB offline store, XP engine, Delta-Sync queue, planner */
const DB='edubridge', V=2;
const store = {
  db:null,
  _open:null,
  /* Lazily open the database. Inline page scripts can call store.* before
     DOMContentLoaded, so every accessor awaits this same promise. */
  open(){ if(this._open) return this._open;
    this._open=new Promise(res=>{ const r=indexedDB.open(DB,V);
      r.onupgradeneeded=e=>{ const d=e.target.result;
        if(!d.objectStoreNames.contains('kv'))d.createObjectStore('kv');
        if(!d.objectStoreNames.contains('queue'))d.createObjectStore('queue',{autoIncrement:true});
        if(!d.objectStoreNames.contains('progress'))d.createObjectStore('progress',{keyPath:'id'});
        if(!d.objectStoreNames.contains('msgs'))d.createObjectStore('msgs',{autoIncrement:true}); };
      r.onsuccess=e=>{store.db=e.target.result;res();}; r.onerror=()=>res(); });
    return this._open; },
  /* localStorage mirror keeps data readable when IndexedDB is unavailable. */
  _ls(k,fb){ try{ const v=localStorage.getItem('eb_'+k); return v==null?fb:JSON.parse(v);}catch(e){ return fb; } },
  async putKV(k,v){ await this.open();
    try{ const t=store.db.transaction('kv','readwrite'); t.objectStore('kv').put(v,k);
      await new Promise(r=>{t.oncomplete=r;t.onerror=r;}); }
    catch(e){ try{ localStorage.setItem('eb_'+k,JSON.stringify(v)); }catch(e2){} } },
  async getKV(k,fb){ await this.open();
    try{ const t=store.db.transaction('kv').objectStore('kv').get(k);
      const v=await new Promise(r=>{t.onsuccess=()=>r(t.result);t.onerror=()=>r(undefined);});
      return v===undefined||v===null?this._ls(k,fb):v; }
    catch(e){ return this._ls(k,fb); } },
  async enqueue(op){ await this.open();
    try{ const t=store.db.transaction('queue','readwrite'); t.objectStore('queue').add({...op,ts:Date.now()});
      await new Promise(r=>{t.oncomplete=r;t.onerror=r;}); }catch(e){ return; }
    updateSyncBadge(); },
  async queueAll(){ await this.open();
    try{ const t=store.db.transaction('queue').objectStore('queue').getAll();
      const rows=await new Promise(r=>{t.onsuccess=()=>r(t.result);t.onerror=()=>r([]);});
      return rows||[]; }catch(e){ return []; } },
  async clearQueue(){ await this.open();
    try{ const t=store.db.transaction('queue','readwrite'); t.objectStore('queue').clear();
      await new Promise(r=>{t.oncomplete=r;t.onerror=r;}); }catch(e){} }
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
  try{
    const r=await fetch('api/sync.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({events:q})});
    const j=await r.json();
    // Only drop the local queue when the server confirmed the write.
    if(j&&j.ok&&(j.acked||0)===q.length){ await store.clearQueue(); toast('☁️ Synced '+q.length+' offline actions'); }
    else { updateSyncBadge(); return; }
  }catch(e){}
  updateSyncBadge();
}
async function updateSyncBadge(){ const q=await store.queueAll(); const s=document.getElementById('syncStatus');
  if(s){ s.textContent = navigator.onLine ? (q.length?('● Online · '+q.length+' pending'):'● Online · synced') : '● Offline · '+q.length+' queued'; } }
function speak(t){ if(!('speechSynthesis'in window))return toast('TTS not supported'); speechSynthesis.cancel(); speechSynthesis.speak(new SpeechSynthesisUtterance(t)); }
window.addEventListener('online',()=>{document.getElementById('offlineBar').style.display='none';flushQueue();updateSyncBadge();});
window.addEventListener('offline',()=>{document.getElementById('offlineBar').style.display='block';updateSyncBadge();});
document.addEventListener('DOMContentLoaded',async()=>{ await store.open(); renderXP(); updateSyncBadge(); flushQueue();
  if(!navigator.onLine)document.getElementById('offlineBar').style.display='block'; });
