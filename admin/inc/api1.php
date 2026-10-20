<?php // Admin API part 1: taxonomy, courses, lessons. Returns true when the action is handled.
function admin_api_content($a,$actor){
  if($a==='stats'){
    $s=[]; foreach(['users','courses','lessons','questions','attempts','forum_threads','schools'] as $t)
      $s[$t]=(int)db()->query("SELECT COUNT(*) c FROM $t")->fetch()['c'];
    $s['by_subject']=db()->query("SELECT (SELECT name FROM subjects s WHERE s.id=q.subject_id) AS subject, COUNT(*) c FROM questions q GROUP BY subject ORDER BY c DESC")->fetchAll();
    admin_api_out($s);
  }
  if($a==='levels'){ admin_api_out(db_levels()); }
  if($a==='subjects'){ admin_api_out(db_subjects_all()); }
  if($a==='topics'){ $sid=(int)($_GET['subject_id']??0);
    admin_api_out($sid?db()->query('SELECT * FROM topics WHERE subject_id='.$sid.' ORDER BY sort')->fetchAll():db_topics_all()); }
  if($a==='exams'){ admin_api_out(db_exams_all()); }
  if($a==='add_subject'){
    $lid=(int)($_POST['level_id']??0); $name=trim($_POST['name']??'');
    if($name==='') admin_api_out(['error'=>'Name required'],400);
    $lv=db()->query('SELECT id FROM levels WHERE id='.$lid)->fetchColumn();
    if(!$lv) admin_api_out(['error'=>'Unknown level'],400);
    db()->prepare('INSERT INTO subjects(level_id,slug,name,sort) VALUES(?,?,?,?)')->execute([$lid,slugify($name),$name,99]);
    audit($actor,'create','subject',db()->lastInsertId(),$name); admin_api_out(['ok'=>true]);
  }
  if($a==='add_topic'){
    $sid=(int)($_POST['subject_id']??0); $name=trim($_POST['name']??'');
    if($name==='') admin_api_out(['error'=>'Name required'],400);
    $sb=db()->query('SELECT id FROM subjects WHERE id='.$sid)->fetchColumn();
    if(!$sb) admin_api_out(['error'=>'Unknown subject'],400);
    db()->prepare('INSERT INTO topics(subject_id,slug,name,sort) VALUES(?,?,?,?)')->execute([$sid,slugify($name),$name,99]);
    audit($actor,'create','topic',db()->lastInsertId(),$name); admin_api_out(['ok'=>true]);
  }
  if($a==='course_list'){
    $rows=db()->query('SELECT c.*, s.name AS subject, l.name AS level_name, (SELECT COUNT(*) FROM lessons WHERE course_id=c.id) AS lessons FROM courses c LEFT JOIN subjects s ON s.id=c.subject_id LEFT JOIN levels l ON l.id=c.level_id ORDER BY c.id DESC LIMIT 200')->fetchAll();
    admin_api_out($rows);
  }
  if($a==='save_course'){
    $id=(int)($_POST['id']??0);
    $title=trim($_POST['title']??''); if($title==='') admin_api_out(['error'=>'Title required'],400);
    $f=[$_POST['code']??('C'.time()),$title,(int)($_POST['level_id']??0),(int)($_POST['subject_id']??0),$_POST['description']??'',$_POST['status']??'draft'];
    if(!$f[2]||!$f[3]) admin_api_out(['error'=>'Level + subject required'],400);
    if($id) db()->prepare('UPDATE courses SET code=?,title=?,level_id=?,subject_id=?,description=?,status=? WHERE id=?')->execute([...$f,$id]);
    else { db()->prepare('INSERT INTO courses(code,title,level_id,subject_id,description,status) VALUES(?,?,?,?,?,?)')->execute($f); $id=db()->lastInsertId(); }
    audit($actor,'save','course',$id,$title); admin_api_out(['ok'=>true,'id'=>$id]);
  }
  if($a==='publish_course'){ $id=(int)($_POST['id']??0); $st=$_POST['status']??'published';
    if(!$id) admin_api_out(['error'=>'Missing id'],400);
    db()->prepare('UPDATE courses SET status=? WHERE id=?')->execute([$st,$id]);
    audit($actor,'status','course',$id,$st); admin_api_out(['ok'=>true]); }
  if($a==='delete_course'){ $id=(int)($_POST['id']??0); if(!$id) admin_api_out(['error'=>'Missing id'],400);
    db()->prepare('DELETE FROM lessons WHERE course_id=?')->execute([$id]);
    db()->prepare('DELETE FROM courses WHERE id=?')->execute([$id]);
    audit($actor,'delete','course',$id,''); admin_api_out(['ok'=>true]); }
  if($a==='lesson_list'){ $cid=(int)($_GET['course_id']??0);
    $st=db()->prepare('SELECT * FROM lessons WHERE course_id=? ORDER BY sort,id'); $st->execute([$cid]); admin_api_out($st->fetchAll()); }
  if($a==='save_lesson'){
    $id=(int)($_POST['id']??0); $cid=(int)($_POST['course_id']??0); $title=trim($_POST['title']??'');
    if(!$cid||$title==='') admin_api_out(['error'=>'Course + title required'],400);
    $f=[$cid,empty($_POST['topic_id'])?null:(int)$_POST['topic_id'],$title,$_POST['body']??'',(int)($_POST['duration_min']??10),(int)($_POST['sort']??0),$_POST['status']??'published'];
    if($id) db()->prepare('UPDATE lessons SET course_id=?,topic_id=?,title=?,body=?,duration_min=?,sort=?,status=? WHERE id=?')->execute([...$f,$id]);
    else { db()->prepare('INSERT INTO lessons(course_id,topic_id,title,body,duration_min,sort,status) VALUES(?,?,?,?,?,?,?)')->execute($f); $id=db()->lastInsertId(); }
    audit($actor,'save','lesson',$id,$title); admin_api_out(['ok'=>true,'id'=>$id]);
  }
  if($a==='delete_lesson'){ $id=(int)($_POST['id']??0); if(!$id) admin_api_out(['error'=>'Missing id'],400);
    db()->prepare('DELETE FROM lessons WHERE id=?')->execute([$id]); audit($actor,'delete','lesson',$id,''); admin_api_out(['ok'=>true]); }
  return false;
}
