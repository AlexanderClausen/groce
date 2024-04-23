-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2024 at 03:55 PM
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
-- Database: `grocery`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `category_parent` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `category_parent`) VALUES
(1, 'Fresh', NULL),
(2, 'Dairy & Eggs', NULL),
(3, 'Meat & Seafood', NULL),
(4, 'Bakery', NULL),
(5, 'Pantry Staples', NULL),
(6, 'Frozen', NULL),
(7, 'Snacks', NULL),
(8, 'Beverages', NULL),
(10, 'Household & Personal Care', NULL),
(11, 'Fruits', 1),
(12, 'Vegetables', 1),
(13, 'Milk & Alternatives', 2),
(14, 'Cheese', 2),
(15, 'Eggs', 2),
(16, 'Meat', 3),
(17, 'Seafood', 3),
(18, 'Bread', 4),
(19, 'Pastries & Sweets', 4),
(20, 'Cereals & Breakfast', 5),
(21, 'Cooking Essentials', 5),
(22, 'Grains & Pasta', 5),
(23, 'Ready Meals', 6),
(24, 'Ice Cream & Desserts', 6),
(25, 'Chips & Savoury Snacks', 7),
(26, 'Chocolate & Sweets', 7),
(27, 'Non-Alcoholic', 8),
(28, 'Alcoholic', 8),
(31, 'Cleaning Supplies', 10),
(32, 'Personal Care', 10),
(33, 'Homewares', 10);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `order_total_price` decimal(10,2) NOT NULL,
  `order_status` enum('pending','confirmed','cancelled') NOT NULL,
  `order_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_buyer`
--

CREATE TABLE `order_buyer` (
  `buyer_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `street` text NOT NULL,
  `suburb` varchar(255) NOT NULL,
  `state` enum('ACT','NSW','NT','QLD','SA','TAS','VIC','WA') NOT NULL,
  `postcode` int(11) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `order_detail_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_ordered_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_description` text NOT NULL,
  `product_image` varchar(255) NOT NULL,
  `unit_quantity` varchar(255) NOT NULL,
  `unit_price` decimal(8,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `product_description`, `product_image`, `unit_quantity`, `unit_price`, `stock_quantity`, `category_id`) VALUES
(1, 'Groce Spaghetti', 'yummy', 'spaghetti.webp', '512g', 2.50, 47, 22),
(2, 'Groce Fusilli', 'Best pasta type, hands down.', 'fusilli.webp', '512g', 2.50, 45, 22),
(3, 'Groce Chocolate Tablet', 'It\'s chocolate.', 'chocolate.webp', '256g', 7.00, 0, 26),
(4, 'Groce Crinkle Cut Potato Chips', 'Crispy crisps innit', 'chips.webp', '256g', 2.70, 2, 25),
(5, 'Galaxy Chocolate Bar', 'Universally delicious 🚀', 'galaxy_choc.webp', '64g', 2.30, 15, 26),
(6, 'Groce Cola Glass Bottle', 'This looks better than real soft drinks in this country, oof.', 'cola.webp', '330ml', 2.10, 32, 27),
(7, 'Groce Lemonade Glass Bottle', 'So classy', 'lemonade.webp', '500ml', 4.60, 499, 27),
(8, 'Groce Still Spring Water', 'Crystal clear', 'water.webp', '1l', 1.40, 500, 27),
(9, 'Groce Vodka', 'Cheap, but classy', 'vodka_cheap.webp', '700ml', 38.00, 23, 28),
(10, 'Floral Classy Vodka', 'REAL classy', 'vodka_classy.webp', '700ml', 130.00, 6, 28),
(11, 'Bread', 'It\'s bread', 'bread.webp', '600g', 7.20, 0, 18),
(12, 'Ramekin', 'Ceramic thingy that you put sauce in', 'ramekin.webp', '1 pc', 3.50, 15, 33),
(13, 'Apples', 'Probably won\'t look as nice as in the picture', 'apples.webp', '1kg', 3.50, 99, 11),
(14, 'Organic Strawberries', '100% Australian-grown in Queensland', 'strawberries.webp', '250g', 5.40, 14, 11),
(15, 'Brushed Potatoes', 'Can be made into unhealthy food if you want. Mmmh, fries, yummy.', 'potatoes.webp', '2kg', 3.90, 26, 12),
(16, 'Tomatoes', 'Technically not a vegetable but as Miles Kington said: “Knowledge is knowing that a tomato is a fruit; wisdom is not putting it in a fruit salad.”', 'tomatoes.webp', '500g', 7.00, 14, 12),
(17, 'Groce Full Cream Milk', 'From happy cows.', 'milk_full.webp', '1l', 1.50, 130, 13),
(18, 'Groce Soy Milk', 'From happy soybeans.', 'milk_soy.webp', '1l', 1.80, 16, 13),
(19, 'Premium Local Milk', 'From the happiest cows in New South Wales.', 'milk_premium.webp', '1.5l', 4.30, 35, 13),
(20, 'Australian Cheese Slices', 'Tastes like Sydney.', 'cheese_australian.webp', '250g', 3.70, 450, 14),
(21, 'Swiss Cheese', 'THAT\'S cheese.', 'cheese_swiss.webp', '300g', 23.00, 5, 14),
(22, '12 Free Range Eggs', 'They have soooo much space, y\'know?', 'eggs.webp', '600g', 5.50, 160, 15),
(23, 'Chicken Breast Fillets', 'Can be made into nuggies.', 'chicken_breast.webp', '500g', 7.00, 180, 16),
(24, 'Premium Beef Rump Steak', 'Be your own favourite steak house.', 'beef_rump.webp', '300g', 18.85, 79, 16),
(25, 'Atlantic Salmon', 'Seems fishy, innit', 'salmon.webp', '300g', 16.00, 56, 17),
(26, 'Groce Italian Tiramisu', 'Small pack', 'tiramisu.webp', '200g', 4.00, 600, 19),
(27, 'German Müsli', '*Not actually from Berlin', 'muesli.webp', '500g', 13.50, 58, 20),
(28, 'American Corn Flakes', 'Howdy, pardnerr.', 'cornflakes.webp', '400g', 6.00, 80, 20),
(29, 'Groce Sunflower Oil', 'To make your food look sunnier.', 'oil_sunflower.webp', '1l', 2.00, 69, 21),
(30, 'Extra Virgin Spanish Olive Oil', '¡Parece de primera!', 'oil_olive.webp', '800ml', 27.50, 3, 21),
(31, 'American Mac and Cheese', 'Put it in the microwave for 3 minutes and your Macaroni and Cheese meal is ready.', 'macandcheese.webp', '350g', 7.00, 86, 23),
(32, 'Strawberry Gourmet Ice Cream', 'From fresh Australian strawberries.', 'icecream_strawberry.webp', '500ml', 7.00, 999, 24),
(33, 'Groce Antibacterial Wet Wipes Lemon', 'Suitable for food preparation surfaces.', 'wetwipes.webp', '90 wipes', 3.20, 8000, 31),
(34, 'Groce Toothbrushes', 'To fill your toothbrush storage.', 'toothbrush.webp', '4 pack', 6.00, 78, 32),
(35, 'Groce Toilet Tissue 3 Ply', 'Super soft and suitable for all your toilet activities.', 'toiletpaper.webp', '33 pack', 21.00, 808, 32);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_buyer`
--
ALTER TABLE `order_buyer`
  ADD PRIMARY KEY (`buyer_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_detail_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_buyer`
--
ALTER TABLE `order_buyer`
  MODIFY `buyer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `order_detail_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
