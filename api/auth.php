<?php require_once __DIR__.'/../config.php';
$action=$_GET['action']??'login';
if($action==='logout'){ unset($_SESSION['user']); header('Location: ../?p=home'); exit; }
if($_SERVER['REQUEST_METHOD']==='POST' && ($action==='login'||$action==='register_legacy')){
  // LEGACY demo login kept for backwards-compat; new users should use ?p=register + api/register.php
  $name=trim($_POST['name']??'Guest Learner'); $email=strtolower(trim($_POST['email']??'guest@edubridge.ng'));
  $role=$_POST['role']??'student';
  if(!in_array($role,['student','parent','teacher'])) $role='student';
  $uid=null; $sid=null;
  try{ if(db_ok()){
    $row=db()->query('SELECT u.*, r.slug AS role FROM users u JOIN roles r ON r.id=u.role_id WHERE u.email='.db()->quote($email).' LIMIT 1')->fetch();
    if($row){ $uid=$row['id']; $sid=$row['school_id']; $name=$row['name']; $role=$row['role']=='mentor'?'teacher':$row['role']; }
  }}catch(Exception $e){}
  $_SESSION['user']=['id'=>$uid,'name'=>$name,'email'=>$email,'role'=>$role,'is_admin'=>false,'school_id'=>$sid,'login_at'=>time()];
  header('Location: ../?p=dashboard'); exit;
}
// Password login for registered users (email + password)
if($_SERVER['REQUEST_METHOD']==='POST' && $action==='password'){
  $email=strtolower(trim($_POST['email']??'')); $pass=$_POST['password']??'';
  try{
    $row=db()->query('SELECT u.*, r.slug AS role FROM users u JOIN roles r ON r.id=u.role_id WHERE u.email='.db()->quote($email).' LIMIT 1')->fetch();
    if($row && $row['password_hash'] && password_verify($pass,$row['password_hash']) && $row['status']=='active' && in_array($row['role'],['student','parent','mentor'])){
      $_SESSION['user']=['id'=>$row['id'],'name'=>$row['name'],'email'=>$row['email'],'role'=>$row['role'],'is_admin'=>false,'school_id'=>$row['school_id'],'login_at'=>time()];
      audit($row['id'],'login',$row['role'],$row['id'],$email);
      header('Location: ../?p=dashboard'); exit;
    }
    header('Location: ../?p=login&e='.urlencode('Invalid email/password — or register first.')); exit;
  }catch(Exception $e){ header('Location: ../?p=login&e='.urlencode($e->getMessage())); exit; }
}
