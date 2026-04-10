<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "healthsphere");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo '<div style="padding:10px; background-color:#f2f2f2; font-family: sans-serif;">
    <a href="../donationForm.html" style="text-decoration:none; color:#007bff;">← Submit New Donation</a>
</div>';

echo '<h2 style="font-family: sans-serif; margin-left: 10px;">📋 List of Donations</h2>';

// FIX: Changed ORDER BY donor_id to ORDER BY id
$sql = "SELECT * FROM donations ORDER BY id ASC";
$result = $conn->query($sql);

if ($result === false) {
    die("Query failed: " . htmlspecialchars($conn->error));
}

if ($result->num_rows > 0) {
    echo "<p style='margin-left:10px;'>Total Donations: " . $result->num_rows . "</p>";

    echo "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; margin: 10px; font-family: sans-serif; font-size: 14px;'>
            <tr style='background-color: #0E7490; color: white;'>
                <th>Type</th>
                <th>Amount</th>
                <th>Frequency</th>
                <th>Blood Group</th>
                <th>Availability</th>
                <th>Hospital</th>
                <th>Donor Name</th>
                <th>Email</th>
                <th>Country</th>
            </tr>";

    while ($row = $result->fetch_assoc()) {
        // Logic to pick whichever date is available
        $availability = !empty($row['availability_date']) ? $row['availability_date'] : ($row['availability_date_blood'] ?? '-');
        
        // Handle names safely
        $firstName = $row['donor_first_name'] ?? '';
        $lastName = $row['donor_last_name'] ?? '';
        $name = trim($firstName . ' ' . $lastName);

        // Format amount if it's money
        $displayAmount = ($row['donation_type'] === 'money' && !empty($row['amount'])) 
                         ? '$' . number_format($row['amount'], 2) 
                         : '-';

        echo "<tr>
            <td style='text-transform: capitalize;'><strong>" . htmlspecialchars($row['donation_type'] ?? '-') . "</strong></td>
            <td>" . htmlspecialchars($displayAmount) . "</td>
            <td>" . htmlspecialchars($row['frequency'] ?? '-') . "</td>
            <td>" . htmlspecialchars($row['blood_group'] ?? '-') . "</td>
            <td>" . htmlspecialchars($availability) . "</td>
            <td>" . htmlspecialchars($row['hospital'] ?? '-') . "</td>
            <td>" . htmlspecialchars($row['heart_condition'] ?? '-') . "</td>
            <td>" . htmlspecialchars($name) . "</td>
            <td>" . htmlspecialchars($row['donor_email'] ?? '-') . "</td>
            <td>" . htmlspecialchars($row['donor_country'] ?? '-') . "</td>
        </tr>";
    }

    echo "</table>";
} else {
    echo "<p style='margin-left: 10px;'>No donations found in the database.</p>";
}

$conn->close();
?>