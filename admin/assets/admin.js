/* EduBridge Admin Console — talks to inc/api.php only (staff session). */
const API='inc/api.php';
const post=async(action,body)=>{
  const r=await fetch(API+'?action='+action,{method:'POST',body:new URLSearchParams(body)});
  return r.json();
};
const get=async(action,qs='')=>{
  const r=await fetch(API+'?action='+action+qs);
  return r.json();
};
function toast(m){ const t=document.getElementById('toast'); if(!t)return;
  t.textContent=m; t.style.display='block'; setTimeout(()=>t.style.display='none',2600); }
function esc(s){ return String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }
let LVLS=[],SUBS=[],TOPS=[],EXAMS=[],SEL_COURSE=0;

document.querySelectorAll('#adminTabs .tab').forEach(b=>b.onclick=()=>{
  document.querySelectorAll('#adminTabs .tab').forEach(x=>x.classList.remove('active'));
  b.classList.add('active');
  document.querySelectorAll('.tabpane').forEach(p=>p.style.display='none');
  document.getElementById('tab-'+b.dataset.t).style.display='block';
});


async function boot(){
  const s=await get('stats');
  document.getElementById('statCards').innerHTML=['users|Learners','courses|Courses','lessons|Lessons','questions|Questions'].map(k=>{const p=k.split('|');
   return `<div class="card" style="text-align:center"><div class="stat-num">${s[p[0]]??0}</div><div class="muted">${p[1]}</div></div>`;}).join('');
  const maxC=Math.max(1,...(s.by_subject||[]).map(r=>r.c));
  document.getElementById('bySubj').innerHTML=(s.by_subject||[]).map(r=>`<div class="mastery-row"><span>${esc(r.subject||'—')}</span><div class="progress" style="flex:1"><div style="width:${Math.round(r.c/maxC*100)}%"></div></div><b>${r.c}</b></div>`).join('')||'<p class="muted">No questions yet.</p>';
  LVLS=await get('levels'); SUBS=await get('subjects'); EXAMS=await get('exams'); TOPS=await get('topics');
  document.getElementById('lvlList').innerHTML=LVLS.map(l=>`<div class="opt">📌 ${esc(l.name)} <span class="pill">${l.subjects} subjects</span></div>`).join('');
  document.getElementById('subList').innerHTML=SUBS.map(x=>`<div class="opt">${esc(x.level_name)} · <b>${esc(x.name)}</b></div>`).join('');
  document.getElementById('topList').innerHTML=TOPS.slice(0,60).map(x=>`<div class="opt">${esc(x.subject)} · ${esc(x.name)}</div>`).join('');
  const fill=(id,arr,val)=>{const el=document.getElementById(id); if(el) el.innerHTML=arr.map(o=>`<option value="${o[val]}">${esc(o.name)}</option>`).join('');};
  fill('ns_level',LVLS,'id'); fill('nt_sub',SUBS,'id'); fill('cf_level',LVLS,'id'); fill('cf_sub',SUBS,'id');
  fill('qf_sub',SUBS,'id'); fill('qq_sub',SUBS,'id'); fill('pf_exam',EXAMS,'id'); fill('pf_sub',SUBS,'id'); fill('qq_exam',EXAMS,'id');
  fill('mt_sub',SUBS,'id');
  document.getElementById('qq_opts').innerHTML=[0,1,2,3].map(i=>`<div><label>Option ${'ABCD'[i]} <input type="radio" name="qc" value="${i}" ${i==1?'checked':''} style="width:auto"> correct</label><input id="qo_${i}"></div>`).join('');
  loadCourses(); loadQuestions(); loadPapers(); loadFlags(); loadAudit();
  loadFeatures(); loadSchools(); loadPlans(); loadMentors(); loadUsers(); loadMessages();
}
async function addSubject(){ const r=await post('add_subject',{level_id:document.getElementById('ns_level').value,name:document.getElementById('ns_name').value});
  if(r.error) return toast(r.error); toast('Subject added'); boot(); }
async function addTopic(){ const r=await post('add_topic',{subject_id:document.getElementById('nt_sub').value,name:document.getElementById('nt_name').value});
  if(r.error) return toast(r.error); toast('Topic added'); boot(); }


