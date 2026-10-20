<?php require_once __DIR__.'/../config.php';
// Delta-sync receiver: accepts offline queue, returns authoritative ack
$in=json_decode(file_get_contents('php://input'),true)??[];
$events=$in['events']??[];
// In production: write to MySQL sync_queue + resolve conflicts by server timestamp.
file_put_contents(DATA_DIR.'/sync_log.json', json_encode(['at'=>date('c'),'count'=>count($events),'events'=>$events],JSON_PRETTY_PRINT));
api_out(['ok'=>true,'acked'=>count($events),'server_time'=>date('c')]);
