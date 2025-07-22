<?php
session_start();
// Authentication: Must be a logged-in seller
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_seller']) || $_SESSION['is_seller'] != 1) {
    header('Location: signin.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Wallet - CraftNest</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cardo:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/seller_profile.css"> <!-- Re-use profile styles for consistency -->
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

  <main class="placeholder-page">
    <h1>My Wallet</h1>
    <p>Your transaction history, balance, and withdrawal options will be displayed here.</p>
    <a href="seller_profile.php" class="back-link">← Back to Profile</a>
  </main>

</body>
</html>