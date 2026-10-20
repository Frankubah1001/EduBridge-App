<?php require_once __DIR__.'/../config.php';
header('Content-Type: application/json');
$r=$_GET['resource']??'courses';
// DB-first for content resources; JSON fallback keeps offline/PWA working
try{
  if(db_ok()){
    if($r==='courses'){
      $f=[]; if(!empty($_GET['subject_id']))$f['subject_id']=$_GET['subject_id'];
      if(!empty($_GET['subject'])){ $s=db()->query('SELECT id FROM subjects WHERE name='.db()->quote($_GET['subject']).' LIMIT 1')->fetchColumn(); if($s)$f['subject_id']=$s; }
      if(!empty($_GET['q']))$f['q']=$_GET['q'];
      $rows=db_courses($f);
      foreach($rows as &$c){ $c['lessons']=db_lessons($c['id']); $c['level']=$c['level_name']??''; }
      echo json_encode($rows); exit;
    }
    if($r==='questions'){
      $f=[]; foreach(['subject','topic','exam','year','subject_id','topic_id','exam_id'] as $k) if(isset($_GET[$k])&&$_GET[$k]!=='')$f[$k]=$_GET[$k];
      $limit=min(100,max(1,(int)($_GET['limit']??10)));
      echo json_encode(db_questions($f,$limit)); exit;
    }
    if($r==='subjects'){ echo json_encode(db_subjects_all()); exit; }
    if($r==='topics'){ echo json_encode(db_topics_all()); exit; }
    if($r==='exams'){ echo json_encode(db_exams_all()); exit; }
    if($r==='levels'){ echo json_encode(db_levels()); exit; }
    if($r==='schools'){ echo json_encode(db_schools(true)); exit; }
    if($r==='plans'){ echo json_encode(db_plans()); exit; }
    if($r==='settings'){ $pub=[]; foreach(settings_all() as $k=>$v){ if(str_starts_with($k,'features.')||str_starts_with($k,'home.')||$k=='site_name'||$k=='tagline') $pub[$k]=$v; } echo json_encode($pub); exit; }
  }
}catch(Exception $e){ /* fall through to JSON */ }
$map=['courses'=>'courses.json','questions'=>'questions.json','mentors'=>'mentors.json','badges'=>'badges.json','forum'=>'forum.json','curriculum'=>'curriculum.json'];
if(!isset($map[$r])){http_response_code(404);echo json_encode(['error'=>'unknown']);exit;}
echo json_encode(json_data($map[$r]));
