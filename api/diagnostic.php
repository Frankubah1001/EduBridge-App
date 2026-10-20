<?php require_once __DIR__.'/../config.php';
// Diagnostic: accept attempt, recompute simple theta server-side (mirrors IRT)
$in=json_decode(file_get_contents('php://input'),true)??[];
$responses=$in['responses']??[]; $theta=0.0;
foreach($responses as $r){ $a=$r['a']??1.0; $b=$r['b']??0.0; $c=$r['correct']?1:0;
  $p=1/(1+exp(-$a*($theta-$b))); $theta+=0.5*$a*($c-$p); $theta=max(-3,min(3,$theta)); }
$label=$theta<-1?'Beginner':($theta<0?'Developing':($theta<1?'Proficient':'Mastery'));
$weak=[]; foreach($responses as $r){ if(!$r['correct']) $weak[]=$r['topic']??'General'; }
api_out(['theta'=>round($theta,2),'label'=>$label,'weak_topics'=>array_values(array_unique($weak)),
 'recommendation'=>$theta<0?'Revisit fundamentals + 15 min daily drills':'Attempt timed WAEC mocks + past questions']);
