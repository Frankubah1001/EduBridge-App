<?php // Admin app data repair: Medium plan, plan features, starter voucher, stray column cleanup.
// Run: php database/migrate_admin_seed.php
require_once __DIR__.'/../config.php';
if(!db_ok()){ echo "DB offline\n"; exit(1); }
$db=db();

// 1. drop the stray mentors.user_id2 column (created by an earlier migration, never used)
$c=(int)$db->query("SELECT COUNT(*) c FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='mentors' AND COLUMN_NAME='user_id2'")->fetch()['c'];
if($c){ $db->exec("ALTER TABLE mentors DROP COLUMN user_id2"); echo "DROP mentors.user_id2\n"; }

// 2. plans: Free / Medium / Pro / School with real feature copy
$features=[
 'free'=>['Free',0,'forever','Courses · CBT mocks · AI tutor (daily limit) · Offline bundles'],
 'medium'=>['Medium',50000,'term','Courses · CBT mocks · 60 AI messages/day · Study planner · Forum'],
 'pro'=>['Pro',150000,'term','Unlimited AI · Adaptive plans · Verified mentors · Certificates · Parent SMS · No ads'],
 'school'=>['School',0,'custom','Teacher studio · Class analytics · Bulk vouchers · Scholarships'],
];
$up=$db->prepare('UPDATE plans SET name=?,price_kobo=?,term=?,features=? WHERE slug=?');
foreach($features as $slug=>$f){ $up->execute([$f[0],$f[1],$f[2],$f[3],$slug]); }
if(!$db->query("SELECT id FROM plans WHERE slug='medium'")->fetchColumn()){
  $db->prepare('INSERT INTO plans(slug,name,price_kobo,term,features) VALUES(?,?,?,?,?)')
    ->execute(['medium','Medium',50000,'term','Courses · CBT mocks · 60 AI messages/day · Study planner · Forum']);
  echo "INSERT plans.medium\n";
}

// 3. starter vouchers so redemption is testable (one per paid plan)
$pro=$db->query("SELECT id FROM plans WHERE slug='pro'")->fetchColumn();
$med=$db->query("SELECT id FROM plans WHERE slug='medium'")->fetchColumn();
$vs=[['SCHOLAR-2026',$pro,50],['EDUBRIDGE-MEDIUM-2026',$med,100]];
$ins=$db->prepare("INSERT INTO vouchers(code,plan_id,max_uses,status) VALUES(?,?,?,'active')");
foreach($vs as $v){
  if(!$v[1]) continue;
  if(!$db->query('SELECT id FROM vouchers WHERE code='.$db->quote($v[0]))->fetchColumn()){ $ins->execute($v); echo "INSERT voucher {$v[0]}\n"; }
}

// 4. remove leftover test account from earlier auditing
$tid=$db->query("SELECT id FROM users WHERE email='teststu002@edubridge.ng'")->fetchColumn();
if($tid){
  $db->prepare('DELETE FROM consents WHERE user_id=?')->execute([$tid]);
  $db->prepare('DELETE FROM enrollments WHERE user_id=?')->execute([$tid]);
  $db->prepare('DELETE FROM attempts WHERE user_id=?')->execute([$tid]);
  $db->prepare('DELETE FROM users WHERE id=?')->execute([$tid]);
  echo "DELETE test user $tid\n";
}
echo "ADMIN SEED OK\n";
