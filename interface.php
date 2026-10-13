<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header('Location: system.php'); exit;
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Queue Monitoring and Notification System</title>
    <link rel="stylesheet" href="interface.css">
</head>
<body>
    <!-- =========================
         SIDEBAR
    ========================== -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="02.png" alt="CSU Logo">
            <div>
                <h2>Smart Queue</h2>
                <p>Registrar System</p>
            </div>
        </div>

        <nav class="sidebar-menu">
            <a href="#dashboard" class="menu-item active">
                <span>▣</span>
                Dashboard
            </a>
            <a href="#my-queue" class="menu-item">
                <span>🎫</span>
                My Queue
            </a>
            <a href="#notifications" class="menu-item">
                <span>🔔</span>
                Notifications
                <span class="notification-count">2</span>
            </a>
            <a href="#profile" class="menu-item">
                <span>👤</span>
                Profile
            </a>
            
<a href="#Developers" class="menu-item">
    <span>👨🏻‍💻</span>
    Developers
</a>

            <a href="auth.php?action=logout" class="menu-item logout" onclick="event.preventDefault(); logoutUser();">
                <span>↪</span>
                Log Out
            </a>
        </nav>
    </aside>

    <!-- =========================
         MAIN CONTENT
    ========================== -->
    <main class="main-content" id="dashboard">
        <!-- HEADER -->
        <header class="top-header">
            <div>
                <h1>Dashboard</h1>
                <p>
                    Monitor the registrar queue in real time.
                </p>
            </div>

            <div class="user-info">
                <div class="user-avatar">
                    S
                </div>
                <div>
                    <strong><?= htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                    <small><?= htmlspecialchars($user['student_id'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
                </div>
            </div>
        </header>

        <!-- =========================
             WELCOME
        ========================== -->
        <section class="welcome-panel">
            <div>
                <h2>
                    Welcome back! 👋
                </h2>
                <p>
                    Check the current status of all registrar windows
                    and get your queue number.
                </p>
            </div>
        </section>

        <!-- =========================
             REGISTRAR WINDOWS
        ========================== -->
        <section class="section">
            <div class="section-heading">
                <div>
                    <h2>Registrar Windows</h2>
                    <p>
                        See what number each registrar window is currently serving.
                    </p>
                </div>
                <div class="system-status">
                    <span class="status-dot"></span>
                    System Online
                </div>
            </div>
            <div class="windows-grid">

                <!-- WINDOW 1 -->
                <div class="window-card">
                    <div class="window-header">
                        <div>
                            <span class="window-label">
                                REGISTRAR
                            </span>
                            <h3>
                                Window 1
                            </h3>
                        </div>
                        <span class="open-badge">
                            OPEN
                        </span>
                    </div>
                    <p class="serving-label">
                        NOW SERVING
                    </p>
                    <div
                        class="serving-number"
                        id="window1Number"
                    >
                        125
                    </div>

                    <div class="window-footer">
                        <span>
                            ● Serving
                        </span>
                        <span>
                            Active
                        </span>
                    </div>
                </div>

                <!-- WINDOW 2 -->
                <div class="window-card">
                    <div class="window-header">
                        <div>
                            <span class="window-label">
                                REGISTRAR
                            </span>
                            <h3>
                                Window 2
                            </h3>
                        </div>
                        <span class="open-badge">
                            OPEN
                        </span>
                    </div>
                    <p class="serving-label">
                        NOW SERVING
                    </p>
                    <div
                        class="serving-number"
                        id="window2Number"
                    >
                        87
                    </div>

                    <div class="window-footer">
                        <span>
                            ● Serving
                        </span>
                        <span>
                            Active
                        </span>
                    </div>
                </div>

                <!-- WINDOW 3 -->
                <div class="window-card">
                    <div class="window-header">
                        <div>
                            <span class="window-label">
                                REGISTRAR
                            </span>
                            <h3>
                                Window 3
                            </h3>
                        </div>
                        <span class="open-badge">
                            OPEN
                        </span>
                    </div>
                    <p class="serving-label">
                        NOW SERVING
                    </p>
                    <div
                        class="serving-number"
                        id="window3Number"
                    >
                        143
                    </div>
                    <div class="window-footer">
                        <span>
                            ● Serving
                        </span>
                        <span>
                            Active
                        </span>
                    </div>
                </div>
            </div>
        </section>
        <!-- =========================
             GET QUEUE
        ========================== -->
        <section class="queue-request-panel">
            <div>
                <h2>
                    Get Your Queue Number
                </h2>
                <p>
                    You can get up to
                    <strong>2 queue numbers</strong>.
                    Each number will be assigned to a registrar window.
                </p>
            </div>
            <button
                type="button"
                class="get-queue-btn"
                id="getQueueBtn"
            >
                + Get Queue Number
            </button>
        </section>

        <!-- =========================
             MY QUEUE
        ========================== -->
        <section
            class="section"
            id="my-queue"
        >
            <div class="section-heading">
                <div>

                    <h2>
                        My Queue Numbers
                    </h2>
                    <p>
                        Monitor your active queue numbers.
                    </p>
                </div>

                <div
                    class="queue-limit"
                    id="queueLimit"
                >
                    0 / 2 Queue Numbers
                </div>
            </div>

            <div
                class="my-queues-grid"
                id="myQueuesContainer"
            >
                <div class="empty-queue">
                    <div class="empty-icon">
                        🎫
                    </div>
                    <h3>
                        No Queue Number Yet
                    </h3>
                    <p>
                        Click "Get Queue Number" to get your first queue number.
                    </p>
                </div>
            </div>
        </section>

        <!-- =========================
             NOTIFICATIONS
        ========================== -->
        <section
            class="section"
            id="notifications"
        >
            <div class="section-heading">
                <div>
                    <h2>
                        Notifications
                    </h2>
                    <p>
                        Important updates about your queue.
                    </p>
                </div>
            </div>

            <div class="notifications-panel">
                <div class="notification-item">
                    <div class="notification-icon">
                        🔔
                    </div>

                    <div>
                        <strong>
                            Queue System Online
                        </strong>

                        <p>
                            All three registrar windows are currently operating.
                        </p>
                        <small>
                            Just now
                        </small>
                    </div>
                </div>

                <div class="notification-item">
                    <div class="notification-icon blue">
                        ℹ
                    </div>
                    <div>
                        <strong>
                            Queue Reminder
                        </strong>
                        <p>
                            Please monitor your queue number and be ready when
                            your number is approaching.
                        </p>
                        <small>
                            Today
                        </small>
                    </div>
                </div>
            </div>
        </section>

        <!-- =========================
             PROFILE
        ========================== -->
        <section
            class="section"
            id="profile"
        >
            <div class="section-heading">
                <div>
                    <h2>
                        Profile
                    </h2>
                    <p>
                        Your student information.
                    </p>
                </div>
            </div>

            <div class="profile-panel">
                <div class="profile-avatar">
                    S
                </div>

                <div class="profile-details">
                    <h3><?= htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p>
                        Student ID:
                        <strong><?= htmlspecialchars($user['student_id'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                    </p>
                    <p>
                        Account:
                        <span class="verified">
                            Active
                        </span>
                    </p>
                </div>
            </div>
        </section>

  
<!-- DEVELOPERS SECTION -->
 
<section class="section developers-section" id="Developers">
    <div class="section-heading">
        <div>
            <h2>Meet the Developers</h2>
            <p>
                The people behind the Smart Queue Monitoring
                and Notification System.
            </p>
        </div>
    </div>

    <div class="developers-grid">
        <div class="developer-card">
            <div class="developer-avatar">M</div>
            <h3>MARIANO, CHAREST EDISON M.</h3>
        </div>

        <div class="developer-card">
            <div class="developer-avatar">V</div>
            <h3>VILORIA, PRINCESS RHEALYN M.</h3>
        </div>

        <div class="developer-card">
            <div class="developer-avatar">A</div>
            <h3>ACADEMIA, GILLIAN MAR A.</h3>
        </div>

        <div class="developer-card">
            <div class="developer-avatar">D</div>
            <h3>DELA CRUZ, JIREH CHRISTIAN M.</h3>
        </div>

        <div class="developer-card">
            <div class="developer-avatar">G</div>
            <h3>GARDUQUE, MARY JONICA M.</h3>
        </div>

        <div class="developer-card">
            <div class="developer-avatar">B</div>
            <h3>BANEZ, SHARLENE M.</h3>
        </div>
    </div>
</section>


        <!-- =========================
             ACTIVITY
        ========================== -->
        <section class="section">
            <div class="section-heading">
                <div>
                    <h2>
                        Recent Queue Activity
                    </h2>
                    <p>
                        Your recent queue transactions.
                    </p>
                </div>
            </div>

            <div class="activity-table-container">
                <table>
                    <thead>
                        <tr>
                            <th>
                                Queue Number
                            </th>
                            <th>
                                Window
                            </th>
                            <th>
                                Date
                            </th>
                            <th>
                                Status
                            </th>
                        </tr>
                    </thead>

                    <tbody id="activityTable">
                        <tr>
                            <td
                                colspan="4"
                                class="no-activity"
                            >
                                No queue activity yet.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- FOOTER -->
        <footer>
            <p>
                © 2026 Smart Queue Monitoring and Notification System
            </p>
        </footer>
    </main>
    <!-- =========================
         JAVASCRIPT
    ========================== -->
    <script>
const API = 'queue_api.php';
async function api(action, method='GET', extra={}) {
    const opts = {method, headers: {}};
    let url = API;
    if (method === 'GET') {
        const qs = new URLSearchParams({action, ...extra});
        url += '?' + qs.toString();
    } else {
        opts.headers['Content-Type'] = 'application/x-www-form-urlencoded';
        opts.body = new URLSearchParams({action, ...extra}).toString();
    }
    const res = await fetch(url, opts);
    const data = await res.json();
    if (!res.ok) throw new Error(data.error || 'Request failed.');
    return data;
}
function showError(message) { alert(message); }
async function logoutUser() { if (!confirm('Are you sure you want to log out?')) return; await fetch('auth.php', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'action=logout'}); location.href='front.php'; }
function servingText(windows, id) {
    const w = windows.find(x => Number(x.id) === id);
    return w && w.serving ? w.serving : '—';
}
function renderWindows(windows) {
    [1,2,3].forEach(id => {
        const w = windows.find(x => Number(x.id) === id);
        const num = document.getElementById('window' + id + 'Number');
        if (num) num.textContent = w && w.serving ? w.serving : '—';
        const card = num ? num.closest('.window-card') : null;
        if (card && w) {
            const badge = card.querySelector('.open-badge');
            if (badge) { badge.textContent = Number(w.is_open) ? 'OPEN' : 'CLOSED'; badge.style.background = Number(w.is_open) ? '' : '#777'; }
            const footer = card.querySelector('.window-footer');
            if (footer) footer.lastElementChild.textContent = Number(w.is_open) ? 'Active' : 'Closed';
        }
    });
}
function renderMyQueues(data) {
    const container = document.getElementById('myQueuesContainer');
    const limit = document.getElementById('queueLimit');
    const active = data.my_queues.filter(q => ['Waiting','Processing'].includes(q.status));
    if (limit) limit.textContent = active.length + ' / 2 Queue Numbers';
    if (!container) return;
    if (!data.my_queues.length) {
        container.innerHTML = '<div class="empty-queue"><div class="empty-icon">🎫</div><h3>No Queue Number Yet</h3><p>Click "Get Queue Number" to get your first queue number.</p></div>';
        return;
    }
    container.innerHTML = data.my_queues.map(q => {
        const ahead = Number(q.people_ahead || 0);
        const statusClass = q.status === 'Processing' ? 'serving' : (q.status === 'Completed' ? 'completed' : 'waiting');
        return `<div class="my-queue-card"><div class="my-queue-header"><span class="queue-window">Window ${q.window_id || '—'}</span><h3>Queue #${q.queue_number}</h3><span class="queue-status ${statusClass}">${q.status}</span></div><div class="queue-details"><div class="queue-detail"><span>Queue Number</span><strong>${q.queue_number}</strong></div><div class="queue-detail"><span>Window</span><strong>${q.window_id || '—'}</strong></div><div class="queue-detail"><span>People Ahead</span><strong>${q.status === 'Processing' ? 0 : (q.status === 'Completed' ? 0 : ahead)}</strong></div><div class="queue-detail"><span>Requested</span><strong>${new Date(q.created_at.replace(' ', 'T')).toLocaleString()}</strong></div></div></div>`;
    }).join('');
}
function renderActivity(queues) {
    const tbody = document.getElementById('activityTable');
    if (!tbody) return;
    if (!queues.length) { tbody.innerHTML = '<tr><td colspan="4" class="no-activity">No queue activity yet.</td></tr>'; return; }
    tbody.innerHTML = queues.map(q => `<tr><td>${q.queue_number}</td><td>${q.window_id ? 'Window ' + q.window_id : '—'}</td><td>${new Date(q.created_at.replace(' ', 'T')).toLocaleString()}</td><td>${q.status}</td></tr>`).join('');
}
async function refreshDashboard() {
    try {
        const data = await api('state');
        renderWindows(data.windows);
        renderMyQueues(data);
        renderActivity(data.my_queues);
    } catch (e) { console.error(e); }
}
const getQueueBtn = document.getElementById('getQueueBtn');
if (getQueueBtn) getQueueBtn.addEventListener('click', async () => {
    getQueueBtn.disabled = true;
    try {
        const data = await api('get_queue', 'POST');
        alert('Your queue number is ' + data.queue_number + ' for Window ' + data.window_id + '.');
        await refreshDashboard();
    } catch (e) { showError(e.message); }
    finally { getQueueBtn.disabled = false; }
});

refreshDashboard();
setInterval(refreshDashboard, 3000);
</script>
</body>
</html>

