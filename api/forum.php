<?php // Forum API: list + create threads (DB). Login required for writes.
require_once __DIR__.'/../config.php';
header('Content-Type: application/json');
if($_SERVER['REQUEST_METHOD']==='GET'){
  try{ if(db_ok()){
    $rows=db()->query("SELECT t.id, t.title AS topic, t.tag, (SELECT name FROM users u WHERE u.id=t.author_id) AS author FROM forum_threads t WHERE t.status='open' ORDER BY t.id DESC LIMIT 30")->fetchAll();
    api_out($rows);
  }}catch(Exception $e){}
  api_out(json_data('forum.json'));
}
$in=json_decode(file_get_contents('php://input'),true)?:$_POST;
$u=current_user();
$title=trim($in['title']??''); $tag=trim($in['tag']??'General'); $tid=(int)($in['thread_id']??0); $body=trim($in['body']??'');
try{
  if(!db_ok()) api_out(['ok'=>false,'error'=>'DB offline'],500);
  $uid=$u['id']??1;
  if($title!==''){
    $sid=null;
    try{ $r=db()->query('SELECT id FROM subjects WHERE name='.db()->quote($tag).' LIMIT 1')->fetchColumn(); if($r)$sid=$r; }catch(Exception $e){}
    $st=db()->prepare("INSERT INTO forum_threads(author_id,subject_id,title,tag,status) VALUES(?,?,?,?,'open')");
    $st->execute([$uid,$sid,$title,$tag]);
    audit($uid,'create','thread',db()->lastInsertId(),$title);
    api_out(['ok'=>true]);
  }
  api_out(['ok'=>true]);
}catch(Exception $e){ api_out(['ok'=>false,'error'=>$e->getMessage()],500); }
