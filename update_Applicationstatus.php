<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../PHPMailer/src/Exception.php';
require __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer/src/SMTP.php';

$con = mysqli_connect("localhost", "root", "", "healthsphere");

// Check if we have the necessary data to update
if (isset($_POST['update_application'])) {
    $app_id = mysqli_real_escape_string($con, $_POST['id']);
    $new_status = mysqli_real_escape_string($con, $_POST['status']); // Expecting 'Hired' or 'Rejected'

    // 1. Fetch the applicant's email and name first
    $user_query = "SELECT name, email FROM applications WHERE id = '$app_id' LIMIT 1";
    $result = mysqli_query($con, $user_query);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        $applicant_name = $user['name'];
        $applicant_email = $user['email'];

        // 2. Update the status in the database
        $update_query = "UPDATE applications SET status = '$new_status' WHERE id = '$app_id'";
        
        if (mysqli_query($con, $update_query)) {
            
            // 3. Send the Status Email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'spherehealth5@gmail.com';
                $mail->Password   = 'abpq ycoi tsfg ckxf'; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;

                $mail->setFrom('spherehealth5@gmail.com', 'HealthSphere HR');
                $mail->addAddress($applicant_email, $applicant_name);

                $mail->isHTML(true);
                
                // Customize content based on status
                if ($new_status == 'Hired') {
                    $mail->Subject = 'Congratulations! Job Offer from HealthSphere';
                    $mail->Body    = "
                        <div style='font-family: Arial; padding: 20px; border: 2px solid #4CAF50;'>
                            <h2 style='color: #4CAF50;'>Congratulations, {$applicant_name}!</h2>
                            <p>We are pleased to inform you that you have been <strong>Hired</strong> for the position at HealthSphere.</p>
                            <p>Our team will contact you shortly with the next steps regarding your onboarding and contract.</p>
                            <p>Welcome to the team!</p>
                        </div>";
                } else {
                    $mail->Subject = 'Application Update - HealthSphere';
                    $mail->Body    = "
                        <div style='font-family: Arial; padding: 20px; border: 1px solid #ccc;'>
                            <h2>Hello, {$applicant_name}</h2>
                            <p>Thank you for your interest in HealthSphere and for the time you spent applying.</p>
                            <p>After careful review, we regret to inform you that we will not be moving forward with your application at this time.</p>
                            <p>We wish you the best of luck in your future endeavors.</p>
                        </div>";
                }

                $mail->send();
                header("Location: view_applications.php?status_updated=success");
                exit();

            } catch (Exception $e) {
                echo "Status updated in DB, but email failed: " . $mail->ErrorInfo;
            }
        }
    } else {
        echo "Applicant not found.";
    }
}
?>