<?php // PART 4: Economics (50) + Government (50) + build writer
for($i=0;$i<50;$i++){ $e=pickExam($i+9); $y=pickYear($i+2); $k=$i%5;
 if($k==0){ Q($bank,$id,'Economics','Demand & Supply',$e,$y,"A rise in price leads to ___ of demand.",['extension','contraction','increase','shift'],1,"Upward movement along the curve = contraction of demand."); }
 elseif($k==1){ $p=mt_rand(10,100); $q=mt_rand(20,200);
  Q($bank,$id,'Economics','Demand & Supply',$e,$y,"Price ₦$p, quantity demanded $q units. Total revenue is:",["₦".($p*$q),"₦".($p+$q),"₦".($q-$p),"₦".($p*2)],0,"TR = P x Q = $p x $q = ₦".($p*$q)."."); }
 elseif($k==2){ Q($bank,$id,'Economics','Market Structures',$e,$y,"Which market has many sellers of identical goods?",['Monopoly','Perfect competition','Oligopoly','Monopsony'],1,"Perfect competition: many sellers, homogeneous product."); }
 elseif($k==3){ Q($bank,$id,'Economics','National Income',$e,$y,"GDP stands for:",['Gross Domestic Product','General Domestic Price','Great Demand Pull','Gross Demand Product'],0,"GDP = Gross Domestic Product."); }
 else { Q($bank,$id,'Economics','Money & Banking',$e,$y,"The apex bank in Nigeria is the:",['First Bank','Central Bank of Nigeria','World Bank','Access Bank'],1,"CBN regulates money supply and banks."); }
}
for($i=0;$i<50;$i++){ $e=pickExam($i+2); $y=pickYear($i+4); $k=$i%5;
 if($k==0){ Q($bank,$id,'Government','Democracy',$e,$y,"Nigeria returned to civilian rule in:",['1993','1999','2007','2015'],1,"Fourth Republic began 29 May 1999."); }
 elseif($k==1){ Q($bank,$id,'Government','Arms of Government',$e,$y,"Which arm interprets the law?",['Executive','Legislature','Judiciary','Electorate'],2,"Judiciary (courts) interprets laws."); }
 elseif($k==2){ Q($bank,$id,'Government','Federalism',$e,$y,"How many states are in Nigeria?",['28','30','36','37'],2,"36 states + FCT Abuja."); }
 elseif($k==3){ Q($bank,$id,'Government','ECOWAS',$e,$y,"Headquarters of ECOWAS is in:",['Lagos','Accra','Abuja','Dakar'],2,"ECOWAS Secretariat is in Abuja, Nigeria."); }
 else { Q($bank,$id,'Government','Democracy',$e,$y,"Universal adult suffrage means:",['Only men vote','All adults vote','Only elites vote','Only soldiers vote'],1,"All adults (18+) can vote."); }
}
// ---------- WRITE ----------
define('EB_ROOT', dirname(__DIR__));
$orig=json_decode(file_get_contents(EB_ROOT.'/data/questions.json'),true);
// keep only original curated 10 (ids 1..10 from before seeding)
$curated=array_values(array_filter($orig,function($q){return ($q['id']??0)<=10 && isset($q['explanation']);}));
if(count($curated)<10){ $curated=$orig; $curated=array_slice($curated,0,10); }
foreach($curated as &$c){ $c['id']=$id++; }
$all=array_merge($bank,$curated);
file_put_contents(EB_ROOT.'/data/questions.json',json_encode($all,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
echo "GEN=".count($bank)." CUR=".count($curated)." TOTAL=".count($all).PHP_EOL;
