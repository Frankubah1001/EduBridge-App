<?php // Admin API part 3: papers, moderation, settings
function admin_paper_status($s){ return in_array($s,['draft','published','archived']) ? $s : 'draft'; }
function admin_api_papers($a,$actor){
  if($a==='paper_list'){ admin_api_out(db()->query('SELECT p.*, (SELECT name FROM exams e WHERE e.id=p.exam_id) AS exam, (SELECT name FROM subjects s WHERE s.id=p.subject_id) AS subject, (SELECT COUNT(*) FROM paper_items WHERE paper_id=p.id) AS items FROM papers p ORDER BY p.id DESC LIMIT 50')->fetchAll()); }
  if($a==='save_paper'){
    $id=(int)($_POST['id']??0); $title=trim($_POST['title']??'');
    if($title===''||!(int)($_POST['exam_id']??0)||!(int)($_POST['subject_id']??0)) admin_api_out(['error'=>'Title + exam + subject required'],400);
    $f=[$title,(int)$_POST['exam_id'],(int)$_POST['subject_id'],(int)($_POST['duration_min']??20),admin_paper_status($_POST['status']??'draft')];
    if($id) db()->prepare('UPDATE papers SET title=?,exam_id=?,subject_id=?,duration_min=?,status=? WHERE id=?')->execute([...$f,$id]);
    else { db()->prepare('INSERT INTO papers(title,exam_id,subject_id,duration_min,status) VALUES(?,?,?,?,?)')->execute($f); $id=db()->lastInsertId(); }
    audit($actor,'save','paper',$id,$title); admin_api_out(['ok'=>true,'id'=>$id]);
  }
  if($a==='delete_paper'){ $id=(int)($_POST['id']??0); if(!$id) admin_api_out(['error'=>'Missing id'],400);
    db()->prepare('DELETE FROM paper_items WHERE paper_id=?')->execute([$id]);
    db()->prepare('DELETE FROM papers WHERE id=?')->execute([$id]);
    audit($actor,'delete','paper',$id,''); admin_api_out(['ok'=>true]); }
  if($a==='paper_items'){ $pid=(int)($_GET['paper_id']??0);
    $st=db()->prepare('SELECT q.id,q.stem FROM paper_items pi JOIN questions q ON q.id=pi.question_id WHERE pi.paper_id=? ORDER BY pi.sort'); $st->execute([$pid]);
    admin_api_out($st->fetchAll()); }
  if($a==='paper_remove_item'){ $pid=(int)($_POST['paper_id']??0); $qid=(int)($_POST['question_id']??0);
    db()->prepare('DELETE FROM paper_items WHERE paper_id=? AND question_id=?')->execute([$pid,$qid]);
    audit($actor,'remove_item','paper',$pid,'q='.$qid); admin_api_out(['ok'=>true]); }
  if($a==='paper_autofill'){ $pid=(int)($_POST['paper_id']??0); $n=min(100,max(5,(int)($_POST['n']??20)));
    if(!$pid) admin_api_out(['error'=>'Missing paper'],400);
    $p=db()->query('SELECT * FROM papers WHERE id='.$pid)->fetch();
    if(!$p) admin_api_out(['error'=>'Paper not found'],404);
    $ids=db()->query('SELECT id FROM questions WHERE subject_id='.(int)$p['subject_id']." AND exam_id=".(int)$p['exam_id']." AND status='published' ORDER BY RAND() LIMIT $n")->fetchAll();
    db()->prepare('DELETE FROM paper_items WHERE paper_id=?')->execute([$pid]);
    $io=db()->prepare('INSERT IGNORE INTO paper_items(paper_id,question_id,sort) VALUES(?,?,?)');
    foreach($ids as $i=>$r) $io->execute([$pid,$r['id'],$i]);
    audit($actor,'autofill','paper',$pid,"n=$n"); admin_api_out(['ok'=>true,'added'=>count($ids)]); }
  if($a==='flagged'){ admin_api_out(db()->query("SELECT t.*, (SELECT name FROM users u WHERE u.id=t.author_id) AS author FROM forum_threads t WHERE t.status='flagged' ORDER BY t.id DESC LIMIT 50")->fetchAll()); }
  if($a==='moderate'){ $id=(int)($_POST['id']??0); if(!$id) admin_api_out(['error'=>'Missing id'],400);
    $st=in_array($_POST['status']??'', ['open','flagged','removed','closed'])?$_POST['status']:'open';
    db()->prepare('UPDATE forum_threads SET status=? WHERE id=?')->execute([$st,$id]);
    audit($actor,'moderate','thread',$id,$st); admin_api_out(['ok'=>true]); }
  if($a==='audit_list'){ admin_api_out(db()->query('SELECT a.*, (SELECT email FROM users u WHERE u.id=a.actor_id) AS actor FROM audit_log a ORDER BY a.id DESC LIMIT 100')->fetchAll()); }
  return false;
}
