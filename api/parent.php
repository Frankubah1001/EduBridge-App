<?php // Parent data: linked child's attempts + subject averages (DB)
require_once __DIR__.'/../config.php';
header('Content-Type: application/json');
$u=current_user();
if(!$u) api_out(['error'=>'Login required'],401);
try{
  if(!db_ok()) api_out(['avg'=>null,'by_subject'=>[],'recent'=>[]]);
  $cid=$u['id'];
  if(($u['role']??'')=='parent'){
    $k=db()->query('SELECT student_user_id FROM guardians WHERE parent_user_id='.(int)($u['id']??0).' ORDER BY student_user_id LIMIT 1')->fetchColumn();
    if($k) $cid=$k;
  }
  $avg=db()->query('SELECT ROUND(AVG(score)) a FROM attempts WHERE user_id='.(int)$cid)->fetch()['a'];
  $by=db()->query("SELECT (SELECT name FROM subjects s WHERE s.id=a.subject_id) AS subject, ROUND(AVG(score)) AS avg FROM attempts a WHERE a.user_id=".(int)$cid." GROUP BY subject")->fetchAll();
  $rec=db()->query('SELECT score,total,theta,created_at FROM attempts WHERE user_id='.(int)$cid.' ORDER BY id DESC LIMIT 8')->fetchAll();
  api_out(['avg'=>$avg,'by_subject'=>$by,'recent'=>$rec]);
}catch(Exception $e){ api_out(['error'=>$e->getMessage()],500); }
