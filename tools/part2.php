<?php // PART 2: English (80) + Physics (70)
$syn=[['TRANSPARENT','Clear','Opaque','Cloudy','Dark'],['AUDACIOUS','Bold','Timid','Fearful','Weak'],['PRUDENT','Wise','Foolish','Reckless','Careless'],['VIVID','Bright','Vague','Dull','Dim'],['SCARCE','Rare','Common','Plentiful','Abundant'],['DILIGENT','Hardworking','Lazy','Idle','Slow'],['FRAGILE','Breakable','Strong','Tough','Sturdy'],['VICTORY','Triumph','Defeat','Loss','Failure'],['GENEROUS','Kind','Stingy','Mean','Selfish'],[' sw'.'IFT','Fast','Slow','Dull','Late']];
$syn[9]=['SWIFT','Fast','Slow','Dull','Late'];
for($i=0;$i<80;$i++){ $e=pickExam($i+7); $y=pickYear($i+3); $k=$i%8;
 if($k==0){ $s=$syn[$i%count($syn)]; $r=$i%3;
  if($r==0){$o=[$s[1],$s[2],$s[3],$s[4]];$a=0;}elseif($r==1){$o=[$s[2],$s[1],$s[3],$s[4]];$a=1;}else{$o=[$s[2],$s[3],$s[4],$s[1]];$a=3;}
  Q($bank,$id,'English','Lexis',$e,$y,"Choose the synonym of ".$s[0].".",$o,$a,$s[0]." means ".strtolower($s[1])."."); }
 elseif($k==1){ $subs=['Neither of the boys ___ arrived.'=>'has','Each of the girls ___ a laptop.'=>'has','The committee ___ agreed.'=>'has','Neither the teacher nor the students ___ late.'=>'were'];
  $keys=array_keys($subs); $st=$keys[$i%4]; $ans=$subs[$st];
  $o=$ans=='has'?['have','has','are','were']:['is','are','was','were']; $a=array_search($ans,$o);
  Q($bank,$id,'English','Grammar',$e,$y,$st,$o,$a,"Concord: verb agrees with the true subject."); }
 elseif($k==2){ $pairs=[['for','since'],['since','for'],['from','to'],['on','at']]; $p=$pairs[$i%4];
  Q($bank,$id,'English','Grammar',$e,$y,"She has lived here ___ 2019.",[$p[0],$p[1],'from','by'],$p[1]=='since'?1:0,"'Since' goes with a point in time (2019)."); }
 elseif($k==3){ $ws=[['Occasion','Occassion','Ocassion','Ocasion',0],['Committee','Comittee','Commitee','Comitee',0],['Embarrass','Embarass','Embaras','Embarras',0],['Necessary','Neccessary','Necesary','Neccesary',0]];
  $w=$ws[$i%4]; $o=[$w[0],$w[1],$w[2],$w[3]];
  Q($bank,$id,'English','Grammar',$e,$y,"Choose the correctly spelt word.",$o,0,"Correct spelling: ".$w[0]."."); }
 elseif($k==4){ Q($bank,$id,'English','Oral Forms',$e,$y,"Which word contains the /th/ sound as in 'think'?",['thin','this','that','those'],0,"/th/ voiceless appears in 'thin' and 'think'."); }
 elseif($k==5){ $vs=[['deplores','condemns it','praises it','ignores it','joins it','Deplore = strongly condemn.'],['lauds','praises him','attacks him','ignores him','fears him','Laud = praise.'],['abhors','hates it','loves it','tolerates it','funds it','Abhor = hate.']];
  $v=$vs[$i%3]; Q($bank,$id,'English','Comprehension',$e,$y,"The writer says she '".$v[0]."' corruption. This means she:",[$v[1],$v[2],$v[3],$v[4]],0,$v[5]); }
 elseif($k==6){ Q($bank,$id,'English','Grammar',$e,$y,"The teacher, with all the students, ___ present.",['were','was','are','have been'],1,"Verb agrees with main subject 'teacher' (singular)."); }
 else { $s=$syn[($i+3)%count($syn)];
  Q($bank,$id,'English','Lexis',$e,$y,"Choose the antonym of ".$s[0].".",[$s[1],$s[2],$s[3],$s[4]],1,"Antonym of ".$s[0]." (".$s[1].") is ".$s[2]."."); }
}
// ---------- PHYSICS (70) ----------
for($i=0;$i<70;$i++){ $e=pickExam($i+3); $y=pickYear($i+5); $k=$i%7;
 if($k==0){ $v=mt_rand(2,10)*5; $t=mt_rand(2,8); $a=$v/$t;
  Q($bank,$id,'Physics','Motion',$e,$y,"A car accelerates from rest to $v m/s in $t s. Acceleration is:",[$a." m/s2",($a+2)." m/s2",($a*2)." m/s2",($a-1)." m/s2"],0,"a = (v-u)/t = $v/$t = $a m/s2."); }
 elseif($k==1){ $f=mt_rand(2,20)*10; $l=mt_rand(1,5); $v=$f*$l;
  Q($bank,$id,'Physics','Waves',$e,$y,"A wave has frequency $f Hz and wavelength $l m. Its speed is:",[($v/2)." m/s",($v+50)." m/s",$v." m/s",($v*2)." m/s"],2,"v = f x wavelength = $f x $l = $v m/s."); }
 elseif($k==2){ $m=mt_rand(2,10); $g=10; $w=$m*$g;
  Q($bank,$id,'Physics','Motion',$e,$y,"Weight of a $m kg mass (g = 10 m/s2) is:",[($w-10)." N",$w." N",($w+10)." N",($m)." N"],1,"W = mg = $m x 10 = $w N."); }
 elseif($k==3){ $v=mt_rand(6,30); $r=mt_rand(2,6); $I=round($v/$r,1);
  Q($bank,$id,'Physics','Electricity',$e,$y,"A $v V battery drives current through a $r ohm resistor. Current is:",[($I+1)." A",$I." A",($I*2)." A","0.5 A"],1,"I = V/R = $v/$r = $I A (Ohm's law)."); }
 elseif($k==4){ Q($bank,$id,'Physics','Optics',$e,$y,"Which instrument measures relative humidity?",['Barometer','Hygrometer','Thermometer','Manometer'],1,"Hygrometer measures humidity; barometer measures pressure."); }
 elseif($k==5){ $h=mt_rand(2,20); $pe=$h*10;
  Q($bank,$id,'Physics','Motion',$e,$y,"Potential energy of 1 kg mass at height $h m (g=10) is:",[($pe-10)." J",$pe." J",($pe+100)." J",($h)." J"],1,"PE = mgh = 1 x 10 x $h = $pe J."); }
 else { Q($bank,$id,'Physics','Waves',$e,$y,"Sound waves cannot travel through:",['Solids','Liquids','Gases','Vacuum'],3,"Sound needs a material medium; light does not."); }
}