async function loadCourses(){ const rows=await get('course_list'); window._courses=rows;
 document.getElementById('courseList').innerHTML=rows.map(c=>`<div class="opt"><b>${esc(c.title)}</b> <span class="pill">${esc(c.subject||'')}</span> <span class="pill">${esc(c.status)}</span><br><span class="muted">${esc(c.code)} · ${c.lessons} lessons</span><br>
 <button class="btn btn-ghost" onclick="editCourse(${c.id})">Edit</button>
 <button class="btn btn-ghost" onclick="manageLessons(${c.id})">Lessons</button>
 <button class="btn btn-ghost" onclick="pubCourse(${c.id},'${c.status=='published'?'draft':'published'}')">${c.status=='published'?'Unpublish':'Publish'}</button>
 <button class="btn btn-danger" onclick="delCourse(${c.id})">Delete</button></div>`).join('')||'<p class="muted">No courses.</p>'; }
function resetCourse(){ ['cf_id','cf_code','cf_title','cf_desc'].forEach(id=>document.getElementById(id).value='');
 document.getElementById('courseFormTitle').textContent='+ New course'; }
function editCourse(id){ const c=(window._courses||[]).find(x=>x.id==id); if(!c)return;
 document.getElementById('cf_id').value=c.id; document.getElementById('cf_code').value=c.code;
 document.getElementById('cf_title').value=c.title; document.getElementById('cf_desc').value=c.description||'';
 document.getElementById('cf_status').value=c.status;
 document.getElementById('courseFormTitle').textContent='Edit course #'+c.id; }
async function saveCourse(){ const b={id:document.getElementById('cf_id').value,code:document.getElementById('cf_code').value,title:document.getElementById('cf_title').value,level_id:document.getElementById('cf_level').value,subject_id:document.getElementById('cf_sub').value,description:document.getElementById('cf_desc').value,status:document.getElementById('cf_status').value};
 if(!b.title.trim())return toast('Title required');
 const r=await post('save_course',b); if(r.error)return toast(r.error); toast('Saved #'+r.id); resetCourse(); loadCourses(); }
async function pubCourse(id,status){ await post('publish_course',{id,status}); loadCourses(); }

async function loadQuestions(){ const q='&subject_id='+document.getElementById('qf_sub').value+'&exam_id='+document.getElementById('qf_exam').value+'&status='+document.getElementById('qf_status').value+'&q='+encodeURIComponent(document.getElementById('qf_q').value||'');
 const rows=await get('question_list',q);
 document.getElementById('qCount').textContent=rows.length+' shown';
 document.getElementById('qList').innerHTML=rows.map(x=>`<div class="opt"><b>#${x.id}</b> [${esc(x.exam||'')} ${x.year||''}] ${esc(x.subject||'')}/${esc(x.topic||'')} <span class="pill">${esc(x.status)}</span><br>${esc((x.stem||'').slice(0,140))}<br>
 <button class="btn btn-ghost" onclick="editQ(${x.id})">Edit</button>
 <button class="btn btn-ghost" onclick="qStatus(${x.id},'${x.status=='published'?'draft':'published'}')">Toggle</button>
 <button class="btn btn-danger" onclick="delQ(${x.id})">Delete</button></div>`).join('')||'<p class="muted">None.</p>'; }
function newQuestion(){ document.getElementById('qForm').style.display='block';
 document.getElementById('qq_id').value=''; document.getElementById('qq_stem').value=''; document.getElementById('qq_exp').value='';
 [0,1,2,3].forEach(i=>document.getElementById('qo_'+i).value=''); }
async function editQ(id){ const q=await get('question_get','&id='+id); if(q.error)return toast(q.error);
 document.getElementById('qForm').style.display='block';
 document.getElementById('qq_id').value=q.id; document.getElementById('qq_stem').value=q.stem;
 document.getElementById('qq_exp').value=q.explanation||''; document.getElementById('qq_diff').value=q.difficulty||0;
 document.getElementById('qq_status').value=q.status; document.getElementById('qq_year').value=q.year||2024;
 (q.options||[]).forEach((o,i)=>{ const el=document.getElementById('qo_'+i); if(el){el.value=o.body; const r=document.querySelector(`input[name=qc][value="${i}"]`); if(r)r.checked=!!o.is_correct;} }); }

async function loadPapers(){ const rows=await get('paper_list');
 document.getElementById('paperList').innerHTML=rows.map(p=>`<div class="opt"><b>${esc(p.title)}</b> <span class="pill">${esc(p.exam)}/${esc(p.subject)}</span> ${p.items} Qs <button class="btn btn-ghost" onclick="showItems(${p.id})">Items</button> <button class="btn btn-danger" onclick="delPaper(${p.id})">Delete</button></div>`).join('')||'<p class="muted">No papers.</p>';
 document.getElementById('ab_paper').innerHTML=rows.map(p=>`<option value="${p.id}">${esc(p.title)}</option>`).join(''); }
