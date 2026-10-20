<?php // Admin login endpoint (POST). Staff-only, bcrypt, lockout, audited.
require_once __DIR__.'/inc/boot.php';
require_once __DIR__.'/inc/auth.php';
$action=$_GET['action']??'login';
if($action==='logout'){ admin_logout(); header('Location: '.admin_url('login')); exit; }
if($_SERVER['REQUEST_METHOD']!=='POST'){ header('Location: '.admin_url('login')); exit; }
$ip=$_SERVER['REMOTE_ADDR']??'cli';
$r=admin_attempt_login($_POST['email']??'', $_POST['password']??'', $ip);
if($r['ok']){ header('Location: '.admin_url('console')); exit; }
header('Location: '.admin_url('login').'&e='.urlencode($r['error']??'Login failed'));
