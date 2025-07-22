<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Craft Nest - Sign Up</title>
    <link href="https://fonts.googleapis.com/css2?family=Cardo:wght@400;700&family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for the eye icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Make sure the path to your CSS is correct -->
    <link rel="stylesheet" href="../css/Signup.css">
    <style>
        /* This CSS is for the new features. You can move it to your Signup.css file if you prefer. */
        .password-wrapper {
            position: relative;
            width: 100%; /* Ensure wrapper takes full width of its container */
        }
        
        .password-wrapper input {
            width: 100%; /* Make input fill the wrapper */
            padding-right: 45px; /* Add space for the icon */
        }

        .password-wrapper .toggle-password {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
        }

        .error-text {
            color: #D8000C; /* Red color for error text */
            font-size: 0.8em;
            text-align: left; /* Align error text to the left */
            margin-top: 5px;
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
            <h2>Sign Up</h2>
            <?php
// ADD THESE THREE LINES
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once './db_connect.php';

// ... the rest of your code ...

            // Initialize variables to hold form data and errors
            $full_name = '';
            $dob = '';
            $email = '';
            $phone_number = '';
            $username = '';
            $is_seller = 0; // Default to not a seller
            $errors = []; // Use an associative array for errors

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // --- Get all form data and store them to repopulate the form ---
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
                    while ($row = $result->fetch_assoc()) {
                        if ($row['username'] === $username) {
                            $errors['username'] = "This username is already taken.";
                        }
                        if ($row['email'] === $email) {
                            $errors['email'] = "This email address is already registered.";
                        }
                    }
                    $check_stmt->close();
                }

                // If after all checks, there are still no errors, proceed with account creation
                if (empty($errors)) {
                    $password_hash = password_hash($password, PASSWORD_BCRYPT);

                    $query = "INSERT INTO users (full_name, username, email, dob, phone_number, password_hash, is_seller) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ssssssi", $full_name, $username, $email, $dob, $phone_number, $password_hash, $is_seller);

                    if ($stmt->execute()) {
                        if ($is_seller) {
                            $user_id = $conn->insert_id;
                            $seller_query = "INSERT INTO sellers (seller_id, user_id, store_name) VALUES (?, ?, ?)";
                            $seller_stmt = $conn->prepare($seller_query);
                            $store_name = $full_name . "'s Store"; // Default store name
                            $seller_stmt->bind_param("iis", $user_id, $user_id, $store_name);
                            $seller_stmt->execute();
                            $seller_stmt->close();
                        }
                        // --- REDIRECT TO SIGN IN PAGE ON SUCCESS ---
                        header("Location: ./signin.php?success=1");
                        exit();
                    } else {
                        // Generic error if database insertion fails
                        echo '<p class="error-text" style="text-align: center; margin-bottom: 15px;">Error creating account. Please try again.</p>';
                    }
                    $stmt->close();
                }
            }
            $conn->close();
            ?>
            
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