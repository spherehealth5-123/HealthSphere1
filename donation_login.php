<?php
session_start(); // Start a session to "remember" the user
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "healthsphere");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 1. Fetch the user by email
    $stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $users = $result->fetch_assoc();

        // 2. Verify the hashed password
        if (password_verify($password, $users['password'])) {
            
            // 3. Password is correct! Create session variables
            $_SESSION['user_id'] = $users['id'];
            $_SESSION['user_name'] = $users['full_name'];

            // Redirect to a dashboard or home page
            header("Location: ../donationForm.html");
            exit();

        } else {
            echo "<script>alert('Invalid password!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('No account found with that email!'); window.history.back();</script>";
    }
    $stmt->close();
}
$conn->close();
?>