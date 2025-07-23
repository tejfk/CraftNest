<?php
// 1. Start the session to gain access to it.
session_start();

// 2. Unset all session variables to clear the user's login state.
$_SESSION = array();

// 3. Destroy the session cookie on the user's browser for added security.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Finally, destroy the session on the server.
session_destroy();

// 5. Redirect the user back to the main sign-in page.
// We need to go up one directory (../) to get out of /admin/ and into /php/.
header("Location: ../php/signin.php");
exit();
?>