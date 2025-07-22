<?php
// Start the session to access session variables
session_start();
require_once 'db_connect.php';

// PROTECT THE PAGE
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit();
}

// FETCH AUCTION ITEMS FROM THE DATABASE
$auction_items = [];
// Assuming your products table has a 'buy_now_price' column. If not, you can remove it.
$query = "SELECT product_id, name, description, start_bid, image_url, auction_end_time, buy_now_price FROM products WHERE is_for_auction = 1 AND status = 'active'";

// FIXED: The line below had a critical typo ("$result = a") which is now corrected.
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $auction_items[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Auction - CraftNest</title>
  <link rel="stylesheet" href="../css/auction.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
  <header>
    <div class="logo">
      <a href="home.php"><img src="../img/logo.png" alt="CraftNest Logo" style="height: 50px;"></a>
    </div>
    <nav>
      <ul>
        <li><a href="home.php">Home</a></li>
        <li><a href="auction.php" class="active">Auction</a></li>
        <li><a href="Product.php">Products</a></li>
        <li><a href="userprofile.php">Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </nav>
    <div class="cart">
      <a href="cart.php">🛒</a>
    </div>
  </header>

  <div class="product-gallery">
    <!-- This section now dynamically generates product previews from the database -->
    <?php if (!empty($auction_items)): ?>
      <?php foreach ($auction_items as $item): ?>
        <div class="product-preview" 
             data-id="<?php echo htmlspecialchars($item['product_id']); ?>"
             data-name="<?php echo htmlspecialchars($item['name']); ?>" 
             data-image="../<?php echo htmlspecialchars($item['image_url']); ?>" 
             data-description="<?php echo htmlspecialchars($item['description']); ?>" 
             data-start-bid="<?php echo htmlspecialchars($item['start_bid']); ?>"
             data-buy-now="<?php echo htmlspecialchars($item['buy_now_price'] ?? ($item['start_bid'] * 2)); ?>"
             data-end-time="<?php echo htmlspecialchars($item['auction_end_time']); ?>">
          <img src="../<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" />
          <h3><?php echo htmlspecialchars($item['name']); ?></h3>
          <p>Start Bid: ₱<?php echo number_format($item['start_bid']); ?></p>
          <button type="button" class="join-bidding-btn">Join Bidding</button>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>There are currently no items up for auction.</p>
    <?php endif; ?>
  </div>

  <!-- The modal structure remains the same, ready to be filled by JavaScript -->
  <div id="product-modal" class="modal">
    <div class="modal-content">
      <span class="close-modal">×</span>
      <div class="modal-grid">
        <div class="modal-left">
          <img id="modal-image" src="" alt="Product Image" />
        </div>
        <div class="modal-right">
          <h2 id="modal-title">Product</h2>
          <p class="sub">Limited Edition</p>
          <div class="start-bid-container">
              <span class="start-bid-label">Start Bid</span>
              <span id="modal-start-bid" class="start-bid-price">₱0</span>
          </div>
          <div class="bid-controls">
            <button id="increase-bid">+</button>
            <input type="text" id="current-bid" value="₱0" readonly />
            <button id="decrease-bid">-</button>
          </div>
          <button class="place-bid-button">Start Bid</button>
        </div>
      </div>

      <div class="modal-description-section">
        <h3>Description</h3>
        <p id="modal-description">Description will load here.</p>
        <div class="details-grid">
            <div class="detail-item">
                <span class="detail-label">Auction Ends In:</span>
                <span id="countdown-timer" class="detail-value">--:--:--</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Delivery Option:</span>
                <span class="detail-value">J&T, Lalamove, DHL Express</span>
            </div>
        </div>
        <div class="buy-now-section">
            <div class="buy-now-price-wrapper">
                <p id="modal-buy-now-price" class="buy-now-price">₱0</p>
                <button class="buy-now-button">Buy Now</button>
            </div>
            <p class="buy-now-text">Or you can buy it now and the item will be yours.</p>
        </div>
      </div>
    </div>
  </div>

  <script src="../js/auction.js"></script>
</body>
</html>