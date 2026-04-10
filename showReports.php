<?php
session_start();

// 1. Security Check: If no search was performed, send them back to the search page
if (!isset($_SESSION['reports']) || !isset($_SESSION['patient_email'])) {
    header("Location: ../patient_download.html"); // Adjust this to your actual HTML filename
    exit();
}

$reports = $_SESSION['reports'];
$patient_email = $_SESSION['patient_email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Medical Reports</title>
    <link rel="stylesheet" href="../css/patient_reports.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; display: flex; justify-content: center; padding: 40px; }
        .report-container { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 800px; }
        h1 { color: #333; font-size: 24px; margin-bottom: 10px; }
        .email-display { color: #666; margin-bottom: 25px; font-style: italic; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #007bff; color: white; text-align: left; padding: 12px; }
        td { padding: 12px; border-bottom: 1px solid #eee; color: #444; }
        tr:hover { background-color: #f9f9f9; }
        .btn-download { background-color: #28a745; color: white; padding: 8px 16px; text-decoration: none; border-radius: 5px; font-size: 14px; transition: 0.3s; }
        .btn-download:hover { background-color: #218838; }
        .no-data { text-align: center; padding: 40px; color: #888; }
        .back-link { display: inline-block; margin-top: 20px; color: #007bff; text-decoration: none; }
    </style>
</head>
<body>

<div class="report-container">
    <h1>Medical Test Reports</h1>
    <p class="email-display">Showing results for: <strong><?php echo htmlspecialchars($patient_email); ?></strong></p>

    <?php if (empty($reports)): ?>
        <div class="no-data">
            <p>No reports found for this email address. Please contact the clinic if you believe this is an error.</p>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Report ID</th>
                    <th>Sample Type</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                <tr>
                    <td><?php echo date('d M Y', strtotime($report['upload_date'])); ?></td>
                    <td><?php echo htmlspecialchars($report['report_id']); ?></td>
                    <td><?php echo htmlspecialchars($report['sample_type']); ?></td>
                    <td>
                        <a href="<?php echo htmlspecialchars($report['file_path']); ?>" 
                           class="btn-download" 
                           download>
                           Download PDF
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="../patient_report.html" class="back-link">← Search another email</a>
</div>

</body>
</html>