<?php // EduBridge seeder: JSON -> MariaDB (idempotent, re-runnable)
require_once __DIR__.'/../includes/db.php';
$dsn='mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME.';charset=utf8mb4';
if(DB_SOCK && file_exists(DB_SOCK)) $dsn.=';unix_socket='.DB_SOCK;
$pdo=new PDO($dsn, DB_USER, DB_PASS,
 [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$D=__DIR__.'/../data';
$J=fn($f)=>json_decode(file_get_contents("$D/$f"),true)?:[];
$slug=fn($s)=>strtolower(trim(preg_replace('/[^a-z0-9]+/i','-',$s),'-'));
// levels from curriculum
$cur=$J('curriculum.json');
$lvlMap=[]; $i=1;
foreach(($cur['levels']??[]) as $l){
  $pdo->prepare('INSERT IGNORE INTO levels(slug,name,sort) VALUES(?,?,?)')->execute([$l['id'],$l['name'],$i++]);
  $lvlMap[$l['id']]=$pdo->query("SELECT id FROM levels WHERE slug=".$pdo->quote($l['id']))->fetchColumn();
}
// subjects + topics
$subMap=[]; $topMap=[];
foreach(($cur['levels']??[]) as $l){ $lid=$lvlMap[$l['id']]??1;
  foreach(($l['subjects']??[]) as $k=>$s){
    $pdo->prepare('INSERT IGNORE INTO subjects(level_id,slug,name,sort) VALUES(?,?,?,?)')->execute([$lid,$slug($s),$s,$k]);
    $sid=$pdo->query('SELECT id FROM subjects WHERE level_id='.(int)$lid.' AND slug='.$pdo->quote($slug($s)))->fetchColumn();
    $subMap[$l['id'].'|'.$s]=$sid;
  }
}
$si=0;
foreach(($cur['topics']??[]) as $subj=>$tops){ $si++;
  $sid=$pdo->query('SELECT id FROM subjects WHERE name='.$pdo->quote($subj).' LIMIT 1')->fetchColumn();
  if(!$sid){ // attach orphan subjects to waec level
    $wl=$lvlMap['waec']??1;
    $pdo->prepare('INSERT IGNORE INTO subjects(level_id,slug,name,sort) VALUES(?,?,?,?)')->execute([$wl,$slug($subj),$subj,$si]);
    $sid=$pdo->query('SELECT id FROM subjects WHERE level_id='.(int)$wl.' AND slug='.$pdo->quote($slug($subj)))->fetchColumn();
  }
  foreach($tops as $k=>$t){
    $pdo->prepare('INSERT IGNORE INTO topics(subject_id,slug,name,sort) VALUES(?,?,?,?)')->execute([$sid,$slug($t),$t,$k]);
    $tid=$pdo->query('SELECT id FROM topics WHERE subject_id='.(int)$sid.' AND slug='.$pdo->quote($slug($t)))->fetchColumn();
    $topMap[$subj.'|'.$t]=$tid;
  }
}
// courses + lessons
$courses=$J('courses.json'); $nc=0;$nl=0;
foreach($courses as $c){
  $sid=$pdo->query('SELECT id FROM subjects WHERE name='.$pdo->quote($c['subject']).' LIMIT 1')->fetchColumn()?:1;
  $lvlName=['jss'=>2,'sss'=>3,'waec'=>4,'jamb'=>5,'primary'=>1];
  $lid=$lvlName[$c['level']]??4;
  $pdo->prepare("INSERT IGNORE INTO courses(code,title,level_id,subject_id,description,status) VALUES(?,?,?,?,?,'published')")
    ->execute([$c['code'],$c['title'],$lid,$sid,$c['description']]);
  $cid=$pdo->query('SELECT id FROM courses WHERE code='.$pdo->quote($c['code']))->fetchColumn(); $nc++;
  foreach(($c['lessons']??[]) as $k=>$l){
    $pdo->prepare("INSERT INTO lessons(course_id,title,body,duration_min,sort,status) VALUES(?,?,?,?,?,'published')")
      ->execute([$cid,$l['title'],$l['body'],$l['duration_min']??10,$k]); $nl++;
  }
}
// exams map
$exMap=[]; foreach($pdo->query('SELECT id,slug FROM exams')->fetchAll() as $r) $exMap[$r['slug']]=$r['id'];
// questions + options
$qs=$J('questions.json'); $nq=0;
$insQ=$pdo->prepare('INSERT INTO questions(subject_id,topic_id,exam_id,year,stem,explanation,difficulty,discrimination,status) VALUES(?,?,?,?,?,?,?,?,\'published\')');
$insO=$pdo->prepare('INSERT INTO options(question_id,label,body,is_correct,sort) VALUES(?,?,?,?,?)');
foreach($qs as $q){
  $sid=$pdo->query('SELECT id FROM subjects WHERE name='.$pdo->quote($q['subject']).' LIMIT 1')->fetchColumn();
  if(!$sid){ $wl=$lvlMap['waec']??1; $pdo->prepare('INSERT IGNORE INTO subjects(level_id,slug,name,sort) VALUES(?,?,?,?)')->execute([$wl,$slug($q['subject']),$q['subject'],99]);
    $sid=$pdo->query('SELECT id FROM subjects WHERE level_id='.(int)$wl.' AND slug='.$pdo->quote($slug($q['subject'])))->fetchColumn(); }
  $tid=$topMap[$q['subject'].'|'.$q['topic']]??null;
  if(!$tid){ $pdo->prepare('INSERT IGNORE INTO topics(subject_id,slug,name,sort) VALUES(?,?,?,?)')->execute([$sid,$slug($q['topic']),$q['topic'],99]);
    $tid=$pdo->query('SELECT id FROM topics WHERE subject_id='.(int)$sid.' AND slug='.$pdo->quote($slug($q['topic'])))->fetchColumn(); }
  $eid=$exMap[strtolower($q['exam'])]??$exMap['waec'];
  $insQ->execute([$sid,$tid,$eid,$q['year']??2024,$q['stem'],$q['explanation']??'',$q['difficulty']??0,$q['discrimination']??1]);
  $qid=$pdo->lastInsertId(); $nq++;
  foreach(($q['options']??[]) as $idx=>$body)
    $insO->execute([$qid,chr(65+$idx),$body,$idx==($q['answer']??0)?1:0,$idx]);
}
// mentors
$mentors=$J('mentors.json'); $nm=0;
foreach($mentors as $m){ $sid=$pdo->query('SELECT id FROM subjects WHERE name LIKE '.$pdo->quote('%'.$m['subject'].'%').' LIMIT 1')->fetchColumn();
  $pdo->prepare('INSERT INTO mentors(name,subject_id,school,rating,status) VALUES(?,?,?,?,?)')
    ->execute([$m['name'],$sid?:null,$m['school']??'',$m['rating']??5,$m['status']??'offline']); $nm++; }
// forum seed
$forum=$J('forum.json');
foreach($forum as $t){ $pdo->prepare("INSERT INTO forum_threads(author_id,title,tag,status) VALUES(1,?,?,'open')")->execute([$t['topic'],$t['tag']??'General']); }
// default admin (password: admin123)
$pdo->prepare("INSERT IGNORE INTO users(name,email,password_hash,role_id) VALUES('Admin','admin@edubridge.ng',?,5)")
  ->execute([password_hash('admin123',PASSWORD_DEFAULT)]);
echo "SEED OK: levels=".count($lvlMap)." courses=$nc lessons=$nl questions=$nq mentors=$nm".PHP_EOL;
