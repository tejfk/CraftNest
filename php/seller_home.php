<?php
session_start();

// Protect the page: only sellers can access this page
if (!isset($_SESSION['is_seller']) || $_SESSION['is_seller'] != 1) {
    // If not a seller, redirect them to the regular user homepage
    header('Location: home.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Home - CraftNest</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- Link to the new seller homepage stylesheet -->
    <link rel="stylesheet" href="../css/seller_home.css">
</head>
<body>

    <header>
        <a href="seller_home.php" class="logo">
            <!-- Ensure you have this logo image -->
            <img src="../img/logo.png" alt="CraftNest Logo">
        </a>
        <nav>
            <!-- Simplified Navigation for Sellers -->
            <ul>
                <li><a href="seller_home.php" class="active">Home</a></li>
                <li><a href="seller_profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="hero-search-container">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search your listed products...">
            </div>
            <div class="hero-content">
                <h1>Welcome, Seller!</h1>
                <p>Manage your store, view your sales analytics, and add new products to your collection. Let's grow your craft business together.</p>
                <button onclick="document.location='add_product.php'">+ Add New Product</button>
            </div>
        </section>

        <section class="about">
            <div class="about-content">
                <h2>Your Seller Dashboard</h2>
                <p>This is your central hub for managing everything related to your CraftNest store. Keep track of orders, respond to customer inquiries, and see how your products are performing.</p>
            </div>
        </section>

        <section class="stories-section">
            <div class="slideshow-container">
                <!-- Slide 1 -->
                <div class="slide active">
                    <!-- Make sure you have this image -->
                    <img src="../img/slide1.png" alt="Artisan at work">
                    <div class="slide-content">
                        <h2>Feature a Product</h2>
                        <p>Use this space to highlight your most popular or newest creations. Tell the story behind the craft to connect with your customers.</p>
                        <p class="artist-name">- Your Featured Product</p>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="slide">
                    <!-- Make sure you have this image -->
                    <img src="../img/slide2.jpg" alt="Crafting tools">
                    <div class="slide-content">
                        <h2>Seller Tips</h2>
                        <p>Learn how to take better product photos, write compelling descriptions, and market your store effectively to increase sales and reach a wider audience.</p>
                        <p class="artist-name">- CraftNest Seller Guide</p>
                    </div>
                </div>

                <!-- Navigation Arrows -->
                <a class="prev" onclick="changeSlide(-1)">❮</a>
                <a class="next" onclick="changeSlide(1)">❯</a>
            </div>
        </section>
    </main>

    <script>
        let slideIndex = 0;
        showSlide(slideIndex);

        function changeSlide(n) {
            showSlide(slideIndex += n);
        }

        function showSlide(n) {
            let i;
            let slides = document.getElementsByClassName("slide");
            if (n >= slides.length) { slideIndex = 0; }
            if (n < 0) { slideIndex = slides.length - 1; }
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            slides[slideIndex].style.display = "block";
        }

        // Optional: Auto-slide
        setInterval(function() {
            changeSlide(1);
        }, 8000); // Change image every 8 seconds
    </script>

</body>
</html>