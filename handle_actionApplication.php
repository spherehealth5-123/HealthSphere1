<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../PHPMailer/src/Exception.php';
require __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer/src/SMTP.php';

$con = mysqli_connect("localhost", "root", "", "healthsphere");

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = mysqli_real_escape_string($con, $_GET['id']);
    $action = mysqli_real_escape_string($con, $_GET['action']); 

    // 1. Fetch Name and Email
    $query = "SELECT name, email FROM applications WHERE id = '$id'";
    $result = mysqli_query($con, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        $name = $user['name'];
        $email = $user['email'];

        // 2. Update Database Status
        $update = "UPDATE applications SET status = '$action' WHERE id = '$id'";
        
        if (mysqli_query($con, $update)) {
            
            // 3. Configure PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'spherehealth5@gmail.com';
                $mail->Password   = 'abpq ycoi tsfg ckxf'; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;

                $mail->setFrom('spherehealth5@gmail.com', 'HealthSphere HR Team');
                $mail->addAddress($email, $name);
                $mail->isHTML(true);

                // 4. Set Content based on Hired (Confirmed) or Rejected
                if ($action == 'Confirmed') {
                    $mail->Subject = 'Job Offer: Application Confirmed at HealthSphere';
                    $mail->Body    = "
                        <div style='font-family: Arial, sans-serif; border: 1px solid #4CAF50; padding: 20px;'>
                            <h2 style='color: #4CAF50;'>Congratulations, $name!</h2>
                            <p>We are delighted to inform you that you have been <strong>Hired</strong> for the position at HealthSphere.</p>
                            <p>Your qualifications and experience impressed our team, and we are excited to have you join us.</p>
                            <p>An HR representative will follow up with you shortly regarding your start date and contract details.</p>
                            <br>
                            <p>Welcome aboard!</p>
                            <p><strong>HealthSphere HR Team</strong></p>
                        </div>";
                } else {
                    $mail->Subject = 'Update regarding your application at HealthSphere';
                    $mail->Body    = "
                        <div style='font-family: Arial, sans-serif; border: 1px solid #ddd; padding: 20px;'>
                            <h2>Hello $name,</h2>
                            <p>Thank you for giving us the opportunity to review your application for a position at HealthSphere.</p>
                            <p>We regret to inform you that we will not be moving forward with your application at this time.</p>
                            <p>We appreciate your interest in our organization and wish you the very best in your job search and future professional endeavors.</p>
                            <br>
                            <p>Sincerely,</p>
                            <p><strong>HealthSphere HR Team</strong></p>
                        </div>";
                }

                $mail->send();
            } catch (Exception $e) {
                error_log("Mail Error: " . $mail->ErrorInfo);
            }

            // Redirect back to view page
            header("Location: view_applications.php?msg=updated");
            exit();
        }
    }
}
?>