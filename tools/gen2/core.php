<?php // GEN2 core: helpers + driver (deterministic seed)
mt_srand(20260613);
$B=[];$ID=1;$SEEN=[];$N=0;
function EX($i){$e=['WAEC','WAEC','NECO','JAMB'];return $e[$i%4];}
function YR($i){return 2015+($i%10);}
function EM($s,$t,$stem,$o,$a,$x,$d=null){
 global $B,$ID,$SEEN,$N;
 $o=array_values(array_map('strval',$o));
 if(count($o)!=4||count(array_unique($o))!=4||!isset($o[$a]))return false;
 $k=trim(preg_replace('/\s+/',' ',$stem)).'||'.implode('~',$o);
 if(isset($SEEN[$k]))return false;
 $SEEN[$k]=1;
 if($d===null)$d=round(mt_rand(-80,110)/100,2);
 $B[]=['id'=>$ID++,'subject'=>$s,'topic'=>$t,'exam'=>EX($N),'year'=>YR($N),'stem'=>$stem,'options'=>$o,'answer'=>$a,'difficulty'=>$d,'discrimination'=>round(mt_rand(70,140)/100,2),'explanation'=>$x];
 $N++;return true;
}
function MK($c,$d,$r){$d=array_values($d);$o=array_fill(0,4,'');$o[$r%4]=strval($c);$j=0;for($i=0;$i<4;$i++){if($i==$r%4)continue;$o[$i]=strval($d[$j++]??('opt'.$i));}return [$o,$r%4];}
function ROWS($s){return array_values(array_filter(array_map('trim',explode(';',$s))));}
function POOL3($s,$t,$stems,$rows,$n,$scol,$acol,$dcol){
 $ST=is_array($stems)?$stems:[$stems];
 if(count($ST)<2)$ST[]='What is correct about "{S}"?';
 $R=array_map(fn($r)=>explode('|',$r),ROWS($rows));$m=count($R);$md=0;$at=0;$i=0;$ns=count($ST);
 while($md<$n&&$at<$n*4000){$at++;$ri=($at*7+3)%$m;$slot=$at%4;$si=$at%$ns;
  $C=$R[$ri][$acol]??null;if($C===null||$C==='')continue;
  if($dcol==='ROW'){$tmp=[];foreach($R[$ri] as $k=>$v){if($k!==$acol&&$k!==$scol&&$v!=='')$tmp[]=$v;}$d=array_values(array_unique($tmp));}
  else{$d=[];foreach($R as $j=>$rr){if($j!==$ri&&isset($rr[$dcol])&&$rr[$dcol]!==''&&$rr[$dcol]!==$C)$d[]=$rr[$dcol];}$d=array_values(array_unique($d));}
  if(count($d)<4)continue;$off=$slot%count($d);$d=array_merge(array_slice($d,$off),array_slice($d,0,$off));$d=array_slice($d,0,3);
  [$o,$a]=MK($C,$d,($at+$md)%4);
  $st=$ST[$si];foreach($R[$ri] as $k=>$v)$st=str_replace('{'.$k.'}',$v,$st);
  $st=str_replace('{S}',$R[$ri][$scol]??'',$st);
  if(EM($s,$t,$st,$o,$a,'Recall: '.implode(' / ',$R[$ri]).'.'))$md++;
 }
 if($md<$n)echo "SHORT $s|$t $md/$n\n";
}
function OPT4($s,$t,$stems,$rows,$n){
 $ST=is_array($stems)?$stems:[$stems];
 if(count($ST)<2)$ST[]='Which is correct about "{M}"?';
 $R=array_map(fn($r)=>explode('|',$r),ROWS($rows));$m=count($R);$md=0;$at=0;$i=0;
 while($md<$n&&$at<$n*4000){$at++;$ri=($at*7+3)%$m;$slot=$at%4;$rot=$at%3;$si=$at%count($ST);$r=$R[$ri];
  if(count($r)<5)continue;[$M,$C,$d1,$d2,$d3]=$r;
  $ord=[$d1,$d2,$d3];$ord=array_merge(array_slice($ord,$rot),array_slice($ord,0,$rot));
  [$o,$a]=MK($C,$ord,($at+$md)%4);
  $st=$ST[$si];foreach($r as $k=>$v)$st=str_replace('{'.$k.'}',$v,$st);
  $st=str_replace('{M}',$M,$st);
  if(EM($s,$t,$st,$o,$a,'Recall: '.$M.' — '.$C.'.'))$md++;
 }
 if($md<$n)echo "SHORT $s|$t $md/$n\n";
}
function FIX($s,$t,$stemT,$stims,$opts,$ans,$expT,$n){
 $S=ROWS($stims);$m=count($S);$md=0;$at=0;$i=0;
 while($md<$n&&$at<$n*4000){$at++;$stim=$S[($i*11+5)%$m];$i++;
  $stem=str_replace('{S}',$stim,$stemT);$exp=str_replace('{S}',$stim,$expT);
  if(EM($s,$t,$stem,$opts,$ans,$exp))$md++;
 }
 if($md<$n)echo "SHORT $s|$t $md/$n\n";
}
function GCD($a,$b){$a=abs($a);$b=abs($b);while($b){$t=$a%$b;$a=$b;$b=$t;}return $a?:1;}
