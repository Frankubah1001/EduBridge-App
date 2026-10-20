<?php // Admin API part 5: mentors, live sessions, messages, users
function admin_api_people($a,$actor){
  if($a==='mentor_list'){ admin_api_out(db()->query('SELECT m.*, (SELECT name FROM subjects s WHERE s.id=m.subject_id) AS subject, (SELECT email FROM users u WHERE u.id=m.user_id) AS email FROM mentors m ORDER BY m.id DESC LIMIT 100')->fetchAll()); }
  if($a==='save_mentor'){
    $id=(int)($_POST['id']??0); $nm=trim($_POST['name']??''); if($nm==='') admin_api_out(['error'=>'Name required'],400);
    $f=[$nm,empty($_POST['subject_id'])?null:(int)$_POST['subject_id'],$_POST['school']??'',(float)($_POST['rating']??5),$_POST['status']??'offline',(int)($_POST['verified']??0),$_POST['bio']??''];
    if($id) db()->prepare('UPDATE mentors SET name=?,subject_id=?,school=?,rating=?,status=?,verified=?,bio=? WHERE id=?')->execute([...$f,$id]);
    else { db()->prepare('INSERT INTO mentors(name,subject_id,school,rating,status,verified,bio) VALUES(?,?,?,?,?,?,?)')->execute($f); $id=db()->lastInsertId(); }
    audit($actor,'save','mentor',$id,$nm); admin_api_out(['ok'=>true,'id'=>$id]);
  }
  if($a==='delete_mentor'){ $id=(int)($_POST['id']??0); if(!$id) admin_api_out(['error'=>'Missing id'],400);
    db()->prepare('DELETE FROM mentors WHERE id=?')->execute([$id]); audit($actor,'delete','mentor',$id,''); admin_api_out(['ok'=>true]); }
  if($a==='session_list'){ admin_api_out(db()->query('SELECT s.*, (SELECT name FROM mentors m WHERE m.id=s.mentor_id) AS who FROM sessions s ORDER BY s.starts_at DESC LIMIT 50')->fetchAll()); }
  if($a==='save_session'){
    $id=(int)($_POST['id']??0); $mid=(int)($_POST['mentor_id']??0); $title=trim($_POST['title']??'');
    $when=trim($_POST['starts_at']??'');
    $when=str_replace('T',' ',$when); $when=str_pad($when,16,':00');
    if(!$mid||$title===''||trim($_POST['starts_at']??'')==='') admin_api_out(['error'=>'Mentor + title + date required'],400);
    $st=in_array($_POST['status']??'', ['scheduled','live','ended'])?$_POST['status']:'scheduled';
    $f=[$mid,$title,$when,$_POST['join_url']??'',$st];
    if($id) db()->prepare('UPDATE sessions SET mentor_id=?,title=?,starts_at=?,join_url=?,status=? WHERE id=?')->execute([...$f,$id]);
    else { db()->prepare('INSERT INTO sessions(mentor_id,title,starts_at,join_url,status) VALUES(?,?,?,?,?)')->execute($f); $id=db()->lastInsertId(); }
    audit($actor,'save','session',$id,$title); admin_api_out(['ok'=>true,'id'=>$id]);
  }
  if($a==='delete_session'){ $id=(int)($_POST['id']??0); if(!$id) admin_api_out(['error'=>'Missing id'],400);
    db()->prepare('DELETE FROM sessions WHERE id=?')->execute([$id]); audit($actor,'delete','session',$id,''); admin_api_out(['ok'=>true]); }
  if($a==='message_list'){ admin_api_out(db()->query('SELECT m.*, (SELECT name FROM users a WHERE a.id=m.sender_id) AS from_name, (SELECT name FROM users b WHERE b.id=m.receiver_id) AS to_name FROM messages m ORDER BY m.id DESC LIMIT 50')->fetchAll()); }
  if($a==='user_list'){
    $w=['1=1']; $g=$_GET;
    if(!empty($g['role']))$w[]='r.slug='.db()->quote($g['role']);
    if(!empty($g['school_id']))$w[]='u.school_id='.(int)$g['school_id'];
    if(!empty($g['q']))$w[]='(u.name LIKE '.db()->quote('%'.$g['q'].'%').' OR u.email LIKE '.db()->quote('%'.$g['q'].'%').')';
    $rows=db()->query('SELECT u.id,u.name,u.email,u.phone,u.role_id,u.class_level,u.school_id,u.xp,u.streak,u.status,u.verified,u.created_at, r.slug AS role, (SELECT name FROM schools s WHERE s.id=u.school_id) AS school FROM users u JOIN roles r ON r.id=u.role_id WHERE '.implode(' AND ',$w).' ORDER BY u.id DESC LIMIT 100')->fetchAll();
    admin_api_out($rows);
  }
  if($a==='user_status'){ $id=(int)($_POST['id']??0); if(!$id) admin_api_out(['error'=>'Missing id'],400);
    $st=in_array($_POST['status']??'', ['active','suspended','deleted'])?$_POST['status']:'active';
    db()->prepare('UPDATE users SET status=? WHERE id=?')->execute([$st,$id]);
    audit($actor,'status','user',$id,$st); admin_api_out(['ok'=>true]); }
  if($a==='verify_mentor'){ $id=(int)($_POST['id']??0); if(!$id) admin_api_out(['error'=>'Missing id'],400);
    $v=(int)($_POST['verified']??1);
    db()->prepare('UPDATE users SET verified=? WHERE id=?')->execute([$v,$id]);
    try{ db()->prepare('UPDATE mentors SET verified=? WHERE user_id=?')->execute([$v,$id]); }catch(Exception $e){}
    audit($actor,'verify','mentor',$id,'v='.$v); admin_api_out(['ok'=>true]); }
  return false;
}
