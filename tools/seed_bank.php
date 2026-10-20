<?php // EduBridge 500-question seeder — deterministic, realistic WAEC/NECO/JAMB style
mt_srand(2026);
$bank=[]; $id=1;
function Q(&$b,&$id,$s,$t,$e,$y,$stem,$opts,$ans,$exp){
  $b[]= ['id'=>$id++,'subject'=>$s,'topic'=>$t,'exam'=>$e,'year'=>$y,'stem'=>$stem,'options'=>$opts,'answer'=>$ans,
    'difficulty'=>round(mt_rand(-90,120)/100,2),'discrimination'=>round(mt_rand(70,140)/100,2),'explanation'=>$exp];
}
function pickExam($i){ $e=['WAEC','WAEC','NECO','JAMB']; return $e[$i%4]; }
function pickYear($i){ return 2015+($i%10); }
// ---------- MATHEMATICS (100) ----------
for($i=0;$i<100;$i++){
  $e=pickExam($i); $y=pickYear($i); $k=$i%10;
  if($k==0){ $a=mt_rand(2,9); $c=mt_rand(5,30); $x=$c+$a; // x - a = c
    Q($bank,$id,'Mathematics','Algebra',$e,$y,"If x − $a = $c, find x.",["".($x-2),"".($x-1),"".$x,"".($x+1)],2,"Add $a to both sides: x = $c + $a = $x."); }
  elseif($k==1){ $r1=mt_rand(1,6); $r2=mt_rand(1,6); $b=$r1+$r2; $c=$r1*$r2; $sgn='+';
    Q($bank,$id,'Mathematics','Algebra',$e,$y,"Factorise: x² + {$b}x + $c",["(x+$r1)(x-$r2)","(x-$r1)(x-$r2)","(x+$r1)(x+$r2)","(x+1)(x+$c)"],2,"Two numbers that multiply to $c and add to $b: $r1 and $r2."); }
  elseif($k==2){ $p1=mt_rand(1,4); $p2=mt_rand(5,8); $p3=12-$p1-$p2; if($p3<1){$p3=2;$p2=12-$p1-$p3;} $tot=180; $u=round($tot/($p1+$p2+$p3)); $big=max($p1,$p2,$p3)*$u;
    Q($bank,$id,'Mathematics','Geometry',$e,$y,"Angles of a triangle are in ratio $p1:$p2:$p3. Find the largest angle.",["".($big-20),"".$big,"".($big+10),"".($big-10)],1,"Sum of parts = ".($p1+$p2+$p3).", 180 ÷ parts = {$u}°, largest = ".max($p1,$p2,$p3)." × $u = {$big}°."); }
  elseif($k==3){ $p=mt_rand(5,25); $n=mt_rand(200,2000); $pct=$p*10; $ans=$n*$p/100;
    Q($bank,$id,'Mathematics','Statistics',$e,$y,"$p% of $n is equal to:",["".$ans,"".($ans+10),"".($ans*2),"".($ans-5)],0,"$p% × $n = $p/100 × $n = $ans."); }
  elseif($k==4){ $u=mt_rand(2,10); $t=mt_rand(2,6); $v=$u+mt_rand(2,8)*$t; $a=($v-$u)/$t;
    Q($bank,$id,'Mathematics','Algebra',$e,$y,"If 3x − 7 = ".(3*mt_rand(3,9)-7).", find x.",["4","5","6","7"],2,"3x = RHS + 7, then divide by 3."); }
  elseif($k==5){ $a=mt_rand(2,5); $r=mt_rand(2,3); $n3=$a*$r*$r; // GP
    Q($bank,$id,'Mathematics','Algebra',$e,$y,"The 1st term of a GP is $a with ratio $r. Find the 3rd term.",["".$a*$r,"".$n3,"".($a+$r*2),"".($a*$r*2)],1,"nth term = a.r^(n-1): {$a} x {$r} x {$r} = $n3."); }
  elseif($k==6){ $m=mt_rand(10,50); $s=mt_rand(2,9);
    Q($bank,$id,'Mathematics','Trigonometry',$e,$y,"If sin θ = 0.5, what is θ?",["30°","45°","60°","90°"],0,"sin 30° = 0.5."); }
  elseif($k==7){ $l=mt_rand(4,12); $w=mt_rand(3,9); $area=$l*$w;
    Q($bank,$id,'Mathematics','Geometry',$e,$y,"Area of a rectangle length {$l}cm breadth {$w}cm is:",["".($area+2)."cm²","".$area."cm²","".($l+$w)."cm²","".($area*2)."cm²"],1,"Area = l × b = $l × $w = $area cm²."); }
  elseif($k==8){ $x=mt_rand(2,12); $sq=$x*$x;
    Q($bank,$id,'Mathematics','Algebra',$e,$y,"Simplify: √$sq + √".($x*2*($x).'')." is not asked; instead: If x² = $sq, x =",["".$x,"".($x+1),"".($x*2),"".($x-1)],0,"x = √$sq = $x (positive root)."); }
  else { $a=mt_rand(100,500); $d=mt_rand(10,60); $sp=$a+$d;
    Q($bank,$id,'Mathematics','Statistics',$e,$y,"A trader bought goods at ₦$a and sold at ₦$sp. Profit % is:",[round($d/$a*100)."%",round($d/$sp*100)."%","10%","5%"],0,"Profit = ₦$d; % = $d/$a × 100 = ".round($d/$a*100)."%." ); }
}
