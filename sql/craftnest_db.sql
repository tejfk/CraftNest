-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 23, 2025 at 08:35 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `craftnest_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password_hash`, `created_at`) VALUES
(1, 'admin', '$2y$10$60QFOSSeNaX56hYgRFkLte5k5.FNrpdUSMa6Ll7NOWcXYJCxhBmK2', '2025-07-23 03:35:46'),
(2, 'Jaylvn', 'jetskie123', '2025-07-23 03:45:31');

-- --------------------------------------------------------

--
-- Table structure for table `bidding_products`
--

CREATE TABLE `bidding_products` (
  `bidding_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_bid` decimal(10,2) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `end_time` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `disputes`
--

CREATE TABLE `disputes` (
  `dispute_id` int(11) NOT NULL,
  `reporter_id` int(11) NOT NULL,
  `reported_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `reason` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'The ID of the user (seller) who listed the product',
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) DEFAULT NULL COMMENT 'Used for regular "Buy Now" products',
  `stock` int(11) NOT NULL DEFAULT 0 COMMENT 'Available stock for regular products',
  `start_bid` decimal(10,2) DEFAULT NULL COMMENT 'Used for auction items',
  `buy_now_price` decimal(10,2) DEFAULT NULL,
  `current_bid` decimal(10,2) DEFAULT NULL COMMENT 'The current highest bid on an auction item',
  `image_url` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `is_for_auction` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = Regular Product, 1 = Auction Item',
  `auction_start_time` datetime DEFAULT NULL,
  `auction_end_time` datetime DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active' COMMENT 'e.g., active, ended, sold',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `user_id`, `name`, `description`, `price`, `stock`, `start_bid`, `buy_now_price`, `current_bid`, `image_url`, `category`, `is_for_auction`, `auction_start_time`, `auction_end_time`, `status`, `created_at`) VALUES
(1, 1, 'Handcrafted Vase', 'A beautiful handmade vase perfect for any decor. Crafted from locally sourced clay and glazed with a unique pattern.', NULL, 0, 1000.00, NULL, NULL, 'img/products/p1.png', NULL, 1, NULL, '2025-07-15 22:00:00', 'active', '2025-07-04 05:56:18'),
(2, 1, 'Clay Bowl Set', 'Elegant bowl set made from pure clay, ideal for serving or as a decorative piece. Includes three bowls of varying sizes.', NULL, 0, 2000.00, NULL, NULL, 'img/products/p2.png', NULL, 1, NULL, '2025-07-16 18:30:00', 'active', '2025-07-04 05:56:18'),
(3, 2, 'Decorative Pot', 'A unique decorative pot for indoor plants. Features an intricate, hand-painted design that adds a touch of artistry to your space.', NULL, 0, 1500.00, NULL, NULL, 'img/products/p3.png', NULL, 1, NULL, '2025-07-14 20:00:00', 'active', '2025-07-04 05:56:18'),
(4, 2, 'Wooden Sculpture', 'A hand-carved abstract sculpture made from solid mahogany. A stunning centerpiece for any room.', NULL, 0, NULL, NULL, NULL, 'img/products/p4.png', NULL, 0, NULL, NULL, 'active', '2025-07-04 05:56:18'),
(7, 5, 'VaseS', 'SAMPLE', 12000.00, 5, NULL, NULL, NULL, 'uploads/prod_687f0b0530bf9.jpg', NULL, 0, NULL, NULL, 'active', '2025-07-22 03:52:37'),
(8, 5, 'Cups', 'Sample 2', 10000.00, 10, NULL, NULL, NULL, 'uploads/prod_687f1a46ba16b.png', NULL, 0, NULL, NULL, 'active', '2025-07-22 04:57:42'),
(9, 5, 'Elf-Art', 'Bidsample', 0.00, 1, 10000.00, 50000.00, NULL, 'uploads/bid_687f20dc41752.png', NULL, 1, NULL, '2025-07-30 00:00:00', 'active', '2025-07-22 05:25:48');

-- --------------------------------------------------------

--
-- Table structure for table `sellers`
--

CREATE TABLE `sellers` (
  `seller_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `store_name` varchar(255) DEFAULT 'My Craft Store',
  `store_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sellers`
--

INSERT INTO `sellers` (`seller_id`, `user_id`, `store_name`, `store_description`, `created_at`) VALUES
(1, 1, 'My Craft Store', NULL, '2025-04-24 07:33:21'),
(3, 3, 'My Craft Store', NULL, '2025-04-24 07:35:34'),
(5, 5, 'My Craft Store', NULL, '2025-07-07 05:17:55'),
(6, 6, 'ony\'s Store', NULL, '2025-07-14 05:17:00'),
(8, 8, 'Maricar\'s Store', NULL, '2025-07-21 07:50:13');

-- --------------------------------------------------------

--
-- Table structure for table `stories`
--

CREATE TABLE `stories` (
  `story_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `dob` date DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT 'default.png',
  `password_hash` varchar(255) NOT NULL,
  `is_seller` tinyint(1) DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `username`, `email`, `dob`, `phone_number`, `profile_picture`, `password_hash`, `is_seller`, `status`, `created_at`) VALUES
(1, '', 'Tej', 'jancinaljetjet@gmail.com', NULL, NULL, '686b557fbbfcc.jpg', '$2y$10$khnxUOSrevV9tGnucKeKmeiD5gx3shdVRGnEmygO40Q1prTlTWquG', 1, 'active', '2025-04-24 07:33:21'),
(2, '', 'Ayeisha', '', NULL, NULL, 'default.png', '$2y$10$7Dwups7bzut79yGa/mf6HePpeordeyD0K0POwWeqqc7vB8lEmSmY6', 0, 'active', '2025-04-24 07:35:00'),
(3, '', 'Akeisha', '', NULL, NULL, 'default.png', '$2y$10$mUnqwHvNb0eIs9c9BjFCVuz53HBt0HlNzOEVcrUCvdOEj3..Qferq', 1, 'active', '2025-04-24 07:35:34'),
(4, '', 'Tejey03', '', NULL, NULL, 'default.png', '$2y$10$9ZsCtSlml06NvqoH9Zc8SeZg7g69nLrfD4r92qg/nb6RrHz1c9Q5m', 0, 'active', '2025-07-02 06:18:07'),
(5, '', 'jlvn03', '', NULL, NULL, 'uploads/profiles/user_5_1753166157.jpg', '$2y$10$ssv3oOVaZOSuWaWmdFMPm.noE9omfGFHwnC35/H8fXuXQbfLdxWsi', 1, 'active', '2025-07-07 05:17:55'),
(6, 'ony', 'Hakdogins', 'mendozaony17@gmail.com', '2003-10-27', '09284785737', '6874930e86d1a.png', '$2y$10$XqbCVub.ydJY29EiIwN2tuQ1lQSIeRUiRxano3u/044xpB5OT4uD2', 1, 'active', '2025-07-14 05:17:00'),
(7, 'binimaricar', 'maricar', 'binimaricar@gmail.com', '2002-11-11', '0922938472', 'default.png', '$2y$10$BFqdvLBUbppULaVz1t6rO.AAtYTXShStFQ7Uzquoc3GgcmDU.s5jS', 0, 'active', '2025-07-21 07:46:32'),
(8, 'Maricar', 'Maricar123', 'maricar@gmail.com', '2003-11-10', '09284785737', 'default.png', '$2y$10$PSFD0fFiMjpKBGbHE6xcqO6va9TdUUhiyEGA6VuUHJFCUbL1F/8bO', 1, 'active', '2025-07-21 07:50:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `bidding_products`
--
ALTER TABLE `bidding_products`
  ADD PRIMARY KEY (`bidding_id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `disputes`
--
ALTER TABLE `disputes`
  ADD PRIMARY KEY (`dispute_id`),
  ADD KEY `reporter_id` (`reporter_id`),
  ADD KEY `reported_id` (`reported_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `sellers`
--
ALTER TABLE `sellers`
  ADD PRIMARY KEY (`seller_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `stories`
--
ALTER TABLE `stories`
  ADD PRIMARY KEY (`story_id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `buyer_id` (`buyer_id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bidding_products`
--
ALTER TABLE `bidding_products`
  MODIFY `bidding_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `disputes`
--
ALTER TABLE `disputes`
  MODIFY `dispute_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `stories`
--
ALTER TABLE `stories`
  MODIFY `story_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bidding_products`
--
ALTER TABLE `bidding_products`
  ADD CONSTRAINT `bidding_products_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `disputes`
--
ALTER TABLE `disputes`
  ADD CONSTRAINT `disputes_ibfk_1` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `disputes_ibfk_2` FOREIGN KEY (`reported_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `sellers`
--
ALTER TABLE `sellers`
  ADD CONSTRAINT `sellers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `stories`
--
ALTER TABLE `stories`
  ADD CONSTRAINT `stories_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
