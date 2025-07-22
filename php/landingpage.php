<?php
// Start the session to check for a logged-in user.
session_start();
require_once 'db_connect.php';

// SMART REDIRECT: If the user is already logged in, send them to their homepage.
if (isset($_SESSION['user_id'])) {
    header('Location: home.php');
    exit();
}

// --- FETCH FEATURED PRODUCTS ---
$featured_products = [];
$query = "SELECT product_id, name, price, image_url FROM products WHERE status = 'active' AND is_for_auction = 0 ORDER BY created_at DESC LIMIT 4";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $featured_products[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>CraftNest - Welcome</title>
  <link rel="stylesheet" href="../css/landingpage.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
  <header>
    <a href="landingpage.php" class="logo">
      <img src="../img/logo.png" alt="CraftNest Logo">
    </a>
    <nav>
      <ul>
        <li><a href="landingpage.php" class="active">Home</a></li>
        <li><a href="#about-us">Stories</a></li>
        <!-- MODIFIED: This link now points to the section ID -->
        <li><a href="#featured-products">Products</a></li> 
        <li><a href="signin.php">Login</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <section class="hero">
      <div class="hero-content">
        <h1>Digital Marketplace for Crafts</h1>
        <p>A platform for local artisans to sell handmade goods online, promoting local craftsmanship and providing a space for unique products not typically available in the market.</p>
        <!-- MODIFIED: This button also scrolls down to the products -->
        <button onclick="document.location='#featured-products'">View More Products</button>
      </div>
    </section>

    <!-- MODIFIED: Added an id="featured-products" to this section -->
    <section class="featured-products" id="featured-products">
        <h2>Featured Products</h2>
        <div class="product-grid">
            <?php if (!empty($featured_products)): ?>
                <?php foreach ($featured_products as $product): ?>
                    <a href="signin.php" class="product-card-link" title="Log in or sign up to purchase">
                        <div class="product-card">
                            <img src="../<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="price">₱<?php echo number_format($product['price']); ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No featured products available at the moment.</p>
            <?php endif; ?>
        </div>
    </section>
  </main>

  <footer class="about" id="about-us">
      <h2>About Us</h2>
      <p>We are passionate about craftsmanship and community, connecting talented artisans with customers seeking unique, high-quality handmade goods. Our platform celebrates the art of handmade crafts and supports local economies.</p>
  </footer>
</body>
</html>