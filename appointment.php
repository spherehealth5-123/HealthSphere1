<?php
// Show all errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Place use statements at the top of the file, outside of any blocks.
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// DB config
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "healthsphere";

// Connect
$con = new mysqli($servername, $username, $password, $dbname);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Check request method and if form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['newAppointment'])) {

    // Get and sanitize inputs
    $id = isset($_POST['id']) && $_POST['id'] !== '' ? intval($_POST['id']) : null;
    $patientName = trim($_POST['patientName']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $speciality = $_POST['speciality'];
    $doctor = $_POST['doctor'];

    // Basic required field check
    if (!$patientName || !$phone || !$email || !$date || !$time || !$speciality || !$doctor) {
        die("Please fill in all required fields.");
    }

    // Prepare SQL
    if ($id !== null) {
        $sql = "INSERT INTO appointments (id, patientName, phone, email, date, time, speciality, doctor)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("isssssss", $id, $patientName, $phone, $email, $date, $time, $speciality, $doctor);
    } else {
        $sql = "INSERT INTO appointments (patientName, phone, email, date, time, speciality, doctor)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("sssssss", $patientName, $phone, $email, $date, $time, $speciality, $doctor);
    }

    // Execute
    if ($stmt->execute()) {
        echo "✅ Appointment booked successfully!";

        // Include PHPMailer files here.
        // Make sure to correct the path if necessary.
        require '../PHPMailer/src/Exception.php';
        require '../PHPMailer/src/PHPMailer.php';
        require '../PHPMailer/src/SMTP.php';

        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'spherehealth5@gmail.com';
            $mail->Password   = 'abpq ycoi tsfg ckxf';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Correct constant for TLS
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('spherehealth5@gmail.com', 'Health Sphere');
            $mail->addAddress($email, $patientName);

            // Content
            $mail->isHTML(false);
            $mail->Subject = 'Appointment Confirmation: ' . $speciality;
            $mail->Body    = "Dear " . $patientName . ",\n\nYour appointment with Dr. " . $doctor . " on " . $date . " at " . $time . " has been confirmed.";

            $mail->send();
            echo ' Message has been sent.';
        } catch (Exception $e) {
            echo " Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "❌ No data received or form not submitted properly.";
}

$con->close();
?>