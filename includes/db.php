<?php
// EduBridge DB layer — MariaDB/MySQL via PDO, JSON fallback when DB is down.
//
// Credentials resolve in this order (first non-empty wins):
//   1. config.local.php at the app root  (git-ignored, per-developer overrides)
//   2. EDU_DB_* environment variables     (EDU_DB_HOST, EDU_DB_PORT, EDU_DB_SOCK,
//                                         EDU_DB_NAME, EDU_DB_USER, EDU_DB_PASS)
//   3. the safe defaults below            (no password)
//
// Never commit real credentials — copy config.local.example.php to
// config.local.php and fill it in.
$__edu_local = [];
$__edu_local_file = dirname(__DIR__).'/config.local.php';
if(is_file($__edu_local_file)){
  $__edu_local = (array)require $__edu_local_file;
}
function edu_cfg($key, $default=''){
  global $__edu_local;
  if(isset($__edu_local[$key]) && $__edu_local[$key] !== '') return $__edu_local[$key];
  $env = getenv('EDU_'.$key);
  if($env !== false && $env !== '') return $env;
  return $default;
}
define('DB_HOST', edu_cfg('DB_HOST','127.0.0.1'));
define('DB_PORT', edu_cfg('DB_PORT','3306'));
define('DB_SOCK', edu_cfg('DB_SOCK',''));
define('DB_NAME', edu_cfg('DB_NAME','edubridge'));
define('DB_USER', edu_cfg('DB_USER','root'));
define('DB_PASS', edu_cfg('DB_PASS',''));
function db(){
  static $pdo=null, $failed=false;
  if($pdo||$failed) return $pdo;
  $opts=[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC];
  try{
    $dsn='mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME.';charset=utf8mb4';
    if(DB_SOCK && file_exists(DB_SOCK)) $dsn.=';unix_socket='.DB_SOCK;
    $pdo=new PDO($dsn, DB_USER, DB_PASS, $opts);
  }catch(Exception $e){ $failed=true; $pdo=null; }
  return $pdo;
}
function db_ok(){ try{ return (bool)db(); }catch(Exception $e){ return false; } }
function db_levels(){ return db()->query('SELECT l.*, (SELECT COUNT(*) FROM subjects s WHERE s.level_id=l.id) AS subjects FROM levels l ORDER BY sort')->fetchAll(); }
function db_subjects_all(){ return db()->query('SELECT s.*, l.name AS level_name, l.slug AS level_slug FROM subjects s JOIN levels l ON l.id=s.level_id ORDER BY l.sort, s.sort')->fetchAll(); }
function db_exams_all(){ return db()->query('SELECT * FROM exams ORDER BY id')->fetchAll(); }
function db_topics_all(){ return db()->query('SELECT t.*, s.name AS subject FROM topics t JOIN subjects s ON s.id=t.subject_id ORDER BY s.name, t.sort')->fetchAll(); }
function db_courses($f=[]){
  $w=["c.status='published'"];
  if(!empty($f['subject_id'])) $w[]='c.subject_id='.(int)$f['subject_id'];
  if(!empty($f['level_id'])) $w[]='c.level_id='.(int)$f['level_id'];
  if(!empty($f['q'])){ $q=db()->quote('%'.$f['q'].'%'); $w[]="(c.title LIKE $q OR c.description LIKE $q)"; }
  return db()->query('SELECT c.*, s.name AS subject, l.name AS level_name FROM courses c '.
    'LEFT JOIN subjects s ON s.id=c.subject_id LEFT JOIN levels l ON l.id=c.level_id '.
    'WHERE '.implode(' AND ',$w).' ORDER BY c.id DESC LIMIT 200')->fetchAll();
}
function db_course($id){
  $st=db()->prepare('SELECT c.*, s.name AS subject, l.name AS level_name FROM courses c LEFT JOIN subjects s ON s.id=c.subject_id LEFT JOIN levels l ON l.id=c.level_id WHERE c.id=?');
  $st->execute([(int)$id]); $c=$st->fetch(); if(!$c) return null;
  $st=db()->prepare("SELECT * FROM lessons WHERE course_id=? AND status='published' ORDER BY sort,id");
  $st->execute([(int)$id]); $c['lessons']=$st->fetchAll(); return $c;
}
function db_lessons($course_id){
  $st=db()->prepare("SELECT * FROM lessons WHERE course_id=? AND status='published' ORDER BY sort,id");
  $st->execute([(int)$course_id]); return $st->fetchAll();
}
function db_questions($f=[], $limit=10){
  $w=["q.status='published'"];
  if(!empty($f['subject_id'])) $w[]='q.subject_id='.(int)$f['subject_id'];
  if(!empty($f['topic_id'])) $w[]='q.topic_id='.(int)$f['topic_id'];
  if(!empty($f['exam_id'])) $w[]='q.exam_id='.(int)$f['exam_id'];
  if(!empty($f['subject'])) $w[]='(SELECT name FROM subjects WHERE id=q.subject_id)='.db()->quote($f['subject']);
  if(!empty($f['topic'])) $w[]='(SELECT name FROM topics WHERE id=q.topic_id)='.db()->quote($f['topic']);
  if(!empty($f['exam'])) $w[]='(SELECT slug FROM exams WHERE id=q.exam_id)='.db()->quote(strtolower($f['exam']));
  if(!empty($f['year'])) $w[]='q.year='.(int)$f['year'];
  $rows=db()->query('SELECT q.*, (SELECT name FROM subjects s WHERE s.id=q.subject_id) AS subject, '.
    '(SELECT name FROM topics t WHERE t.id=q.topic_id) AS topic, '.
    '(SELECT UPPER(name) FROM exams e WHERE e.id=q.exam_id) AS exam FROM questions q '.
    'WHERE '.implode(' AND ',$w).' ORDER BY RAND() LIMIT '.(int)$limit)->fetchAll();
  foreach($rows as &$r){
    $st=db()->prepare('SELECT label, body, is_correct FROM options WHERE question_id=? ORDER BY sort');
    $st->execute([$r['id']]); $opts=$st->fetchAll();
    $r['options']=array_column($opts,'body'); $r['answer']=0;
    foreach($opts as $i=>$o){ if($o['is_correct']){ $r['answer']=$i; break; } }
  }
  return $rows;
}
function db_question_count($f=[]){
  $w=["q.status='published'"];
  if(!empty($f['subject'])) $w[]='(SELECT name FROM subjects WHERE id=q.subject_id)='.db()->quote($f['subject']);
  $r=db()->query('SELECT COUNT(*) c FROM questions q WHERE '.implode(' AND ',$w))->fetch();
  return (int)($r['c']??0);
}
function audit($actor,$action,$entity='',$eid=null,$meta=''){
  try{ $st=db()->prepare('INSERT INTO audit_log(actor_id,action,entity,entity_id,meta) VALUES(?,?,?,?,?)');
    $st->execute([$actor,$action,$entity,$eid,substr((string)$meta,0,480)]); }catch(Exception $e){}
}

