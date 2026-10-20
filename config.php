<?php
// EduBridge config — brand, paths, helpers
define('EDU_NAME','EduBridge AI');
define('EDU_TAGLINE','Learn anywhere. Even offline.');
define('BRAND', [
  'navy'=>'#0A2540','emerald'=>'#00A86B','amber'=>'#FFC107',
  'bg'=>'#F4F6F9','ink'=>'#1E293B','card'=>'#FFFFFF'
]);
define('DATA_DIR', __DIR__.'/data');
if(session_status()===PHP_SESSION_NONE) session_start();
require_once __DIR__.'/includes/db.php';
function json_data($file){ $p=DATA_DIR."/$file"; return file_exists($p)?json_decode(file_get_contents($p),true):[]; }
function api_out($data,$code=200){ http_response_code($code); header('Content-Type: application/json'); echo json_encode($data); exit; }
function current_user(){ return $_SESSION['user'] ?? null; }
function is_admin(){ $u=current_user(); return $u && ($u['is_admin']??false) && in_array($u['role']??'', ['admin','teacher','school_admin']); }
function require_admin(){ $u=current_user(); if(!is_admin()){ header('Location: ?p=admin-login'); exit; } return $u; }
