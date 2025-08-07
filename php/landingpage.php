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
$query = "SELECT product_id, name, price, image_url, description FROM products WHERE status = 'active' AND is_for_auction = 0 ORDER BY created_at DESC LIMIT 6";
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
  <title>Craft Nest - Digital Marketplace for Woodcrafts</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cardo:wght@400;700&family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./css/landingpage.css">
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
        <li><a href="/landingpage.php">Home</a></li>
        <li><a href="/landing-auction.php">Auction</a></li>
        <li><a href="/landing-products.php">Products</a></li>
        <li><a href="/signin.php">Login</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <section class="hero">
      <div class="search-container">
        <div class="search-bar">
          <button class="search-btn" id="searchBtn">
            <div class="search-btn-icon"></div>
          </button>
          <input type="text" id="searchInput" placeholder="Search Products">
          <button class="filter-icon" id="filterBtn">
            <span class="line"></span><span class="line"></span><span class="line"></span>
          </button>
        </div>
        <div class="filter-dropdown" id="filterDropdown">
          <button class="filter-option" data-category="all">All</button>
          <button class="filter-option" data-category="decor">Decor</button>
          <button class="filter-option" data-category="furniture">Furniture</button>
          <button class="filter-option" data-category="utility">Utility</button>
        </div>
      </div>
      <div class="hero-content">
        <h1>Digital Marketplace for Woodcrafts</h1>
        <p>A platform for local artisans to sell handmade goods online. This will promote local craftsmanship and provide a space for unique products not typically available in the market.</p>
        <a href="#products" class="cta-button">View More Products</a>
      </div>
    </section>

    <section class="features">
      <div class="card-container">
        <div class="card card-1"><div class="card-content"><div class="text-content"><h2>About Us</h2><p>Welcome to CraftNest, a vibrant digital marketplace dedicated to celebrating the artistry and craftsmanship of woodcrafters. At CraftNest, we provide a dedicated platform where talented artisans, particularly from Cebu, can showcase their unique, handmade wooden creations to a global audience.</p></div><div class="image-content"><img src="./img/fc1.jpg" alt="Modern wooden table in a well-lit room"></div></div></div>
        <div class="card card-2"><div class="card-content centered-text"><p>Our mission is to empower local woodcrafters by giving them the tools and visibility they need to thrive in a competitive market, while preserving and promoting the rich tradition of woodworking.</p><p>CraftNest was born out of a deep appreciation for the skill, creativity, and cultural heritage embedded in every handcrafted wooden piece. Cebu, known for its thriving artisanal community and centuries-old woodworking traditions, serves as the heart of our inspiration.</p></div></div>
        <div class="card card-3"><div class="card-content"><div class="text-content"><p>We understand the challenges that local artisans face in reaching broader markets, which is why we’ve created a platform that bridges the gap between these skilled creators and customers who value quality, authenticity, and sustainability.</p></div><div class="image-content"><img src="./img/fc3.jpg" alt="A busy woodworking workshop"></div></div></div>
      </div>
    </section>
    
    <section class="story-section">
        <div class="story-content">
            <div class="text-content">
                <p>Through CraftNest, woodcrafters can share their stories, showcase their craftsmanship, and connect with customers who appreciate the beauty of handmade products. From intricately carved furniture to delicate wooden decor, every item on our platform reflects the dedication, passion, and expertise of its maker. We aim to not only promote their products but also to highlight the cultural and artistic value of their work, fostering a deeper appreciation for handcrafted goods in a world dominated by mass production.</p>
            </div>
            <div class="image-content">
                <img src="./img/woodcrafter.jpg" alt="A craftsman working with wood shavings around him">
            </div>
        </div>
    </section>

    <section class="testimonial-slider">
        <div class="slides-container">
            <div class="slide" style="background-image: url('./img/ss1.jpg');">
              <div class="slide-content">
                <h3>Artisan's Stories</h3>
                <p>I'm a woodcarver from Cebu, and my teak wall art is inspired by the intricate patterns of our local banig mats, a craft my mother taught me. Each cut I make tells a story of resilience, connecting my family’s weaving traditions to the wood I shape for your home through CraftNest.</p></div></div>
            <div class="slide" style="background-image: url('./img/ss2.jpg');">
              <div class="slide-content">
                <h3>Artisan's Stories</h3>
                <p>As a Cebu artisan, I crafted this narra coffee table from a fallen tree, honoring its natural grain to reflect the strength of our forests. Every knot and curve is a piece of my hometown’s spirit, shared with you through CraftNest’s platform.</p></div></div>
        </div>
    </section>
    
    <section id="products" class="peoples-choice">
        <h2 class="section-title">People's Choice in the Market</h2>
        <div class="product-grid">
            <?php if (!empty($featured_products)): ?>
                <?php foreach ($featured_products as $product): ?>
                    <div class="product-card">
                        <a href="signin.php" title="Log in or sign up to purchase">
                            <img src="./<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </a>
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p><?php echo htmlspecialchars(substr($product['description'], 0, 150)) . '...'; ?></p>
                        <button class="add-to-cart-btn" onclick="location.href='signin.php'">Add To Cart</button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No featured products available at the moment.</p>
            <?php endif; ?>
        </div>
        <button class="browse-more-btn" onclick="location.href='/landing-products.php';">Browse More Products</button>
    </section>
    
  </main>
  
  <footer class="site-footer">
    <div class="footer-top-bar"></div>
    <div class="footer-content">
        <div class="footer-logo">
            <div class="logo-container">
              <div class="craft-text">
                <span class="letter">C</span><span class="letter">r</span><span class="letter">a</span><span class="letter">f</span><span class="letter">t</span>
              </div>
              <div class="nest-text">Nest</div>
            </div>
        </div>
        <div class="footer-center">
            <h2>Where Passion Meets Purpose</h2>
            <p>© 2025 CraftNest. All Rights Reserved.</p>
        </div>
        <div class="footer-contact">
            <p style="text-align: left;">Contact Us:</p>
            <p>104-7890-456</p>
        </div>
    </div>
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // --- Generic Carousel Function ---
      const createCarousel = (containerSelector, itemSelector, interval) => {
        const container = document.querySelector(containerSelector);
        if (!container) return;
        const items = container.querySelectorAll(itemSelector);
        if (items.length <= 1) return;
        
        let currentIndex = 0;
        items[0].classList.add('active');

        const showNextItem = () => {
          const outgoingItem = items[currentIndex];
          currentIndex = (currentIndex + 1) % items.length;
          const incomingItem = items[currentIndex];
          
          outgoingItem.classList.add('exit');
          incomingItem.classList.add('active', 'enter');
          
          outgoingItem.addEventListener('animationend', () => {
            outgoingItem.classList.remove('active', 'exit');
          }, { once: true });
          
          incomingItem.addEventListener('animationend', () => {
            incomingItem.classList.remove('enter');
          }, { once: true });
        };
        
        setInterval(showNextItem, interval);
      };

      // --- Initialize Carousels ---
      createCarousel('.card-container', '.card', 5000);
      createCarousel('.slides-container', '.slide', 5000);
      
      // --- Search and Filter ---
      const searchInput = document.getElementById('searchInput');
      const filterBtn = document.getElementById('filterBtn');
      const filterDropdown = document.getElementById('filterDropdown');
      
      const performSearch = () => { /* Add search filtering logic here */ };
      searchInput.addEventListener('input', performSearch);
      
      filterBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        filterDropdown.classList.toggle('show');
      });

      document.addEventListener('click', () => {
        if (filterDropdown.classList.contains('show')) {
            filterDropdown.classList.remove('show');
        }
      });
    });
  </script>
</body>
</html>