// ---------- settings (admin-controlled feature config) ----------
function setting($k,$fb=''){ try{
  if(!db_ok()) return $fb;
  $st=db()->prepare('SELECT v FROM settings WHERE k=?'); $st->execute([$k]);
  $v=$st->fetchColumn(); return $v===false?$fb:$v;
}catch(Exception $e){ return $fb; } }
function setting_int($k,$fb=0){ return (int)setting($k,$fb); }
function setting_on($k,$fb=true){ $v=setting($k,$fb?'1':'0'); return $v==='1'||$v===true; }
function settings_all(){ try{
  if(!db_ok()) return [];
  return db()->query('SELECT k,v FROM settings ORDER BY k')->fetchAll(PDO::FETCH_KEY_PAIR);
}catch(Exception $e){ return []; } }
function settings_save($pairs,$actor=1){ $st=db()->prepare('INSERT INTO settings(k,v) VALUES(?,?) ON DUPLICATE KEY UPDATE v=VALUES(v)');
  foreach($pairs as $k=>$v) $st->execute([$k,(string)$v]);
  audit($actor,'save','settings','',implode(',',array_keys($pairs))); }
function db_subjects_by_level($level_id){ $st=db()->prepare('SELECT s.*, l.name AS level_name FROM subjects s JOIN levels l ON l.id=s.level_id WHERE s.level_id=? ORDER BY s.sort'); $st->execute([(int)$level_id]); return $st->fetchAll(); }
function db_topics_by_subject($sid){ $st=db()->prepare('SELECT * FROM topics WHERE subject_id=? ORDER BY sort'); $st->execute([(int)$sid]); return $st->fetchAll(); }
function db_schools($approved_only=true){ try{
  $w=$approved_only?"WHERE status='approved'":'';
  return db()->query("SELECT * FROM schools $w ORDER BY name")->fetchAll();
}catch(Exception $e){ return []; } }
function db_roles_public(){ return [['slug'=>'student','name'=>'Student'],['slug'=>'parent','name'=>'Parent'],['slug'=>'mentor','name'=>'Mentor']]; }
function dd_options($rows,$val='id',$lab='name',$sel=''){
  $h='';
  foreach($rows as $r){ $v=$r[$val]??''; $l=$r[$lab]??$v;
    $s=((string)$v===(string)$sel)?' selected':'';
    $h.='<option value="'.htmlspecialchars($v).'"'.$s.'>'.htmlspecialchars($l).'</option>'; }
  return $h;
}
function db_plans(){ try{ return db()->query('SELECT * FROM plans ORDER BY price_kobo')->fetchAll(); }catch(Exception $e){ return []; } }
function db_leaderboard($school_id=null,$limit=20){
  $w=$school_id?'WHERE school_id='.(int)$school_id:'';
  return db()->query("SELECT name, xp, streak, (SELECT name FROM schools s WHERE s.id=u.school_id) AS school FROM users u $w AND status='active' ORDER BY xp DESC LIMIT ".(int)$limit)->fetchAll();
}