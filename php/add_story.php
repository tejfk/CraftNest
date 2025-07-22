<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Story - CraftNest</title>
    <link rel="stylesheet" href="../css/seller_profile.css">
</head>
<body>
    <nav class="navbar">
        <a href="seller_home.php" class="logo">Craft Nest</a>
        <div class="navbar-links">
            <a href="seller_home.php" class="nav-link">Home</a>
            <a href="seller_profile.php" class="nav-link active">Profile</a>
            <a href="logout.php" class="nav-link">Logout</a>
        </div>
    </nav>
    <main class="profile-main">
        <h1>Create a New Story</h1>
        <p>This is where you will be able to create and share stories about your craft.</p>
        <p>This feature is coming soon!</p>
        <br>
        <a href="seller_profile.php">← Back to Profile</a>
    </main>
</body>
</html>