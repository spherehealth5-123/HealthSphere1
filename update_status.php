<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "healthsphere";

$con = new mysqli($servername, $username, $password, $dbname);
if ($con->connect_error) die("Connection failed: " . $con->connect_error);

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$status = isset($_POST['status']) ? trim($_POST['status']) : '';

if ($id && $status) {
    $sql = "UPDATE appointments SET status=? WHERE id=?";
    $stmt = $con->prepare($sql);
    if (!$stmt) die("Prepare failed: " . $con->error);

    $stmt->bind_param("si", $status, $id);
    if ($stmt->execute()) {
        // Optional: send email via PHPMailer here
        header("Location: view_appointment.php"); // redirect back to table
        exit();
    } else {
        echo "Execute failed: " . $stmt->error;
    }
} else {
    echo "Invalid request";
}

$con->close();
?>