async function showItems(pid){ const rows=await get('paper_items','&paper_id='+pid);
 document.getElementById('abOut').innerHTML='<b>Items</b><br>'+rows.map(q=>`<div class="opt">#${q.id} ${esc((q.stem||'').slice(0,90))} <button class="btn btn-danger" onclick="rmItem(${pid},${q.id})">x</button></div>`).join('')||'<p class="muted">No items.</p>'; }
async function rmItem(pid,qid){ await post('paper_remove_item',{paper_id:pid,question_id:qid}); showItems(pid); loadPapers(); }
async function savePaper(){ const r=await post('save_paper',{title:document.getElementById('pf_title').value,exam_id:document.getElementById('pf_exam').value,subject_id:document.getElementById('pf_sub').value,duration_min:document.getElementById('pf_min').value,status:'published'});
 if(r.error)return toast(r.error); toast('Paper #'+r.id); loadPapers(); }
async function delPaper(id){ if(!confirm('Delete paper + its items?'))return; await post('delete_paper',{id}); loadPapers(); }
async function autofill(){ const r=await post('paper_autofill',{paper_id:document.getElementById('ab_paper').value,n:document.getElementById('ab_n').value});
 document.getElementById('abOut').innerHTML=r.error?`<p class="pill amber">${esc(r.error)}</p>`:`<p class="pill">Added ${r.added}</p>`; loadPapers(); }
async function loadFlags(){ const rows=await get('flagged');
 document.getElementById('flagList').innerHTML=rows.map(t=>`<div class="opt">#${t.id} ${esc(t.title)} <span class="muted">by ${esc(t.author||'?')}</span> <button class="btn btn-primary" onclick="mod(${t.id},'open')">Restore</button> <button class="btn btn-danger" onclick="mod(${t.id},'removed')">Remove</button></div>`).join('')||'<p class="muted">None flagged. 🎉</p>'; }
async function mod(id,status){ await post('moderate',{id,status}); loadFlags(); }
async function loadAudit(){ const rows=await get('audit_list');
 document.getElementById('auditList').innerHTML=rows.slice(0,15).map(a=>`<div class="opt"><b>${esc(a.action)}</b> <span class="pill">${esc(a.entity||'')}</span> <span class="muted">${esc(a.actor||a.actor_id)} · ${esc(a.created_at)}</span> ${esc((a.meta||'').slice(0,60))}</div>`).join('')||'<p class="muted">No activity.</p>'; }


/* ---------- features / branding / auth policy ---------- */
const FEATS=[['features.quiz','CBT Mock'],['features.diagnostic','Diagnostics'],['features.ai_tutor','AI Tutor'],
 ['features.mentor_chat','Mentor chat'],['features.forum','Forum'],['features.leaderboard','Leaderboard'],
 ['features.challenge','Challenges'],['features.offline_bundles','Offline bundles'],['features.lowdata','Low-data mode'],
 ['features.certificates','Certificates'],['features.parent_sms','Parent SMS'],['features.payments','Payments']];
const NUMS=[['features.quiz_n','Questions per quiz'],['features.quiz_minutes','Quiz minutes'],
 ['features.diagnostic_n','Diagnostic questions'],['features.ai_daily_limit','AI messages/day'],
 ['features.challenge_n','Daily challenges'],['features.challenge_xp','XP per challenge']];
const XPS=[['xp.lesson','XP / lesson'],['xp.quiz_high','XP / correct (hard)'],['xp.quiz_mid','XP / correct (mid)'],
 ['xp.quiz_low','XP / correct (easy)'],['xp.diagnostic','XP / diagnostic']];
const HOME=[['home.hero_badge','Hero badge'],['home.stat_questions','Stat: questions'],['home.stat_subjects','Stat: subjects'],['home.stat_offline','Stat: offline stat']];
const AUTH=[['auth.allow_student','Allow student signup'],['auth.allow_parent','Allow parent signup'],
 ['auth.allow_mentor','Allow mentor signup'],['auth.require_school','Require school on signup'],['auth.require_password','Require password on signup']];
