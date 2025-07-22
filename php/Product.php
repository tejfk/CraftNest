<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Start the session to manage user authentication and the shopping cart
session_start();
require_once 'db_connect.php';

// --- (Section 1) PROTECT THE PAGE & HANDLE AJAX REQUESTS ---

// If user is not logged in, redirect to the sign-in page
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit();
}

// This block handles AJAX POST requests for "Add to Cart" and "Buy Now"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    // Set the response header to JSON
    header('Content-Type: application/json');
    $response = ['success' => false, 'message' => 'An unknown error occurred.'];

    $product_id = (int)$_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    // Fetch product details from DB for validation
    $stmt = $conn->prepare("SELECT product_id, name, price, stock, image_url FROM products WHERE product_id = ? AND status = 'active'");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product_result = $stmt->get_result();

    if ($product = $product_result->fetch_assoc()) {
        // Initialize cart if it doesn't exist
        $_SESSION['cart'] = $_SESSION['cart'] ?? [];
        
        // Check if item is already in cart to update quantity
        $item_in_cart_key = null;
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] == $product_id) {
                $item_in_cart_key = $key;
                break;
            }
        }
        
        $current_cart_qty = ($item_in_cart_key !== null) ? $_SESSION['cart'][$item_in_cart_key]['quantity'] : 0;

        // Check for sufficient stock
        if ($product['stock'] >= ($current_cart_qty + $quantity)) {
            if ($item_in_cart_key !== null) {
                // Update quantity if item is already in cart
                $_SESSION['cart'][$item_in_cart_key]['quantity'] += $quantity;
            } else {
                // Add new item to cart
                $_SESSION['cart'][] = [
                    'id' => $product['product_id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'image' => $product['image_url'],
                    'stock' => $product['stock'],
                    'quantity' => $quantity
                ];
            }
            $response = ['success' => true, 'message' => $product['name'] . ' was added to your cart!'];
        } else {
            $response = ['success' => false, 'message' => 'Not enough stock available for ' . $product['name'] . '.'];
        }
    } else {
        $response['message'] = 'Product not found or is inactive.';
    }
    
    // Send JSON response and stop script execution
    echo json_encode($response);
    exit();
}


// --- (Section 2) FETCH PRODUCTS FOR PAGE DISPLAY ---
// This part runs only on a normal page load (GET request)

$products = [];
// This query gets only active products that are NOT for auction
$query = "SELECT product_id, name, description, price, stock, image_url FROM products WHERE status = 'active' AND is_for_auction = 0";
$result = $conn->query($query);
echo "<h1>User Debug Info</h1>";
echo "Products found: " . $result->num_rows . "<br>";
var_dump($result->fetch_all(MYSQLI_ASSOC));
exit(); // This stops the rest of the page from loading
// -------------------------
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Craft Nest - Products</title>
  <link href="https://fonts.googleapis.com/css2?family=Cardo:wght@400;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/Product.css">
