<?php
$con = mysqli_connect("localhost", "root", "", "healthsphere");

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action']; // Will be 'Confirmed' or 'Rejected'

    $query = "UPDATE applications SET status = '$action' WHERE id = $id";
    
    if (mysqli_query($con, $query)) {
        header("Location: view_applications.php");
        exit();
    }
}
?>