<?php // Admin API part 4: settings, schools, plans, vouchers, mentors, sessions, users
function admin_api_settings($a,$actor){
  if($a==='settings_get'){ admin_api_out(settings_all()); }
  if($a==='settings_save'){
    $pairs=json_decode($_POST['settings_json']??'[]',true)?:[];
    if(!is_array($pairs)||!$pairs) admin_api_out(['error'=>'No settings supplied'],400);
    $allow=['site_name','tagline','features.quiz','features.quiz_n','features.quiz_minutes','features.diagnostic','features.diagnostic_n','features.ai_tutor','features.ai_daily_limit','features.mentor_chat','features.forum','features.leaderboard','features.challenge','features.challenge_n','features.challenge_xp','features.offline_bundles','features.lowdata','features.certificates','features.parent_sms','features.payments','xp.lesson','xp.quiz_high','xp.quiz_mid','xp.quiz_low','xp.diagnostic','home.hero_badge','home.stat_questions','home.stat_subjects','home.stat_offline','auth.allow_student','auth.allow_parent','auth.allow_mentor','auth.require_school','auth.require_password'];
    $pairs=array_intersect_key($pairs,array_flip($allow));
    if(!$pairs) admin_api_out(['error'=>'No recognised settings supplied'],400);
    settings_save($pairs,$actor); admin_api_out(['ok'=>true,'saved'=>count($pairs)]);
  }
  return false;
}
function admin_api_schools($a,$actor){
  if($a==='school_list'){ admin_api_out(db()->query('SELECT *, (SELECT COUNT(*) FROM users u WHERE u.school_id=schools.id) AS members FROM schools ORDER BY name')->fetchAll()); }
  if($a==='save_school'){
    $id=(int)($_POST['id']??0);
    $nm=trim($_POST['name']??''); $code=strtoupper(trim($_POST['code']??''));
    if($nm===''||$code==='') admin_api_out(['error'=>'Name + code required'],400);
    $st=in_array($_POST['status']??'', ['pending','approved','suspended'])?$_POST['status']:'approved';
    if($id) db()->prepare('UPDATE schools SET name=?,code=?,state=?,contact_phone=?,status=? WHERE id=?')->execute([$nm,$code,$_POST['state']??'',$_POST['contact_phone']??'',$st,$id]);
    else { db()->prepare("INSERT INTO schools(name,code,state,contact_phone,status) VALUES(?,?,?,?,?)")->execute([$nm,$code,$_POST['state']??'',$_POST['contact_phone']??'',$st]); $id=db()->lastInsertId(); }
    audit($actor,'save','school',$id,$nm); admin_api_out(['ok'=>true,'id'=>$id]);
  }
  if($a==='school_status'){ $id=(int)($_POST['id']??0); if(!$id) admin_api_out(['error'=>'Missing id'],400);
    $st=in_array($_POST['status']??'', ['pending','approved','suspended'])?$_POST['status']:'approved';
    db()->prepare('UPDATE schools SET status=? WHERE id=?')->execute([$st,$id]);
    audit($actor,'status','school',$id,$st); admin_api_out(['ok'=>true]); }
  if($a==='plan_list'){ admin_api_out(db()->query('SELECT * FROM plans ORDER BY price_kobo')->fetchAll()); }
  if($a==='save_plan'){
    $id=(int)($_POST['id']??0); $slug=trim($_POST['slug']??''); $nm=trim($_POST['name']??'');
    if($slug===''||$nm==='') admin_api_out(['error'=>'Slug + name required'],400);
    $f=[$slug,$nm,(int)($_POST['price_kobo']??0),$_POST['term']??'term',$_POST['features']??''];
    if($id) db()->prepare('UPDATE plans SET slug=?,name=?,price_kobo=?,term=?,features=? WHERE id=?')->execute([...$f,$id]);
    else { db()->prepare('INSERT INTO plans(slug,name,price_kobo,term,features) VALUES(?,?,?,?,?)')->execute($f); $id=db()->lastInsertId(); }
    audit($actor,'save','plan',$id,$nm); admin_api_out(['ok'=>true,'id'=>$id]);
  }
  if($a==='voucher_list'){ admin_api_out(db()->query("SELECT v.*, (SELECT name FROM plans p WHERE p.id=v.plan_id) AS plan FROM vouchers v ORDER BY v.id DESC LIMIT 50")->fetchAll()); }
  if($a==='save_voucher'){
    $code=strtoupper(trim($_POST['code']??'')); if($code==='') admin_api_out(['error'=>'Code required'],400);
    if(!(int)($_POST['plan_id']??0)) admin_api_out(['error'=>'Plan required'],400);
    $ex=db()->query('SELECT id FROM vouchers WHERE code='.db()->quote($code))->fetchColumn();
    if($ex) admin_api_out(['error'=>'Code already exists'],400);
    db()->prepare('INSERT INTO vouchers(code,plan_id,max_uses,status) VALUES(?,?,?,?)')->execute([$code,(int)$_POST['plan_id'],(int)($_POST['max_uses']??100),'active']);
    audit($actor,'create','voucher',db()->lastInsertId(),$code); admin_api_out(['ok'=>true]);
  }
  if($a==='voucher_status'){ $id=(int)($_POST['id']??0); if(!$id) admin_api_out(['error'=>'Missing id'],400);
    $st=in_array($_POST['status']??'', ['active','expired','disabled'])?$_POST['status']:'active';
    db()->prepare('UPDATE vouchers SET status=? WHERE id=?')->execute([$st,$id]);
    audit($actor,'status','voucher',$id,$st); admin_api_out(['ok'=>true]); }
  return false;
}
