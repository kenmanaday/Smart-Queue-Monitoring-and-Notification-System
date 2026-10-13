<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: ' . ($_SESSION['user']['role'] === 'admin' ? 'admin.php' : 'interface.php'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Queue Monitoring and Notification System</title>
    <link rel="stylesheet" href="front.css">
</head>
<body>
    <nav class="navbar"><div class="system-title">
        <h1>Smart Queue Monitoring &amp; Notification System</h1>
    </div></nav>
    <div class="container">
        <img src="02.png" alt="Cagayan State University Logo" class="logo">
        <h1 class="school-name">Cagayan State University</h1>
        <p class="location">Sanchez Mira, Cagayan</p>
        <a href="system.php" class="role-button">Student</a>
        <a href="adminlogin.php" class="role-button">Administrator</a>
    </div>
</body>
</html>