let SETTINGS={};
function chk(key,label){ return `<label class="chk"><input type="checkbox" data-s="${key}" ${SETTINGS[key]==='1'?'checked':''}> ${label}</label>`; }
function num(key,label){ return `<label>${label}</label><input type="number" data-s="${key}" value="${esc(SETTINGS[key]??'')}">`; }
function txt(key,label){ return `<label>${label}</label><input type="text" data-s="${key}" value="${esc(SETTINGS[key]??'')}">`; }
async function loadFeatures(){ SETTINGS=await get('settings_get');
 document.getElementById('featForm').innerHTML=FEATS.map(f=>chk(f[0],f[1])).join('')+NUMS.map(n=>num(n[0],n[1])).join('')+XPS.map(x=>num(x[0],x[1])).join('');
 document.getElementById('brandForm').innerHTML=txt('site_name','Site name')+txt('tagline','Tagline')+HOME.map(h=>txt(h[0],h[1])).join('');
 document.getElementById('authForm').innerHTML=AUTH.map(a=>chk(a[0],a[1])).join(''); }
function collectSettings(){ const out={};
 document.querySelectorAll('#featForm [data-s],#brandForm [data-s],#authForm [data-s]').forEach(el=>{
  out[el.dataset.s]= el.type==='checkbox' ? (el.checked?'1':'0') : el.value; });
 return out; }
async function saveFeatures(){ const r=await post('settings_save',{settings_json:JSON.stringify(collectSettings())});
 if(r.error)return toast(r.error); toast('Saved '+r.saved+' settings'); loadFeatures(); }
/* ---------- schools ---------- */
async function loadSchools(){ const rows=await get('school_list'); window._schools=rows;
 document.getElementById('schCount').textContent=rows.length+' schools';
 document.getElementById('schList').innerHTML=rows.map(s=>`<div class="opt"><b>${esc(s.name)}</b> <span class="pill">${esc(s.code)}</span> <span class="pill ${s.status=='approved'?'':'amber'}">${esc(s.status)}</span><br><span class="muted">${esc(s.state||'')} · ${esc(s.contact_phone||'')} · ${s.members} members</span><br>
 <button class="btn btn-ghost" onclick="editSchool(${s.id})">Edit</button>
 <button class="btn btn-ghost" onclick="schoolStatus(${s.id},'${s.status=='approved'?'suspended':'approved'}')">${s.status=='approved'?'Suspend':'Approve'}</button></div>`).join('')||'<p class="muted">No schools.</p>';
 document.getElementById('uq_school').innerHTML='<option value="">All schools</option>'+rows.map(s=>`<option value="${s.id}">${esc(s.name)}</option>`).join(''); }

/* ---------- plans + vouchers ---------- */
async function loadPlans(){ const rows=await get('plan_list'); window._plans=rows;
 document.getElementById('planList').innerHTML=rows.map(p=>`<div class="opt"><b>${esc(p.name)}</b> <span class="pill">${esc(p.slug)}</span> ₦${(p.price_kobo/100).toLocaleString()}/${esc(p.term||'')}<br><span class="muted">${esc((p.features||'—').slice(0,140))}</span><br>
 <button class="btn btn-ghost" onclick="editPlan(${p.id})">Edit</button></div>`).join('')||'<p class="muted">No plans.</p>';
 document.getElementById('vc_plan').innerHTML=rows.map(p=>`<option value="${p.id}">${esc(p.name)}</option>`).join('');
 loadVouchers(); }
function editPlan(id){ const p=(window._plans||[]).find(x=>x.id==id); if(!p)return;
 document.getElementById('pl_id').value=p.id; document.getElementById('pl_slug').value=p.slug;
 document.getElementById('pl_name').value=p.name; document.getElementById('pl_price').value=Math.round(p.price_kobo/100);
 document.getElementById('pl_term').value=p.term||''; document.getElementById('pl_feat').value=p.features||''; }
async function savePlan(){ const r=await post('save_plan',{id:document.getElementById('pl_id').value,slug:document.getElementById('pl_slug').value,name:document.getElementById('pl_name').value,price_kobo:Math.round(Number(document.getElementById('pl_price').value||0)*100),term:document.getElementById('pl_term').value,features:document.getElementById('pl_feat').value});
 if(r.error)return toast(r.error); toast('Plan saved'); loadPlans(); }
async function loadVouchers(){ const rows=await get('voucher_list');
 document.getElementById('vcList').innerHTML=rows.map(v=>`<div class="opt"><b>${esc(v.code)}</b> <span class="pill">${esc(v.plan||'')}</span> <span class="pill ${v.status=='active'?'':'amber'}">${esc(v.status)}</span> <span class="muted">${v.used}/${v.max_uses} used</span>
 <button class="btn btn-ghost" onclick="voucherStatus(${v.id},'${v.status=='active'?'disabled':'active'}')">${v.status=='active'?'Disable':'Enable'}</button></div>`).join('')||'<p class="muted">No vouchers yet.</p>'; }
