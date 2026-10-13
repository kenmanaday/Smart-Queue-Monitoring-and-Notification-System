<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/db.php';

function respond(array $data, int $status = 200): never {
    http_response_code($status);
    echo json_encode($data);
    exit;
}
if (!isset($_SESSION['user'])) respond(['error' => 'Please log in.'], 401);
$user = $_SESSION['user'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'state') {
    $windows = $conn->query("SELECT w.id, w.is_open, q.queue_number AS serving FROM registrar_windows w LEFT JOIN queue_numbers q ON q.id=w.serving_queue_id ORDER BY w.id")->fetch_all(MYSQLI_ASSOC);
    $stmt = $conn->prepare("SELECT id, queue_number, window_id, status, created_at FROM queue_numbers WHERE user_id=? ORDER BY id DESC LIMIT 20");
    $stmt->bind_param('i', $user['id']); $stmt->execute();
    $mine = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    foreach ($mine as &$ticket) {
        $ticket['people_ahead'] = 0;
        if ($ticket['status'] === 'Waiting' && $ticket['window_id'] !== null) {
            $aheadStmt = $conn->prepare("SELECT COUNT(*) c FROM queue_numbers WHERE window_id=? AND status='Waiting' AND queue_number<?");
            $windowId = (int)$ticket['window_id'];
            $number = (int)$ticket['queue_number'];
            $aheadStmt->bind_param('ii', $windowId, $number);
            $aheadStmt->execute();
            $ticket['people_ahead'] = (int)$aheadStmt->get_result()->fetch_assoc()['c'];
        }
    }
    unset($ticket);
    $counts = $conn->query("SELECT COUNT(*) total, COALESCE(SUM(status='Waiting'),0) waiting, COALESCE(SUM(status='Completed'),0) completed FROM queue_numbers")->fetch_assoc();
    respond(['windows'=>$windows, 'my_queues'=>$mine, 'counts'=>$counts]);
}

if ($action === 'get_queue') {
    if ($user['role'] !== 'student') respond(['error'=>'Only student accounts can request queue numbers.'],403);
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) c FROM queue_numbers WHERE user_id=? AND status IN ('Waiting','Processing') FOR UPDATE");
        $stmt->bind_param('i',$user['id']); $stmt->execute();
        $active = (int)$stmt->get_result()->fetch_assoc()['c'];
        if ($active >= 2) { $conn->rollback(); respond(['error'=>'You can have up to 2 active queue numbers.'],409); }
        $openWindows = $conn->query('SELECT id, serving_queue_id FROM registrar_windows WHERE is_open=1 ORDER BY id FOR UPDATE')->fetch_all(MYSQLI_ASSOC);
        if (!$openWindows) { $conn->rollback(); respond(['error'=>'All registrar windows are closed.'],409); }
        // Assign to the open window with the fewest waiting/processing tickets.
        $chosen = null; $best = PHP_INT_MAX;
        foreach ($openWindows as $w) {
            $wid=(int)$w['id'];
            $s=$conn->prepare("SELECT COUNT(*) c FROM queue_numbers WHERE window_id=? AND status IN ('Waiting','Processing')");
            $s->bind_param('i',$wid); $s->execute(); $load=(int)$s->get_result()->fetch_assoc()['c'];
            if ($load < $best) { $best=$load; $chosen=$wid; }
        }
        $max = (int)$conn->query('SELECT COALESCE(MAX(queue_number),0) m FROM queue_numbers')->fetch_assoc()['m'];
        if ($max >= 10000) { $conn->rollback(); respond(['error'=>'The queue number limit (10,000) has been reached.'],409); }
        $number=$max+1;
        $stmt=$conn->prepare("INSERT INTO queue_numbers (queue_number,user_id,window_id,status) VALUES (?,?,?,'Waiting')");
        $stmt->bind_param('iii',$number,$user['id'],$chosen); $stmt->execute();
        $message="Your queue number is $number for Window $chosen.";
        $n=$conn->prepare('INSERT INTO notifications (user_id,message) VALUES (?,?)'); $n->bind_param('is',$user['id'],$message); $n->execute();
        $conn->commit(); respond(['success'=>true,'queue_number'=>$number,'window_id'=>$chosen]);
    } catch (Throwable $e) { $conn->rollback(); respond(['error'=>'Could not create a queue number. Please try again.'],500); }
}

