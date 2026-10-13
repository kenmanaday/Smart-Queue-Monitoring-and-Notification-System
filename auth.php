<?php
session_start();
require __DIR__ . '/db.php';

function redirect_with_message(string $url, string $message): never {
    $_SESSION['flash_message'] = $message;
    header('Location: ' . $url);
    exit;
}

$action = $_POST['action'] ?? '';
$role = ($_POST['role'] ?? 'student') === 'admin' ? 'admin' : 'student';

if ($action === 'signup') {
    $name = trim($_POST['full_name'] ?? '');
    $studentId = trim($_POST['student_id'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        redirect_with_message($role === 'admin' ? 'adminsignup.php' : 'signupsystem.php', 'Enter a name, valid email, and password with at least 8 characters.');
    }
    if ($password !== $confirm) {
        redirect_with_message($role === 'admin' ? 'adminsignup.php' : 'signupsystem.php', 'Passwords do not match.');
    }
    if ($role === 'student' && !preg_match('/^[0-9]{2}-[0-9]{5}$/', $studentId)) {
        redirect_with_message('signupsystem.php', 'Student ID must follow the format 00-00000.');
    }
    if ($role === 'admin') $studentId = '';

    try {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (full_name, student_id, email, password_hash, role) VALUES (?, NULLIF(?, ''), ?, ?, ?)");
        $stmt->bind_param('sssss', $name, $studentId, $email, $hash, $role);
        $stmt->execute();
        redirect_with_message($role === 'admin' ? 'adminlogin.php' : 'system.php', 'Account created. Please log in.');
    } catch (mysqli_sql_exception $e) {
        if ((int)$e->getCode() === 1062) {
            redirect_with_message($role === 'admin' ? 'adminsignup.php' : 'signupsystem.php', 'That email or Student ID is already registered.');
        }
        throw $e;
    }
}

if ($action === 'login') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $stmt = $conn->prepare('SELECT id, full_name, student_id, email, password_hash, role FROM users WHERE email = ? AND role = ? LIMIT 1');
    $stmt->bind_param('ss', $email, $role);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if (!$user || !password_verify($password, $user['password_hash'])) {
        redirect_with_message($role === 'admin' ? 'adminlogin.php' : 'system.php', 'Invalid email or password.');
    }
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => (int)$user['id'], 'full_name' => $user['full_name'],
        'student_id' => $user['student_id'], 'email' => $user['email'], 'role' => $user['role']
    ];
    header('Location: ' . ($role === 'admin' ? 'admin.php' : 'interface.php'));
    exit;
}

if ($action === 'logout') {
    $_SESSION = [];
    session_destroy();
    header('Location: front.php');
    exit;
}
http_response_code(400);
echo 'Invalid action.';
