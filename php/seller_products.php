<?php
session_start();
// Authentication
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_seller']) || $_SESSION['is_seller'] != 1) {
    header('Location: signin.php');
    exit();
}
require_once 'db_connect.php';

$user_id = $_SESSION['user_id'];
$market_products = [];
$auction_products = [];

// Fetch all products for this specific seller.
// This is the correct way to handle prepared statements to avoid the error.
$query = "SELECT product_id, name, price, stock, start_bid, image_url, is_for_auction FROM products WHERE user_id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result(); // This is now safe to call.
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if ($row['is_for_auction']) {
                $auction_products[] = $row;
            } else {
                $market_products[] = $row;
            }
        }
    }
    // Always close the statement after you're done.
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Products - CraftNest</title>
    <!-- Assuming you have this CSS file from a previous step -->
    <link rel="stylesheet" href="../css/seller_product.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cardo:wght@400;700&family=Poppins:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <a href="seller_home.php" class="logo">
            <div class="logo-container">
                <span class="craft-text">Craft</span><span class="nest-text">Nest</span>
            </div>
        </a>
        <nav>
            <ul>
                <li><a href="seller_home.php">Home</a></li>
                <li><a href="seller_profile.php" class="active">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="main-content">
        <h1 class="page-title">My Posted Products</h1>
        <div class="tabs-container">
            <button id="market-tab" class="tab-btn active">Market</button>
            <button id="auction-tab" class="tab-btn">Auction</button>
        </div>

        <!-- Market Products Grid -->
        <div id="market-grid" class="product-grid">
            <?php if (!empty($market_products)): ?>
                <?php foreach ($market_products as $product): ?>
                    <div class="product-card" data-product-id="<?php echo $product['product_id']; ?>">
                        <div class="product-image-container">
                            <img src="../<?php echo htmlspecialchars($product['image_url'] ?? 'img/placeholder.png'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="price">₱<?php echo number_format($product['price']); ?></p>
                            <p class="stock">Stock: <?php echo $product['stock']; ?></p>
                        </div>
                        <div class="product-actions">
                            <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="action-btn edit-btn">Edit</a>
                            <button class="action-btn remove-btn">Remove</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-products">You have not posted any market products yet.</p>
            <?php endif; ?>
        </div>

        <!-- Auction Products Grid -->
        <div id="auction-grid" class="product-grid" style="display: none;">
             <?php if (!empty($auction_products)): ?>
                <?php foreach ($auction_products as $product): ?>
                    <div class="product-card" data-product-id="<?php echo $product['product_id']; ?>">
                         <div class="product-image-container">
                            <img src="../<?php echo htmlspecialchars($product['image_url'] ?? 'img/placeholder.png'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="price">Start Bid: ₱<?php echo number_format($product['start_bid']); ?></p>
                            <p class="stock">Auction Item</p>
                        </div>
                        <div class="product-actions">
                            <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="action-btn edit-btn">Edit</a>
                            <button class="action-btn remove-btn">Remove</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-products">You have not posted any auction products yet.</p>
            <?php endif; ?>
        </div>
    </main>
    
    <!-- Your existing chat icon and JavaScript for tabs/remove button -->
    <div class="chat-icon"><i class="fa-solid fa-comment-dots"></i></div>
    <script>
        // Your JavaScript for tabs and the remove button goes here
        // No changes needed for the script if it was working before
    </script>
</body>
</html>