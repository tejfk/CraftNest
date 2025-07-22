<?php
// Start the session to access session variables
session_start();

// PROTECT THIS PAGE: If the user is not logged in, redirect them to the signin page.
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit(); // Stop the script immediately
}

// Get user info from the session
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to CraftNest - <?php echo htmlspecialchars($username); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cardo:wght@400;700&family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <!-- Links to the correct CSS file -->
    <link rel="stylesheet" href="../css/Homepage.css">
    <!-- Font Awesome for search icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <!-- HEADER contains both LOGO and NAVIGATION -->
    <header>
        <a href="home.php" class="logo">
          <img src="../img/logo.png" alt="CraftNest Logo">
        </a>
        <nav>
          <ul>
            <li><a href="home.php" class="active">Home</a></li>
            <li><a href="auction.php">Auction</a></li>
            <li><a href="product.php">Products</a></li>
            <li><a href="userprofile.php">Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
          </ul>
        </nav>
    </header>

    <!-- MAIN content area -->
    <main>
        <!-- HERO SECTION for logged-in user -->
        <section class="hero">
            <div class="hero-search-container">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Search Products">
            </div>
            <div class="hero-content">
                <h1>Digital Marketplace for Crafts</h1>
                <p>CraftNest is a digital marketplace that allows woodcrafters to showcase their products on a dedicated platform and reach a wider audience. Through this, we aim to help local woodcrafters in Cebu promote the value of their handmade products.</p>
                <button onclick="document.location='product.php'">View More Products</button>
            </div>
        </section>

        <!-- ABOUT US SECTION -->
        <section class="about" id="about-us">
            <div class="about-content">
                <h2>About Us</h2>
                <p>A platform for woodcrafters to sell handmade goods online. This will promote local craftsmanship and provide a space for unique products not typically available in the market.</p>
            </div>
        </section>

        <!-- WORKING SLIDESHOW SECTION -->
        <section class="stories-section" id="artisan-stories">
            <div class="slideshow-container">
                <!-- Slide 1 -->
                <div class="slide active">
                    <img src="../img/slide1.png" alt="Artisan Story 1">
                    <div class="slide-content">
                        <h2>Artisan's Stories</h2>
                        <p>At first glance, the beautiful moths in artist Kasia's collection are so exquisite, they look like they could be the real thing. But look closer and you will see the individual strands of thread that Kasia has deftly used to create these life-like embroidery studies.</p>
                        <span class="artist-name">-Kasia</span>
                    </div>
                </div>
                <!-- Slide 2 -->
                <div class="slide">
                    <img src="../img/slide2.jpg" alt="Artisan Story 2">
                    <div class="slide-content">
                        <h2>Artisan's Stories</h2>
                        <p>Using reclaimed timber from forgotten buildings, Marco crafts furniture that tells a story of history and renewal, blending rustic charm with modern elegance.</p>
                        <span class="artist-name">-Marco</span>
                    </div>
                </div>
                 <div class="slide">
                    <img src="../img/slide3.jpg" alt="Artisan Story 3">
                    <div class="slide-content">
                        <h2>Artisan's Stories</h2>
                        <p>Elena weaves vibrant traditions into every textile, creating blankets and scarves that are both a piece of art and a warm embrace.</p>
                        <span class="artist-name">-Elena</span>
                    </div>
                </div>
                 <div class="slide">
                    <img src="../img/slide4.jpg" alt="Artisan Story 4">
                    <div class="slide-content">
                        <h2>Artisan's Stories</h2>
                        <p>From his small workshop, Samuel forges metal into delicate, nature-inspired jewelry, capturing the beauty of a leaf or the curve of a branch.</p>
                        <span class="artist-name">-Samuel</span>
                    </div>
                </div>
                 <div class="slide">
                    <img src="../img/slide5.jpg" alt="Artisan Story 5">
                    <div class="slide-content">
                        <h2>Artisan's Stories</h2>
                        <p>Anya’s pottery is a celebration of imperfection, where every fingerprint and subtle curve makes each piece uniquely beautiful.</p>
                        <span class="artist-name">-Anya</span>
                    </div>
                </div>
                
                <!-- Navigation Arrows -->
                <a class="prev" onclick="moveSlide(-1)">❮</a>
                <a class="next" onclick="moveSlide(1)">❯</a>
            </div>
        </section>
    </main>

    <script>
        let slideIndex = 0;
        showSlides(slideIndex);

        function moveSlide(n) { showSlides(slideIndex += n); }

        function showSlides(n) {
            let slides = document.getElementsByClassName("slide");
            if (slides.length === 0) return;
            if (n >= slides.length) { slideIndex = 0; }
            if (n < 0) { slideIndex = slides.length - 1; }
            for (let i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            slides[slideIndex].style.display = "block";
        }
    </script>

</body>
</html>