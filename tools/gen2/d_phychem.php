<?php // GEN2 Physics/Chemistry
function gen_phy_full(){
 $m=0;$at=0;$w=100;while($m<$w&&$at<$w*600){$at++;$v=5+mt_rand(0,95);$t=1+mt_rand(0,11);
  $a=$v/$t;if(abs($a-round($a,1))>1e-9)continue;$a=round($a,1);
  if(EM('Physics','Motion',"Car 0 to $v m/s in $t s. a =",[$a.' m/s2',($a+2).' m/s2',($a*2).' m/s2',max(0,$a-1).' m/s2'],0,"a=(v-u)/t=$v/$t=$a m/s2."))$m++;}
 $m=0;$at=0;$w=60;while($m<$w&&$at<$w*600){$at++;$u=2+mt_rand(0,28);$a=1+mt_rand(0,8);$t=1+mt_rand(0,7);$v=$u+$a*$t;
  if(EM('Physics','Motion',"u=$u m/s, a=$a m/s2, t=$t s. v =",[$v.' m/s',($v+5).' m/s',($v-3).' m/s',($v*2).' m/s'],0,"v=u+at=$v m/s."))$m++;}
 $m=0;$at=0;$w=70;while($m<$w&&$at<$w*400){$at++;$f=10+mt_rand(0,490);$l=1+mt_rand(0,6);$v=$f*$l;
  if(EM('Physics','Waves',"f=$f Hz, wavelength=$l m. v =",[$v.' m/s',($v+50).' m/s',($v/2).' m/s',($v*2).' m/s'],0,"v=fL=$v m/s."))$m++;}
 $m=0;$at=0;$w=70;while($m<$w&&$at<$w*400){$at++;$V=3+mt_rand(0,45);$R=1+mt_rand(0,11);$I=$V/$R;if(abs($I-round($I,1))>1e-9)continue;$I=round($I,1);
  if(EM('Physics','Electricity',"$V V across $R ohm. I =",[$I.' A',($I+1).' A',($I*2).' A','0.5 A'],0,"I=V/R=$I A."))$m++;}
 $m=0;$at=0;$w=60;while($m<$w&&$at<$w*300){$at++;$h=2+mt_rand(0,48);
  if(EM('Physics','Motion',"PE of 1 kg at height $h m (g=10) =",[($h*10).' J',($h*10-10).' J',($h*10+100).' J',$h.' J'],0,"PE=".($h*10)." J."))$m++;}
 $inst=['Hygrometer|humidity|Barometer|Thermometer|Manometer','Barometer|pressure|Hygrometer|Thermometer|Manometer','Thermometer|temperature|Hygrometer|Barometer|Ammeter','Ammeter|current|Voltmeter|Hygrometer|Barometer','Voltmeter|voltage|Ammeter|Hygrometer|Galvanometer','Stopwatch|time|Ruler|Balance|Thermometer','Ruler|length|Stopwatch|Balance|Protractor','Beam balance|mass|Ruler|Stopwatch|Thermometer','Spring balance|weight|Ruler|Barometer|Hygrometer','Measuring cylinder|volume|Ruler|Balance|Ammeter','Protractor|angle|Ruler|Stopwatch|Balance','Calipers|diameter|Ruler|Stopwatch|Balance','Galvanometer|small current|Ammeter|Voltmeter|Barometer','Sonometer|frequency|Ruler|Balance|Ammeter','Hydrometer|density|Barometer|Thermometer|Ammeter','Anemometer|wind speed|Barometer|Hygrometer|Ruler'];
 OPT4('Physics','Optics',["Which instrument measures '{M}'?",'"{M}" is measured with:'],implode(';',$inst),60);
}
function gen_chem_full(){
 POOL3('Chemistry','Atomic Structure','The symbol for {S} is:','Sodium|Na|N|So;Zinc|Zn|Z|Zi;Lead|Pb|L|Le;Tin|Sn|T|Tn;Gold|Au|G|Go;Silver|Ag|S|Si;Potassium|K|P|Po;Copper|Cu|C|Co;Iron|Fe|I|Ir;Calcium|Ca|C|Cl',40,0,1,1);
 OPT4('Chemistry','Atomic Structure',["Element '{M}' has symbol:",'The correct symbol for "{M}" is:'],'Sodium|Na|N|So|XX;Magnesium|Mg|M|Ma|XX;Aluminium|Al|A|Au|XX;Oxygen|O|Ox|Og|XX;Carbon|C|Ca|Cb|XX;Nitrogen|N|Ni|Na|XX;Sulphur|S|Su|Si|XX;Iron|Fe|I|Ir|XX;Copper|Cu|C|Co|XX;Calcium|Ca|C|Cl|XX;Potassium|K|P|Po|XX;Zinc|Zn|Z|Zi|XX',30);
 $m=0;$at=0;$w=40;while($m<$w&&$at<$w*400){$at++;$ph=1+mt_rand(0,12);
  if(EM('Chemistry','Acids & Bases',"Solution pH $ph is:",['Acidic','Neutral','Alkaline','Salty'],$ph<7?0:($ph==7?1:2),"pH<7 acidic, =7 neutral, >7 alkaline."))$m++;}
 OPT4('Chemistry','Chemical Bonding',["'{M}' bonding type:",'Bond check — "{M}":'],'Sodium chloride|Ionic|Covalent|Metallic|Hydrogen;Methane|Covalent|Ionic|Metallic|Hydrogen;Iron|Metallic|Ionic|Covalent|Dative;Water|Covalent|Ionic|Metallic|Hydrogen;Diamond|Covalent|Ionic|Metallic|Hydrogen;Magnesium oxide|Ionic|Covalent|Metallic|Hydrogen;Oxygen molecule|Covalent|Ionic|Metallic|Hydrogen;Calcium chloride|Ionic|Covalent|Metallic|Hydrogen;Graphite|Covalent|Ionic|Metallic|Hydrogen;Copper|Metallic|Ionic|Covalent|Dative',50);
 OPT4('Chemistry','Organic Chemistry',["'{M}' family:",'Classify "{M}":'],'Methane|Alkane|Alkene|Alkyne|Alcohol;Ethene|Alkene|Alkane|Alkyne|Alcohol;Ethyne|Alkyne|Alkene|Alkane|Alcohol;Ethanol|Alcohol|Alkane|Alkene|Acid;Ethanoic acid|Acid|Alcohol|Alkane|Alkene;Propane|Alkane|Alkene|Alkyne|Ester;Butene|Alkene|Alkane|Alkyne|Alcohol;Propyne|Alkyne|Alkene|Alkane|Alcohol;Methanol|Alcohol|Acid|Alkane|Alkene;Benzene|Aromatic|Alkane|Alkene|Alcohol',60);
 OPT4('Chemistry','Periodic Table',["'{M}' fact:",'Periodic check — "{M}":'],'Nitrogen|78pc of air|21pc of air|1pc of air|50pc of air;Noble gases|Group 0|Group 1|Group 7|Group 2;Halogens|Group 7|Group 1|Group 0|Group 2;Alkali metals|Group 1|Group 7|Group 0|Group 2;Transition metals|Middle block|Group 1|Group 0|Group 8 only',30);
 OPT4('Chemistry','Electrolysis',["'{M}':",'Electrolysis check — "{M}":'],'Water electrolysis|H2 at cathode|H2 at anode|O2 at cathode|Na at cathode;Battery charging|Electrical to chemical|Chemical to electrical|Heat to light|Sound to heat;Copper refining|Pure Cu cathode|Pure Cu anode|Cu anode dissolves|No ions;Aluminium ore|Bauxite|Haematite|Galena|Cinnabar;Iron ore|Haematite|Bauxite|Galena|Cinnabar;Electroplating cathode|Object coated|Anode only|No reaction|Gas only;Dilute acid cation|H+ discharged|OH- discharged|Na+ discharged|Cl- formed',36);
}
