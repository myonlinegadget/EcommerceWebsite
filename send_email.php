<?php
require 'vendor/autoload.php'; // Make sure this path is correct

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
    $mail->SMTPAuth = true;
    $mail->Username = '3110nagesh@gmail.com'; // SMTP username
    $mail->Password = 'test'; // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('3110nagesh@gmail.com', 'Your Name'); // Sender's email address
    $mail->addAddress('3110nagesh@gmail.com'); // Recipient's email address

    // Form data
    $firstName = htmlspecialchars($_POST['first-name']);
    $lastName = htmlspecialchars($_POST['last-name']);
    $email = htmlspecialchars($_POST['email']);
    $mobile = htmlspecialchars($_POST['mobile']);
    $reason = htmlspecialchars($_POST['reason']);
    
    // Content
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = 'Contact Us Form Submission';
    $mail->Body = "
    <html>
    <head>
        <title>Contact Us Form Submission</title>
    </head>
    <body>
        <h2>Contact Us Form Submission</h2>
        <p><strong>First Name:</strong> $firstName</p>
        <p><strong>Last Name:</strong> $lastName</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Mobile:</strong> $mobile</p>
        <p><strong>Reason for Contact:</strong></p>
        <p>$reason</p>
    </body>
    </html>
    ";

    // Send email
    $mail->send();
    header('Location: contact_success.html'); // Redirect to the success page
    exit();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
}
?>
