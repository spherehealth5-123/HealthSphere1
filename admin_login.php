<?php
session_start();

// Fixed admin credentials (SET HERE)
$admin_email = "admin@gmail.com";
$admin_password = "Admin@123"; // You can change this

// Get form data
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Check login
if ($email === $admin_email && $password === $admin_password) {
    
    $_SESSION['admin'] = $email;

    // Redirect to admin dashboard
    header("Location: ../adminDashboard.html");
    exit();

} else {
    echo "<script>
            alert('Invalid Admin Email or Password');
            window.location.href='../adminlogin.html';
          </script>";
}
?>