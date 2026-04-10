<?php
$con = mysqli_connect("localhost", "root", "", "healthsphere");

echo '<style>
    body { font-family: sans-serif; background: #f4f4f9; padding: 20px; }
    table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; }
    th { background: #0E7490; color: white; padding: 12px; text-align: left; }
    td { padding: 12px; border-bottom: 1px solid #eee; }
    .status { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
    .Pending { background: #FEF3C7; color: #92400E; }
    .Confirmed { background: #D1FAE5; color: #065F46; }
    .Rejected { background: #FEE2E2; color: #991B1B; }
    .btn { text-decoration: none; padding: 6px 12px; border-radius: 4px; color: white; margin-right: 5px; font-size: 13px; }
    .btn-confirm { background: #10B981; }
    .btn-reject { background: #EF4444; }
</style>';

echo "<h2>👨‍⚕️ Job Applications</h2>";

$result = mysqli_query($con, "SELECT * FROM applications ORDER BY id DESC");

if (mysqli_num_rows($result) > 0) {
  // --- START OF TABLE ---
echo "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; width: 100%; font-family: sans-serif;'>";
echo "<tr style='background-color: #0E7490; color: white;'>
        <th>ID</th>
        <th>Name</th>
        <th>Age</th>
        <th>Email</th>
        <th>Gender</th>
        <th>Contact</th>
        <th>Address</th>
        <th>Qualification</th>
        <th>Specialization</th>
        <th>Experience</th>
        <th>CV</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>";

// --- START OF DATA ROWS ---
while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['id'];
    $status = isset($row['status']) ? $row['status'] : 'Pending';
    
    // Using clean concatenation to avoid "unexpected token" errors
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
    echo "<td><strong>" . htmlspecialchars($row['name']) . "</strong></td>";
    echo "<td>" . htmlspecialchars($row['age']) . "</td>";
    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
    echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
    echo "<td>" . htmlspecialchars($row['number']) . "</td>";
    echo "<td>" . htmlspecialchars($row['address']) . "</td>";
    echo "<td>" . htmlspecialchars($row['qualification']) . "</td>";
    echo "<td>" . htmlspecialchars($row['specialization']) . "</td>";
    echo "<td>" . htmlspecialchars($row['experience']) . "</td>";
    echo "<td><a href='uploads/" . htmlspecialchars($row['cv']) . "' target='_blank'>View CV</a></td>";
    echo "<td><span class='status $status'>" . htmlspecialchars($status) . "</span></td>";
    echo "<td>
            <div style='display: flex; gap: 5px;'>
                <a href='handle_actionApplication.php?id=$id&action=Confirmed' class='btn btn-confirm'>Confirm</a>
                <a href='handle_actionApplication.php?id=$id&action=Rejected' class='btn btn-reject'>Reject</a>
            </div>
          </td>";
    echo "</tr>";
}
echo "</table>";

} else {
    echo "No applications found.";
}
?>