<?php require_once __DIR__.'/../config.php';
// Delta-sync receiver: accepts offline queue, returns authoritative ack
$in=json_decode(file_get_contents('php://input'),true)??[];
$events=$in['events']??[];
$u=current_user(); $uid=$u&&!empty($u['id'])?(int)$u['id']:null;
$acked=0; $persisted=0; $errors=[];

// 1) Persist to MySQL sync_queue (source of truth)
if(db_ok()){
  try{
    $st=db()->prepare('INSERT INTO sync_queue(user_id,op,payload,synced) VALUES(?,?,?,1)');
    foreach($events as $e){
      if(!isset($e['op'])) continue;
      $st->execute([$uid, substr((string)$e['op'],0,40), json_encode($e['payload']??$e)]);
      $persisted++;
    }
  }catch(Exception $ex){ $errors[]='db:'.$ex->getMessage(); }
}
// 2) Mirror to a JSON log for debugging — never fatal if the dir is not writable
$logBody=json_encode(['at'=>date('c'),'count'=>count($events),'events'=>$events],JSON_PRETTY_PRINT);
$logFile=DATA_DIR.'/sync_log.json';
if(@file_put_contents($logFile,$logBody)===false){
  // data/ may not be writable by the web user — fall back to the system temp dir.
  $fallback=sys_get_temp_dir().'/edubridge_sync_log.json';
  if(@file_put_contents($fallback,$logBody)!==false){
    $errors[]='log:'.DATA_DIR.' not writable, using '.$fallback;
  }else{
    $errors[]='log:'.DATA_DIR.' not writable';
  }
}
api_out(['ok'=>true,'acked'=>count($events),'persisted'=>$persisted,
  'server_time'=>date('c')] + ($errors?['warnings'=>$errors]:[]));
