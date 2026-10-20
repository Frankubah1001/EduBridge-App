<?php // Migration: settings table + school/user/mentor verification + seeds. Run: php database/migrate_settings.php
require_once __DIR__.'/../config.php';
if(!db_ok()){ echo "DB offline\n"; exit(1); }
$db=db();
$col=function($t,$c,$ddl) use($db){
  $e=$db->query("SELECT COUNT(*) c FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='$t' AND COLUMN_NAME='$c'")->fetch()['c'];
  if(!$e){ $db->exec("ALTER TABLE $t ADD COLUMN $c $ddl"); echo "ADD $t.$c\n"; }
};
$db->exec("CREATE TABLE IF NOT EXISTS settings (k VARCHAR(80) PRIMARY KEY, v MEDIUMTEXT, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB");
$col('schools','status',"ENUM('pending','approved','suspended') DEFAULT 'approved'");
$col('schools','code_prefix',"VARCHAR(10) DEFAULT ''");
$col('users','school_code',"VARCHAR(30) DEFAULT ''");
$col('users','verified',"TINYINT(1) DEFAULT 1");
$col('users','mentor_subject_id',"INT DEFAULT NULL");
$col('users','mentor_bio',"VARCHAR(500) DEFAULT ''");
$col('mentors','user_id2',"INT DEFAULT NULL");
$col('mentors','verified',"TINYINT(1) DEFAULT 1");
$col('mentors','bio',"VARCHAR(500) DEFAULT ''");
$defaults=[
 'site_name'=>'EduBridge AI','tagline'=>'Learn anywhere. Even offline.',
 'features.quiz'=>'1','features.quiz_n'=>'10','features.quiz_minutes'=>'20',
 'features.diagnostic'=>'1','features.diagnostic_n'=>'6',
 'features.ai_tutor'=>'1','features.ai_daily_limit'=>'20',
 'features.mentor_chat'=>'1','features.forum'=>'1','features.leaderboard'=>'1',
 'features.challenge'=>'1','features.challenge_n'=>'5','features.challenge_xp'=>'30',
 'features.offline_bundles'=>'1','features.lowdata'=>'1',
 'features.certificates'=>'1','features.parent_sms'=>'0','features.payments'=>'1',
 'xp.lesson'=>'10','xp.quiz_high'=>'50','xp.quiz_mid'=>'25','xp.quiz_low'=>'10','xp.diagnostic'=>'20',
 'home.hero_badge'=>'WAEC · NECO · JAMB Ready','home.stat_questions'=>'500+','home.stat_subjects'=>'12','home.stat_offline'=>'100%',
 'auth.allow_student'=>'1','auth.allow_parent'=>'1','auth.allow_mentor'=>'1','auth.require_school'=>'0','auth.require_password'=>'0',
];
$ins=$db->prepare('INSERT IGNORE INTO settings(k,v) VALUES(?,?)');
foreach($defaults as $k=>$v) $ins->execute([$k,$v]);
$sch=[['Queens College, Yaba','QC-YABA','Lagos','Lagos',1],['Kings College, Lagos','KC-LAG','Lagos','Lagos',1],
 ['Barewa College, Zaria','BAR-ZAR','Kaduna','Kaduna',1],['Holy Child College, Lagos','HCC-LAG','Lagos','Lagos',1],
 ['CKC Onitsha','CKC-ONI','Anambra','Anambra',1],['Demo Community School','DEMO-001','FCT','Abuja',1]];
$si=$db->prepare("INSERT IGNORE INTO schools(name,code,address,state,status) VALUES(?,?,?,?,'approved')");
foreach($sch as $s) $si->execute([$s[0],$s[1],$s[0],$s[3]]);
echo "MIGRATE OK\n";
