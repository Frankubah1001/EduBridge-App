<?php
$q=trim($_GET['q']??''); $subj=$_GET['subject']??''; $sid=(int)($_GET['subject_id']??0);
$useDB=false; $list=[]; $subjects=[];
try{ if(db_ok()){
  $subjects=db_subjects_all();
  $f=[]; if($q)$f['q']=$q;
  if($sid)$f['subject_id']=$sid;
  elseif($subj){ $s=db()->query('SELECT id FROM subjects WHERE name='.db()->quote($subj).' LIMIT 1')->fetchColumn(); if($s)$f['subject_id']=$s; }
  $list=db_courses($f); $useDB=true;
}}catch(Exception $e){ $useDB=false; }
if(!$useDB){ $courses=json_data('courses.json');
  $subjects=array_map(fn($s)=>['name'=>$s],['Mathematics','English','Physics','Chemistry','Biology','Economics','Government']);
  $list=array_values(array_filter($courses,fn($c)=> (!$q||stripos($c['title'].$c['description'],$q)!==false)&&(!$subj||$c['subject']==$subj))); }
if($useDB){ foreach($list as &$c){ $c['lessons']=db_lessons($c['id']); $c['code']=$c['code']??('C'.$c['id']); } }
?>
<h2>📚 Courses <span class="pill"><?=count($list)?> available</span></h2>
<form method="GET" class="card"><input type="hidden" name="p" value="courses">
<div style="display:flex;gap:10px;flex-wrap:wrap"><input name="q" placeholder="Search e.g. Algebra, JAMB English…" value="<?=htmlspecialchars($q)?>" style="flex:2;margin:0">
<select name="subject" style="flex:1;margin:0"><option value="">All subjects</option><?php foreach($subjects as $s):$sn=is_array($s)?$s['name']:$s;?><option value="<?=$sn?>" <?= $subj==$sn?'selected':'' ?>><?=$sn?></option><?php endforeach;?></select>
<button class="btn btn-navy">Search</button></div></form>
<div class="grid g3"><?php foreach($list as $c):?><div class="card">
<span class="pill navy"><?=$c['code']?></span> <span class="pill"><?=$c['subject']?></span>
<h3><?=$c['title']?></h3><p class="muted"><?=$c['description']?></p>
<p class="muted">📝 <?=count($c['lessons'])?> lessons · 📴 offline-ready</p>
<a class="btn btn-primary" href="?p=lesson&id=<?=$c['id']?>">Open →</a>
<button class="btn btn-ghost" onclick="dlBundle(<?=$c['id']?>,'<?=addslashes($c['title'])?>')">⬇ Bundle</button></div><?php endforeach;?></div>
<script>async function dlBundle(id,t){ const r=await fetch('api/data.php?resource=courses'); const all=await r.json();
 const c=all.find(x=>x.id==id); await store.putKV('bundle_'+id,c); queueSync('download',{course:id}); addXP(5,'Downloaded '+t); toast('📦 '+t+' saved offline'); }</script>
