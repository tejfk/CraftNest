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
            <?php
            session_start();
            require_once './db_connect.php';

            // --- Check for success message from signup page ---
            if (isset($_GET['success']) && $_GET['success'] == 1) {
                echo '<p class="success-message">Account created successfully! Please sign in.</p>';
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $username = htmlspecialchars($_POST['username']);
                $password = $_POST['password'];

                if (empty($username) || empty($password)) {
                     echo '<p class="error-message">Username and Password are required.</p>';
                } else {
                    $query = "SELECT user_id, username, password_hash, is_seller FROM users WHERE username = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($user = $result->fetch_assoc()) {
                        if (password_verify($password, $user['password_hash'])) {
                            // Password is correct, set session variables
                            $_SESSION['user_id'] = $user['user_id'];
                            $_SESSION['username'] = $user['username'];
                            $_SESSION['is_seller'] = $user['is_seller'];

                            // Role-based redirection
                            if ($_SESSION['is_seller'] == 1) {
                                header('Location: seller_home.php');
                            } else {
                                header('Location: home.php');
                            }
                            exit(); // Always exit after a header redirect
                        } else {
                            echo '<p class="error-message">Incorrect password.</p>';
                        }
                    } else {
                        echo '<p class="error-message">Username not found.</p>';
                    }
                    $stmt->close();
                }
                $conn->close();
            }
            ?>
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