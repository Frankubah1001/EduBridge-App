<?php // Teacher API: save lessons/questions (go to review), live school stats
require_once __DIR__.'/../config.php';
header('Content-Type: application/json');
$u=current_user();
if(!$u) api_out(['error'=>'Login required'],401);
$actor=$u['id']??1;
try{
  if(!db_ok()) api_out(['error'=>'DB offline'],500);
  $a=$_GET['action']??'stats';
  if($a==='save_lesson'){
    $st=db()->prepare('INSERT INTO lessons(course_id,title,body,duration_min,sort,status) VALUES(?,?,?,?,0,?)');
    $st->execute([(int)$_POST['course_id'],trim($_POST['title']??''),$_POST['body']??'',10,$_POST['status']??'review']);
    $id=db()->lastInsertId(); audit($actor,'save','lesson',$id,'teacher');
    api_out(['ok'=>true,'id'=>$id]);
  }
  if($a==='save_question'){
    $st=db()->prepare("INSERT INTO questions(subject_id,exam_id,year,stem,explanation,status) VALUES(?,?,2024,?,?,'review')");
    $st->execute([(int)$_POST['subject_id'],(int)$_POST['exam_id'],trim($_POST['stem']??''),$_POST['explanation']??'']);
    $id=db()->lastInsertId();
    $opts=json_decode($_POST['options_json']??'[]',true)?:[];
    $io=db()->prepare('INSERT INTO options(question_id,label,body,is_correct,sort) VALUES(?,?,?,?,?)');
    foreach($opts as $i=>$o) $io->execute([$id,chr(65+$i),$o['body']??'',!empty($o['correct'])?1:0,$i]);
    audit($actor,'save','question',$id,'teacher');
    api_out(['ok'=>true,'id'=>$id]);
  }
  if($a==='stats'){
    $sid=(int)($u['school_id']??0);
    $students=$sid?(int)db()->query('SELECT COUNT(*) c FROM users WHERE school_id='.$sid)->fetch()['c']:0;
    $avg=$sid?(int)db()->query('SELECT AVG(xp) a FROM users WHERE school_id='.$sid)->fetch()['a']:0;
    $att=(int)db()->query('SELECT COUNT(*) c FROM attempts')->fetch()['c'];
    $q=db()->query("SELECT q.id,q.stem,(SELECT name FROM subjects s WHERE s.id=q.subject_id) AS subject,(SELECT slug FROM exams e WHERE e.id=q.exam_id) AS exam FROM questions q WHERE q.status='review' ORDER BY q.id DESC LIMIT 10")->fetchAll();
    api_out(['students'=>$students,'avg_xp'=>$avg,'attempts'=>$att,'queue'=>$q]);
  }
  api_out(['error'=>'Unknown'],400);
}catch(Exception $e){ api_out(['error'=>$e->getMessage()],500); }
