<?php // Public registration: student | parent | mentor — school-linked, saved to DB
require_once __DIR__.'/../config.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){ header('Location: ../?p=register'); exit; }
$role=$_POST['role']??'student';
if(!in_array($role,['student','parent','mentor'])) $role='student';
$allow=['student'=>setting_on('auth.allow_student',true),'parent'=>setting_on('auth.allow_parent',true),'mentor'=>setting_on('auth.allow_mentor',true)];
if(!$allow[$role]){ header('Location: ../?p=register&role='.$role.'&e='.urlencode('Registration closed for '.$role)); exit; }
$name=trim($_POST['name']??''); $email=strtolower(trim($_POST['email']??''));
$phone=trim($_POST['phone']??''); $pass=$_POST['password']??'';
$sid=($_POST['school_id']??'')!==''?(int)$_POST['school_id']:null;
$scode=strtoupper(trim($_POST['school_code']??''));
$reqSchool=setting_on('auth.require_school',false); $reqPass=setting_on('auth.require_password',false);
if($name===''||!filter_var($email,FILTER_VALIDATE_EMAIL)){ header('Location: ../?p=register&role='.$role.'&e='.urlencode('Valid name + email required')); exit; }
if($reqSchool&&!$sid){ header('Location: ../?p=register&role='.$role.'&e='.urlencode('Please select your school')); exit; }
if($reqPass&&strlen($pass)<4){ header('Location: ../?p=register&role='.$role.'&e='.urlencode('Password required (4+ chars)')); exit; }
try{
  if(!db_ok()) throw new Exception('Database offline — try again.');
  // validate school + code
  if($sid){ $sch=db()->query('SELECT * FROM schools WHERE id='.$sid." AND status='approved'")->fetch();
    if(!$sch) throw new Exception('Unknown school.');
    if($scode!=='' && $sch['code']!==$scode) throw new Exception('School code does not match '.$sch['name'].'.');
  }
  $rid=db()->query('SELECT id FROM roles WHERE slug='.db()->quote($role))->fetchColumn();
  $exists=db()->query('SELECT id FROM users WHERE email='.db()->quote($email))->fetchColumn();
  if($exists) throw new Exception('Email already registered — please log in.');
  $st=db()->prepare('INSERT INTO users(name,email,phone,password_hash,role_id,class_level,school_id,school_code,verified) VALUES(?,?,?,?,?,?,?,?,?)');
  $st->execute([$name,$email,$phone,$pass?password_hash($pass,PASSWORD_DEFAULT):'', $rid, $_POST['class_level']??'', $sid, $scode, $role=='mentor'?0:1]);
  $uid=db()->lastInsertId();
  db()->prepare('INSERT INTO consents(user_id,type,granted) VALUES(?,\'ndpr\',1)')->execute([$uid]);
  if($role=='parent' && !empty($_POST['child_email'])){
    $cid=db()->query('SELECT id FROM users WHERE email='.db()->quote(strtolower(trim($_POST['child_email']))))->fetchColumn();
    if($cid) db()->prepare('INSERT IGNORE INTO guardians(parent_user_id,student_user_id) VALUES(?,?)')->execute([$uid,$cid]);
  }
  if($role=='mentor'){
    $msid=$_POST['mentor_subject']!==''?(int)$_POST['mentor_subject']:null;
    db()->prepare('INSERT INTO mentors(user_id,name,subject_id,school,rating,status,verified,bio) VALUES(?,?,?,?,?, ?,0,?)')
      ->execute([$uid,$name,$msid,($sid?db()->query('SELECT name FROM schools WHERE id='.$sid)->fetchColumn():''),5.0,'offline',trim($_POST['mentor_bio']??'')]);
  }
  audit($uid,'register',$role,$uid,$email);
  // auto-login
  $_SESSION['user']=['id'=>$uid,'name'=>$name,'email'=>$email,'role'=>$role,'is_admin'=>false,'school_id'=>$sid,'login_at'=>time()];
  $dest=$role=='mentor'?'mentor':($role=='parent'?'parent':'dashboard');
  header('Location: ../?p='.$dest.'&welcome=1'); exit;
}catch(Exception $e){ header('Location: ../?p=register&role='.$role.'&e='.urlencode($e->getMessage())); exit; }
