<?php
session_start();
// Authentication and Authorization
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_seller']) || $_SESSION['is_seller'] != 1) {
    header('Location: signin.php');
    exit();
}
require_once 'db_connect.php';

// Check if product ID is provided in URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Error: No product ID specified.");
}

$product_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch product details, ensuring it belongs to the current seller
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ? AND user_id = ?");
$stmt->bind_param("ii", $product_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();
$conn->close();

// If product not found or doesn't belong to the seller, stop.
if (!$product) {
    die("Error: Product not found or you do not have permission to edit it.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - CraftNest</title>
    <!-- We can reuse the profile CSS for a consistent form style -->
    <link rel="stylesheet" href="../css/seller_profile.css"> 
    <link rel="stylesheet" href="../css/seller_products.css">
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
        <div class="edit-form-container">
            <h1>Edit <?php echo $product['is_for_auction'] ? 'Auction' : 'Market'; ?> Product</h1>
            <div id="form-message" class="message" style="display:none;"></div>
            
            <form id="edit-product-form" enctype="multipart/form-data">
                <!-- Hidden input for product ID -->
                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>
                
                <?php if (!$product['is_for_auction']): // Fields for Market products ?>
                    <div class="form-group">
                        <label for="price">Price (₱)</label>
                        <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="stock">Stock Quantity</label>
                        <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required>
                    </div>
                <?php else: // Fields for Auction products ?>
                     <div class="form-group">
                        <label for="start_bid">Start Bid (₱)</label>
                        <input type="number" id="start_bid" name="start_bid" step="0.01" value="<?php echo htmlspecialchars($product['start_bid']); ?>" required>
                    </div>
                     <div class="form-group">
                        <label for="auction_end_time">Auction End Time</label>
                        <input type="datetime-local" id="auction_end_time" name="auction_end_time" value="<?php echo date('Y-m-d\TH:i', strtotime($product['auction_end_time'])); ?>" required>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label>Current Image</label>
                    <img src="../<?php echo htmlspecialchars($product['image_url']); ?>" alt="Current Image" style="max-width: 150px; display: block; margin-bottom: 10px;">
                    <label for="image">Change Image (optional)</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>
                
                <button type="submit" class="submit-btn">Save Changes</button>
                <a href="seller_products.php" class="cancel-btn">Cancel</a>
            </form>
        </div>
    </main>

    <script>
        document.getElementById('edit-product-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const messageDiv = document.getElementById('form-message');

            fetch('ajax_update_product.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                messageDiv.textContent = data.message;
                messageDiv.className = `message ${data.status}`; // 'success' or 'error'
                messageDiv.style.display = 'block';

                if(data.status === 'success') {
                    setTimeout(() => {
                        window.location.href = 'seller_products.php';
                    }, 1500);
                }
            })
            .catch(error => {
                messageDiv.textContent = 'A network error occurred.';
                messageDiv.className = 'message error';
                messageDiv.style.display = 'block';
            });
        });
    </script>
    <style>
        .edit-form-container { max-width: 700px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .edit-form-container h1 { text-align: center; margin-bottom: 25px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 8px; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .submit-btn, .cancel-btn { display: inline-block; width: auto; padding: 12px 30px; border-radius: 8px; border: none; cursor: pointer; text-align: center; font-weight: 600; text-decoration: none; }
        .submit-btn { background-color: #a0522d; color: white; }
        .cancel-btn { background-color: #f0f0f0; color: #555; margin-left: 10px; }
        .message { padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
        .message.success { background-color: #d4edda; color: #155724; }
        .message.error { background-color: #f8d7da; color: #721c24; }
    </style>
</body>
</html>