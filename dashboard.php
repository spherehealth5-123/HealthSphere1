<?php
session_start();

// If the session variable isn't set, send them back to login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
?>

<h1>Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
<p>Welcome to your HealthSphere Dashboard.</p>
<a href="logout.php">Logout</a>