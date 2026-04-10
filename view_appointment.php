<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$con = new mysqli("localhost", "root", "", "healthsphere");
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Styling for the table and buttons
echo '<style>
    body { font-family: "Inter", sans-serif; background-color: #f4f7f6; padding: 20px; }
    .table-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th { background-color: #0E7490; color: white; text-align: left; padding: 12px; }
    td { padding: 12px; border-bottom: 1px solid #ddd; }
    tr:hover { background-color: #f9f9f9; }
    .btn { padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; color: white; text-decoration: none; margin-right: 5px; }
    .btn-confirm { background-color: #10b981; }
    .btn-reject { background-color: #ef4444; }
    .btn:hover { opacity: 0.8; }
    .nav-link { display: inline-block; margin-bottom: 20px; color: #0E7490; text-decoration: none; font-weight: bold; }
</style>';

echo '<a href="../newpatient.html" class="nav-link">← Book New Appointment</a>';
echo '<div class="table-container">';
echo '<h2>📅 Patient Appointments</h2>';

$sql = "SELECT * FROM appointments ORDER BY date ASC, time ASC";
$result = $con->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<table>
            <tr>
                <th>Patient Name</th>
                <th>Contact Info</th>
                <th>Date & Time</th>
                <th>Speciality</th>
                <th>Doctor</th>
                <th>Actions</th>
            </tr>";

 while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $status = $row['status'] ?? 'Pending';
    $rowStyle = "";
    if ($status == 'Confirmed') $rowStyle = "style='background-color: #ecfdf5;'";
    if ($status == 'Rejected') $rowStyle = "style='background-color: #fef2f2;'";

    echo "<tr $rowStyle>
            <td>" . htmlspecialchars($row['patientName']) . "</td>
            <td>" . htmlspecialchars($row['doctor']) . "</td>
            <td>" . $row['date'] . "</td>
            <td><strong>$status</strong></td>
            <td>
                <form method='POST' action='update_status.php' style='display:inline-block;'>
                    <input type='hidden' name='id' value='".$id."'>
                    <input type='hidden' name='status' value='Confirmed'>
                    <button type='submit' class='btn btn-confirm'>Confirm</button>
                </form>

                <form method='POST' action='update_status.php' style='display:inline-block;'>
                    <input type='hidden' name='id' value='".$id."'>
                    <input type='hidden' name='status' value='Rejected'>
                    <button type='submit' class='btn btn-reject'>Reject</button>
                </form>
            </td>
        </tr>";
}
    echo "</table>";
} else {
    echo "<p>No appointments found.</p>";
}
echo '</div>';

$con->close();
?>

<script>
function updateStatus(id, action) {
    if(confirm("Are you sure you want to " + action + " this appointment?")) {
        // For now, this just alerts. 
        // In a real app, you would use fetch() to send this to an 'update_status.php' file.
        alert("Appointment ID " + id + " has been " + action);
        location.reload(); 
    }
}
</script>