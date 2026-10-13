<?php
session_start();
$message = $_SESSION['flash_message'] ?? '';
unset($_SESSION['flash_message']);
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Smart Queue Monitoring and Notification System - Administrator Sign Up</title><link rel="stylesheet" href="adminsignup.css"></head>
<body><div class="main-container"><div class="logo-section"><img src="02.png" alt="CSU Logo" class="logo"></div>
<div class="signup-card"><h2><span style="color:red;">Sign</span>Up</h2>
<?php if ($message): ?><p class="form-message"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
<form action="auth.php" method="post"><input type="hidden" name="action" value="signup"><input type="hidden" name="role" value="admin">
<input type="text" name="full_name" placeholder="Full Name" autocomplete="name" required>
<input type="email" name="email" placeholder="Gmail / Administrator Email" autocomplete="email" required>
<input type="password" name="password" placeholder="Password (at least 8 characters)" minlength="8" autocomplete="new-password" required>
<input type="password" name="confirm_password" placeholder="Confirm Password" minlength="8" autocomplete="new-password" required>
<button type="submit">Create Account</button></form>
<p class="signin-text">Already have an account? <a href="adminlogin.php">LogIn</a></p>
<p class="continue">Or continue with</p><div class="social-buttons"><button class="google" type="button" onclick="alert('Google sign-up is not configured.');">G</button></div>
</div></div></body></html>
