<?php
// OTP VERIFICATION PAGE (with Resend functionality)

session_start();

// If the user hasn't started the signup process, redirect them away
if (!isset($_SESSION['otp']) || !isset($_SESSION['user_data'])) {
    header("Location: ./signup.php");
    exit();
}

require_once './db_connect.php';

$error_message = ''; // To hold any error messages
$success_message = ''; // To hold the "resent" success message

// Check for a success message from the resend script
if (isset($_GET['resent']) && $_GET['resent'] == 1) {
    $success_message = "A new code has been sent to your email address.";
}

// Check for an error message from the resend script
if (isset($_GET['error']) && $_GET['error'] == 1) {
    $error_message = "Could not resend the code. Please try again in a moment.";
}

// Check if the user submitted the OTP form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted_otp = $_POST['otp'];
    $stored_otp = $_SESSION['otp'];

    if ($submitted_otp == $stored_otp) {
        // --- OTP IS CORRECT ---
        $user_data = $_SESSION['user_data'];
        
        $full_name = trim(htmlspecialchars($user_data['full_name']));
        $username = trim(htmlspecialchars($user_data['username']));
        $email = trim(htmlspecialchars($user_data['email']));
        $dob = $user_data['dob'];
        $phone_number = trim(htmlspecialchars($user_data['phone_number']));
        $is_seller = isset($user_data['seller']) ? 1 : 0;
        
        $password_hash = password_hash($user_data['password'], PASSWORD_BCRYPT);

        // Insert the new user into the database
        $query = "INSERT INTO users (full_name, username, email, dob, phone_number, password_hash, is_seller) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssi", $full_name, $username, $email, $dob, $phone_number, $password_hash, $is_seller);

        if ($stmt->execute()) {
            if ($is_seller) {
                $user_id = $conn->insert_id;
                $seller_query = "INSERT INTO sellers (seller_id, user_id, store_name) VALUES (?, ?, ?)";
                $seller_stmt = $conn->prepare($seller_query);
                $store_name = $full_name . "'s Store";
                $seller_stmt->bind_param("iis", $user_id, $user_id, $store_name);
                $seller_stmt->execute();
                $seller_stmt->close();
            }
            
            session_unset();
            session_destroy();
            
            header("Location: ./signin.php?success=1");
            exit();
        } else {
            $error_message = "Database error: Could not create your account. Please try again later.";
        }
        $stmt->close();

    } else {
        // --- OTP IS INCORRECT ---
        $error_message = "The OTP you entered is incorrect. Please try again.";
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Craft Nest - Verify Account</title>
    <link href="https://fonts.googleapis.com/css2?family=Cardo:wght@400;700&family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/Signup.css">
    <style>
        .error-text { color: #D8000C; font-size: 0.9em; text-align: center; margin-top: 15px; }
        .success-text { color: #4F8A10; font-size: 0.9em; text-align: center; margin-bottom: 15px; padding: 10px; border: 1px solid #4F8A10; background-color: #DFF2BF; border-radius: 5px;}
        .info-text { color: #333; text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="logo-container">
            <div class="craft-text"><span class="letter">C</span><span class="letter">r</span><span class="letter">a</span><span class="letter">f</span><span class="letter">t</span></div>
            <div class="nest-text">Nest</div>
        </div>
        <div class="signup-container">
            <h2>Verify Your Email</h2>

            <!-- Display the success message here -->
            <?php if (!empty($success_message)): ?>
                <p class="success-text"><?php echo $success_message; ?></p>
            <?php endif; ?>

            <p class="info-text">We've sent a verification code to your email address. Please enter it below.</p>
            
            <form method="POST" action="verify.php">
                <div class="form-group">
                    <input type="text" name="otp" placeholder="Enter 6-Digit OTP" required autofocus>
                </div>
                
                <?php if (!empty($error_message)): ?>
                    <p class="error-text"><?php echo $error_message; ?></p>
                <?php endif; ?>

                <button type="submit">VERIFY & CREATE ACCOUNT</button>
            </form>
            <div class="login-link">
                <!-- This is the updated link -->
                <p>Didn't get a code? <a href="resend_otp.php">Resend code</a></p>
            </div>
        </div>
    </div>
</body>
</html>