<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "healthsphere");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['search'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Select all reports matching the patient's email
    $sql = "SELECT * FROM reports WHERE patient_email = '$email' ORDER BY upload_date DESC";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Query Error: " . mysqli_error($conn));
    }

    $reports = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $reports[] = $row;
    }

    // Store the email and reports in the session to show on the next page
    $_SESSION['patient_email'] = $email;
    $_SESSION['reports'] = $reports;

    header("Location: showReports.php");
    exit();
}
mysqli_close($conn);
?>