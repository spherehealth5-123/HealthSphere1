<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "healthsphere");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all invoices
$sql = "SELECT * FROM invoices ORDER BY pid ASC";
$result = $conn->query($sql);

// Check if query failed
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin - All Invoices</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>All Patient Invoices (Admin View)</h1>
    <?php if ($result->num_rows > 0): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Patient ID</th>
                    <th>Patient Name</th>
                    <th>Email</th>
                    <th>Invoice Number</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['pid']); ?></td>
                        <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['invoice_number']); ?></td>
                        <td>$<?php echo htmlspecialchars(number_format($row['total_amount'], 2)); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No invoices found.</p>
    <?php endif; ?>
</body>
</html>
<?php $conn->close(); ?>
