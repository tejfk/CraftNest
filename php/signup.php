<?php
// FINAL PRODUCTION SIGNUP SCRIPT (Using Brevo for Email OTP)

// Start session to store OTP and user data temporarily
session_start();

// Include the PHPMailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Make sure the paths are correct for your project structure
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// Include your database connection file
require_once './db_connect.php';

// Initialize variables
$full_name = '';
$dob = '';
$email = '';
$phone_number = '';
$username = '';
$is_seller = 0;
$errors = [];
$form_error = ''; // For general errors like email sending failure

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get all form data and sanitize it
    $full_name = trim(htmlspecialchars($_POST['full_name']));
    $dob = $_POST['dob'];
    $email = trim(htmlspecialchars($_POST['email']));
    $phone_number = trim(htmlspecialchars($_POST['phone_number']));
    $username = trim(htmlspecialchars($_POST['username']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $is_seller = isset($_POST['seller']) ? 1 : 0;
    
    // --- Perform all validations ---
    if (empty($full_name)) { $errors['full_name'] = "Name is required."; }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors['email'] = "A valid email is required."; }
    if (!preg_match('/^[a-zA-Z0-9]{4,20}$/', $username)) { $errors['username'] = "Username must be 4-20 alphanumeric characters."; }
    if (!preg_match('/^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) { $errors['password'] = "Password must be at least 8 characters, with an uppercase letter and a number."; }
    if ($password !== $confirm_password) { $errors['confirm_password'] = "Passwords do not match."; }

    // If no basic validation errors, check database for existing user
    if (empty($errors)) {
        $check_query = "SELECT username, email FROM users WHERE username = ? OR email = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if ($row['username'] === $username) { $errors['username'] = "This username is already taken."; }
                if ($row['email'] === $email) { $errors['email'] = "This email address is already registered."; }
            }
        }
        $check_stmt->close();
    }

    // If after all checks, there are still no errors, proceed with sending the OTP
    if (empty($errors)) {
        // Generate a random 6-digit OTP
        $otp = rand(100000, 999999);
        
        // Store the OTP and all user data in the session to use on the next page
        $_SESSION['otp'] = $otp;
        $_SESSION['user_data'] = $_POST;

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
            $mail->Subject = 'Your Craft Nest Verification Code';
            $mail->Body    = "Welcome to Craft Nest! <br><br>Your One-Time Password (OTP) is: <b>$otp</b>";
            $mail->AltBody = "Your Craft Nest One-Time Password (OTP) is: $otp";

            $mail->send();
            
            // Redirect the user to the verification page
            header("Location: ./verify.php");
            exit();

        } catch (Exception $e) {
            // If the email fails to send, show a user-friendly error
            $form_error = "Could not send verification email. Please contact support. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Craft Nest - Sign Up</title>
    <link href="https://fonts.googleapis.com/css2?family=Cardo:wght@400;700&family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/Signup.css">
    <style>
        .password-wrapper { position: relative; width: 100%; }
        .password-wrapper input { width: 100%; padding-right: 45px; }
        .password-wrapper .toggle-password { position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer; color: #888; }
        .error-text { color: #D8000C; font-size: 0.8em; text-align: left; margin-top: 5px; }
        .form-error-text { color: #D8000C; text-align: center; margin-bottom: 15px; padding: 10px; border: 1px solid #D8000C; background-color: #FFBABA; border-radius: 5px;}
    </style>
</head>
<body>
    <div class="main-container">
        <div class="logo-container">
            <div class="craft-text"><span class="letter">C</span><span class="letter">r</span><span class="letter">a</span><span class="letter">f</span><span class="letter">t</span></div>
            <div class="nest-text">Nest</div>
        </div>
        <div class="signup-container">
            <h2>Sign Up</h2>
            <?php if (!empty($form_error)): ?>
                <p class="form-error-text"><?php echo $form_error; ?></p>
            <?php endif; ?>
            <form method="POST" action="signup.php">
                <div class="form-row">
                    <div class="form-group half-width">
                        <input type="text" name="full_name" placeholder="Name" required value="<?php echo htmlspecialchars($full_name); ?>">
                        <?php if (isset($errors['full_name'])): ?><p class="error-text"><?php echo $errors['full_name']; ?></p><?php endif; ?>
                    </div>
                    <div class="form-group half-width">
                        <input type="text" onfocus="(this.type='date')" onblur="(this.type='text')" name="dob" placeholder="Date of Birth" required value="<?php echo htmlspecialchars($dob); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email Address" required value="<?php echo htmlspecialchars($email); ?>">
                    <?php if (isset($errors['email'])): ?><p class="error-text"><?php echo $errors['email']; ?></p><?php endif; ?>
                </div>
                <div class="form-group">
                    <input type="tel" name="phone_number" placeholder="Phone Number" required value="<?php echo htmlspecialchars($phone_number); ?>">
                </div>
                <div class="form-group">
                    <input type="text" name="username" placeholder="Create Username" required value="<?php echo htmlspecialchars($username); ?>">
                    <?php if (isset($errors['username'])): ?><p class="error-text"><?php echo $errors['username']; ?></p><?php endif; ?>
                </div>
                <div class="form-row">
                    <div class="form-group half-width">
                        <div class="password-wrapper">
                            <input type="password" name="password" id="password" placeholder="Create Password" required>
                            <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                        </div>
                        <?php if (isset($errors['password'])): ?><p class="error-text"><?php echo $errors['password']; ?></p><?php endif; ?>
                    </div>
                    <div class="form-group half-width">
                        <div class="password-wrapper">
                            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                            <i class="fas fa-eye toggle-password" id="toggleConfirmPassword"></i>
                        </div>
                        <?php if (isset($errors['confirm_password'])): ?><p class="error-text"><?php echo $errors['confirm_password']; ?></p><?php endif; ?>
                    </div>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" id="seller" name="seller" value="1" <?php if ($is_seller) echo 'checked'; ?>>
                    <label for="seller">Want to be a seller?</label>
                </div>
                <button type="submit">SUBMIT</button>
            </form>
            <div class="login-link">
                <p>Already have an account? <a href="signin.php">Login</a></p>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');
            togglePassword.addEventListener('click', function () {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.classList.toggle('fa-eye-slash');
            });

            const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
            const confirmPassword = document.querySelector('#confirm_password');
            toggleConfirmPassword.addEventListener('click', function () {
                const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmPassword.setAttribute('type', type);
                this.classList.toggle('fa-eye-slash');
            });
        });
    </script>
</body>
</html>
