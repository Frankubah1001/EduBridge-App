/* EduBridge Service Worker — precache shell + offline bundles */
const CACHE='edubridge-v2';
const CORE=['/EduBridge/','/EduBridge/index.php','/EduBridge/assets/css/style.css','/EduBridge/assets/js/app.js','/EduBridge/assets/js/irt.js','/EduBridge/data/courses.json','/EduBridge/data/questions.json'];
self.addEventListener('install',e=>{e.waitUntil(caches.open(CACHE).then(c=>c.addAll(CORE)).then(()=>self.skipWaiting()));});
self.addEventListener('activate',e=>{e.waitUntil(self.clients.claim());});
self.addEventListener('fetch',e=>{const u=new URL(e.request.url);if(e.request.method!=='GET')return;
e.respondWith(caches.match(e.request).then(hit=>{const net=fetch(e.request).then(r=>{if(r.ok&&u.origin===location.origin){const cp=r.clone();caches.open(CACHE).then(c=>c.put(e.request,cp));}return r;}).catch(()=>hit);return hit||net;}));});
