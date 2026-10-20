<?php // GEN2 extra: trig cofunction (70), calculus coeff (40), oral (60)
function gen_trig_extra(){
 $m=0;for($a=1;$a<=89&&$m<80;$a++){ $b=90-$a;
  if(EM('Mathematics','Trigonometry',"If sin $a deg = cos $b deg (co-functions), and sin $a deg = cos x deg with x acute, x =",[$b.' deg',$a.' deg','45 deg',(90+$a).' deg'],0,"Cofunction: sin a = cos(90-a); x = 90-$a = $b deg."))$m++; }
 if($m<70)echo "SHORT Mathematics|Trigonometry $m/70\n";
}
function gen_calc_extra(){
 $m=0;$at=0;$want=44;while($m<$want&&$at<$want*800){$at++;$c=1+mt_rand(0,11);$q=2+mt_rand(0,9);$dc=$c*$q;$e=$q-1;
  $pool=[$c.'x^'.$e,$dc.'x^'.$q,$q.'x^'.($c-1),$dc.'x^'.($e>0?$e-1:0),$c.'x^'.$q];
  $pool=array_values(array_unique(array_filter($pool,fn($v)=>$v!=$dc.'x^'.$e)));
  $rot=$at%count($pool);$pool=array_merge(array_slice($pool,$rot),array_slice($pool,0,$rot));
  $d=array_slice($pool,0,3);[$o,$a]=MK($dc.'x^'.$e,$d,($at+$m)%4);
  if(EM('Mathematics','Calculus',"If y = {$c}x^$q, dy/dx is:",$o,$a,"d/dx: $c x $q = $dc; y' = {$dc}x^$e."))$m++;}
 if($m<30)echo "SHORT Mathematics|Calculus $m/30\n";
}
function gen_oral(){
 $th='thin|/TH/ as in thin|/TH/ as in this|/SH/ as in ship|/CH/ as in chip;think|/TH/ as in thin|/TH/ as in this|/SH/ as in ship|/T/ as in tip;thank|/TH/ as in thin|/TH/ as in this|/S/ as in sip|/T/ as in tank;thick|/TH/ as in thin|/TH/ as in this|/F/ as in fin|/T/ as in tick;three|/TH/ as in thin|/TH/ as in this|/FREE/ sound|/T/ as in tree;thumb|/TH/ as in thin|/TH/ as in this|/B/ as in bum|/D/ as in dumb;thought|/TH/ as in thin|/TH/ as in this|/T/ as in taught|/S/ as in sought;theme|/TH/ as in thin|/TH/ as in this|/D/ as in dream|/T/ as in team;thirst|/TH/ as in thin|/TH/ as in this|/F/ as in first|/S/ as in surst;thorn|/TH/ as in thin|/TH/ as in this|/T/ as in torn|/S/ as in sorn;thread|/TH/ as in thin|/TH/ as in this|/T/ as in tread|/D/ as in dread;thrill|/TH/ as in thin|/TH/ as in this|/TR/ blend only|/R/ as in rill;throne|/TH/ as in thin|/TH/ as in this|/T/ as in trone|/D/ as in drone;this|/TH/ as in this|/TH/ as in thin|/SH/ as in ship|/T/ as in tis;that|/TH/ as in this|/TH/ as in thin|/D/ as in dat|/T/ as in tat;those|/TH/ as in this|/TH/ as in thin|/Z/ as in zoze|/S/ as in sose;they|/TH/ as in this|/TH/ as in thin|/D/ as in dey|/T/ as in tey;them|/TH/ as in this|/TH/ as in thin|/D/ as in dem|/T/ as in tem;their|/TH/ as in this|/TH/ as in thin|/D/ as in deir|/R/ as in reir;than|/TH/ as in this|/TH/ as in thin|/D/ as in dan|/T/ as in tan;then|/TH/ as in this|/TH/ as in thin|/D/ as in den|/W/ as in when;though|/TH/ as in this|/TH/ as in thin|/T/ as in tough|/D/ as in dough;breathe|/TH/ as in this|/TH/ as in thin|/T/ as in treat|/D/ as in breed;father|/TH/ as in this|/TH/ as in thin|/T/ as in fater|/D/ as in fader;mother|/TH/ as in this|/TH/ as in thin|/T/ as in moter|/D/ as in moder;brother|/TH/ as in this|/TH/ as in thin|/T/ as in broter|/R/ as in brorer;other|/TH/ as in this|/TH/ as in thin|/T/ as in oter|/D/ as in oder;smooth|/TH/ as in this|/TH/ as in thin|/T/ as in smoot|/D/ as in smood;clothing|/TH/ as in this|/TH/ as in thin|/T/ as in cloting|/D/ as in cloding';
 OPT4('English','Oral Forms',"The word '{M}' contains which 'th' sound?",$th,30);
 $st='table|First syllable|Second syllable|Equal stress|No stress;teacher|First syllable|Second syllable|Equal stress|No stress;water|First syllable|Second syllable|Equal stress|No stress;market|First syllable|Second syllable|Equal stress|No stress;doctor|First syllable|Second syllable|Equal stress|No stress;sister|First syllable|Second syllable|Equal stress|No stress;begin|Second syllable|First syllable|Equal stress|No stress;above|Second syllable|First syllable|Equal stress|No stress;between|Second syllable|First syllable|Equal stress|No stress;today|Second syllable|First syllable|Equal stress|No stress;machine|Second syllable|First syllable|Equal stress|No stress;about|Second syllable|First syllable|Equal stress|No stress;father|First syllable|Second syllable|Equal stress|No stress;mother|First syllable|Second syllable|Equal stress|No stress;happy|First syllable|Second syllable|Equal stress|No stress';
 OPT4('English','Oral Forms',"In '{M}', the stressed syllable is the:",$st,30);
}