</head>
<body>
  <header>
    <a href="home.php" class="logo-container">
        <div class="craft-text">
            <span class="letter">C</span><span class="letter">r</span><span class="letter">a</span><span class="letter">f</span><span class="letter">t</span>
        </div>
        <div class="nest-text">Nest</div>
    </a>
    <nav>
      <ul>
        <li><a href="home.php">Home</a></li>
        <li><a href="auction.php">Auction</a></li>
        <li><a href="Product.php" class="active">Products</a></li>
        <li><a href="userprofile.php">Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </nav>
    <div class="cart-icon">
        <a href="cart.php"><span>🛒</span></a>
    </div>
  </header>

  <main>
    <div class="search-bar">
      <span class="search-icon">🔍</span>
      <input type="text" id="searchInput" placeholder="Search Products">
    </div>

    <div class="product-grid">
      <!-- Dynamically generate product cards from the database -->
      <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
          <div class="product-card" 
               data-product-id="<?php echo $product['product_id']; ?>" 
               data-stock="<?php echo $product['stock']; ?>" 
               data-description="<?php echo htmlspecialchars($product['description']); ?>">
            <img src="../<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
            <p class="price">₱<?php echo number_format($product['price'], 2); ?></p>
            <p class="stock">Stock: <?php echo $product['stock']; ?></p>
            <button class="add-to-cart-btn" data-product-id="<?php echo $product['product_id']; ?>">Add to Cart</button>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="no-products-message">No products are available at the moment. Please check back later!</p>
      <?php endif; ?>
    </div>

    <!-- Modal for Product Details -->
    <div class="modal-overlay" id="productModal">
      <div class="modal-content">
        <span class="modal-close" id="modalClose">×</span>
        <div class="product-header">
          <img id="modalImage" src="" alt="Product Image">
          <div class="product-info">
            <h3 id="modalName"></h3>
            <p class="price" id="modalPrice"></p>
            <p class="stock" id="modalStock"></p>
            <div class="quantity-container">
              <button class="quantity-decrement">-</button>
              <input type="number" id="quantityInput" value="1" min="1">
              <button class="quantity-increment">+</button>
            </div>
            <div class="button-container" id="modalButtonContainer">
              <button class="add-to-cart-btn" id="modalAddToCart">Add to Cart</button>
              <button class="buy-now-btn" id="modalBuyNow">Buy Now</button>
            </div>
          </div>
        </div>
        <div class="description-title">Description</div>
        <p class="description" id="modalDescription"></p>
      </div>
    </div>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const productCards = document.querySelectorAll('.product-card');
      const modal = document.getElementById('productModal');
      const modalClose = document.getElementById('modalClose');
      const modalImage = document.getElementById('modalImage');
      const modalName = document.getElementById('modalName');
      const modalPrice = document.getElementById('modalPrice');
      const modalStock = document.getElementById('modalStock');
      const modalDescription = document.getElementById('modalDescription');
      const modalAddToCart = document.getElementById('modalAddToCart');
      const modalBuyNow = document.getElementById('modalBuyNow');
      const quantityInput = document.getElementById('quantityInput');
      const quantityDecrement = document.querySelector('.quantity-decrement');
      const quantityIncrement = document.querySelector('.quantity-increment');

      let currentStockInModal = 0;

      // Reusable function to add an item to the cart via AJAX
      const addItemToServer = (productId, quantity) => {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', quantity);

        return fetch('Product.php', {
          method: 'POST',
          body: formData
        }).then(response => response.json());
      };

      // Open modal when a product card is clicked
      productCards.forEach(card => {
        card.addEventListener('click', (e) => {
          // Do not open modal if the 'Add to Cart' button on the card was clicked
          if (e.target.classList.contains('add-to-cart-btn')) return;

          currentStockInModal = parseInt(card.dataset.stock, 10);
          
          modalImage.src = card.querySelector('img').src;
          modalName.textContent = card.querySelector('h3').textContent;
          modalPrice.textContent = card.querySelector('.price').textContent;
          modalStock.textContent = card.querySelector('.stock').textContent;
          modalDescription.textContent = card.dataset.description;

          quantityInput.value = 1;
          quantityInput.max = currentStockInModal;
          
          const productId = card.dataset.productId;
          modalAddToCart.dataset.productId = productId;
          modalBuyNow.dataset.productId = productId;

          modal.style.display = 'flex';
        });
      });

      // Close modal events
      modalClose.addEventListener('click', () => modal.style.display = 'none');
      modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.style.display = 'none';
      });
      
      // Modal quantity controls
      quantityDecrement.addEventListener('click', () => {
        let value = parseInt(quantityInput.value);
        if (value > 1) quantityInput.value = value - 1;
      });
      quantityIncrement.addEventListener('click', () => {
        let value = parseInt(quantityInput.value);
        if (value < currentStockInModal) quantityInput.value = value + 1;
      });

      // Handle "Add to Cart" click from the cards in the grid
      document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        // This condition prevents attaching the event to the modal's button again
        if(button.id !== 'modalAddToCart') {
            button.addEventListener('click', (e) => {
                e.stopPropagation(); // Prevents the modal from opening
                const productId = button.dataset.productId;
                addItemToServer(productId, 1).then(data => {
                    alert(data.message);
                });
            });
        }
      });
      
      // Handle "Add to Cart" click from inside the modal
      modalAddToCart.addEventListener('click', () => {
        const productId = modalAddToCart.dataset.productId;
        const quantity = parseInt(quantityInput.value, 10);
        addItemToServer(productId, quantity).then(data => {
          alert(data.message);
          if (data.success) {
            modal.style.display = 'none';
          }
        });
      });

      // Handle "Buy Now" click from inside the modal
      modalBuyNow.addEventListener('click', () => {
        const productId = modalBuyNow.dataset.productId;
        const quantity = parseInt(quantityInput.value, 10);

        addItemToServer(productId, quantity).then(data => {
          if (data.success) {
            // If successfully added, redirect to the payment page
            window.location.href = 'payment.php';
          } else {
            // If it fails (e.g., out of stock), show the error
            alert(data.message);
          }
        });
      });

      // Search functionality
      const searchInput = document.getElementById('searchInput');
      searchInput.addEventListener('input', () => {
        const searchQuery = searchInput.value.trim().toLowerCase();
        productCards.forEach(card => {
          const productName = card.querySelector('h3').textContent.toLowerCase();
          card.style.display = productName.includes(searchQuery) ? 'flex' : 'none';
        });
      });
    });
  </script>
</body>
</html>