async function saveVoucher(){ const r=await post('save_voucher',{code:document.getElementById('vc_code').value,plan_id:document.getElementById('vc_plan').value,max_uses:document.getElementById('vc_max').value});
 if(r.error)return toast(r.error); toast('Voucher created'); document.getElementById('vc_code').value=''; loadVouchers(); }

/* ---------- mentors + sessions ---------- */
async function loadMentors(){ const rows=await get('mentor_list'); window._mentors=rows;
 document.getElementById('mCount').textContent=rows.length+' mentors';
 document.getElementById('mentorList').innerHTML=rows.map(m=>`<div class="opt"><b>${esc(m.name)}</b> <span class="pill">${esc(m.subject||'No subject')}</span> <span class="pill ${m.status=='online'?'':'amber'}">${esc(m.status)}</span> ${m.verified?'<span class="pill">✔ verified</span>':''}<br><span class="muted">${esc(m.school||'')} · ${esc(m.email||'not linked')}</span><br>
 <button class="btn btn-ghost" onclick="editMentor(${m.id})">Edit</button>
 <button class="btn btn-ghost" onclick="verifyMentor(${m.id},${m.verified?0:1})">${m.verified?'Unverify':'Verify'}</button>
 <button class="btn btn-danger" onclick="delMentor(${m.id})">Delete</button></div>`).join('')||'<p class="muted">No mentors.</p>';
 document.getElementById('ss_mentor').innerHTML=rows.map(m=>`<option value="${m.id}">${esc(m.name)}</option>`).join('');
 loadSessions(); }
function editMentor(id){ const m=(window._mentors||[]).find(x=>x.id==id); if(!m)return;
 document.getElementById('mt_id').value=m.id; document.getElementById('mt_name').value=m.name;
 document.getElementById('mt_sub').value=m.subject_id||''; document.getElementById('mt_school').value=m.school||'';
 document.getElementById('mt_status').value=m.status; document.getElementById('mt_verified').value=m.verified?1:0;
 document.getElementById('mt_bio').value=m.bio||''; document.getElementById('mentorTitle').textContent='Edit mentor #'+m.id; }
function resetMentor(){ ['mt_id','mt_name','mt_school','mt_bio'].forEach(id=>document.getElementById(id).value='');
 document.getElementById('mentorTitle').textContent='+ New mentor'; }
async function saveMentor(){ const r=await post('save_mentor',{id:document.getElementById('mt_id').value,name:document.getElementById('mt_name').value,subject_id:document.getElementById('mt_sub').value,school:document.getElementById('mt_school').value,status:document.getElementById('mt_status').value,verified:document.getElementById('mt_verified').value,bio:document.getElementById('mt_bio').value});
 if(r.error)return toast(r.error); toast('Mentor saved'); resetMentor(); loadMentors(); }
async function verifyMentor(id,v){ await post('verify_mentor',{id,verified:v}); loadMentors(); }
async function delMentor(id){ if(!confirm('Delete mentor?'))return; await post('delete_mentor',{id}); loadMentors(); }
async function loadSessions(){ const rows=await get('session_list');
 document.getElementById('sessionList').innerHTML=rows.map(s=>`<div class="opt"><b>${esc(s.title)}</b> <span class="pill">${esc(s.status)}</span> <span class="muted">${esc(s.who||'')} · ${esc(s.starts_at)}</span>
 <button class="btn btn-danger" onclick="delSession(${s.id})">x</button></div>`).join('')||'<p class="muted">No sessions.</p>'; }
async function saveSession(){ const r=await post('save_session',{mentor_id:document.getElementById('ss_mentor').value,title:document.getElementById('ss_title').value,starts_at:document.getElementById('ss_when').value,status:document.getElementById('ss_status').value,join_url:document.getElementById('ss_url').value});
 if(r.error)return toast(r.error); toast('Session added'); document.getElementById('ss_title').value=''; loadSessions(); }
