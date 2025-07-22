<?php
session_start();
require_once 'db_connect.php';

// 1. --- AUTHENTICATION & CART CHECK ---
// User must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit();
}
// Cart must not be empty
if (empty($_SESSION['cart'])) {
    header('Location: cart.php?error=emptycart');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_data = [];
$total_price = 0;

// 2. --- FETCH USER DATA for pre-filling the form ---
$stmt = $conn->prepare("SELECT full_name, email, phone_number FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
}
$stmt->close();

// 3. --- CALCULATE TOTAL PRICE from cart session ---
foreach ($_SESSION['cart'] as $item) {
    $total_price += $item['price'] * $item['quantity'];
}


// 4. --- HANDLE FORM SUBMISSION (ORDER CREATION) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize form inputs
    $name = trim(htmlspecialchars($_POST['name']));
    $phone = trim(htmlspecialchars($_POST['phone']));
    $address = trim(htmlspecialchars($_POST['address']));
    $email = trim(htmlspecialchars($_POST['email']));
    $message = trim(htmlspecialchars($_POST['message']));
    
    // Begin a database transaction
    $conn->begin_transaction();

    try {
        // Step A: Insert into the main 'orders' table
        $order_stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, delivery_address, phone_number, message_to_seller, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
        $order_stmt->bind_param("idsss", $user_id, $total_price, $address, $phone, $message);
        $order_stmt->execute();
        
        // Get the ID of the order we just created
        $order_id = $conn->insert_id;

        // Step B: Insert each cart item into the 'order_items' table
        $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_per_item) VALUES (?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $item) {
            $item_stmt->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
            $item_stmt->execute();
        }
        
        // If everything was successful, commit the transaction
        $conn->commit();
        
        // Clear the cart
        $_SESSION['cart'] = [];
        
        // Redirect to a success page (e.g., user profile)
        header('Location: userprofile.php?order_success=1');
        exit();

    } catch (mysqli_sql_exception $exception) {
        // If any query failed, roll back the entire transaction
        $conn->rollback();
        
        // You can log the error and show a generic failure message
        // error_log("Order creation failed: " . $exception->getMessage());
        $error_message = "There was a problem placing your order. Please try again.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Craft Nest - Payment Details</title>
  <link href="https://fonts.googleapis.com/css2?family=Cardo:wght@400;700&family=Poppins:wght@400;700&display=swap" rel="stylesheet">
  <!-- Corrected path to CSS -->
  <link rel="stylesheet" href="../css/payment.css">  
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
        <!-- Corrected links to .php files -->
        <li><a href="../php/home.php">Home</a></li>
        <li><a href="../php/auction.php">Auction</a></li>
        <li><a href="../php/product.php">Products</a></li>
        <li><a href="../php/userprofile.php">Profile</a></li>
        <li><a href="../php/logout.php">Logout</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <div class="payment-details">
      <h2>Payment Details</h2>

      <?php if (isset($error_message)): ?>
        <p class="error-notice"><?php echo $error_message; ?></p>
      <?php endif; ?>

      <!-- Form now points to itself and uses POST method -->
      <form action="payment.php" method="POST">
        <div class="form-group">
          <label for="name">Name</label>
          <!-- Pre-filled with user data -->
          <input type="text" id="name" name="name" placeholder="Name" value="<?php echo htmlspecialchars($user_data['full_name'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
          <label for="phone">Phone Number</label>
          <input type="text" id="phone" name="phone" placeholder="Phone Number" value="<?php echo htmlspecialchars($user_data['phone_number'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
          <label for="address">Delivery Address</label>
          <input type="text" id="address" name="address" placeholder="Delivery Address" required>
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
          <label for="message">Message to the seller regarding delivery (Optional)</label>
          <textarea id="message" name="message" placeholder="e.g., Please leave at the front door."></textarea>
        </div>
        <div class="form-group">
            <label for="payment-method">Payment Method</label>
            <select id="payment-method" name="payment_method" required>
                <option value="cod">Cash on Delivery (COD)</option>
                <option value="gcash">GCash</option>
                <!-- Add other payment options here if needed -->
            </select>
        </div>
        <div class="total-order">
          <!-- Dynamic total price -->
          <span>Total: ₱<?php echo number_format($total_price, 2); ?></span>
          <!-- Button is now type="submit" -->
          <button type="submit" class="request-order-btn">Request Order</button>
        </div>
      </form>
    </div>
  </main>
</body>
</html>