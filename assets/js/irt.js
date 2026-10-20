/* IRT 2-parameter logistic engine (client-side, mirrors Python backend) */
const IRT = {
  prob(theta, a, b){ return 1/(1+Math.exp(-a*(theta-b))); },
  // EAP-ish theta update after each response
  updateTheta(theta, correct, a, b, step=0.35){
    const p = this.prob(theta,a,b);
    // gradient step on log-likelihood
    theta += step * a * (correct - p);
    return Math.max(-3, Math.min(3, Math.round(theta*100)/100));
  },
  estimate(responses){ // responses: [{correct,a,b}]
    let t=0; responses.forEach(r=>{ t=this.updateTheta(t,r.correct,r.a??1,r.b??0,0.5); }); return t;
  },
  nextQuestion(theta, bank, answeredIds){
    let best=null,bestInfo=0;
    bank.filter(q=>!answeredIds.includes(q.id)).forEach(q=>{
      const a=q.discrimination||1,b=q.difficulty||0;
      const p=this.prob(theta,a,b);
      const info=a*a*p*(1-p); // Fisher information — pick max
      if(info>bestInfo){bestInfo=info;best=q;}
    });
    return best;
  },
  masteryLabel(t){ if(t<-1)return'Beginner'; if(t<0)return'Developing'; if(t<1)return'Proficient'; return'Mastery'; }
};
