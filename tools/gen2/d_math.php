<?php // GEN2 Math (numeric, unique stems) — emits full counts
function gen_math_full(){
 // A1 linear 1-step x-a=c (140)
 $m=0;$at=0;$want=140;while($m<$want&&$at<$want*600){$at++;$a=2+mt_rand(0,28);$c=5+mt_rand(0,115);$x=$c+$a;
  if(EM('Mathematics','Algebra',"If x − $a = $c, find x.",["".($x-2),"".($x-1),"".$x,"".($x+1)],2,"Add $a: x = $c + $a = $x."))$m++;}
 if($m<$want)echo "SHORT M|A1 $m/$want\n";
 // A2 factorise (140) — unique via shuffled distractors+rotation
 $m=0;$at=0;$want=140;$f=0;while($m<$want&&$at<$want*2000){$at++;$r1=1+mt_rand(0,14);$r2=1+mt_rand(0,14);if($r1>$r2){$t=$r1;$r1=$r2;$r2=$t;}$b=$r1+$r2;$cc=$r1*$r2;
  $cands=["(x-$r1)(x-$r2)","(x+$r1)(x-$r2)","(x-$r1)(x+$r2)","(x+$r2)(x+$r1)","(x+1)(x+$cc)","(x+$cc)(x+1)","(x+$b)(x+1)"];$cands=array_values(array_unique(array_filter($cands,fn($v)=>$v!="(x+$r1)(x+$r2)")));
  shuffle($cands);$d=array_slice($cands,0,3);$r=$at%4;$o=[];$o[$r]="(x+$r1)(x+$r2)";$j=0;for($i=0;$i<4;$i++){if($i==$r)continue;$o[$i]=$d[$j++];}
  if(EM('Mathematics','Algebra',"Factorise: x2 + {$b}x + $cc",$o,$r,"Numbers to $cc adding to $b: $r1 and $r2."))$m++;}
 if($m<$want)echo "SHORT M|A2 $m/$want\n";
 // A3 3x-7 full window q 2..200 (unique stems)
 $m=0;$at=0;$want=140;while($m<$want&&$at<$want*600){$at++;$q=2+mt_rand(0,198);$S=3*$q-7;
  $d=["".($q-1),"".($q+1),"".($q+2)];$rot=$at%3;$d=array_merge(array_slice($d,$rot),array_slice($d,0,$rot));
  [$o,$ans]=MK("".$q,$d,($at+$m)%4);
  if(EM('Mathematics','Algebra',"If 3x − 7 = $S, find x.",$o,$ans,"3x = ".($S+7).", x = $q."))$m++;}
 if($m<$want)echo "SHORT M|A3 $m/$want\n";
 // A4 GP (90) a 2..12 r 2..6 pw 2..5
 $m=0;$at=0;$want=90;while($m<$want&&$at<$want*800){$at++;$a=2+mt_rand(0,10);$r=2+mt_rand(0,4);$pw=2+mt_rand(0,3);$v=$a;for($k=1;$k<$pw;$k++)$v*=$r;$pu=$pw-1;
  if(EM('Mathematics','Algebra',"A GP: first term $a, ratio $r. Term $pw =",["".($a*$r),"".$v,"".($a+$r*$pu),"".($a*$r*2)],1,"T$pw = $a x $r^$pu = $v."))$m++;}
 if($m<$want)echo "SHORT M|A4 $m/$want\n";
 // A5 x+x0=y0 (90) windows
 $m=0;$at=0;$want=90;while($m<$want&&$at<$want*800){$at++;$x0=2+mt_rand(0,60);$y0=5+mt_rand(0,120);
  if(EM('Mathematics','Algebra',"Solve: x + $x0 = $y0.",["".($y0-$x0),"".($y0-$x0+1),"".($y0-$x0-1),"".($y0+$x0)],0,"x = $y0 − $x0 = ".($y0-$x0)."."))$m++;}
 if($m<$want)echo "SHORT M|A5 $m/$want\n";
 $m=0;$at=0;$want=90;while($m<$want&&$at<$want*400){$at++;$p1=1+mt_rand(0,4);$p2=2+mt_rand(0,5);$p3=1+mt_rand(0,6);$S=$p1+$p2+$p3;if(180%$S!=0)continue;$u=180/$S;$big=max($p1,$p2,$p3)*$u;
  if(EM('Mathematics','Geometry',"Angles of a triangle are in ratio $p1:$p2:$p3. Largest angle is:",["".($big)." deg","".($big-10)." deg","".($big+10)." deg","".($big-20)." deg"],0,"Unit = 180/$S = $u deg; largest = $big deg."))$m++;}
 if($m<$want)echo "SHORT Mathematics|Geometry $m/$want\n";
 $m=0;$at=0;$want=70;while($m<$want&&$at<$want*300){$at++;$l=1+($at%30);$w=1+mt_rand(0,14);$a=$l*$w;
  if(EM('Mathematics','Geometry',"Area of a $l cm by $w cm rectangle is:",["".$a." cm2","".($a+2)." cm2","".($l+$w)." cm2","".($a*2)." cm2"],0,"Area = $l x $w = $a cm2."))$m++;}
 if($m<$want)echo "SHORT Mathematics|Geometry2 $m/$want\n";
 $m=0;$at=0;$want=70;while($m<$want&&$at<$want*400){$at++;
  $a=1+($at%89);$b=90-$a; // distinct cofunction pairs 1..89
  $d=[$a.' deg','45 deg',($b+7).' deg',($a+11).' deg'];$d=array_values(array_filter($d,fn($v)=>$v!=$b.' deg'));
  $rot=$at%3;$d=array_merge(array_slice($d,$rot),array_slice($d,0,$rot));$d=array_slice($d,0,3);
  [$o,$ans]=MK($b.' deg',$d,($at+$m)%4);
  if(EM('Mathematics','Trigonometry',"sin $a deg = cos x deg (x acute). x =",[$o[0],$o[1],$o[2],$o[3]],$ans,"Cofunction: x = 90-$a = $b deg."))$m++;}
 if($m<$want)echo "SHORT Mathematics|Trig1 $m/$want\n";
 $m=0;$at=0;$want=70;while($m<$want&&$at<$want*400){$at++;$p=1+($at%24);$n=100+mt_rand(0,4900);$g=GCD($p,100);$pp=$p/$g;$qq=100/$g;$v=$n*$p/100;$ok=(abs($v-round($v))<1e-9);if(!$ok)continue;$v=(int)round($v);
  if(EM('Mathematics','Statistics',"$p% of $n equals:",["".$v,"".($v+10),"".($v*2),"".max(0,$v-5)],0,"$p% = $pp/$qq; $pp/$qq x $n = $v."))$m++;}
 if($m<$want)echo "SHORT Mathematics|Stats $m/$want\n";
 $m=0;$at=0;$want=70;while($m<$want&&$at<$want*400){$at++;$cp=100+mt_rand(0,900);$sp=$cp+10+mt_rand(0,200);$pf=$sp-$cp;$g=GCD($pf,$cp);$pc="$pf/$cp";
  if(EM('Mathematics','Statistics',"Bought at N$cp, sold at N$sp. Profit is:",["N".$pf,"N".($pf+10),"N".($pf*2),"N".max(0,$pf-5)],0,"Profit = $sp − $cp = N$pf ($pc of cost)."))$m++;}
 if($m<$want)echo "SHORT Mathematics|Stats2 $m/$want\n";
 $m=0;$at=0;$want=30;while($m<$want&&$at<$want*400){$at++;$p=2+mt_rand(0,7);
  if(EM('Mathematics','Calculus',"If y = x^$p, dy/dx is:",["{$p}x^".($p-1),"x^".($p-1),"{$p}x^$p","$p"],0,"Power rule: d/dx x^$p = $p x^".($p-1)."."))$m++;}
 if($m<$want)echo "SHORT Mathematics|Calculus $m/$want\n";
}
