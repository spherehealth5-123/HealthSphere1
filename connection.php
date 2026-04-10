<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Load PHPMailer Classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../PHPMailer/src/Exception.php';
require __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer/src/SMTP.php';

$server = "localhost";
$username = "root";
$password = "";
$dbname = "healthsphere";

$con = mysqli_connect($server, $username, $password, $dbname);

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['register'])) {
    // Retrieve and sanitize input (Basic sanitization added)
    $id = mysqli_real_escape_string($con, $_POST['id'] ?? '');
    $name = mysqli_real_escape_string($con, $_POST['name'] ?? '');
    $age = intval($_POST['age'] ?? 0);
    $email = mysqli_real_escape_string($con, $_POST['email'] ?? '');
    $gender = mysqli_real_escape_string($con, $_POST['gender'] ?? '');
    $address = mysqli_real_escape_string($con, $_POST['address'] ?? '');
    $number = mysqli_real_escape_string($con, $_POST['number'] ?? '');
    $qualification = mysqli_real_escape_string($con, $_POST['qualification'] ?? '');
    $specialization = mysqli_real_escape_string($con, $_POST['specialization'] ?? '');
    $experience = mysqli_real_escape_string($con, $_POST['experience'] ?? '');
    $cv = mysqli_real_escape_string($con, $_POST['cv'] ?? '');

    if ($name && $age && $gender && $email) {
        $query = "INSERT INTO `applications`(`id`, `name`, `age`, `email`, `gender`, `address`, `number`, `qualification`, `specialization`, `experience`, `cv`)
                  VALUES ('$id', '$name', '$age', '$email', '$gender', '$address', '$number', '$qualification', '$specialization', '$experience', '$cv')";

        $data = mysqli_query($con, $query);

        if ($data) {
            // 2. Start PHPMailer Logic
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'spherehealth5@gmail.com';
                $mail->Password   = 'abpq ycoi tsfg ckxf'; // Use your 16-char App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;

                // Recipients
                $mail->setFrom('spherehealth5@gmail.com', 'HealthSphere Careers');
                $mail->addAddress($email, $name); 

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Application Received - HealthSphere';
                $mail->Body    = "
                    <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee;'>
                        <h2 style='color: #2c3e50;'>Hello, {$name}!</h2>
                        <p>Thank you for applying to <strong>HealthSphere</strong>.</p>
                        <p>We have successfully received your application for the <strong>{$specialization}</strong> position. Our HR team is currently reviewing your qualifications and experience.</p>
                        <p>We will notify you shortly regarding the status of your application (Hired/Not Hired).</p>
                        <br>
                        <p>Best Regards,<br>HealthSphere Recruitment Team</p>
                    </div>";

                $mail->send();
            } catch (Exception $e) {
                // We log the error but don't stop the redirect so the user knows the DB save worked
                error_log("Mail Error: {$mail->ErrorInfo}");
            }

            // Redirect after email is sent
            header("Location: view_applications.php?msg=success");
            exit(); 
        } else {
            echo "Error: " . mysqli_error($con);
        }
    } else {
        echo "Missing required fields.";
    }
}

mysqli_close($con);
?>