async function delSession(id){ await post('delete_session',{id}); loadSessions(); }
/* ---------- users ---------- */
async function loadUsers(){ const q='&role='+document.getElementById('uq_role').value+'&school_id='+document.getElementById('uq_school').value+'&q='+encodeURIComponent(document.getElementById('uq_q').value||'');
 const rows=await get('user_list',q);
 document.getElementById('uCount').textContent=rows.length+' users';
 document.getElementById('uList').innerHTML=rows.map(u=>`<div class="opt"><b>${esc(u.name)}</b> <span class="pill">${esc(u.role)}</span> <span class="pill ${u.status=='active'?'':'amber'}">${esc(u.status)}</span><br><span class="muted">${esc(u.email)} · ${esc(u.school||'no school')} · ${u.xp} XP</span><br>
 <button class="btn btn-ghost" onclick="userStatus(${u.id},'${u.status=='active'?'suspended':'active'}')">${u.status=='active'?'Suspend':'Activate'}</button></div>`).join('')||'<p class="muted">No users.</p>'; }
async function userStatus(id,status){ await post('user_status',{id,status}); loadUsers(); }
async function loadMessages(){ const rows=await get('message_list');
 document.getElementById('msgList').innerHTML=rows.map(m=>`<div class="opt"><b>${esc(m.from_name||'?')} → ${esc(m.to_name||'?')}</b> <span class="muted">${esc(m.created_at)} ${m.read_at?'· read':'· unread'}</span><br>${esc((m.body||'').slice(0,140))}</div>`).join('')||'<p class="muted">No messages.</p>'; }
async function voucherStatus(id,status){ await post('voucher_status',{id,status}); loadVouchers(); }

function editSchool(id){ const s=(window._schools||[]).find(x=>x.id==id); if(!s)return;
 document.getElementById('sc_id').value=s.id; document.getElementById('sc_name').value=s.name;
 document.getElementById('sc_code').value=s.code; document.getElementById('sc_state').value=s.state||'';
 document.getElementById('sc_phone').value=s.contact_phone||''; document.getElementById('sc_status').value=s.status;
 document.getElementById('schTitle').textContent='Edit school #'+s.id; }
async function saveSchool(){ const r=await post('save_school',{id:document.getElementById('sc_id').value,name:document.getElementById('sc_name').value,code:document.getElementById('sc_code').value,state:document.getElementById('sc_state').value,contact_phone:document.getElementById('sc_phone').value,status:document.getElementById('sc_status').value});
 if(r.error)return toast(r.error); toast('School saved'); loadSchools(); }
async function schoolStatus(id,status){ await post('school_status',{id,status}); loadSchools(); }

async function saveQuestion(){ const ck=document.querySelector('input[name=qc]:checked');
 const opts=[0,1,2,3].map(i=>({body:document.getElementById('qo_'+i).value.trim(),correct:!!ck&&ck.value==String(i)})).filter(o=>o.body);
 const r=await post('save_question',{id:document.getElementById('qq_id').value,subject_id:document.getElementById('qq_sub').value,exam_id:document.getElementById('qq_exam').value,year:document.getElementById('qq_year').value,stem:document.getElementById('qq_stem').value,explanation:document.getElementById('qq_exp').value,difficulty:document.getElementById('qq_diff').value,status:document.getElementById('qq_status').value,options_json:JSON.stringify(opts)});
 if(r.error)return toast(r.error); toast('Saved #'+r.id); loadQuestions(); }
async function qStatus(id,status){ await post('question_status',{id,status}); loadQuestions(); }
async function delQ(id){ if(!confirm('Delete?'))return; await post('delete_question',{id}); loadQuestions(); }

async function delCourse(id){ if(!confirm('Delete course + lessons?'))return; await post('delete_course',{id}); loadCourses(); }
async function manageLessons(id){ SEL_COURSE=id; const rows=await get('lesson_list','&course_id='+id);
 document.getElementById('lessonList').innerHTML=rows.map(l=>`<div class="opt"><b>${esc(l.title)}</b> <span class="pill">${esc(l.status)}</span> <span class="muted">${l.duration_min} min</span> <button class="btn btn-danger" onclick="delLesson(${l.id},${id})">x</button></div>`).join('')||'<p class="muted">No lessons.</p>'; }
async function saveLesson(){ if(!SEL_COURSE)return toast('Click Lessons first');
 const r=await post('save_lesson',{course_id:SEL_COURSE,title:document.getElementById('lf_title').value,body:document.getElementById('lf_body').value,duration_min:document.getElementById('lf_min').value,status:document.getElementById('lf_status').value});
 if(r.error)return toast(r.error);
 toast('Lesson added'); document.getElementById('lf_title').value=''; document.getElementById('lf_body').value=''; manageLessons(SEL_COURSE); loadCourses(); }
async function delLesson(id,cid){ await post('delete_lesson',{id}); manageLessons(cid); loadCourses(); }

/* ---------- start ---------- */
boot();
