<?php // Admin API part 2: questions + papers
function admin_api_questions($a,$actor){
  if($a==='question_list'){
    $w=['1=1']; $g=$_GET;
    if(!empty($g['subject_id']))$w[]='q.subject_id='.(int)$g['subject_id'];
    if(!empty($g['topic_id']))$w[]='q.topic_id='.(int)$g['topic_id'];
    if(!empty($g['exam_id']))$w[]='q.exam_id='.(int)$g['exam_id'];
    if(!empty($g['status']))$w[]='q.status='.db()->quote($g['status']);
    if(!empty($g['q']))$w[]='q.stem LIKE '.db()->quote('%'.$g['q'].'%');
    $rows=db()->query('SELECT q.*, (SELECT name FROM subjects s WHERE s.id=q.subject_id) AS subject, (SELECT name FROM topics t WHERE t.id=q.topic_id) AS topic, (SELECT name FROM exams e WHERE e.id=q.exam_id) AS exam, (SELECT COUNT(*) FROM options o WHERE o.question_id=q.id) AS opts FROM questions q WHERE '.implode(' AND ',$w).' ORDER BY q.id DESC LIMIT 100')->fetchAll();
    admin_api_out($rows);
  }
  if($a==='question_get'){ $id=(int)($_GET['id']??0);
    $q=db()->query('SELECT * FROM questions WHERE id='.$id)->fetch();
    if(!$q) admin_api_out(['error'=>'Not found'],404);
    $st=db()->prepare('SELECT * FROM options WHERE question_id=? ORDER BY sort'); $st->execute([$id]); $q['options']=$st->fetchAll();
    admin_api_out($q);
  }
  if($a==='save_question'){
    $id=(int)($_POST['id']??0);
    $stem=trim($_POST['stem']??''); if($stem==='') admin_api_out(['error'=>'Stem required'],400);
    if(!(int)($_POST['subject_id']??0)||!(int)($_POST['exam_id']??0)) admin_api_out(['error'=>'Subject + exam required'],400);
    $opts=json_decode($_POST['options_json']??'[]',true)?:[];
    $bodies=array_values(array_filter(array_map(fn($o)=>trim($o['body']??''),$opts)));
    if(count($bodies)<2) admin_api_out(['error'=>'At least 2 options required'],400);
    if(count($bodies)!==count(array_unique($bodies))) admin_api_out(['error'=>'Options must be unique'],400);
    $hasCorrect=false; foreach($opts as $o){ if(!empty($o['correct'])&&trim($o['body']??'')!=='') $hasCorrect=true; }
    if(!$hasCorrect) admin_api_out(['error'=>'Mark one correct option'],400);
    $f=[(int)$_POST['subject_id'],empty($_POST['topic_id'])?null:(int)$_POST['topic_id'],(int)$_POST['exam_id'],(int)($_POST['year']??2024),$stem,$_POST['explanation']??'',(float)($_POST['difficulty']??0),$_POST['status']??'published'];
    if($id) db()->prepare('UPDATE questions SET subject_id=?,topic_id=?,exam_id=?,year=?,stem=?,explanation=?,difficulty=?,status=? WHERE id=?')->execute([...$f,$id]);
    else { db()->prepare('INSERT INTO questions(subject_id,topic_id,exam_id,year,stem,explanation,difficulty,status) VALUES(?,?,?,?,?,?,?,?)')->execute($f); $id=db()->lastInsertId(); }
    db()->prepare('DELETE FROM options WHERE question_id=?')->execute([$id]);
    $io=db()->prepare('INSERT INTO options(question_id,label,body,is_correct,sort) VALUES(?,?,?,?,?)');
    foreach($opts as $i=>$o) $io->execute([$id,chr(65+$i),$o['body']??'',!empty($o['correct'])?1:0,$i]);
    audit($actor,'save','question',$id,substr($stem,0,80)); admin_api_out(['ok'=>true,'id'=>$id]);
  }
  if($a==='question_status'){ $id=(int)($_POST['id']??0); if(!$id) admin_api_out(['error'=>'Missing id'],400);
    db()->prepare('UPDATE questions SET status=? WHERE id=?')->execute([$_POST['status']??'draft',$id]);
    audit($actor,'status','question',$id,$_POST['status']??''); admin_api_out(['ok'=>true]); }
  if($a==='delete_question'){ $id=(int)($_POST['id']??0); if(!$id) admin_api_out(['error'=>'Missing id'],400);
    db()->prepare('DELETE FROM options WHERE question_id=?')->execute([$id]);
    db()->prepare('DELETE FROM questions WHERE id=?')->execute([$id]);
    audit($actor,'delete','question',$id,''); admin_api_out(['ok'=>true]); }
  return false;
}
