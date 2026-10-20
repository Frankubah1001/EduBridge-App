<?php // PART 3: Chemistry (70) + Biology (70)
$elem=[['Sodium','Na',11],['Magnesium','Mg',12],['Aluminium','Al',13],['Potassium','K',19],['Calcium','Ca',20],['Oxygen','O',8],['Chlorine','Cl',17],['Carbon','C',6]];
for($i=0;$i<70;$i++){ $e=pickExam($i+11); $y=pickYear($i+1); $k=$i%7;
 if($k==0){ $el=$elem[$i%count($elem)];
  Q($bank,$id,'Chemistry','Atomic Structure',$e,$y,"The chemical symbol for ".$el[0]." is:",[$el[1],substr($el[0],0,1),substr($el[0],0,2),$el[1].'2'],0,$el[0]." has symbol ".$el[1]."."); }
 elseif($k==1){ $el=$elem[($i+2)%count($elem)]; $m=$el[2]*2; $n=$m-$el[2];
  Q($bank,$id,'Chemistry','Atomic Structure',$e,$y,$el[0]." has mass number $m and $n neutrons. Its atomic number is:",["".($el[2]-2),"".$el[2],"".$m,"".($el[2]+2)],1,"Atomic number = protons = mass - neutrons = $m - $n = ".$el[2]."."); }
 elseif($k==2){ Q($bank,$id,'Chemistry','Chemical Bonding',$e,$y,"The bond in NaCl is:",['Covalent','Ionic','Metallic','Hydrogen'],1,"NaCl is an ionic (electrovalent) compound."); }
 elseif($k==3){ $ph=mt_rand(1,13);
  $cls=$ph<7?'Acidic':($ph==7?'Neutral':'Alkaline'); $a=$ph<7?0:($ph==7?1:2);
  Q($bank,$id,'Chemistry','Acids & Bases',$e,$y,"A solution with pH $ph is:",['Acidic','Neutral','Alkaline','Salty'],$ph<7?0:($ph==7?1:2),"pH < 7 acidic, pH 7 neutral, pH > 7 alkaline."); }
 elseif($k==4){ Q($bank,$id,'Chemistry','Organic Chemistry',$e,$y,"The general formula of alkanes is:",['CnH2n','CnH2n+2','CnH2n-2','CnHn'],1,"Alkanes: CnH2n+2 (e.g. methane CH4)."); }
 elseif($k==5){ Q($bank,$id,'Chemistry','Periodic Table',$e,$y,"Which gas is most abundant in the atmosphere?",['Oxygen','Hydrogen','Nitrogen','Carbon dioxide'],2,"Nitrogen is ~78% of air."); }
 else { Q($bank,$id,'Chemistry','Electrolysis',$e,$y,"During electrolysis of water, hydrogen collects at the:",['Anode','Cathode','Salt bridge','Electrolyte'],1,"H2 (cations gain electrons) at the cathode."); }
}
// ---------- BIOLOGY (70) ----------
$org=[['Mitochondrion','powerhouse','respiration'],['Ribosome','protein synthesis','translation'],['Nucleus','control centre','DNA storage'],['Chloroplast','photosynthesis','chlorophyll'],['Cell membrane','selective barrier','transport']];
for($i=0;$i<70;$i++){ $e=pickExam($i+5); $y=pickYear($i+7); $k=$i%7;
 if($k==0){ $o=$org[$i%count($org)];
  Q($bank,$id,'Biology','Cell Biology',$e,$y,"The ".$o[1]." of the cell is the:",[$o[0]=='Mitochondrion'?'Nucleus':$o[0],$o[0],'Vacuole','Cell wall'],$o[0]=='Mitochondrion'?1:1,$o[0]." is responsible for ".$o[1]." (".$o[2].")."); }
 elseif($k==1){ Q($bank,$id,'Biology','Genetics',$e,$y,"In humans, the sex chromosomes in a female are:",['XY','XX','XO','YY'],1,"Female = XX, male = XY."); }
 elseif($k==2){ Q($bank,$id,'Biology','Ecology',$e,$y,"Which is a primary producer?",['Grass','Goat','Lion','Fungus'],0,"Producers (green plants) start every food chain."); }
 elseif($k==3){ Q($bank,$id,'Biology','Cell Biology',$e,$y,"Photosynthesis occurs mainly in the:",['Mitochondrion','Chloroplast','Nucleus','Ribosome'],1,"Chlorophyll in chloroplasts traps light energy."); }
 elseif($k==4){ Q($bank,$id,'Biology','Genetics',$e,$y,"A cross Tt x Tt gives tall:short ratio (T dominant):",['1:1','3:1','2:1','4:0'],1,"Mendelian monohybrid ratio = 3:1."); }
 elseif($k==5){ Q($bank,$id,'Biology','Transport',$e,$y,"Red blood cells transport:",['Oxygen','Urea','Hormones','Enzymes'],0,"Haemoglobin in RBCs carries oxygen."); }
 else { Q($bank,$id,'Biology','Ecology',$e,$y,"Malaria is transmitted by the bite of:",['Tsetse fly','Female Anopheles mosquito','Male Anopheles mosquito','Housefly'],1,"Female Anopheles mosquito transmits Plasmodium."); }
}
