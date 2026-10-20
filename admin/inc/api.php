<?php // Admin API dispatcher: own session gate, routes action -> handler module
require_once __DIR__.'/boot.php';
function need_admin(){
  $u=admin_user();
  if(!$u || !($u['is_admin']??false) || !in_array($u['role']??'', ['admin','teacher','school_admin']))
    admin_api_out(['error'=>'Staff login required.'],403);
  if(time()-($u['login_at']??0)>7200){ unset($_SESSION['admin_user']); admin_api_out(['error'=>'Session expired. Sign in again.'],401); }
  return $u;
}
function slugify($s){ return strtolower(trim(preg_replace('/[^a-z0-9]+/i','-',$s),'-')); }
require_once __DIR__.'/api1.php';
require_once __DIR__.'/api2.php';
require_once __DIR__.'/api3.php';
require_once __DIR__.'/api4.php';
require_once __DIR__.'/api5.php';
try{
  $u=need_admin();
  if(!db_ok()) admin_api_out(['error'=>'Database offline'],500);
  $actor=1;
  if(!empty($u['email'])){ $r=db()->query('SELECT id FROM users WHERE email='.db()->quote($u['email']).' LIMIT 1')->fetchColumn(); if($r)$actor=(int)$r; }
  $a=$_GET['action']??$_POST['action']??'stats';
  // Each handler exits via admin_api_out() when it owns the action; a handler that
  // returns false simply did not match, so we keep looking.
  foreach(['admin_api_content','admin_api_questions','admin_api_papers','admin_api_settings','admin_api_schools','admin_api_people'] as $fn)
    $fn($a,$actor);
  admin_api_out(['error'=>'Unknown action: '.$a],400);
}catch(Exception $e){ admin_api_out(['error'=>$e->getMessage()],500); }
