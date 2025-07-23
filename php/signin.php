 <?php
// --- UPGRADED LOGIN LOGIC ---
session_start();
require_once './db_connect.php'; // Ensure db_connect.php is in the same directory or adjust path

$error_message = ''; // Variable to hold any error messages

// --- Check for success message from signup page ---
$success_message = '';
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success_message = 'Account created successfully! Please sign in.';
}

// --- Process Login Submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Note: Your form uses 'username' for the input field. We'll treat it as either username or email.
    $username_or_email = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];

    if (empty($username_or_email) || empty($password)) {
        $error_message = 'Username and Password are required.';
    } else {
        // Step 1: Check if the login matches an ADMIN account
        $stmt_admin = $conn->prepare("SELECT admin_id, username, password_hash FROM admins WHERE username = ?");
        $stmt_admin->bind_param("s", $username_or_email);
        $stmt_admin->execute();
        $result_admin = $stmt_admin->get_result();

        if ($admin = $result_admin->fetch_assoc()) {
            if (password_verify($password, $admin['password_hash'])) {
                // Admin login successful
                session_regenerate_id(true);
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_username'] = $admin['username'];
                header('Location: ../admin/admin_dashboard.php'); // Redirect to admin dashboard
                exit();
            }
        }
        $stmt_admin->close();
        
        // Step 2: If not an admin, check for a regular USER account
        // Assuming users can log in with username OR email
        $query = "SELECT user_id, username, password_hash, is_seller FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $username_or_email, $username_or_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password_hash'])) {
                // User login successful
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_seller'] = $user['is_seller'];

                if ($_SESSION['is_seller'] == 1) {
                    header('Location: seller_home.php');
                } else {
                    header('Location: home.php');
                }
                exit();
            }
        }
        
        // Step 3: If neither check passed, set a generic error message
        $error_message = 'Incorrect username or password.';
        
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Craft Nest - Sign In</title>
    <link href="https://fonts.googleapis.com/css2?family=Cardo:wght@400;700&family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/SignIn.css">
    <style>
        /* This CSS is for the new features. You can move it to your SignIn.css file if you prefer. */
        .success-message {
            color: #2F855A;
            background-color: #C6F6D5;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid #9AE6B4;
        }
        .error-message {
            color: #D8000C;
            background-color: #FFD2D2;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid #FFBABA;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="logo-container">
            <div class="craft-text">
                <span class="letter">C</span><span class="letter">r</span><span class="letter">a</span><span class="letter">f</span><span class="letter">t</span>
            </div>
            <div class="nest-text">Nest</div>
        </div>
        <div class="signup-container">
            <h2>Sign In</h2>
           
            <form method="POST" action="signin.php">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit">LOGIN</button>
            </form>
            <div class="login-link">
                <p>Don't have an account? <a href="signup.php">Register</a></p>
            </div>
        </div>
    </div>
</body>
</html>
