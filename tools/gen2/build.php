<?php // GEN2 build driver: emit all, slice per syllabus map, validate, write
require __DIR__.'/core.php'; require __DIR__.'/d_eng.php'; require __DIR__.'/d_math.php';
require __DIR__.'/d_phychem.php'; require __DIR__.'/d_biosoc.php'; require __DIR__.'/d_extra.php';
$mode=$argv[1]??'clean500';
gen_eng(); gen_math_full(); gen_phy_full(); gen_chem_full(); gen_bio_full(); gen_soc_full();
gen_trig_extra(); gen_calc_extra(); gen_oral();
global $B;
$map=json_decode(file_get_contents(__DIR__.'/../../data/syllabus_map.json'),true)[$mode];
$groups=[]; foreach($B as $q){ $groups[$q['subject'].'|'.$q['topic']][]=$q; }
$out=[]; $short=[];
foreach($map as $kt=>$need){ $have=$groups[$kt]??[]; $h=count($have);
 if($h<$need){ $short[]="$kt $h/$need"; $take=$have; }
 else { $take=[]; for($i=0;$i<$need;$i++) $take[]=$have[(int)floor($i*$h/$need)]; }
 foreach($take as $q) $out[]=$q;
}
usort($out,function($a,$b){ $c=strcmp($a['subject'],$b['subject']); return $c?:strcmp($a['topic'],$b['topic']); });
$id=1; foreach($out as &$q){ $q['id']=$id; $q['exam']=['WAEC','WAEC','NECO','JAMB'][$id%4]; $q['year']=2015+($id%10); $id++; }
$bad=0;$stems=[];
foreach($out as $q){ if(count($q['options'])!=4||count(array_unique($q['options']))!=4||!isset($q['options'][$q['answer']]))$bad++;
 $kk=trim(preg_replace('/\s+/',' ',$q['stem'])).'||'.implode('~',$q['options']); if(isset($stems[$kk]))$bad++; $stems[$kk]=1; }
$name=$mode=='clean500'?'questions.json':'questions_full.json';
file_put_contents(__DIR__.'/../../data/'.$name,json_encode(array_values($out),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
echo strtoupper($mode)." TOTAL=".count($out)." BAD=$bad".PHP_EOL;
echo "NEED=".array_sum($map).PHP_EOL;
if($short) echo "SHORT:\n".implode("\n",$short)."\n";
$s=[];foreach($out as $q){$s[$q['subject']]=($s[$q['subject']]??0)+1;}foreach($s as $k=>$v)echo "$k=$v ";
echo PHP_EOL;
