<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

$mail = new PHPMailer(true);

try {
    // 🔹 Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'hsphere15@gmail.com'; // your email
    $mail->Password   = 'lijp kvcz ncla cfyj';    // NOT your real password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // 🔹 Sender & Receiver
    $mail->setFrom('hsphere15@gmail.com', 'HealthSphere');
    $mail->addAddress('receiver@gmail.com');

    // 🔹 Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email';
    $mail->Body    = 'This is a test email from PHPMailer';

    $mail->send();
    echo "Email sent successfully!";
} catch (Exception $e) {
    echo "Error: {$mail->ErrorInfo}";
}
?>