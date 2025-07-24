<?php
// RESEND OTP SCRIPT

session_start();

// If the user hasn't started the signup process, they shouldn't be here
if (!isset($_SESSION['user_data'])) {
    header("Location: ./signup.php");
    exit();
}

// Include the PHPMailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// Generate a NEW random 6-digit OTP
$otp = rand(100000, 999999);

// Overwrite the old OTP in the session with the new one
$_SESSION['otp'] = $otp;

// Get the user's data from the session
$user_data = $_SESSION['user_data'];
$email = $user_data['email'];
$full_name = $user_data['full_name'];

// --- Re-send the email using the exact same Brevo logic ---
$mail = new PHPMailer(true);

try {
    // ===================================================================
    // === SMTP SERVER SETTINGS FOR BREVO (PROVEN TO BE WORKING) =======
    // ===================================================================
    $mail->isSMTP();
    $mail->Host       = 'smtp-relay.brevo.com';
    $mail->SMTPAuth   = true;
    $mail->Port       = 587;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->AuthType   = 'LOGIN';

    // --- PASTE THE CREDENTIALS THAT GAVE YOU THE "SUCCESS" MESSAGE ---
    $mail->Username   = '92d36e001@smtp-brevo.com';
    $mail->Password   = 'xsmtpsib-cdba77721e6683aec696408ecd4fe635c50c763db1e88b698b6385c1cca3c1c0-OKxDYWAtZgf3UnJk';
    // ---------------------------------------------------------------

    // --- Email Content ---
    $mail->setFrom('lepatanreccajoy@gmail.com', 'Craft Nest');
    $mail->addAddress($email, $full_name);
    $mail->isHTML(true);
    $mail->Subject = 'Your New Craft Nest Verification Code';
    $mail->Body    = "Here is your new One-Time Password (OTP): <b>$otp</b>";
    $mail->AltBody = "Your new Craft Nest One-Time Password (OTP) is: $otp";

    $mail->send();
    
    // Redirect back to the verification page with a success message
    header("Location: ./verify.php?resent=1");
    exit();

} catch (Exception $e) {
    // If it fails, redirect back with an error message
    header("Location: ./verify.php?error=1");
    exit();
}
?>