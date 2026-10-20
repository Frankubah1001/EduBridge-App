<?php
// EduBridge Admin — standalone staff application (separate from the learner app).
// Routes: ?r=login | console | logout
require_once __DIR__.'/inc/boot.php';
require_once __DIR__.'/inc/auth.php';
$r=$_GET['r']??'console';
if($r==='logout'){ admin_logout(); header('Location: '.admin_url('login')); exit; }
if($r==='login'){
  if(admin_is_in()){ header('Location: '.admin_url('console')); exit; }
  $err=$_GET['e']??'';
  require __DIR__.'/pages/login.php'; exit;
}
// console
if(!admin_is_in()){ header('Location: '.admin_url('login')); exit; }
require __DIR__.'/pages/console.php';
