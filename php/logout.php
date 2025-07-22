<?php
// Step 1: To use session variables, you must start the session.
session_start();

// Step 2: Unset all of the session variables.
$_SESSION = array();

// Step 3: Destroy the session completely.
session_destroy();

// Step 4: Redirect the user to the landing page.
//         Change 'signin.php' to your actual landing page if it's different (e.g., 'index.php').
header("Location: landingpage.php");
exit();
?>