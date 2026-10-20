<?php // Verify admin JS expected fields, seeding throwaway rows for empty tables first.
require_once __DIR__.'/../config.php';
if(!db_ok()){ echo "DB offline\n"; exit(1); }
$B='http://localhost/EduBridge/admin/inc/api.php';
$J='/tmp/vf.txt';
exec("curl -s -c $J -b $J -o /dev/null -d 'email=admin@edubridge.ng&password=admin123' http://localhost/EduBridge/admin/login.php");

// --- temp rows so the empty tables can be checked ---
$mid=(int)db()->query('SELECT id FROM mentors ORDER BY id LIMIT 1')->fetchColumn();
db()->prepare("INSERT INTO sessions(mentor_id,title,starts_at,join_url,status) VALUES(?,?,?,'https://x.test','scheduled')")
  ->execute([$mid,'VFY Session','2026-12-01 10:00:00']);
$sid=db()->lastInsertId();
$uid=(int)db()->query('SELECT id FROM users ORDER BY id LIMIT 1')->fetchColumn();
db()->prepare('INSERT INTO messages(sender_id,receiver_id,body) VALUES(?,?,?)')->execute([$uid,$uid,'VFY message body']);
db()->prepare("INSERT INTO forum_threads(author_id,title,tag,status) VALUES(?,?,?,'flagged')")->execute([$uid,'VFY Flagged Thread','General']);
$fid=db()->lastInsertId();

$expect=[
 'stats'=>['users','courses','lessons','questions','attempts','forum_threads','schools','by_subject'],
 'levels'=>['id','name','subjects'],
 'subjects'=>['id','level_id','name','level_name'],
 'topics'=>['id','subject_id','name','subject'],
 'exams'=>['id','slug','name'],
 'course_list'=>['id','code','title','subject','status','lessons'],
 'question_list'=>['id','exam','year','subject','topic','status','stem'],
 'question_get'=>['id','stem','options','explanation','difficulty','status','year'],
 'paper_list'=>['id','title','exam','subject','items'],
 'paper_items'=>['id','stem'],
 'settings_get'=>[],
 'school_list'=>['id','name','code','status','state','contact_phone','members'],
 'plan_list'=>['id','slug','name','price_kobo','term','features'],
 'voucher_list'=>['id','code','plan','status','used','max_uses'],
 'mentor_list'=>['id','name','subject_id','subject','status','verified','school','email'],
 'session_list'=>['id','title','status','who','starts_at'],
 'user_list'=>['id','name','email','role','status','school','xp'],
 'message_list'=>['id','from_name','to_name','body','created_at','read_at'],
 'audit_list'=>['id','action','entity','actor','created_at','meta'],
 'flagged'=>['id','title','author','status'],
];
$qid=(int)db()->query('SELECT id FROM questions ORDER BY id LIMIT 1')->fetchColumn();
$pid=(int)db()->query('SELECT id FROM papers ORDER BY id LIMIT 1')->fetchColumn();
$bad=0; $empty=[];
foreach($expect as $action=>$fields){
  $raw=shell_exec("curl -s -b $J '$B?action=$action&course_id=1&id=$qid&paper_id=$pid'");
  $j=json_decode($raw,true);
  if($j===null){ echo "FAIL $action: not JSON ($raw)\n"; $bad++; continue; }
  if(is_array($j)&&array_is_list($j)){
    if(!$j){ $empty[]=$action; continue; }
    $row=$j[0];
  } else $row=$j;
  foreach($fields as $f){
    if(!array_key_exists($f,$row)){ echo "FAIL $action: missing field '$f'\n"; $bad++; }
  }
}
if($empty) echo "empty tables: ".implode(', ',$empty)."\n";

// --- cleanup temp rows ---
db()->prepare('DELETE FROM messages WHERE body=?')->execute(['VFY message body']);
db()->prepare('DELETE FROM forum_threads WHERE id=?')->execute([$fid]);
db()->prepare('DELETE FROM sessions WHERE id=?')->execute([$sid]);
echo "temp rows removed\n";
echo "\n".($bad? "FIELD CHECK: $bad problem(s)\n" : "FIELD CHECK: ALL OK\n");
