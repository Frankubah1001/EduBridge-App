<?php // Restore seed rows removed during admin-app verification (course 1, question 1 + options).
// Idempotent: safe to re-run. Run: php database/restore_seed_rows.php
require_once __DIR__.'/../config.php';
if(!db_ok()){ echo "DB offline\n"; exit(1); }
$db=db();
$D=__DIR__.'/../data';
$J=fn($f)=>json_decode(file_get_contents("$D/$f"),true)?:[];
$slug=fn($s)=>strtolower(trim(preg_replace('/[^a-z0-9]+/i','-',$s),'-'));

// --- course 1 (MTH-SSS01) + its 3 lessons ---
$courses=$J('courses.json');
$lvlMap=['primary'=>1,'jss'=>2,'sss'=>3,'waec'=>4,'jamb'=>5];
foreach($courses as $c){
  $cid=$db->query('SELECT id FROM courses WHERE code='.$db->quote($c['code']))->fetchColumn();
  if(!$cid){
    $sid=$db->query('SELECT id FROM subjects WHERE name='.$db->quote($c['subject']).' LIMIT 1')->fetchColumn()?:1;
    $lid=$lvlMap[$c['level']]??4;
    $db->prepare("INSERT INTO courses(code,title,level_id,subject_id,description,status) VALUES(?,?,?,?,?,'published')")
      ->execute([$c['code'],$c['title'],$lid,$sid,$c['description']]);
    $cid=$db->lastInsertId();
    echo "restored course {$c['code']}\n";
  }
  $i=0;
  foreach(($c['lessons']??[]) as $l){
    $lid=$db->query('SELECT id FROM lessons WHERE course_id='.(int)$cid.' AND title='.$db->quote($l['title']).' LIMIT 1')->fetchColumn();
    if(!$lid){
      $db->prepare("INSERT INTO lessons(course_id,title,body,duration_min,sort,status) VALUES(?,?,?,?,?,'published')")
        ->execute([$cid,$l['title'],$l['body'],$l['duration_min']??10,$i]);
      echo "  restored lesson {$l['title']}\n";
    }
    $i++;
  }
}

// --- questions + options (duplicate-stem aware: compares stem+explanation counts) ---
$exMap=[]; foreach($db->query('SELECT id,slug FROM exams')->fetchAll() as $r) $exMap[$r['slug']]=$r['id'];
$topMap=[];
foreach($db->query('SELECT t.id,t.slug,s.name AS subject FROM topics t JOIN subjects s ON s.id=t.subject_id')->fetchAll() as $r)
  $topMap[$r['subject'].'|'.$r['slug']]=$r['id'];
$qs=$J('questions.json');
// tally what the DB already has, keyed by stem|explanation
$have=[];
foreach($db->query('SELECT q.stem, q.explanation, COUNT(*) c FROM questions q GROUP BY q.stem, q.explanation')->fetchAll() as $r)
  $have[$r['stem'].'|'.$r['explanation']]=(int)$r['c'];
$want=[];
foreach($qs as $x){ $k=$x['stem'].'|'.($x['explanation']??''); $want[$k]=($want[$k]??0)+1; }
$restored=0;
foreach($qs as $q){
  $k=$q['stem'].'|'.($q['explanation']??'');
  $have[$k]=($have[$k]??0)-1;
  if($have[$k]>=0) continue;                    // already present
  $have[$k]=-1;                                 // one of this pair is now present
  $sid=$db->query('SELECT id FROM subjects WHERE name='.$db->quote($q['subject']).' LIMIT 1')->fetchColumn();
  if(!$sid){
    $wl=4; $db->prepare('INSERT IGNORE INTO subjects(level_id,slug,name,sort) VALUES(?,?,?,99)')->execute([$wl,$slug($q['subject']),$q['subject']]);
    $sid=$db->query('SELECT id FROM subjects WHERE name='.$db->quote($q['subject']).' LIMIT 1')->fetchColumn();
  }
  $tid=$topMap[$q['subject'].'|'.$slug($q['topic'])]??null;
  if(!$tid){
    $db->prepare('INSERT IGNORE INTO topics(subject_id,slug,name,sort) VALUES(?,?,?,99)')->execute([$sid,$slug($q['topic']),$q['topic']]);
    $tid=$db->query('SELECT id FROM topics WHERE subject_id='.(int)$sid.' AND slug='.$db->quote($slug($q['topic'])))->fetchColumn();
  }
  $eid=$exMap[strtolower($q['exam'])]??$exMap['waec'];
  $db->prepare("INSERT INTO questions(subject_id,topic_id,exam_id,year,stem,explanation,difficulty,discrimination,status) VALUES(?,?,?,?,?,?,?,?,'published')")
    ->execute([$sid,$tid,$eid,$q['year']??2024,$q['stem'],$q['explanation']??'',$q['difficulty']??0,$q['discrimination']??1]);
  $qid=$db->lastInsertId();
  $io=$db->prepare('INSERT INTO options(question_id,label,body,is_correct,sort) VALUES(?,?,?,?,?)');
  foreach(($q['options']??[]) as $i=>$body)
    $io->execute([$qid,chr(65+$i),$body,$i==($q['answer']??0)?1:0,$i]);
  $restored++;
  echo "restored question #$qid\n";
}
if(!$restored) echo "questions already complete\n";

// --- make sure the flagship course + paper are published and wired ---
$db->exec("UPDATE courses SET status='published' WHERE code='MTH-SSS01'");
$db->exec("UPDATE courses SET status='published' WHERE code='PHY-SSS02'");
echo "RESTORE OK\n";
