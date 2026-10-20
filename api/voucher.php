<?php // Voucher redeem: code -> plan activation record
require_once __DIR__.'/../config.php';
header('Content-Type: application/json');
$in=json_decode(file_get_contents('php://input'),true)?:[];
$code=strtoupper(trim($in['code']??''));
try{
  if(!db_ok()) api_out(['ok'=>false,'error'=>'DB offline'],500);
  $st=db()->prepare("SELECT v.*, (SELECT name FROM plans p WHERE p.id=v.plan_id) AS plan FROM vouchers v WHERE code=? AND status='active' LIMIT 1");
  $st->execute([$code]); $v=$st->fetch();
  if(!$v) api_out(['ok'=>false,'error'=>'Invalid or expired code']);
  if($v['expires_at'] && $v['expires_at']<date('Y-m-d')) api_out(['ok'=>false,'error'=>'Code expired']);
  if($v['used']>=$v['max_uses']) api_out(['ok'=>false,'error'=>'Code fully used']);
  db()->prepare('UPDATE vouchers SET used=used+1 WHERE id=?')->execute([$v['id']]);
  audit(current_user()['id']??null,'redeem','voucher',$v['id'],$code);
  api_out(['ok'=>true,'plan'=>$v['plan']]);
}catch(Exception $e){ api_out(['ok'=>false,'error'=>$e->getMessage()],500); }
