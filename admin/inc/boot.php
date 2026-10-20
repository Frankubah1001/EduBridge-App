<?php
// Admin bootstrap: separate session namespace, own auth, shares the app's DB layer.
// ADMIN_ROOT = <app>/admin ; APP_ROOT = <app>
define('ADMIN_ROOT', dirname(__DIR__));
define('APP_ROOT', dirname(ADMIN_ROOT));
if(session_status()===PHP_SESSION_NONE) session_start();
require_once APP_ROOT.'/includes/db.php';
function admin_user(){ return $_SESSION['admin_user'] ?? null; }
function admin_is_in(){ $u=admin_user(); return $u && ($u['is_admin']??false) && in_array($u['role']??'', ['admin','teacher','school_admin']); }
// Absolute URL path to a route in the admin app. Works whether the caller is
// admin/index.php, admin/inc/api.php, or a legacy shim like api/admin_auth.php.
// The admin app always lives in the "admin" folder next to the app root.
function admin_url($route='console'){
  $s=str_replace('\\','/',$_SERVER['SCRIPT_NAME']??'');
  $d=rtrim(dirname($s),'/');
  if(basename($d)==='inc') $d=dirname($d);            // admin/inc/*  -> admin
  elseif(basename($d)==='api') $d=dirname($d).'/admin'; // api/*     -> app/admin
  elseif(basename($d)!=='admin') $d=$d.'/admin';        // app root  -> app/admin
  return $d.'/index.php?r='.rawurlencode($route);
}
function admin_require(){ if(!admin_is_in()){ header('Location: '.admin_url('login')); exit; } return admin_user(); }
function admin_api_out($d,$c=200){ http_response_code($c); header('Content-Type: application/json'); echo json_encode($d); exit; }
