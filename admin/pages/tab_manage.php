<div id="tab-features" class="tabpane" style="display:none">
<div class="grid g2"><div class="card"><h3>⚙️ Feature switches + numbers</h3>
<p class="muted">Every toggle/number below drives the user side live. Unchecked = that page shows "disabled".</p>
<div id="featForm"></div>
<p><button class="btn btn-primary" onclick="saveFeatures()">💾 Save all settings</button></p></div>
<div class="card"><h3>🎨 Branding + home stats</h3>
<div id="brandForm"></div>
<p><button class="btn btn-navy" onclick="saveFeatures()">Save branding</button></p>
<h3>🔐 Registration policy</h3><div id="authForm"></div>
<p><button class="btn btn-amber" onclick="saveFeatures()">Save policy</button></p></div></div>
</div>
<div id="tab-schools" class="tabpane" style="display:none">
<div class="grid g2"><div class="card"><h3>🏫 Schools <span class="pill" id="schCount"></span></h3><div id="schList"></div></div>
<div class="card"><h3 id="schTitle">+ New school</h3>
<input type="hidden" id="sc_id"><label>Name</label><input id="sc_name" placeholder="e.g. Model College, Enugu">
<div style="display:flex;gap:8px"><div style="flex:1"><label>Code</label><input id="sc_code" placeholder="MC-ENU"></div>
<div style="flex:1"><label>State</label><input id="sc_state" placeholder="Enugu"></div></div>
<label>Contact phone</label><input id="sc_phone" placeholder="080...">
<label>Status</label><select id="sc_status"><option>approved</option><option>pending</option><option>suspended</option></select>
<p><button class="btn btn-primary" onclick="saveSchool()">💾 Save school</button></p></div></div>
</div>
<div id="tab-plans" class="tabpane" style="display:none">
<div class="grid g2"><div class="card"><h3>💳 Plans</h3><div id="planList"></div></div>
<div class="card"><h3>Plan editor</h3>
<input type="hidden" id="pl_id"><label>Slug</label><input id="pl_slug" placeholder="pro">
<label>Name</label><input id="pl_name" placeholder="Pro">
<div style="display:flex;gap:8px"><div style="flex:1"><label>Price (₦)</label><input id="pl_price" type="number" value="1500"></div>
<div style="flex:1"><label>Term</label><input id="pl_term" value="term"></div></div>
<label>Features (shown on pricing page)</label><textarea id="pl_feat" rows="3"></textarea>
<p><button class="btn btn-primary" onclick="savePlan()">💾 Save plan</button></p>
<h3>🎟️ New voucher</h3><div style="display:flex;gap:8px"><input id="vc_code" placeholder="SCHOLAR-2026"><select id="vc_plan"></select><input id="vc_max" type="number" value="100" title="uses"></div>
<p><button class="btn btn-amber" onclick="saveVoucher()">Create voucher</button></p><div id="vcList"></div></div></div>
</div>
<div id="tab-mentors" class="tabpane" style="display:none">
<div class="grid g2"><div class="card"><h3>🧑‍🏫 Mentors <span class="pill" id="mCount"></span></h3><div id="mentorList"></div></div>
<div class="card"><h3 id="mentorTitle">+ New mentor</h3>
<input type="hidden" id="mt_id"><label>Name</label><input id="mt_name" placeholder="Mentor name">
<div style="display:flex;gap:8px"><div style="flex:1"><label>Subject</label><select id="mt_sub"></select></div>
<div style="flex:1"><label>Status</label><select id="mt_status"><option>offline</option><option>online</option><option>busy</option></select></div></div>
<label>School</label><input id="mt_school" placeholder="School name">
<label>Bio</label><textarea id="mt_bio" rows="3"></textarea>
<label>Verified</label><select id="mt_verified"><option value="0">No</option><option value="1">Yes</option></select>
<p><button class="btn btn-primary" onclick="saveMentor()">💾 Save mentor</button> <button class="btn btn-ghost" onclick="resetMentor()">New</button></p>
<h3>📅 Live sessions</h3><div id="sessionList"></div>
<p><b>+ Session</b></p><select id="ss_mentor"></select><input id="ss_title" placeholder="Session title">
<div style="display:flex;gap:8px"><input id="ss_when" type="datetime-local"><select id="ss_status"><option>scheduled</option><option>live</option><option>ended</option></select></div>
<input id="ss_url" placeholder="Join URL (optional)">
<p><button class="btn btn-navy" onclick="saveSession()">Add session</button></p></div></div>
</div>
<div id="tab-users" class="tabpane" style="display:none">
<div class="card"><h3>👥 Users <span class="pill" id="uCount"></span></h3>
<div style="display:flex;gap:8px;flex-wrap:wrap"><select id="uq_role"><option value="">All roles</option><option>student</option><option>parent</option><option>mentor</option><option>teacher</option><option>admin</option></select>
<select id="uq_school"><option value="">All schools</option></select>
<input id="uq_q" placeholder="Search name/email…" style="flex:2;margin:0"><button class="btn btn-navy" onclick="loadUsers()">Filter</button></div><br><div id="uList"></div></div>
<div class="card" style="margin-top:16px"><h3>✉️ Recent messages</h3><div id="msgList"><p class="muted">Loading…</p></div></div>
</div>
