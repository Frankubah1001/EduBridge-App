/* EduBridge Service Worker — precache shell + offline bundles */
const CACHE='edubridge-v3';
const CORE=['/EduBridge/','/EduBridge/index.php','/EduBridge/assets/css/style.css','/EduBridge/assets/js/app.js','/EduBridge/assets/js/irt.js','/EduBridge/data/courses.json','/EduBridge/data/questions.json','/EduBridge/manifest.json'];
/* Learner pages that must still open with no network. */
const PAGES=['home','courses','lesson','quiz','diagnostics','ai-tutor','planner','mentor','forum','leaderboard','dashboard','sync','pricing','login','register'];
const SHELL=PAGES.map(p=>'/EduBridge/index.php?p='+p);

self.addEventListener('install',e=>{e.waitUntil(
  caches.open(CACHE)
    .then(c=>Promise.allSettled([...CORE,...SHELL].map(u=>c.add(u))))
    .then(()=>self.skipWaiting()));});

self.addEventListener('activate',e=>{e.waitUntil(
  caches.keys().then(keys=>Promise.all(keys.filter(k=>k!==CACHE).map(k=>caches.delete(k))))
    .then(()=>self.clients.claim()));});

self.addEventListener('fetch',e=>{
  const u=new URL(e.request.url);
  if(e.request.method!=='GET'||u.origin!==location.origin) return;
  // Navigations: try network, fall back to the cached shell for that page.
  if(e.request.mode==='navigate'){
    e.respondWith(
      fetch(e.request).then(r=>{ const cp=r.clone(); caches.open(CACHE).then(c=>c.put(e.request,cp)); return r; })
      .catch(()=>caches.match(e.request).then(hit=>hit||caches.match('/EduBridge/index.php?p=home')||caches.match('/EduBridge/index.php')))
    );
    return;
  }
  // API + data: network first, then cache (so bundled questions stay available offline).
  e.respondWith(caches.match(e.request).then(hit=>{
    const net=fetch(e.request).then(r=>{ if(r.ok){const cp=r.clone();caches.open(CACHE).then(c=>c.put(e.request,cp));} return r; })
      .catch(()=>hit);
    return hit||net;
  }));
});
