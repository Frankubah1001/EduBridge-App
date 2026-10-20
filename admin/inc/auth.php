<?php // Admin auth helpers: bcrypt staff login, lockout, audited. Session key = admin_user.
require_once __DIR__.'/boot.php';
function admin_attempt_login($email,$pass,$ip='cli'){
  $email=strtolower(trim($email)); $key='adm_fail_'.$ip;
  $fails=$_SESSION[$key]??['n'=>0,'until'=>0];
  if(time()<$fails['until']) return ['ok'=>false,'error'=>'Locked 5 min after 5 failed attempts.'];
  $row=null;
  try{
    if(!db_ok()) return ['ok'=>false,'error'=>'Database offline.'];
    $st=db()->prepare('SELECT u.*, r.slug AS role FROM users u JOIN roles r ON r.id=u.role_id WHERE u.email=? LIMIT 1');
    $st->execute([$email]); $row=$st->fetch();
  }catch(Exception $e){ return ['ok'=>false,'error'=>$e->getMessage()]; }
  $passOk=$row && !empty($row['password_hash']) && password_verify($pass,$row['password_hash']);
  $staff=$passOk && in_array($row['role'],['admin','teacher','school_admin']) && $row['status']=='active';
  audit($row?$row['id']:null, $staff?'admin_login':'admin_login_fail','user',$row?$row['id']:null,$email.' @'.$ip);
  if($staff){
    unset($_SESSION[$key]);
    $_SESSION['admin_user']=['id'=>$row['id'],'name'=>$row['name'],'email'=>$row['email'],'role'=>$row['role'],'is_admin'=>true,'login_at'=>time()];
    return ['ok'=>true];
  }
  $n=$fails['n']+1; $_SESSION[$key]=['n'=>$n,'until'=>$n>=5?time()+300:0];
  return ['ok'=>false,'error'=>'Invalid staff credentials. ('.$n.'/5)'];
}
function admin_logout(){ unset($_SESSION['admin_user']); }
