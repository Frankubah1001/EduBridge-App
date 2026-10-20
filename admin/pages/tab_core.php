<div id="tab-overview" class="tabpane">
  <div class="grid g2"><div class="card"><h3>📈 Questions by subject (DB)</h3><div id="bySubj"></div></div>
  <div class="card"><h3>🔒 NDPR &amp; Safety</h3><p class="muted">Consent logs · audit trail · minors need guardian consent.</p>
  <p><a class="btn btn-ghost" href="inc/api.php?action=stats">View stats JSON</a></p></div></div>
  <div class="card" style="margin-top:16px"><h3>🧾 Recent admin activity</h3><div id="auditList"><p class="muted">Loading…</p></div></div>
</div>
<div id="tab-taxonomy" class="tabpane" style="display:none">
  <div class="grid g3"><div class="card"><h3>Levels</h3><div id="lvlList"></div></div>
  <div class="card"><h3>Subjects</h3><div id="subList"></div>
  <p><b>+ Add subject</b></p><select id="ns_level"></select><input id="ns_name" placeholder="e.g. Geography"><button class="btn btn-primary" onclick="addSubject()">Add</button></div>
  <div class="card"><h3>Topics</h3><div id="topList"></div>
  <p><b>+ Add topic</b></p><select id="nt_sub"></select><input id="nt_name" placeholder="e.g. Map Reading"><button class="btn btn-primary" onclick="addTopic()">Add</button></div></div>
</div>
<div id="tab-courses" class="tabpane" style="display:none">
  <div class="grid g2"><div class="card"><h3>📚 Courses</h3><div id="courseList"></div></div>
  <div class="card"><h3 id="courseFormTitle">+ New course</h3>
  <input type="hidden" id="cf_id"><label>Code</label><input id="cf_code" placeholder="MTH-SSS02">
  <label>Title</label><input id="cf_title" placeholder="Course title">
  <div style="display:flex;gap:8px"><div style="flex:1"><label>Level</label><select id="cf_level"></select></div>
  <div style="flex:1"><label>Subject</label><select id="cf_sub"></select></div></div>
  <label>Description</label><textarea id="cf_desc" rows="3"></textarea>
  <label>Status</label><select id="cf_status"><option>draft</option><option>review</option><option selected>published</option><option>archived</option></select>
  <p><button class="btn btn-primary" onclick="saveCourse()">💾 Save course</button> <button class="btn btn-ghost" onclick="resetCourse()">New</button></p>
  <h3>📝 Lessons in selected course</h3><div id="lessonList"><p class="muted">Click Lessons on a course.</p></div>
  <p><b>+ Lesson</b></p><input id="lf_title" placeholder="Lesson title"><textarea id="lf_body" rows="3" placeholder="Lesson body…"></textarea>
  <div style="display:flex;gap:8px"><input id="lf_min" type="number" value="10" title="minutes"><select id="lf_status"><option selected>published</option><option>draft</option><option>review</option></select></div>
  <p><button class="btn btn-navy" onclick="saveLesson()">Add lesson</button></p></div></div>
</div>
<div id="tab-questions" class="tabpane" style="display:none">
  <div class="card"><h3>❓ Question bank <span class="pill" id="qCount"></span></h3>
  <div style="display:flex;gap:8px;flex-wrap:wrap">
  <select id="qf_sub"><option value="">All subjects</option></select>
  <select id="qf_exam"><option value="">All exams</option></select>
  <select id="qf_status"><option value="">Any status</option><option>draft</option><option>review</option><option selected>published</option><option>archived</option></select>
  <input id="qf_q" placeholder="Search stem…" style="flex:2;margin:0"><button class="btn btn-navy" onclick="loadQuestions()">Filter</button>
  <button class="btn btn-amber" onclick="newQuestion()">+ New</button></div><br><div id="qList"></div></div>
  <div class="card" id="qForm" style="display:none"><h3>Edit question</h3>
  <input type="hidden" id="qq_id">
  <div style="display:flex;gap:8px"><div style="flex:1"><label>Subject</label><select id="qq_sub"></select></div>
  <div style="flex:1"><label>Exam</label><select id="qq_exam"></select></div>
  <div style="flex:1"><label>Year</label><input id="qq_year" type="number" value="2024"></div></div>
  <label>Stem</label><textarea id="qq_stem" rows="3"></textarea>
  <div class="grid g2" id="qq_opts"></div>
  <label>Explanation</label><textarea id="qq_exp" rows="2"></textarea>
  <div style="display:flex;gap:8px"><div style="flex:1"><label>Difficulty</label><input id="qq_diff" type="number" step="0.1" value="0"></div>
  <div style="flex:1"><label>Status</label><select id="qq_status"><option>draft</option><option>review</option><option selected>published</option><option>archived</option></select></div></div>
  <p><button class="btn btn-primary" onclick="saveQuestion()">💾 Save question</button></p></div>
</div>
<div id="tab-papers" class="tabpane" style="display:none">
  <div class="grid g2"><div class="card"><h3>📝 Exam papers</h3><div id="paperList"></div>
  <p><b>+ Paper</b></p><input id="pf_title" placeholder="e.g. WAEC Maths Mock 1">
  <div style="display:flex;gap:8px"><select id="pf_exam"></select><select id="pf_sub"></select><input id="pf_min" type="number" value="20" title="minutes"></div>
  <p><button class="btn btn-primary" onclick="savePaper()">Save paper</button></p></div>
  <div class="card"><h3>⚡ Auto-build paper from bank</h3>
  <p class="muted">Fills a paper with N random published questions of its subject+exam.</p>
  <select id="ab_paper"></select><input id="ab_n" type="number" value="20">
  <p><button class="btn btn-amber" onclick="autofill()">Auto-fill</button></p><div id="abOut"></div></div></div>
</div>
<div id="tab-moderation" class="tabpane" style="display:none">
  <div class="card"><h3>🛡️ Flagged threads</h3><div id="flagList"><p class="muted">Loading…</p></div></div>
</div>
