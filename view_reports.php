<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin.html");
    exit();
}

// 1. Database Connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "healthsphere";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// 2. Fetch reports
$sql = "SELECT * FROM reports ORDER BY uploaded_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Reports</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f6f9;
            padding: 20px;
        }
        h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #2c3e50;
            color: white;
        }
        tr:hover {
            background: #f1f1f1;
        }
        a {
            text-decoration: none;
            color: blue;
        }
    </style>
</head>
<body>

<h2>Uploaded Reports</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Report ID</th>
        <th>Patient Name</th>
        <th>Email</th>
        <th>Sample Type</th>
        <th>File</th>
        <th>Date</th>
    </tr>

    <?php
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
        <td>{$row['report_id']}</td>
        <td>{$row['patient_name']}</td>
        <td>{$row['patient_email']}</td>
        <td>{$row['sample_type']}</td>
        <td>
            <a href='{$row['file_path']}' target='_blank'>View</a> | 
            <a href='{$row['file_path']}' download>Download</a> | 
        </td>
        <td>{$row['uploaded_at']}</td>
      </tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No reports found</td></tr>";
    }
    ?>

</table>

</body>
</html>

<?php mysqli_close($conn); ?>