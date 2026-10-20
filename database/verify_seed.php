<?php // Verify live DB matches the JSON seed content (questions per subject, courses + lessons).
require_once __DIR__.'/../config.php';
$db=db();
$D=__DIR__.'/../data';
$J=fn($f)=>json_decode(file_get_contents("$D/$f"),true)?:[];
$bad=0;
$q=$J('questions.json');
$bySub=[]; foreach($q as $x){ $bySub[$x['subject']]=($bySub[$x['subject']]??0)+1; }
$dbRows=$db->query("SELECT s.name, COUNT(q.id) c FROM subjects s JOIN questions q ON q.subject_id=s.id GROUP BY s.name")->fetchAll();
$dbBy=[]; foreach($dbRows as $r) $dbBy[$r['name']]=(int)$r['c'];
foreach($bySub as $s=>$c){ $d=$dbBy[$s]??0; if($c!==$d){ echo "QUESTION MISMATCH $s: json=$c db=$d\n"; $bad++; } }
echo $bad? "QUESTION MISMATCHES: $bad\n" : "ALL QUESTION COUNTS MATCH SEED\n";
foreach($J('courses.json') as $c){
  $cid=$db->query('SELECT id FROM courses WHERE code='.$db->quote($c['code']))->fetchColumn();
  if(!$cid){ echo "COURSE MISSING {$c['code']}\n"; $bad++; continue; }
  $n=(int)$db->query('SELECT COUNT(*) c FROM lessons WHERE course_id='.(int)$cid)->fetch()['c'];
  $want=count($c['lessons']??[]);
  if($n!==$want){ echo "COURSE MISMATCH {$c['code']}: lessons db=$n want=$want\n"; $bad++; }
}
echo $bad? "DATA DRIFT: $bad issue(s)\n" : "COURSES+LESSONS MATCH SEED\n";
// integrity checks
foreach([
  ['questions with no subject','SELECT COUNT(*) c FROM questions WHERE subject_id NOT IN (SELECT id FROM subjects)'],
  ['options with no question','SELECT COUNT(*) c FROM options WHERE question_id NOT IN (SELECT id FROM questions)'],
  ['topics with no subject','SELECT COUNT(*) c FROM topics WHERE subject_id NOT IN (SELECT id FROM subjects)'],
  ['lessons with no course','SELECT COUNT(*) c FROM lessons WHERE course_id NOT IN (SELECT id FROM courses)'],
  ['paper_items with no paper','SELECT COUNT(*) c FROM paper_items WHERE paper_id NOT IN (SELECT id FROM papers)'],
  ['paper_items with no question','SELECT COUNT(*) c FROM paper_items WHERE question_id NOT IN (SELECT id FROM questions)'],
  ['blank subject names','SELECT COUNT(*) c FROM subjects WHERE name IS NULL OR name=""'],
  ['vouchers with no plan','SELECT COUNT(*) c FROM vouchers WHERE plan_id NOT IN (SELECT id FROM plans)'],
] as $lbl){
  $n=(int)$db->query($lbl[1])->fetch()['c'];
  echo ($n?"ORPHAN: ":"ok: ").str_pad($lbl[0],34)." = $n\n";
  if($n) $bad++;
}
echo "\n".($bad? "VERIFY: $bad issue(s)\n" : "VERIFY: ALL CLEAN\n");
