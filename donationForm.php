<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "healthsphere";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . '/../PHPMailer/src/Exception.php';
require __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['register'])) {
    $donation_type = $_POST['donationType'] ?? '';
    $donor_first_name = $_POST['first_name'] ?? '';
    $donor_last_name = $_POST['last_name'] ?? '';
    $donor_company = $_POST['company'] ?? '';
    $donor_address = $_POST['address'] ?? '';
    $donor_city = $_POST['city'] ?? '';
    $donor_state = $_POST['state'] ?? '';
    $donor_zip_postal_code = $_POST['zip_postal_code'] ?? '';
    $donor_country = $_POST['country'] ?? '';
    $donor_email = $_POST['email'] ?? '';

    $stmt = false;

    switch ($donation_type) {
        case "money":
            $amount = (float) ($_POST["amount"] ?? 0);
            $frequency = $_POST["frequency"] ?? '';

            $query = "INSERT INTO donations 
                (donation_type, amount, frequency, donor_first_name, donor_last_name, donor_company, donor_address, donor_city, donor_state, donor_zip_postal_code, donor_country, donor_email) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($query);
            if (!$stmt) {
                die("Prepare failed (money): " . $conn->error);
            }

            $stmt->bind_param("sdssssssssss",
                $donation_type, $amount, $frequency,
                $donor_first_name, $donor_last_name, $donor_company,
                $donor_address, $donor_city, $donor_state,
                $donor_zip_postal_code, $donor_country, $donor_email
            );
       case "blood":
    $blood_group = $_POST["blood_group"] ?? '';
    $availability_date_blood = $_POST["availability_date_blood"] ?? '';

    // 12 Columns listed below:
    $query = "INSERT INTO donations 
        (donation_type, blood_group, availability_date_blood, donor_first_name, donor_last_name, donor_company, donor_address, donor_city, donor_state, donor_zip_postal_code, donor_country, donor_email) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; // Exactly 12 question marks

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Prepare failed (blood): " . $conn->error);
    }

    // Exactly 12 "s" characters and 12 variables:
    $stmt->bind_param("ssssssssssss",
        $donation_type, 
        $blood_group, 
        $availability_date_blood,
        $donor_first_name, 
        $donor_last_name, 
        $donor_company,
        $donor_address, 
        $donor_city, 
        $donor_state,
        $donor_zip_postal_code, 
        $donor_country, 
        $donor_email
    );
            break;

        default:
            die("❌ Invalid donation type.");
    }

    if ($stmt->execute()) {
        echo "<h2>✅ Thank you, your donation has been recorded!</h2>";
   

        // ---------- PHPMailer send thank you email ----------
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'spherehealth5@gmail.com';
            $mail->Password   = 'abpq ycoi tsfg ckxf'; // app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('spherehealth5@gmail.com', 'HealthSphere Donations');
            $mail->addAddress($donor_email, $donor_first_name . ' ' . $donor_last_name);

            $mail->isHTML(true);
            $mail->Subject = "Thank You for Your Donation!";
            $mail->Body    = "
                <h2>Dear {$donor_first_name} {$donor_last_name},</h2>
                <p>Thank you for your generous <strong>{$donation_type}</strong> donation.</p>
                <p>Your support means a lot to us and will help those in need.</p>
                <br><p><strong>HealthSphere Team</strong></p>";
            $mail->AltBody = "Dear {$donor_first_name}, Thank you for your {$donation_type} donation. - HealthSphere Team";

            $mail->send();
            echo "<p>📧 A thank-you email has been sent to <strong>{$donor_email}</strong>.</p>";
        } catch (Exception $e) {
            echo "<p>⚠ Email could not be sent. Mailer Error: {$mail->ErrorInfo}</p>";
        }
    } else {
        echo "❌ Error executing query: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
