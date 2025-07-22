<?php
// Start the session at the very beginning
session_start();

// --- LOGIC TO HANDLE CART ACTIONS ---

// Initialize cart and auction arrays in the session if they don't exist
$_SESSION['cart'] = $_SESSION['cart'] ?? [];
$_SESSION['auction'] = $_SESSION['auction'] ?? [];

// Check if an action is being performed (e.g., update, delete, clear)
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = $_GET['id'] ?? null; // The index of the item in the cart array

    switch ($action) {
        case 'update':
            // Ensure the item exists before trying to update it
            if (isset($_SESSION['cart'][$id])) {
                $change = (int)($_GET['change'] ?? 0);
                $newQuantity = $_SESSION['cart'][$id]['quantity'] + $change;

                // Remove item if quantity is 0 or less
                if ($newQuantity <= 0) {
                    unset($_SESSION['cart'][$id]);
                } 
                // Don't allow quantity to exceed stock
                else if (isset($_SESSION['cart'][$id]['stock']) && $newQuantity > $_SESSION['cart'][$id]['stock']) {
                    // Optional: set an error message
                    // $_SESSION['error_message'] = "Cannot add more than available stock.";
                } 
                // Otherwise, update the quantity
                else {
                    $_SESSION['cart'][$id]['quantity'] = $newQuantity;
                }
            }
            break;

        case 'delete':
            // Remove a specific item from the cart
            if (isset($_SESSION['cart'][$id])) {
                unset($_SESSION['cart'][$id]);
            }
            break;

        case 'clear_cart':
            // Clear the entire cart
            $_SESSION['cart'] = [];
            break;

        case 'clear_auction':
             // Clear the entire auction list
            $_SESSION['auction'] = [];
            break;
    }

    // Re-index the array to prevent gaps from 'unset'
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    $_SESSION['auction'] = array_values($_SESSION['auction']);

    // Redirect back to cart.php to prevent re-submission on refresh
    header('Location: cart.php');
    exit();
}

// Assign session data to variables for easier use in HTML
$cart_items = $_SESSION['cart'];
$auction_items = $_SESSION['auction'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Craft Nest - Cart</title>
  <link href="https://fonts.googleapis.com/css2?family=Cardo:wght@400;700&family=Poppins:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/cart.css">
</head>
<body>
  <header>
    <div class="logo-container">
      <div class="craft-text">
        <span class="letter">C</span><span class="letter">r</span><span class="letter">a</span><span class="letter">f</span><span class="letter">t</span>
      </div>
      <div class="nest-text">Nest</div>
    </div>
    <nav>
      <ul>
        <li><a href="home.php">Home</a></li>
        <li><a href="auction.php">Auction</a></li>
        <li><a href="Product.php">Products</a></li>
        <li><a href="userprofile.php">Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <div class="tab-content">
      <div class="tabs">
        <button class="tab active" onclick="showSection('cart')">My Cart</button>
        <button class="tab" onclick="showSection('auction')">Auction</button>
      </div>

      <div class="slider-container">
        <div class="slider">
          <!-- MY CART SECTION - Powered by PHP Session -->
          <div id="cart" class="section">
            <div id="cart-items">
              <?php if (empty($cart_items)): ?>
                <p>Your cart is empty.</p>
              <?php else: ?>
                <?php foreach ($cart_items as $index => $item): ?>
                  <?php
                    $totalPrice = ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
                    $availableStock = $item['stock'] ?? 'N/A';
                    $stockText = "Limited Stocks: {$availableStock}";
                  ?>
                  <div class="cart-item">
                    <div class="cart-item-left">
                      <div class="store-info">
                        <span class="store-icon">🏬</span>
                        <span class="store-name"><?php echo htmlspecialchars($item['store'] ?? 'BOnusStore'); ?></span>
                      </div>
                      <img src="<?php echo htmlspecialchars($item['image'] ?? ''); ?>" alt="<?php echo htmlspecialchars($item['name'] ?? 'Product'); ?>">
                    </div>
                    <div class="cart-item-details">
                      <h3><a href="product.php"><?php echo htmlspecialchars($item['name'] ?? 'Product'); ?></a></h3>
                      <p class="stock"><?php echo htmlspecialchars($stockText); ?></p>
                      <div class="quantity">
                        <a class="decrement" href="cart.php?action=update&id=<?php echo $index; ?>&change=-1">-</a>
                        <input type="text" value="<?php echo $item['quantity'] ?? 1; ?>" readonly />
                        <a class="increment" href="cart.php?action=update&id=<?php echo $index; ?>&change=1">+</a>
                      </div>
                      <p class="price">Total: ₱<?php echo number_format($totalPrice); ?></p>
                      <!-- This is the functional delete button for each item -->
                      <a href="cart.php?action=delete&id=<?php echo $index; ?>" class="item-delete-btn" onclick="return confirm('Are you sure you want to remove this item?');">Remove</a>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
            
            <div class="cart-actions" style="display: <?php echo empty($cart_items) ? 'none' : 'flex'; ?>;">
              <a href="cart.php?action=clear_cart" class="delete-btn" onclick="return confirm('Are you sure you want to clear the entire cart?');">Clear Cart</a>
              <!-- This is the functional checkout button -->
              <a href="payment.php" class="checkout-btn">Check-Out</a>
            </div>
          </div>
          
          <!-- AUCTION SECTION - Powered by PHP Session -->
          <div id="auction" class="section">
            <div id="auction-items">
              <?php if (empty($auction_items)): ?>
                  <p>Your auction list is empty.</p>
              <?php else: ?>
                  <?php foreach ($auction_items as $index => $item): ?>
                      <?php
                          $stockText = ($item['stock'] ?? 0) > 0 ? "Limited Edition and Limited Stocks: {$item['stock']}" : 'Out of stock';
                      ?>
                      <div class="auction-item">
                          <div class="auction-item-left">
                              <div class="store-info">
                                  <span class="store-icon">🏬</span>
                                  <span class="store-name"><?php echo htmlspecialchars($item['store'] ?? 'Craft Store'); ?></span>
                              </div>
                              <img src="<?php echo htmlspecialchars($item['image'] ?? ''); ?>" alt="<?php echo htmlspecialchars($item['name'] ?? 'Product'); ?>">
                          </div>
                          <div class="auction-item-details">
                              <h3><?php echo htmlspecialchars($item['name'] ?? 'Product'); ?></h3>
                              <p class="stock"><?php echo htmlspecialchars($stockText); ?></p>
                              <p class="bid">Start Bid: ₱<?php echo number_format($item['bid'] ?? 0); ?></p>
                          </div>
                      </div>
                  <?php endforeach; ?>
              <?php endif; ?>
            </div>
            <div class="auction-actions" style="display: <?php echo empty($auction_items) ? 'none' : 'flex'; ?>;">
              <a href="cart.php?action=clear_auction" class="delete-btn">Clear All</a>
              <button class="notify-btn">Notify</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Simplified JavaScript for UI only -->
  <script>
    function showSection(sectionName) {
      document.querySelectorAll('.tab').forEach(tab => {
        tab.classList.remove('active');
        if (tab.textContent.toLowerCase().includes(sectionName)) {
          tab.classList.add('active');
        }
      });

      const slider = document.querySelector('.slider');
      if (sectionName === 'cart') {
        slider.style.transform = 'translateX(0)';
      } else if (sectionName === 'auction') {
        slider.style.transform = 'translateX(-50%)';
      }
    }
    
    document.addEventListener('DOMContentLoaded', () => {
        showSection('cart');
    });
  </script>
</body>
</html>