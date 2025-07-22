<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_seller']) || $_SESSION['is_seller'] != 1) {
    header('Location: signin.php');
    exit();
}
require_once 'db_connect.php';
$user_id = $_SESSION['user_id'];
$seller_data = null; 
$query = "SELECT user_id, username, profile_picture FROM users WHERE user_id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $seller_data = $result->fetch_assoc();
    $stmt->close(); 
}
$conn->close();
$profile_picture_path = (!empty($seller_data['profile_picture'])) ? $seller_data['profile_picture'] : 'img/default-profile.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Profile - CraftNest</title>
    <link rel="stylesheet" href="../css/seller_profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cardo:wght@400;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
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
        <div class="profile-card">
            <div class="profile-info-area">
                <img src="../<?php echo htmlspecialchars($profile_picture_path); ?>" alt="Profile Picture" class="profile-picture">
                <div class="profile-details">
                    <p class="detail-item"><strong>Seller ID:</strong> <?php echo htmlspecialchars($seller_data['user_id'] ?? 'N/A'); ?></p>
                    <p class="detail-item"><strong>Username:</strong> <?php echo htmlspecialchars($seller_data['username'] ?? 'Seller'); ?></p>
                    <a href="#" id="edit-profile-link" class="edit-profile-link">Edit Profile</a>
                    <button id="switch-account-btn" class="switch-account-btn">Switch Account</button>
                </div>
            </div>
            <div class="seller-actions-grid">
                <div id="add-product-choice-btn" class="action-item"><div class="action-icon-wrapper"><i class="fa-solid fa-plus"></i></div><span class="action-label">Add Products</span></div>
                <a href="seller_products.php" class="action-item"><div class="action-icon-wrapper"><i class="fa-solid fa-store"></i></div><span class="action-label">Posted Products</span></a>
                <a href="seller_wallet.php" class="action-item"><div class="action-icon-wrapper"><i class="fa-solid fa-wallet"></i></div><span class="action-label">Wallet</span></a>
                <a href="seller_feedback.php" class="action-item"><div class="action-icon-wrapper"><i class="fa-solid fa-comment-dots"></i></div><span class="action-label">Feedback</span></a>
                <a href="seller_inbox.php" class="action-item"><div class="action-icon-wrapper"><i class="fa-solid fa-inbox"></i></div><span class="action-label">Inbox</span></a>
                <a href="seller_settings.php" class="action-item"><div class="action-icon-wrapper"><i class="fa-solid fa-gear"></i></div><span class="action-label">Settings</span></a>
            </div>
        </div>
    </main>

    <!-- ========= MODALS SECTION ========= -->

    <div id="add-choice-modal" class="modal">
        <div class="modal-content modal-sm"><span class="close-btn" data-modal-id="add-choice-modal">×</span><h2>What would you like to add?</h2><div class="choice-buttons"><button id="open-market-modal-btn" class="choice-btn"><i class="fa-solid fa-shopping-basket"></i><span>For Market</span></button><button id="open-bidding-modal-btn" class="choice-btn"><i class="fa-solid fa-gavel"></i><span>For Bidding</span></button><a href="add_story.php" class="choice-btn"><i class="fa-solid fa-book-open"></i><span>Add a Story</span></a></div></div>
    </div>
    <div id="add-market-modal" class="modal">
        <div class="modal-content"><span class="close-btn" data-modal-id="add-market-modal">×</span><h2>Add a New Market Product</h2><div id="market-form-message" class="form-message" style="display:none;"></div><form id="add-market-form" enctype="multipart/form-data"><div class="form-group"><label for="product_name">Product Name:</label><input type="text" id="product_name" name="product_name" required></div><div class="form-group"><label for="product_description">Description:</label><textarea id="product_description" name="product_description" rows="3"></textarea></div><div class="form-group"><label for="product_price">Price (₱):</label><input type="number" step="0.01" id="product_price" name="product_price" required></div><div class="form-group"><label for="product_stock">Stock Quantity:</label><input type="number" id="product_stock" name="product_stock" required></div><div class="form-group"><label for="product_image">Product Image:</label><input type="file" id="product_image" name="product_image" accept="image/*" required></div><button type="submit" class="submit-btn">Add Product</button></form></div>
    </div>
    <div id="add-bidding-modal" class="modal">
        <div class="modal-content"><span class="close-btn" data-modal-id="add-bidding-modal">×</span><h2>Add a New Bidding Product</h2><div id="bidding-form-message" class="form-message" style="display:none;"></div><form id="add-bidding-form" enctype="multipart/form-data"><div class="form-group"><label for="bidding_name">Product Name:</label><input type="text" id="bidding_name" name="bidding_name" required></div><div class="form-group"><label for="bidding_description">Description:</label><textarea id="bidding_description" name="bidding_description" rows="3"></textarea></div><div class="form-group"><label for="bidding_start_bid">Starting Bid (₱):</label><input type="number" step="0.01" id="bidding_start_bid" name="bidding_start_bid" required></div><div class="form-group"><label for="bidding_buy_now">Buy Now Price (₱, Optional):</label><input type="number" step="0.01" id="bidding_buy_now" name="bidding_buy_now"></div><div class="form-group"><label for="bidding_end_time">Auction End Time:</label><input type="datetime-local" id="bidding_end_time" name="bidding_end_time" required></div><div class="form-group"><label for="bidding_image">Product Image:</label><input type="file" id="bidding_image" name="bidding_image" accept="image/*" required></div><button type="submit" class="submit-btn">Add Bidding Product</button></form></div>
    </div>
    <div id="edit-profile-modal" class="modal">
        <div class="modal-content modal-sm"><span class="close-btn" data-modal-id="edit-profile-modal">×</span><h2>Edit Profile</h2><div id="edit-form-message" class="form-message" style="display:none;"></div><form id="edit-profile-form" enctype="multipart/form-data"><div class="form-group"><label for="username">Username:</label><input type="text" id="username" name="username" value="<?php echo htmlspecialchars($seller_data['username'] ?? ''); ?>" required></div><div class="form-group"><label for="profile_picture">Change Profile Picture (optional):</label><input type="file" id="profile_picture" name="profile_picture" accept="image/*"></div><button type="submit" class="submit-btn">Save Changes</button></form></div>
    </div>
    <div id="switch-account-modal" class="modal">
        <div class="modal-content modal-sm"><span class="close-btn" data-modal-id="switch-account-modal">×</span><h2>Switch to Buyer Account</h2><p>Please enter your password to confirm.</p><div id="switch-form-message" class="form-message" style="display:none;"></div><form id="switch-account-form"><div class="form-group"><label for="switch_password">Password:</label><input type="password" id="switch_password" name="password" required></div><button type="submit" class="submit-btn">Confirm & Switch</button></form></div>
    </div>

    <script src="../js/seller_profile.js"></script>
</body>
</html>