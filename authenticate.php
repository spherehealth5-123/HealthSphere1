<?php
session_start();

$admin_username = "hsphere15@gmail.com";
$password_hash = '$2y$10$Ynd6XG9O7pZzB7m3G5.KueS8R7Wn8k.iA9m7oQ6P2y1R3eT4uV5wS'; 

$error = ""; // store error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = filter_var($_POST['username'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if ($username === $admin_username && password_verify($password, $password_hash)) {
        $_SESSION['admin_logged_in'] = true;
        session_regenerate_id(true);

        header("Location: adminDashboard.html");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>