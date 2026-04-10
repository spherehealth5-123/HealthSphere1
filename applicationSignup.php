<?php
// 1. Start Output Buffering - THIS IS CRITICAL
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// 2. Database Connection
$conn = new mysqli("localhost", "root", "", "healthsphere");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 3. Process the Form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    
    $name     = trim($_POST['full_name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    // Validation: Check if passwords match
    if ($password !== $confirm) {
        // Use JavaScript for both the message AND the redirect to keep it consistent
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit();
    }

    // Validation: Ensure fields are not empty
    if (empty($name) || empty($email) || empty($password)) {
        echo "<script>alert('Please fill in all fields.'); window.history.back();</script>";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            
            // CLEAR BUFFER before redirecting
            ob_clean(); 
            
            // OPTION A: Standard PHP Redirect (Works if no output was sent)
            header("Location: ../doctorPanel.html");
            
            // OPTION B: JavaScript Redirect (Failsafe if Header fails)
            echo "<script>window.location.href='../doctorPanel.html';</script>";
            exit(); 
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            echo "<script>alert('Email already registered!'); window.history.back();</script>";
        } else {
            echo "Error: " . $e->getMessage();
        }
    }
}

$conn->close();
ob_end_flush();
?>