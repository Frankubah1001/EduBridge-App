<?php
$schools=db_schools(true); $levels=db_levels();
$role=$_GET['role']??'student';
if(!in_array($role,['student','parent','mentor'])) $role='student';
$allow=['student'=>setting_on('auth.allow_student',true),'parent'=>setting_on('auth.allow_parent',true),'mentor'=>setting_on('auth.allow_mentor',true)];
$reqSchool=setting_on('auth.require_school',false); $reqPass=setting_on('auth.require_password',false);
?>
<div class="grid g2"><div class="card"><h2>📝 Register — <?=ucfirst($role)?></h2>
<div class="tabs"><a class="tab <?= $role=='student'?'active':'' ?>" href="?p=register&role=student">Student</a>
<a class="tab <?= $role=='parent'?'active':'' ?>" href="?p=register&role=parent">Parent</a>
<a class="tab <?= $role=='mentor'?'active':'' ?>" href="?p=register&role=mentor">Mentor</a></div>
<?php if(!$allow[$role]): ?><div class="opt wrong">🚫 <?=ucfirst($role)?> registration is currently closed by admin.</div><?php else: ?>
<?php if(!empty($_GET['e'])): ?><div class="opt wrong"><?=htmlspecialchars($_GET['e'])?></div><?php endif; ?>
<?php if(!empty($_GET['ok'])): ?><div class="opt correct">✅ Registered! Now <a href="?p=login">log in →</a></div><?php endif; ?>
<form method="POST" action="api/register.php">
<input type="hidden" name="role" value="<?=$role?>">
<label>Full name</label><input name="name" required placeholder="e.g. Adaeze Ngozi">
<label>Email</label><input name="email" type="email" required>
<div style="display:flex;gap:8px"><div style="flex:1"><label>Phone</label><input name="phone" placeholder="080..."></div>
<div style="flex:1"><label>Password <?= $reqPass?'(required)':'(optional)' ?></label><input name="password" type="password" <?= $reqPass?'required':'' ?>></div></div>
<label>🏫 School <?= $reqSchool?'(required)':'' ?></label>
<select name="school_id" <?= $reqSchool?'required':'' ?>><option value="">— Select your school —</option><?= dd_options($schools,'id','name') ?></select>
<p class="muted" style="font-size:.82rem">School missing? Ask your school admin to add it, or pick the nearest. Code: <?=htmlspecialchars($_GET['code']??'optional school code')?></p>
<label>School code (if given)</label><input name="school_code" placeholder="e.g. QC-YABA">
<?php if($role=='student'): ?><label>Class level</label><select name="class_level"><?= dd_options(array_map(fn($l)=>['name'=>$l['name'],'slug'=>$l['slug']],$levels),'slug','name','sss') ?></select><?php endif; ?>
<?php if($role=='parent'): ?><label>Child's email (link account)</label><input name="child_email" placeholder="child email used at registration"><?php endif; ?>
<?php if($role=='mentor'): $subs=db_subjects_all(); ?><label>Subject specialty</label><select name="mentor_subject"><?= dd_options(array_map(fn($s)=>['id'=>$s['id'],'name'=>$s['name'].' ('.$s['level_name'].')'],$subs),'id','name') ?></select>
<label>Short bio</label><textarea name="mentor_bio" rows="2" placeholder="School, experience…"></textarea><?php endif; ?>
<label class="muted" style="font-weight:400"><input type="checkbox" required style="width:auto"> I consent to NDPR data processing</label><br><br>
<button class="btn btn-primary" style="width:100%">Register as <?=ucfirst($role)?> →</button></form><?php endif; ?>
<p class="muted" style="font-size:.82rem">Already have an account? <a href="?p=login">Log in →</a></p></div>
<div class="card" style="background:var(--navy);color:#fff;border:0"><h3 style="color:#FFC107">🏫 School-linked accounts</h3>
<ul style="line-height:2"><li>Pick your school — feeds your school leaderboard</li><li>Parents link to a child's email</li><li>Mentors choose a subject + get verified by admin</li><li>Everything saves to the database</li></ul></div></div>
