<?php require_once __DIR__.'/../config.php';
// AI Tutor — offline rule-based engine. Swap $LLM_URL for OpenAI/Anthropic when online.
$q=strtolower(trim($_GET['q']??$_POST['q']??''));
$courses=json_data('courses.json'); $qs=json_data('questions.json');
$answer="I can help with WAEC/NECO/JAMB topics. Ask e.g. 'quadratic equation', 'photosynthesis', 'demand and supply'.";
if($q!==''){
  if(str_contains($q,'quadrat')) $answer="📐 Quadratic: ax²+bx+c=0. 1) Try factorising (find two numbers ×→c, +→b). 2) Else use formula x=(−b±√(b²−4ac))/2a. Example: x²+5x+6 → (x+2)(x+3), roots −2,−3. Want a 5-question drill?";
  elseif(str_contains($q,'photosynth')||str_contains($q,'cell')) $answer="🌱 Photosynthesis: 6CO₂+6H₂O →(light/chlorophyll) C₆H₁₂O₆+6O₂. WAEC diagram: label chloroplast, note stomata gas exchange. Mitochondria ≠ chloroplast — don't mix them!";
  elseif(str_contains($q,'demand')||str_contains($q,'supply')) $answer="📊 Demand vs Supply: price ↑ → demand contracts (move UP the curve), supply extends. Equilibrium where they cross. Draw arrows — examiners award diagram marks.";
  elseif(str_contains($q,'motion')||str_contains($q,'wave')) $answer="🚀 Motion: v=u+at, s=ut+½at², v²=u²+2as. Waves: v=fλ. Sound needs a medium, light doesn't. Units win marks — always include them.";
  elseif(str_contains($q,'plan')||str_contains($q,'timetable')) $answer="🗓️ Smart plan: 3× 25-min blocks daily (Pomodoro), 1 subject per block, end with 10 past questions. I built you a planner on the Planner tab — want me to fill it for Mathematics?";
  elseif(str_contains($q,'jamb')&&str_contains($q,'english')) $answer="📝 JAMB English hack: skim passage → read questions → scan. Spend ≤1 min/question, flag hard ones, return later. Concord: 'Neither' is singular ('has').";
  else $answer="🤖 Good question on “".htmlspecialchars(substr($q,0,80))."”. Offline mode: here's a study recipe — (1) define the concept in one sentence, (2) one worked example, (3) 5 past questions, (4) teach it back aloud. Tell me the subject and I'll drill you!";
}
if(($_GET['format']??'')==='json') api_out(['answer'=>$answer]);
