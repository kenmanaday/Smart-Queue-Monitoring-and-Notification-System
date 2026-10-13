<?php
session_start();
if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') { header('Location: admin.php'); exit; }
$message = $_SESSION['flash_message'] ?? '';
unset($_SESSION['flash_message']);
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Smart Queue Monitoring and Notification System - Administrator Login</title><link rel="stylesheet" href="adminlogin.css"></head>
<body><nav class="navbar"><div class="navbar-container"><div class="brand"><img src="csu.png" alt="CSU Logo"><div class="csu"><h1>Smart Queue Monitoring &amp; Notification System</h1></div></div></div></nav>
<div class="main-container"><div class="logo-section"><img src="02.png" alt="CSU Logo" class="logo"></div>
<div class="login-card"><h2><span style="color:red;">Log</span>In</h2><br><br>
<?php if ($message): ?><p class="form-message"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
<form action="auth.php" method="post"><input type="hidden" name="action" value="login"><input type="hidden" name="role" value="admin">
<input type="email" name="email" placeholder="Administrator Email" autocomplete="email" required>
<input type="password" name="password" placeholder="Password" autocomplete="current-password" required>
<div class="options"><label><input type="checkbox" name="keep_logged_in" value="1"> Keep me logged in</label><a href="#" onclick="alert('Please contact the system owner to reset your password.'); return false;">Forgot password</a></div>
<button type="submit" class="role-button">Log In</button></form>
<p class="signup-text">Don't have an account? <a href="adminsignup.php">Sign up</a></p>
<p class="continue">Or continue with</p><div class="social-buttons"><button class="google" type="button" onclick="alert('Google sign-in is not configured.');">G</button></div>
</div></div></body></html>
