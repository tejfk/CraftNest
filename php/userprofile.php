<?php
// userprofile.php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit();
}

require_once 'db_connect.php';

$user_id = $_SESSION['user_id'];
$user = null;

// Fetch username, email, and profile_picture
$query = "SELECT username, email, profile_picture FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
if ($stmt === false) { die('Database query preparation failed.'); }

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    session_destroy();
    header('Location: signin.php?error=UserNotFound');
    exit();
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile - <?php echo htmlspecialchars($user['username']); ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="../css/homepage.css" rel="stylesheet">
  <link href="../css/userprofile.css" rel="stylesheet">
</head>
<body>
  <header>
    <a href="home.php" class="logo">
      <img src="../img/logo.png" alt="CraftNest Logo">
    </a>
    <nav>
      <ul>
        <li><a href="home.php">Home</a></li>
        <li><a href="auction.php">Auction</a></li>
        <li><a href="product.php">Products</a></li>
        <li><a href="userprofile.php" class="active">Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </nav>
  </header>

  <div class="profile-container">
    <div class="profile-card">
      <div class="profile-header">
        <div class="profile-info">
          <img
            id="display-picture"
            src="../uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>"
            alt="Profile Picture"
            class="profile-image"
          />
          <div class="profile-details">
            <h2 id="display-username" class="profile-name"><?php echo htmlspecialchars($user['username']); ?></h2>
            <p class="profile-id">User ID: <?php echo htmlspecialchars($user_id); ?></p>
            <button id="open-modal-btn" class="edit-profile-btn">Edit Profile</button>
          </div>
        </div>
        <a href="settings.php" class="settings-link">Settings</a>
      </div>
       <div class="profile-actions">
          <div class="action-item" onclick="document.location='cart.php'"><p class="action-label">My Cart</p></div>
          <div class="action-item" onclick="document.location='orders.php?status=receiving'"><p class="action-label">To Receive</p></div>
          <div class="action-item" onclick="document.location='orders.php?status=rating'"><p class="action-label">To Rate</p></div>
          <div class="action-item" onclick="document.location='payment_methods.php'"><p class="action-label">Payment Details</p></div>
        </div>
    </div>
  </div>

  <!-- The hidden modal for editing the profile -->
  <div id="edit-modal" class="modal">
    <div class="modal-content">
      <span class="close-btn">×</span>
      <h2>Edit Your Profile</h2>
      <div id="modal-message-area"></div>
      <form id="edit-profile-form" enctype="multipart/form-data">
        <div class="profile-picture-wrapper">
          <img id="modal-picture-preview" src="../uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture Preview" class="profile-picture-preview">
        </div>
        <div class="form-group">
          <label for="profile_picture">Change Profile Picture</label>
          <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
        </div>
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
          <button type="submit" class="btn-save">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ########## THE FIX IS HERE ########## -->
  <!-- Correct path: Go up one level from 'php/' and then down into 'js/' -->
  <script src="../js/profile_modal.js"></script>

</body>
</html>