if ($user['role'] !== 'admin') respond(['error'=>'Administrator access required.'],403);
if ($action === 'next') {
    $windowId=(int)($_POST['window_id'] ?? 0);
    if ($windowId < 1 || $windowId > 3) respond(['error'=>'Invalid window.'],400);
    $conn->begin_transaction();
    try {
        $stmt=$conn->prepare('SELECT is_open, serving_queue_id FROM registrar_windows WHERE id=? FOR UPDATE');
        $stmt->bind_param('i',$windowId); $stmt->execute(); $w=$stmt->get_result()->fetch_assoc();
        if (!$w || !(int)$w['is_open']) { $conn->rollback(); respond(['error'=>'This window is closed.'],409); }
        if ($w['serving_queue_id']) {
            $done=$conn->prepare("UPDATE queue_numbers SET status='Completed', completed_at=NOW() WHERE id=? AND status='Processing'");
            $done->bind_param('i',$w['serving_queue_id']); $done->execute();
        }
        $next=$conn->prepare("SELECT id,user_id,queue_number FROM queue_numbers WHERE window_id=? AND status='Waiting' ORDER BY queue_number LIMIT 1 FOR UPDATE");
        $next->bind_param('i',$windowId); $next->execute(); $ticket=$next->get_result()->fetch_assoc();
        $ticketId=$ticket ? (int)$ticket['id'] : null;
        if ($ticket) {
            $upd=$conn->prepare("UPDATE queue_numbers SET status='Processing' WHERE id=?"); $upd->bind_param('i',$ticketId); $upd->execute();
            $msg='Your queue number '.$ticket['queue_number'].' is now being served at Window '.$windowId.'.';
            $n=$conn->prepare('INSERT INTO notifications (user_id,message) VALUES (?,?)'); $n->bind_param('is',$ticket['user_id'],$msg); $n->execute();
        }
        $updW=$conn->prepare('UPDATE registrar_windows SET serving_queue_id=? WHERE id=?'); $updW->bind_param('ii',$ticketId,$windowId); $updW->execute();
        $conn->commit(); respond(['success'=>true,'serving'=>$ticket['queue_number'] ?? null]);
    } catch (Throwable $e) { $conn->rollback(); respond(['error'=>'Could not advance this window.'],500); }
}
if ($action === 'toggle_window') {
    $windowId=(int)($_POST['window_id'] ?? 0);
    if ($windowId<1 || $windowId>3) respond(['error'=>'Invalid window.'],400);
    $stmt=$conn->prepare('UPDATE registrar_windows SET is_open=1-is_open WHERE id=?'); $stmt->bind_param('i',$windowId); $stmt->execute();
    respond(['success'=>true]);
}
if ($action === 'queue_list') {
    $rows=$conn->query('SELECT q.queue_number,q.window_id,q.status,q.created_at,u.full_name,u.student_id FROM queue_numbers q JOIN users u ON u.id=q.user_id ORDER BY q.queue_number DESC LIMIT 1000')->fetch_all(MYSQLI_ASSOC);
    $counts=$conn->query("SELECT COUNT(*) total, COALESCE(SUM(status='Waiting'),0) waiting, COALESCE(SUM(status='Completed'),0) completed FROM queue_numbers")->fetch_assoc();
    respond(['rows'=>$rows,'counts'=>$counts]);
}
if ($action === 'reset') {
    $conn->begin_transaction();
    try {
        $conn->query('DELETE FROM notifications');
        $conn->query('DELETE FROM queue_numbers');
        $conn->query('ALTER TABLE queue_numbers AUTO_INCREMENT=1');
        $conn->query('UPDATE registrar_windows SET serving_queue_id=NULL,is_open=1');
        $conn->commit(); respond(['success'=>true]);
    } catch(Throwable $e) { $conn->rollback(); respond(['error'=>'Reset failed.'],500); }
}
respond(['error'=>'Unknown action.'],400);
