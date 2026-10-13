<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: adminlogin.php'); exit;
}
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Queue Dashboard</title><link rel="stylesheet" href="admin.css"></head>
<body><header><div class="header-content"><div><h1>Admin Dashboard</h1><p>Smart Queue Monitoring and Notification System</p></div><button class="logout-btn" onclick="logout()">Log Out</button></div></header>
<div class="container">
<div class="stats"><div class="stat-card"><h3>Total Queue Numbers</h3><h2 id="totalQueue">0</h2></div><div class="stat-card"><h3>Waiting</h3><h2 id="waitingCount">0</h2></div><div class="stat-card"><h3>Completed</h3><h2 id="completedCount">0</h2></div></div>
<div class="controls-bar"><button class="action-btn reset-btn" onclick="resetAll()">Reset All</button></div>
<div class="windows" id="windows"></div>
<div class="queue-section"><h2>Queue List</h2><table><thead><tr><th>Queue Number</th><th>Student</th><th>Student ID</th><th>Window</th><th>Status</th><th>Created</th></tr></thead><tbody id="queueTable"></tbody></table></div>
</div>
<script>
async function api(action, extra={}) {
  const method = action === 'queue_list' ? 'GET' : 'POST';
  const opts = {method, headers:{}};
  let url='queue_api.php';
  if(method==='GET') url += '?'+new URLSearchParams({action,...extra}).toString();
  else { opts.headers['Content-Type']='application/x-www-form-urlencoded'; opts.body=new URLSearchParams({action,...extra}).toString(); }
  const response=await fetch(url,opts); const data=await response.json();
  if(!response.ok) throw new Error(data.error || 'Request failed'); return data;
}
function renderWindows(windows) {
  const container=document.getElementById('windows');
  container.innerHTML=windows.map(w=>`<div class="window-card"><h2>Window ${w.id}</h2><p class="serving-label">Now Serving</p><div class="serving-number">${w.serving || '—'}</div><span class="status ${Number(w.is_open)?'':'closed'}">${Number(w.is_open)?'Open':'Closed'}</span><button class="next-btn" onclick="nextNumber(${w.id})" ${Number(w.is_open)?'':'disabled'}>Next Number</button><button class="toggle-btn" onclick="toggleWindow(${w.id})">${Number(w.is_open)?'Close Window':'Open Window'}</button></div>`).join('');
}
function renderQueue(data) {
 const tbody=document.getElementById('queueTable');
 tbody.innerHTML=data.rows.map(q=>`<tr><td>${q.queue_number}</td><td>${escapeHtml(q.full_name || '')}</td><td>${escapeHtml(q.student_id || '—')}</td><td>${q.window_id?'Window '+q.window_id:'—'}</td><td><span class="badge ${q.status.toLowerCase()}">${q.status}</span></td><td>${new Date(q.created_at.replace(' ','T')).toLocaleString()}</td></tr>`).join('');
 document.getElementById('totalQueue').textContent=data.counts.total||0;
 document.getElementById('waitingCount').textContent=data.counts.waiting||0;
 document.getElementById('completedCount').textContent=data.counts.completed||0;
}
function escapeHtml(s){return String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));}
async function refresh(){try{const [state,list]=await Promise.all([api('state'),api('queue_list')]);renderWindows(state.windows);renderQueue(list);}catch(e){console.error(e);}}
async function nextNumber(id){try{await api('next',{window_id:id});await refresh();}catch(e){alert(e.message);}}
async function toggleWindow(id){try{await api('toggle_window',{window_id:id});await refresh();}catch(e){alert(e.message);}}
async function resetAll(){if(!confirm('Are you sure you want to reset all queue numbers?'))return;try{await api('reset');await refresh();}catch(e){alert(e.message);}}
async function logout(){if(!confirm('Are you sure you want to log out?'))return;await fetch('auth.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'action=logout'});location.href='front.php';}
refresh();setInterval(refresh,3000);
</script></body></html>
