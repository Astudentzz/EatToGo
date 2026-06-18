-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql210.infinityfree.com
-- Generation Time: Jun 16, 2026 at 03:06 AM
-- Server version: 11.4.12-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_42158944_eattogo`
--

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

CREATE TABLE `feedbacks` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedbacks`
--

INSERT INTO `feedbacks` (`id`, `reservation_id`, `rating`, `comment`, `created_at`) VALUES
(3, 9, 5, 'baikk punyaaa', '2026-05-29 00:53:28'),
(0, 70, 5, 'paduuu bak hangg', '2026-06-11 08:21:38'),
(0, 52, 5, 'The overall dining and reserving expeirence was excellent!', '2026-06-14 12:37:55'),
(0, 138, 3, 'Not bad but forgot about my not too spicy comment T_T', '2026-06-14 17:01:08'),
(0, 138, 3, 'okay...', '2026-06-14 17:01:27'),
(0, 81, 5, 'baikkk', '2026-06-15 00:41:12');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `emoji` varchar(10) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `restaurant_id`, `name`, `price`, `category`, `emoji`, `is_available`, `image`, `description`) VALUES
(1, 2, 'Mi Fen Fish Soup', '10.00', 'Main Course', '🍜', 1, '/uploads/menu_items/6a183dd1d360a.jpg', 'very good'),
(2, 2, 'Mi Hun Kue Fish Soup', '10.00', 'Popular', '🍜', 1, '/uploads/menu_items/deea73b1f11d17eef40026385efc1e85.jpg', 'very bad'),
(3, 2, 'Noodle Curry Fish Soup', '10.00', 'Popular', '🍜', 0, '/uploads/menu_items/6a183e8640adb.jpg', NULL),
(4, 2, 'Silver needle noodles fish soup', '10.00', 'Hot Dishes', '🍜', 1, '/uploads/menu_items/6a183f0a92bd9.jpg', NULL),
(5, 2, 'Rice Fish Soup', '10.00', 'Hot Dishes', '🍚', 1, '/uploads/menu_items/6a183f757ad62.jpg', NULL),
(6, 2, 'Fried Fish Cake', '3.00', 'Sides', '🍥', 1, '/uploads/menu_items/6a183fa331ca7.jpg', NULL),
(7, 2, 'Fried Wanton', '2.50', 'Sides', '🥟', 1, '/uploads/menu_items/6a183fe994cb4.jpg', NULL),
(8, 2, 'Kopi O', '2.50', 'Drinks', '☕', 1, '/uploads/menu_items/6a18408a09282.jpg', NULL),
(9, 2, 'Kopi C', '2.50', 'Drinks', '☕', 1, '/uploads/menu_items/6a1840ad5c420.jpg', NULL),
(10, 3, 'Big Breakfast', '14.00', 'Hot Dishes', '🥓', 1, '/uploads/menu_items/6a184160547b5.jpg', NULL),
(11, 3, 'longtong', '10.00', 'Hot Dishes', '🍜', 1, '/uploads/menu_items/6a18418b49066.jpg', NULL),
(12, 3, 'Grill Salmon with Rice', '15.00', 'Popular', '🐟', 1, '/uploads/menu_items/6a1841c56b0f9.jpg', NULL),
(13, 3, 'Black Pepper Seafood Udon', '16.00', 'Hot Dishes', '🍝', 1, '/uploads/menu_items/6a1841fe42630.jpg', NULL),
(14, 3, 'Nyonya Ayam Masak Merah With Rice', '11.00', 'Hot Dishes', '🍗', 1, '/uploads/menu_items/6a18423052ef8.jpg', NULL),
(15, 3, 'Nyonya Kuih Set', '5.00', 'Sides', '🧁', 1, '/uploads/menu_items/6a184264e65f8.jpg', NULL),
(16, 3, 'Satay', '3.00', 'Sides', '🍖', 1, '/uploads/menu_items/6a184296c4e2a.jpg', NULL),
(17, 3, 'Ice Kacang', '5.00', 'Sides', '🍧', 1, '/uploads/menu_items/6a1842bd25b17.jpg', NULL),
(18, 3, 'Kopi', '3.50', 'Drinks', '☕', 1, '/uploads/menu_items/6a1842eb9d96d.jpg', NULL),
(19, 3, 'Apple Juice', '4.50', 'Drinks', '🧃', 1, '/uploads/menu_items/6a18432661fd6.jpg', NULL),
(20, 2, 'Set A', '30.00', 'Popular', '🥞', 1, '/uploads/menu_items/6a1a90f51e2db.jpg', '5 Fish Soup, 5 fried fish cake, 5 Kopi C'),
(21, 2, 'Mi Fen Fish Soup', '10.00', 'Popular', '🍜', 1, '/uploads/menu_items/6a2068288a896.jpg', 'Milk based fish broth with mifen noodles.'),
(22, 2, 'Thick Mifen Fish Soup', '10.00', 'Popular', '🍽️', 1, '/uploads/menu_items/6a2068c19aced.jpg', 'Milk Based Fish broth with thick mifen noodles'),
(23, 2, 'Big rice with fish soup', '12.00', 'Main Course', '🍚', 0, '/uploads/menu_items/6a206b04c2ed5.jpg', 'big rice yum'),
(24, 7, 'Sushi', '10.00', 'Appetizer', '🍣', 1, '/uploads/menu_items/6a2548caa485f.webp', 'Various types of sushi, 10 per plate.'),
(25, 8, 'Ramyeon', '15.00', 'Main Course', '🍜', 1, '/uploads/menu_items/6a2568a16e068.jpg', 'Simple Korean noodle with vegetables and eggs'),
(26, 8, 'Fresh Kimchi Geotjeori', '20.00', 'Main Course', '🍝', 1, '/uploads/menu_items/6a257c4625cf3.webp', ''),
(27, 8, 'Korean Braised Cod', '20.00', 'Main Course', '🍲', 1, '/uploads/menu_items/6a257d268f5a1.webp', ''),
(28, 8, 'Korean-Chinese Spicy Jjampong', '20.00', 'Main Course', '🍜', 1, '/uploads/menu_items/6a257d8de7960.webp', 'Spicy Seafood Noodles'),
(29, 8, 'Misugaru Latte', '6.00', 'Drinks', '🍶', 1, '/uploads/menu_items/6a257dd4b49a7.webp', ''),
(30, 7, 'Tsukumen', '15.50', 'Main Course', '🍜', 1, '/uploads/menu_items/6a258364932b6.webp', ''),
(31, 7, 'Udon', '12.00', 'Main Course', '🍜', 1, '/uploads/menu_items/6a2583a722c09.webp', ''),
(32, 7, 'Soba', '8.00', 'Main Course', '🍝', 1, '/uploads/menu_items/6a2583e254930.webp', ''),
(33, 7, 'Mugicha', '3.00', 'Drinks', '🫖', 1, '/uploads/menu_items/6a258463a7927.jpg', 'Japanese barley tea'),
(34, 13, 'Dubai Chewy Cookie', '5.00', 'Popular', '🍽️', 1, '/uploads/menu_items/6a2904481d0f9.png', ''),
(35, 13, 'Tiramissyou', '10.00', 'Popular', '🍽️', 1, '/uploads/menu_items/6a29072ced3be.jpg', ''),
(36, 13, 'Matcha Strawberry', '8.00', 'Drinks', '🍽️', 1, '/uploads/menu_items/6a29075ee5463.jpg', ''),
(37, 13, 'Spagetti Carbonara', '13.00', 'Hot Dishes', '🍽️', 1, '/uploads/menu_items/6a29082f928ff.jpg', ''),
(38, 13, 'Soft Chocolate Chip Cookies', '6.00', 'Appetizer', '🍽️', 1, '/uploads/menu_items/6a29090dba8c4.jpg', ''),
(39, 13, 'Chicken Chop', '13.00', 'Hot Dishes', '🍽️', 1, '/uploads/menu_items/6a290a95da752.jpeg', ''),
(40, 14, 'Hot Latte', '8.00', 'Drinks', '🍽️', 1, '/uploads/menu_items/6a2a6ab0f2299.jpg', ''),
(41, 14, 'Ice Latte', '9.00', 'Drinks', '🍽️', 1, '/uploads/menu_items/6a2a6ae4c6242.jpg', ''),
(42, 14, 'Hot Americano', '6.00', 'Drinks', '🍽️', 1, '/uploads/menu_items/6a2a6b1b25d31.webp', ''),
(43, 14, 'Ice Americano', '7.00', 'Drinks', '🍽️', 1, '/uploads/menu_items/6a2a6b3f3d4c9.jpg', ''),
(44, 14, 'Hot mocha', '8.00', 'Drinks', '🍽️', 1, '/uploads/menu_items/6a2a6b655e765.webp', ''),
(45, 14, 'Ice Mocha', '9.00', 'Drinks', '🍽️', 0, '/uploads/menu_items/6a2a6b8588635.jpg', ''),
(46, 14, 'Croissant', '6.00', 'Appetizer', '🍽️', 1, '/uploads/menu_items/67924b03c6f27f8b61c44b887ccb8647.webp', ''),
(47, 14, 'Red Velvet Cake', '11.00', 'Popular', '🍽️', 1, '/uploads/menu_items/c3392f1fd9f2a4203fb1a4c5ab13702b.webp', ''),
(48, 14, 'Carrot Cake', '11.00', 'Popular', '🍽️', 1, '/uploads/menu_items/2719a351d4745adce5c1b8f36d938fe3.jpg', ''),
(49, 13, 'Ice Latte', '8.00', 'Drinks', '🍽️', 1, '/uploads/menu_items/c0dae2611b6938a43e87e87671b3801f.jpg', ''),
(50, 13, 'Ice Lemon Tea', '6.00', 'Drinks', '🍽️', 1, '/uploads/menu_items/27d6a1713ba03b943176362d5690a87e.jpg', ''),
(51, 13, 'Brownies', '10.00', 'Appetizer', '🍽️', 1, '/uploads/menu_items/b7e7732cfe6ab3bcf4237aedb760300c.jpg', ''),
(52, 2, 'Rice Soup', '10.00', 'Popular', '🍙', 1, '/uploads/menu_items/a9ecf557b433fa5c8676cda9eccf36bc.jpg', 'rice with soup sedap');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','preparing','ready','served') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `reservation_id`, `total_amount`, `status`, `created_at`) VALUES
(1, 1, '15.00', 'ready', '2026-05-28 13:44:55'),
(3, 3, '15.00', 'pending', '2026-05-28 13:48:58'),
(4, 4, '20.00', 'ready', '2026-05-28 13:54:16'),
(6, 7, '10.00', 'ready', '2026-05-28 16:56:35'),
(7, 8, '32.00', 'pending', '2026-05-29 00:48:02'),
(8, 9, '15.00', 'pending', '2026-05-29 00:51:52'),
(9, 10, '22.50', 'ready', '2026-05-29 01:22:51'),
(13, 17, '17.50', 'ready', '2026-06-03 15:16:33'),
(14, 18, '30.00', 'pending', '2026-06-03 18:16:47'),
(15, 20, '25.00', 'pending', '2026-06-03 18:26:41'),
(16, 20, '18.50', 'pending', '2026-06-03 18:27:02'),
(28, 34, '32.00', 'ready', '2026-06-04 10:45:46'),
(29, 35, '14.00', 'pending', '2026-06-04 11:34:37'),
(30, 36, '132.00', 'ready', '2026-06-04 14:18:57'),
(31, 37, '48.00', 'ready', '2026-06-04 14:45:53'),
(32, 39, '10.00', 'pending', '2026-06-04 15:21:34'),
(33, 39, '10.00', 'pending', '2026-06-04 15:21:49'),
(34, 40, '10.00', 'pending', '2026-06-04 15:24:48'),
(43, 52, '10.00', 'ready', '2026-06-07 10:34:11'),
(44, 54, '15.00', 'pending', '2026-06-07 12:49:18'),
(46, 57, '131.00', 'ready', '2026-06-09 14:26:05'),
(47, 58, '94.00', 'ready', '2026-06-09 15:16:48'),
(49, 62, '20.00', 'pending', '2026-06-10 07:03:40'),
(50, 63, '24.00', 'pending', '2026-06-10 07:09:02'),
(51, 64, '10.00', 'pending', '2026-06-10 07:49:30'),
(52, 66, '10.00', 'pending', '2026-06-10 07:51:06'),
(53, 67, '14.00', 'pending', '2026-06-10 08:52:07'),
(55, 70, '57.00', 'ready', '2026-06-11 08:16:03'),
(56, 71, '30.00', 'pending', '2026-06-11 11:47:36'),
(58, 73, '30.00', 'pending', '2026-06-11 11:53:06'),
(59, 74, '10.00', 'pending', '2026-06-11 11:53:27'),
(65, 81, '26.00', 'ready', '2026-06-11 17:38:47'),
(66, 82, '37.00', 'pending', '2026-06-11 18:27:14'),
(74, 92, '19.00', 'pending', '2026-06-12 00:16:42'),
(76, 93, '27.50', 'pending', '2026-06-12 00:48:21'),
(78, 102, '47.00', 'ready', '2026-06-12 01:11:45'),
(79, 104, '25.00', 'pending', '2026-06-12 01:27:46'),
(80, 105, '3.50', 'pending', '2026-06-12 02:08:19'),
(81, 106, '9.00', 'pending', '2026-06-12 02:09:32'),
(82, 108, '33.00', 'pending', '2026-06-12 02:12:07'),
(83, 111, '15.50', 'pending', '2026-06-12 02:15:11'),
(84, 112, '52.00', 'pending', '2026-06-12 02:19:42'),
(85, 113, '40.00', 'pending', '2026-06-12 02:22:29'),
(87, 115, '10.00', 'pending', '2026-06-14 07:27:00'),
(88, 116, '5.50', 'pending', '2026-06-14 08:30:04'),
(89, 117, '80.00', 'pending', '2026-06-14 12:50:24'),
(90, 118, '39.00', 'pending', '2026-06-14 14:08:36'),
(96, 122, '20.00', 'pending', '2026-06-14 15:12:51'),
(97, 122, '20.00', 'pending', '2026-06-14 15:13:16'),
(98, 122, '10.00', 'pending', '2026-06-14 15:14:25'),
(99, 122, '16.00', 'pending', '2026-06-14 15:17:43'),
(100, 122, '10.00', 'pending', '2026-06-14 15:18:10'),
(108, 128, '45.00', 'pending', '2026-06-14 15:42:06'),
(110, 127, '20.00', 'ready', '2026-06-14 16:04:31'),
(112, 134, '24.00', 'pending', '2026-06-14 16:06:09'),
(113, 135, '39.00', 'ready', '2026-06-14 16:08:55'),
(114, 137, '25.00', 'ready', '2026-06-14 16:31:31'),
(115, 138, '41.00', 'ready', '2026-06-14 16:58:22'),
(116, 140, '45.00', 'pending', '2026-06-15 00:36:39'),
(117, 142, '10.00', 'pending', '2026-06-15 00:49:58'),
(118, 143, '11.00', 'ready', '2026-06-15 01:41:02'),
(119, 144, '8.00', 'pending', '2026-06-15 02:04:12'),
(120, 146, '93.00', 'ready', '2026-06-15 02:52:07'),
(121, 147, '30.00', 'pending', '2026-06-15 02:58:16'),
(122, 148, '10.00', 'pending', '2026-06-15 03:00:57'),
(123, 149, '10.00', 'ready', '2026-06-15 03:21:13');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `quantity`, `price`) VALUES
(1, 1, 12, 1, '15.00'),
(5, 3, 12, 1, '15.00'),
(6, 4, 3, 1, '10.00'),
(7, 4, 4, 1, '10.00'),
(11, 6, 2, 1, '10.00'),
(12, 7, 10, 1, '14.00'),
(13, 7, 11, 1, '10.00'),
(14, 7, 18, 1, '3.50'),
(15, 7, 19, 1, '4.50'),
(16, 8, 3, 1, '10.00'),
(17, 8, 7, 1, '2.50'),
(18, 8, 8, 1, '2.50'),
(19, 9, 1, 2, '10.00'),
(20, 9, 9, 1, '2.50'),
(27, 13, 10, 1, '14.00'),
(28, 13, 18, 1, '3.50'),
(29, 14, 20, 1, '30.00'),
(30, 15, 12, 1, '15.00'),
(31, 15, 15, 1, '5.00'),
(32, 15, 17, 1, '5.00'),
(33, 16, 12, 1, '15.00'),
(34, 16, 18, 1, '3.50'),
(51, 28, 13, 1, '16.00'),
(52, 28, 14, 1, '11.00'),
(53, 28, 17, 1, '5.00'),
(54, 29, 10, 1, '14.00'),
(55, 30, 10, 6, '14.00'),
(56, 30, 12, 1, '15.00'),
(57, 30, 13, 1, '16.00'),
(58, 30, 14, 1, '11.00'),
(59, 30, 16, 2, '3.00'),
(60, 31, 6, 1, '3.00'),
(61, 31, 7, 1, '2.50'),
(62, 31, 8, 1, '2.50'),
(63, 31, 20, 1, '30.00'),
(64, 31, 22, 1, '10.00'),
(65, 32, 1, 1, '10.00'),
(66, 33, 1, 1, '10.00'),
(67, 34, 2, 1, '10.00'),
(79, 43, 24, 1, '10.00'),
(80, 44, 25, 1, '15.00'),
(82, 46, 24, 3, '10.00'),
(83, 46, 30, 2, '15.50'),
(84, 46, 31, 2, '12.00'),
(85, 46, 32, 2, '8.00'),
(86, 46, 33, 10, '3.00'),
(87, 47, 25, 2, '15.00'),
(88, 47, 26, 1, '20.00'),
(89, 47, 27, 1, '20.00'),
(90, 47, 29, 4, '6.00'),
(92, 49, 1, 1, '10.00'),
(93, 49, 2, 1, '10.00'),
(94, 50, 10, 1, '14.00'),
(95, 50, 11, 1, '10.00'),
(96, 51, 1, 1, '10.00'),
(97, 52, 1, 1, '10.00'),
(98, 53, 10, 1, '14.00'),
(101, 55, 40, 1, '8.00'),
(102, 55, 41, 1, '9.00'),
(103, 55, 45, 2, '9.00'),
(104, 55, 47, 1, '11.00'),
(105, 55, 48, 1, '11.00'),
(106, 56, 20, 1, '30.00'),
(109, 58, 1, 3, '10.00'),
(110, 59, 2, 1, '10.00'),
(117, 65, 34, 1, '5.00'),
(118, 65, 36, 1, '8.00'),
(119, 65, 39, 1, '13.00'),
(120, 66, 43, 1, '7.00'),
(121, 66, 44, 1, '8.00'),
(122, 66, 47, 1, '11.00'),
(123, 66, 48, 1, '11.00'),
(131, 74, 40, 1, '8.00'),
(132, 74, 47, 1, '11.00'),
(134, 76, 1, 1, '10.00'),
(135, 76, 7, 1, '2.50'),
(136, 76, 8, 1, '2.50'),
(137, 76, 9, 1, '2.50'),
(138, 76, 22, 1, '10.00'),
(140, 78, 25, 1, '15.00'),
(141, 78, 28, 1, '20.00'),
(142, 78, 29, 2, '6.00'),
(143, 79, 11, 1, '10.00'),
(144, 79, 12, 1, '15.00'),
(145, 80, 18, 1, '3.50'),
(146, 81, 41, 1, '9.00'),
(147, 82, 47, 3, '11.00'),
(148, 83, 30, 1, '15.50'),
(149, 84, 37, 4, '13.00'),
(150, 85, 1, 1, '10.00'),
(151, 85, 2, 1, '10.00'),
(152, 85, 4, 1, '10.00'),
(153, 85, 5, 1, '10.00'),
(155, 87, 1, 1, '10.00'),
(156, 88, 6, 1, '3.00'),
(157, 88, 7, 1, '2.50'),
(158, 89, 52, 8, '10.00'),
(159, 90, 10, 1, '14.00'),
(160, 90, 11, 1, '10.00'),
(161, 90, 12, 1, '15.00'),
(170, 96, 4, 2, '10.00'),
(171, 97, 2, 2, '10.00'),
(172, 98, 2, 1, '10.00'),
(173, 99, 2, 1, '10.00'),
(174, 99, 6, 2, '3.00'),
(175, 100, 2, 1, '10.00'),
(184, 108, 10, 1, '14.00'),
(185, 108, 11, 1, '10.00'),
(186, 108, 15, 1, '5.00'),
(187, 108, 18, 2, '3.50'),
(188, 108, 19, 2, '4.50'),
(190, 110, 2, 2, '10.00'),
(193, 112, 10, 1, '14.00'),
(194, 112, 11, 1, '10.00'),
(195, 113, 10, 1, '14.00'),
(196, 113, 11, 1, '10.00'),
(197, 113, 12, 1, '15.00'),
(198, 114, 11, 1, '10.00'),
(199, 114, 12, 1, '15.00'),
(200, 115, 25, 1, '15.00'),
(201, 115, 26, 1, '20.00'),
(202, 115, 29, 1, '6.00'),
(203, 116, 10, 2, '14.00'),
(204, 116, 11, 1, '10.00'),
(205, 116, 18, 2, '3.50'),
(206, 117, 24, 1, '10.00'),
(207, 118, 47, 1, '11.00'),
(208, 119, 40, 1, '8.00'),
(209, 120, 6, 31, '3.00'),
(210, 121, 1, 3, '10.00'),
(211, 122, 1, 1, '10.00'),
(212, 123, 2, 1, '10.00');

-- --------------------------------------------------------

--
-- Table structure for table `owner_applications`
--

CREATE TABLE `owner_applications` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `certificate_path` varchar(500) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `owner_applications`
--

INSERT INTO `owner_applications` (`id`, `name`, `email`, `phone`, `password`, `certificate_path`, `status`, `reviewed_by`, `reviewed_at`, `rejection_reason`, `created_at`) VALUES
(11, 'Izzatun', 'nurizzatunnadhirah@graduate.utm.my', '0102550706', '$2y$10$h1ziscn2u6ekjF1FhoDdRumtBRCqVIHmSY2xnvO9jv5wEMOr9gdy.', '/uploads/certificates/835fa098002fd9b9fe1c89756fd3038c.pdf', 'approved', 1, '2026-06-11 19:19:32', NULL, '2026-06-11 19:19:09'),
(16, 'i food corner', 'navinramu2501@gmail.com', '0195302144', '$2y$10$LgCdYFioCaGaMdHj9YC90ejJ4r/w53KMKqlIOFDrMops/IQ.cJ5zu', '/uploads/certificates/7ffc34d1c692201149f820b734ecee0a.jpg', 'approved', 1, '2026-06-14 20:34:53', NULL, '2026-06-14 20:33:30');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `reservation_date` date NOT NULL,
  `reservation_time` time NOT NULL,
  `num_people` int(11) NOT NULL,
  `status` enum('pending_payment','pending','confirmed','rejected','completed') DEFAULT 'pending_payment',
  `arrival_confirmed` tinyint(1) DEFAULT 0,
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `special_requests` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_proof` varchar(255) DEFAULT NULL,
  `payment_verified` tinyint(1) DEFAULT 0,
  `payment_submitted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `restaurant_id`, `reservation_date`, `reservation_time`, `num_people`, `status`, `arrival_confirmed`, `customer_name`, `customer_phone`, `customer_email`, `special_requests`, `created_at`, `payment_proof`, `payment_verified`, `payment_submitted_at`) VALUES
(1, 7, 3, '2026-05-28', '09:00:00', 2, 'confirmed', 0, 'John Doe', '999', 'tis.some.nonesense@gmail.com', 'Nope', '2026-05-28 13:44:46', NULL, 0, NULL),
(3, 7, 3, '2026-05-28', '01:00:00', 2, 'pending', 0, 'John Doe', '999', 'tis.some.nonesense@gmail.com', '', '2026-05-28 13:48:50', NULL, 0, NULL),
(4, 7, 2, '2026-05-28', '11:00:00', 5, 'confirmed', 0, 'John Doe', '999', 'tis.some.nonesense@gmail.com', '', '2026-05-28 13:54:07', NULL, 0, NULL),
(5, 7, 2, '2026-05-28', '11:00:00', 5, 'pending', 0, NULL, NULL, NULL, NULL, '2026-05-28 13:54:09', NULL, 0, NULL),
(7, 7, 2, '2026-05-28', '08:00:00', 2, 'confirmed', 0, NULL, NULL, NULL, NULL, '2026-05-28 16:56:24', NULL, 0, NULL),
(8, 9, 3, '2026-05-29', '09:00:00', 2, 'pending', 1, 'Hadif Parker', '0174545008', 'muhammaddanishhadifmohdhazuan@gmail.com', 'apple juice taknak apple', '2026-05-29 00:47:37', NULL, 0, NULL),
(9, 9, 2, '2026-05-29', '12:00:00', 2, 'rejected', 1, 'Hadif Parker', '0174545008', 'muhammaddanishhadifmohdhazuan@gmail.com', 'masak kasi sedap punya mehh', '2026-05-29 00:51:27', NULL, 0, NULL),
(10, 7, 2, '2026-05-29', '08:00:00', 2, 'confirmed', 0, 'John Doe', '999', 'tis.some.nonesense@gmail.com', 'Kopi C tknk C', '2026-05-29 01:22:35', NULL, 0, NULL),
(12, 7, 3, '2026-05-29', '08:00:00', 2, 'pending', 0, NULL, NULL, NULL, NULL, '2026-05-29 13:22:31', NULL, 0, NULL),
(17, 9, 3, '2026-06-03', '08:00:00', 2, 'confirmed', 1, 'Hadif Parker', '0174545008', 'muhammaddanishhadifmohdhazuan@gmail.com', '', '2026-06-03 15:16:24', NULL, 0, NULL),
(18, 9, 2, '2026-06-05', '08:00:00', 3, 'rejected', 0, 'Hadif Parker', '0174545008', 'muhammaddanishhadifmohdhazuan@gmail.com', '', '2026-06-03 18:16:04', NULL, 0, NULL),
(19, 9, 3, '2026-06-03', '08:00:00', 2, 'pending', 1, NULL, NULL, NULL, NULL, '2026-06-03 18:26:06', NULL, 0, NULL),
(20, 9, 3, '2026-06-06', '01:00:00', 2, 'rejected', 0, 'Hadif Parker', '0174545008', 'muhammaddanishhadifmohdhazuan@gmail.com', '', '2026-06-03 18:26:23', NULL, 0, NULL),
(32, 9, 3, '2026-06-06', '12:00:00', 2, 'rejected', 0, NULL, NULL, NULL, NULL, '2026-06-04 10:29:02', NULL, 0, NULL),
(33, 9, 3, '2026-06-06', '09:00:00', 2, 'rejected', 0, NULL, NULL, NULL, NULL, '2026-06-04 10:31:48', NULL, 0, NULL),
(34, 9, 3, '2026-06-05', '01:00:00', 2, 'confirmed', 0, 'Hadif Parker', '0174545008', 'muhammaddanishhadifmohdhazuan@gmail.com', '', '2026-06-04 10:45:19', '/uploads/payment_proofs/6a215777aa285.png', 1, '2026-06-04 10:46:16'),
(35, 9, 3, '2026-06-05', '12:00:00', 2, 'pending', 0, 'Hadif Parker', '0174545008', 'muhammaddanishhadifmohdhazuan@gmail.com', '', '2026-06-04 10:47:38', NULL, 0, NULL),
(36, 7, 3, '2026-06-06', '09:00:00', 10, 'confirmed', 0, 'John Doe', '999', 'tis.some.nonesense@gmail.com', 'Window seat', '2026-06-04 14:18:31', '/uploads/payment_proofs/6a218984990ab.jpeg', 1, '2026-06-04 14:19:48'),
(37, 9, 2, '2026-06-09', '01:00:00', 6, 'confirmed', 1, 'Hadif Parker', '0174545008', 'muhammaddanishhadifmohdhazuan@gmail.com', 'hehe', '2026-06-04 14:45:22', '/uploads/payment_proofs/6a21901197a9d.png', 1, '2026-06-04 14:47:45'),
(38, 9, 2, '2026-06-09', '12:00:00', 2, 'rejected', 0, NULL, NULL, NULL, NULL, '2026-06-04 14:48:27', NULL, 0, NULL),
(39, 9, 2, '2026-06-04', '10:00:00', 2, 'rejected', 0, 'Hadif Parker', '0174545008', 'muhammaddanishhadifmohdhazuan@gmail.com', '', '2026-06-04 15:21:20', NULL, 0, '2026-06-04 15:22:46'),
(40, 9, 2, '2026-06-04', '09:00:00', 2, 'rejected', 0, 'Hadif Parker', '0174545008', 'muhammaddanishhadifmohdhazuan@gmail.com', '', '2026-06-04 15:24:38', NULL, 0, '2026-06-04 15:25:11'),
(50, 7, 7, '2026-06-07', '11:00:00', 20, 'rejected', 0, NULL, NULL, NULL, NULL, '2026-06-07 10:33:21', NULL, 0, NULL),
(51, 7, 7, '2026-06-07', '01:00:00', 20, 'rejected', 0, NULL, NULL, NULL, NULL, '2026-06-07 10:33:38', NULL, 0, NULL),
(52, 7, 7, '2026-06-12', '03:00:00', 12, 'confirmed', 1, 'John Doe', '999', 'tis.some.nonesense@gmail.com', 'Set the chairs and tables together', '2026-06-07 10:34:03', '/uploads/payment_proofs/6a254978b4d92.pdf', 1, '2026-06-07 10:35:37'),
(53, 7, 8, '2026-06-07', '12:00:00', 2, 'rejected', 0, NULL, NULL, NULL, NULL, '2026-06-07 12:46:41', NULL, 0, NULL),
(54, 7, 8, '2026-06-07', '05:00:00', 2, 'rejected', 0, 'John Doe', '999', 'tis.some.nonesense@gmail.com', '', '2026-06-07 12:48:49', NULL, 0, '2026-06-07 12:50:00'),
(57, 9, 7, '2026-06-09', '01:00:00', 20, 'confirmed', 0, 'Hadif Parker', '0174545008', 'muhammaddanishhadifmohdhazuan@gmail.com', 'hehe', '2026-06-09 14:24:58', '/uploads/payment_proofs/6a2822a7439f8.jpg', 1, '2026-06-09 14:26:47'),
(58, 9, 8, '2026-06-12', '06:00:00', 4, 'confirmed', 0, 'Hadif Parker', '0174545008', 'muhammaddanishhadifmohdhazuan@gmail.com', '', '2026-06-09 15:16:15', '/uploads/payment_proofs/6a282e9adb539.png', 1, '2026-06-09 15:17:47'),
(62, 7, 2, '2026-06-10', '02:00:00', 20, 'rejected', 0, 'John Doe', '999', 'tis.some.nonesense@gmail.com', '', '2026-06-10 07:03:20', NULL, 0, NULL),
(63, 7, 3, '2026-06-10', '08:00:00', 2, 'pending', 0, 'John Doe', '999', 'tis.some.nonesense@gmail.com', '', '2026-06-10 07:08:56', NULL, 0, NULL),
(64, 7, 2, '2026-06-10', '10:00:00', 2, 'pending', 0, NULL, NULL, NULL, NULL, '2026-06-10 07:49:01', NULL, 0, NULL),
(65, 7, 2, '2026-06-10', '08:00:00', 2, 'pending', 0, NULL, NULL, NULL, NULL, '2026-06-10 07:50:22', NULL, 0, NULL),
(66, 7, 2, '2026-06-10', '11:00:00', 2, 'rejected', 0, 'John Doe', '01345728883', 'tis.some.nonesense@gmail.com', 'Nice', '2026-06-10 07:50:57', NULL, 0, NULL),
(67, 7, 3, '2026-06-10', '10:00:00', 50, 'pending', 0, 'John Doe', '999', 'tis.some.nonesense@gmail.com', '', '2026-06-10 08:51:39', NULL, 0, NULL),
(69, 9, 14, '2026-06-13', '08:00:00', 30, 'rejected', 0, NULL, NULL, NULL, NULL, '2026-06-11 08:14:51', NULL, 0, NULL),
(70, 9, 14, '2026-06-13', '07:00:00', 4, 'confirmed', 1, 'Hadif Parker', '0174545008', 'muhammaddanishhadifmohdhazuan@gmail.com', 'window seat', '2026-06-11 08:15:06', '/uploads/payment_proofs/6a2a6f11d6971.jpg', 1, '2026-06-11 08:17:22'),
(81, 9, 13, '2026-06-12', '17:00:00', 3, 'confirmed', 1, 'Hadif Parker', '0174545008', 'muhammaddanishhadifmohdhazuan@gmail.com', '', '2026-06-11 17:34:58', '/uploads/payment_proofs/9700b3d7439f3b97935340d42f2f2c49.png', 1, '2026-06-11 17:59:44'),
(82, 9, 14, '2026-06-11', '20:00:00', 2, 'confirmed', 0, 'Hadif Parker', '0174545008', 'muhammaddanishhadifmohdhazuan@gmail.com', '', '2026-06-11 18:27:01', '/uploads/payment_proofs/fbe6a3300bf35d1e9b4c378b27bbe47d.jpg', 1, '2026-06-11 18:27:52'),
(91, 9, 14, '2026-06-13', '10:00:00', 2, 'rejected', 0, NULL, NULL, NULL, NULL, '2026-06-12 00:14:52', NULL, 0, NULL),
(92, 9, 14, '2026-06-12', '11:00:00', 2, 'confirmed', 0, 'Hadif Parker', '0174545008', 'muhammaddanishhadifmohdhazuan@gmail.com', '', '2026-06-12 00:16:26', '/uploads/payment_proofs/a6a9d656a26205846563cecefc058f96.jpeg', 1, '2026-06-12 00:17:56'),
(93, 9, 2, '2026-06-14', '10:00:00', 4, 'confirmed', 0, 'Hadif Parker', '0174545008', 'muhammaddanishhadifmohdhazuan@gmail.com', 'baby chair', '2026-06-12 00:47:46', '/uploads/payment_proofs/eb0857c3e94de46017f3ad40e972ac50.jpeg', 1, '2026-06-12 00:49:20'),
(95, 9, 7, '2026-06-12', '15:00:00', 5, 'pending', 0, NULL, NULL, NULL, NULL, '2026-06-12 00:56:05', NULL, 0, NULL),
(97, 9, 7, '2026-06-12', '13:00:00', 2, 'pending', 0, NULL, NULL, NULL, NULL, '2026-06-12 00:56:46', NULL, 0, NULL),
(98, 9, 7, '2026-06-12', '17:00:00', 2, 'pending', 0, NULL, NULL, NULL, NULL, '2026-06-12 00:57:06', NULL, 0, NULL),
(99, 9, 7, '2026-06-12', '19:00:00', 2, 'confirmed', 0, NULL, NULL, NULL, NULL, '2026-06-12 00:57:16', NULL, 0, NULL),
(102, 9, 8, '2026-06-12', '18:00:00', 2, 'confirmed', 0, 'Hadif Parker', '0174545008', 'muhammaddanishhadifmohdhazuan@gmail.com', 'heheh', '2026-06-12 01:11:26', '/uploads/payment_proofs/c3adbe57efcd86817e859a898b6c03ca.jpg', 1, '2026-06-12 01:12:11'),
(103, 7, 13, '2026-06-12', '17:00:00', 27, 'pending', 0, NULL, NULL, NULL, NULL, '2026-06-12 01:18:09', NULL, 0, NULL),
(104, 9, 3, '2026-06-12', '17:00:00', 2, 'pending', 0, 'Hadif Parker', '0174545008', 'muhammaddanishhadifmohdhazuan@gmail.com', '', '2026-06-12 01:27:39', NULL, 0, NULL),
(105, 43, 3, '2026-06-12', '13:00:00', 2, 'pending', 0, NULL, NULL, NULL, NULL, '2026-06-12 02:08:02', NULL, 0, NULL),
(106, 44, 14, '2026-06-12', '11:00:00', 2, 'confirmed', 0, 'Norleez Fhatihah', '0174751206', 'norleezf@gmail.com', '', '2026-06-12 02:09:00', NULL, 0, NULL),
(107, 43, 8, '2026-06-12', '14:00:00', 2, 'confirmed', 0, NULL, NULL, NULL, NULL, '2026-06-12 02:11:12', NULL, 0, NULL),
(108, 9, 14, '2026-06-13', '11:00:00', 7, 'confirmed', 0, 'Hadif Parker', '0174545008', 'muhammaddanishhadifmohdhazuan@gmail.com', 'besdayy', '2026-06-12 02:11:28', '/uploads/payment_proofs/edc5ffd582fb785c333d55f4abcb8370.jpg', 1, '2026-06-12 02:15:26'),
(110, 49, 7, '2026-06-12', '17:00:00', 20, 'confirmed', 0, NULL, NULL, NULL, NULL, '2026-06-12 02:14:25', NULL, 0, NULL),
(111, 47, 7, '2026-06-12', '19:00:00', 20, 'confirmed', 0, 'Yasmin cool', '01162461672', 'yasminjeffry0611@gmail.com', 'nak sambal pink', '2026-06-12 02:14:37', '/uploads/payment_proofs/17042e2020f215f4238504910092830e.jpeg', 1, '2026-06-12 02:16:35'),
(112, 52, 13, '2026-06-13', '15:00:00', 4, 'confirmed', 0, 'LEE jh', '0196945321', 'jh1ee393.06@gmail.com', 'annual dinner', '2026-06-12 02:18:44', '/uploads/payment_proofs/64836e395c6adcb6224b4bd1930b1775.pdf', 1, '2026-06-12 02:22:57'),
(113, 55, 2, '2026-06-12', '11:00:00', 2, 'pending', 0, 'MARCUS LAI WEI ZE', '0177653342', 'marcuslaiweize@gmail.com', '', '2026-06-12 02:22:16', NULL, 0, NULL),
(115, 7, 2, '2026-06-15', '08:00:00', 2, 'rejected', 0, 'John Doe', '999', 'tis.some.nonesense@gmail.com', '', '2026-06-14 07:26:54', NULL, 0, NULL),
(116, 63, 2, '2026-06-18', '09:00:00', 2, 'rejected', 0, 'Ng Yue Yang', '016-9330771', 'ngyueyang.316@gmail.com', '-', '2026-06-14 08:29:57', NULL, 0, '2026-06-14 08:30:34'),
(117, 64, 2, '2026-06-26', '08:00:00', 30, 'confirmed', 0, 'Engku Afif', '0139380409', 'engkuijat06@gmail.com', '', '2026-06-14 12:49:37', '/uploads/payment_proofs/df3cd598671b09215cc6b5df82c68826.png', 1, '2026-06-14 12:53:26'),
(118, 7, 3, '2026-06-15', '09:00:00', 50, 'rejected', 0, 'John Doe', '999', 'tis.some.nonesense@gmail.com', '', '2026-06-14 14:08:26', NULL, 0, NULL),
(119, 7, 3, '2026-06-17', '09:00:00', 2, 'rejected', 0, NULL, NULL, NULL, NULL, '2026-06-14 14:11:28', NULL, 0, NULL),
(120, 7, 3, '2026-06-15', '10:00:00', 2, 'rejected', 0, NULL, NULL, NULL, NULL, '2026-06-14 14:12:29', NULL, 0, NULL),
(121, 7, 7, '2026-06-23', '11:00:00', 2, 'pending', 0, NULL, NULL, NULL, NULL, '2026-06-14 14:13:46', NULL, 0, NULL),
(127, 63, 2, '2026-06-15', '08:00:00', 2, 'confirmed', 0, 'Ng Yue Yang', '016-9330771', 'ngyueyang.316@gmail.com', '-', '2026-06-14 15:39:30', '/uploads/payment_proofs/abb4d625307e082972d793f1094fc8f6.jpg', 1, '2026-06-14 16:04:52'),
(128, 9, 3, '2026-06-15', '10:00:00', 4, 'confirmed', 0, 'Hadif Parker', '0174545008', 'muhammaddanishhadifmohdhazuan@gmail.com', '', '2026-06-14 15:40:05', '/uploads/payment_proofs/aca193f018e62b84a34e1a38298625fc.jpg', 1, '2026-06-14 15:43:20'),
(130, 9, 2, '2026-06-16', '08:00:00', 2, 'confirmed', 0, NULL, NULL, NULL, NULL, '2026-06-14 15:46:12', NULL, 0, NULL),
(132, 9, 13, '2026-06-15', '14:00:00', 4, 'pending', 0, NULL, NULL, NULL, NULL, '2026-06-14 15:55:30', NULL, 0, NULL),
(133, 7, 3, '2026-06-23', '08:00:00', 2, 'pending', 0, NULL, NULL, NULL, NULL, '2026-06-14 16:03:23', NULL, 0, NULL),
(135, 7, 3, '2026-06-15', '17:00:00', 2, 'confirmed', 0, 'John Doe', '999', 'tis.some.nonesense@gmail.com', '', '2026-06-14 16:08:04', NULL, 0, NULL),
(136, 7, 3, '2026-06-15', '15:00:00', 2, 'confirmed', 1, NULL, NULL, NULL, NULL, '2026-06-14 16:29:18', NULL, 0, NULL),
(137, 7, 3, '2026-06-15', '11:00:00', 2, 'confirmed', 1, 'John Doe', '999', 'tis.some.nonesense@gmail.com', 'qsadfghbnm,.', '2026-06-14 16:31:27', '/uploads/payment_proofs/43a46701a5abea23bdea97724efc29cd.pdf', 1, '2026-06-14 16:31:57'),
(138, 7, 8, '2026-06-15', '17:00:00', 2, 'confirmed', 1, 'John Doe', '999', 'tis.some.nonesense@gmail.com', 'Not too spicy', '2026-06-14 16:58:14', '/uploads/payment_proofs/35120ce7115d634821563566b4cf2249.pdf', 1, '2026-06-14 16:59:04'),
(139, 9, 7, '2026-06-16', '15:00:00', 2, 'confirmed', 1, NULL, NULL, NULL, NULL, '2026-06-14 17:42:08', NULL, 0, NULL),
(140, 9, 3, '2026-06-16', '14:00:00', 4, 'rejected', 0, 'Hadif Parker', '0174545008', 'muhammaddanishhadifmohdhazuan@gmail.com', 'heheh', '2026-06-15 00:36:21', NULL, 0, '2026-06-15 00:37:45'),
(141, 9, 2, '2026-06-17', '08:00:00', 2, 'rejected', 0, NULL, NULL, NULL, NULL, '2026-06-15 00:38:37', NULL, 0, NULL),
(142, 7, 7, '2026-06-17', '17:00:00', 2, 'confirmed', 1, 'Navin', '0195302144', 'tis.some.nonesense@gmail.com', '', '2026-06-15 00:49:44', '/uploads/payment_proofs/5dc3de81a5a4372b46e494da81f4a9d6.png', 0, '2026-06-15 00:51:32'),
(143, 7, 14, '2026-06-15', '10:00:00', 2, 'confirmed', 1, 'John Doe', '999', 'tis.some.nonesense@gmail.com', '', '2026-06-15 01:40:46', '/uploads/payment_proofs/46b9588a0e490caf777d4cf363045870.jpg', 1, '2026-06-15 01:41:52'),
(144, 7, 14, '2026-06-15', '11:00:00', 2, 'confirmed', 1, 'John Doe', '999', 'tis.some.nonesense@gmail.com', '', '2026-06-15 02:03:36', '/uploads/payment_proofs/3daf781fa73e2fd567a6e779530dabcc.jpg', 0, '2026-06-15 02:05:13'),
(146, 7, 2, '2026-06-15', '14:00:00', 30, 'confirmed', 1, 'yasmin cool', '1234567890', 'tis.some.nonesense@gmail.com', 'six seven', '2026-06-15 02:51:05', '/uploads/payment_proofs/ab3bbc091a2789857bb082cd3e91cf7d.jpg', 1, '2026-06-15 02:54:21'),
(147, 7, 2, '2026-06-15', '11:00:00', 2, 'pending', 0, 'yasmin cool', '1234567890', 'tis.some.nonesense@gmail.com', 'allergic gum', '2026-06-15 02:57:59', '/uploads/payment_proofs/8bed3a9f2c8bc4d76dd1450d9f10bd26.jpg', 0, '2026-06-15 02:59:25'),
(148, 7, 2, '2026-06-15', '13:00:00', 2, 'confirmed', 0, 'John Doe', '999', 'tis.some.nonesense@gmail.com', '', '2026-06-15 03:00:31', '/uploads/payment_proofs/b15624700bc18bc926f549964e375e4f.jpg', 1, '2026-06-15 03:02:56'),
(149, 9, 2, '2026-06-18', '09:00:00', 28, 'confirmed', 1, 'Hadif Parker', '0174545008', 'muhammaddanishhadifmohdhazuan@gmail.com', 'hi', '2026-06-15 03:20:46', '/uploads/payment_proofs/03f5595708cf03049ebf0c2a3dcdf2f3.jpg', 1, '2026-06-15 03:22:40'),
(150, 9, 2, '2026-06-15', '13:00:00', 2, 'pending_payment', 0, NULL, NULL, NULL, NULL, '2026-06-15 03:24:18', NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `price_range` varchar(50) DEFAULT NULL,
  `hours` varchar(100) DEFAULT NULL,
  `slot_duration` int(11) DEFAULT 60,
  `rating` decimal(2,1) DEFAULT 4.5,
  `deal` varchar(255) DEFAULT NULL,
  `total_seats` int(11) DEFAULT 0,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `owner_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `qr_code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `name`, `category`, `description`, `image`, `location`, `price_range`, `hours`, `slot_duration`, `rating`, `deal`, `total_seats`, `status`, `owner_id`, `created_at`, `qr_code`) VALUES
(2, 'TianMan', 'Chinese', 'I sell fish soups 👍', 'uploads/restaurants/6a1832fbe770c.jpg', 'Johor Bahru, Impian Emas', 'RM 10 - RM 20', '8:00 AM - 3:00 PM', 60, '4.5', 'Children eats free!', 30, 'approved', 2, '2026-05-28 12:20:11', '/uploads/qrcodes/596be4690392ed45737fc28c2366322c.jpg'),
(3, 'MoonsCafe', 'Western', 'We serve western food and local food!', 'uploads/restaurants/6a183a3eafa7d.jpeg', 'Johor Bahru, Impian Emas', 'RM 15 - RM 30', '8:00 AM - 5:30 PM', 60, '4.5', 'Limited deserts!', 50, 'approved', 2, '2026-05-28 12:51:10', '/uploads/qrcodes/6dddcf7050f93d319afdfb15193e4cda.jpg'),
(7, 'The grumpy bear', 'Japanese', 'Nice in/outdoors seatings in good outdoor ambience to enhanced the eating experience.', 'uploads/restaurants/6a2545808cfe2.jpg', '301 Upper Thomson Rd, 02-10, Singapore 574408', 'RM 20 - 100', '11:00 AM - 9:00 PM', 120, '4.5', '20% off', 20, 'approved', 13, '2026-06-07 10:18:40', 'uploads/qrcodes/6a2545808d1b5.jpeg'),
(8, 'Gagahoho Box', 'Korean', 'The place specialises in ox bone soup, or seolleongtang, a Korean classic that simmers ox leg bones for hours until the broth turns milky white and thick with beef essence.', '/uploads/restaurants/6a261833e3894.jpg', '175 Bencoolen St, 01-57A, Singapore 189649', 'RM 10-RM 50', '12:00 PM - 9:30 PM', 60, '4.5', '15 %', 15, 'approved', 13, '2026-06-07 12:19:11', 'uploads/qrcodes/6a2561bfc1d90.jpeg'),
(13, 'Happy Dessert Cafe', 'Cafe', 'Western and Dessert', 'uploads/restaurants/6a29019c5e0b1.jpg', 'Presint 8, Putrajaya', 'RM 5 - RM 30', '2:00PM-10:00PM', 30, '4.5', '', 30, 'approved', 21, '2026-06-10 06:18:05', 'uploads/qrcodes/6a29019c5e26e.jpeg'),
(14, 'Coffee House', 'Cafe', 'Coffee and Pastry', 'uploads/restaurants/6a2a119c94158.jpg', 'Pasir Gudang, Johor Bahru', 'RM 5 - RM 20', '10:00AM - 10:00PM', 60, '4.5', '15 % For Students', 30, 'approved', 21, '2026-06-11 01:38:36', 'uploads/qrcodes/6a2a119c94990.jpeg'),
(20, 'suka', 'wsetern', 'jjejj', NULL, 'johor', 'RM 10 - RM 20', '10:00 AM - 9:00PM', 60, '4.5', '', 10, 'rejected', 21, '2026-06-15 01:06:31', 'uploads/qrcodes/9dfdcefe62f2cfce01de98a6f21cee6b.jpg'),
(21, 'AEON', 'Malaya', 'sedap tho', 'uploads/restaurants/6a45db98814d803eb049ac9b28071ea1.jpg', 'Terengganu, Batu buruk', 'RM10 - RM20', '10:00 AM - 9:00PM', 60, '4.5', '', 50, 'pending', 70, '2026-06-15 03:47:18', 'uploads/qrcodes/4521c1e9e2c792583b87028222ae6cee.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','staff','owner','admin') NOT NULL,
  `restaurant_id` int(11) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `restaurant_id`, `reset_token`, `token_expiry`, `created_at`, `email_verified`, `verification_token`) VALUES
(1, 'Admin EatToGo', 'eattogo.test@gmail.com', NULL, '$2y$10$UBo2sySHaJdb0.1hg6h/4OYD4d71Q50uzHzR1Xq2IC7D8yRVGFyKK', 'admin', NULL, '15f3545e524bfdeb9d0016f311e4f4b395729d65683fabe4f153fd3e2ffb3d70', '2026-06-03 09:36:14', '2026-05-28 09:20:19', 1, NULL),
(2, 'Owner1', 'groupokhciproject@gmail.com', '016-9330771', '$2y$10$17Ho/WrKHJ2Wpz.VllpVDOpWqT8HYnFwH9OqC.eUj2pbljyap.H9y', 'owner', 6, NULL, NULL, '2026-05-28 10:37:54', 1, NULL),
(5, 'MoonsCafeStaff1', 'chinming0210@gmail.com', NULL, '$2y$10$aXr/yQkE18xrEsqbLNWkY.cbsZPOeHF/ukeQ68Wcnx0r4HWeGIRNS', 'staff', 3, NULL, NULL, '2026-05-28 13:03:01', 1, NULL),
(7, 'John Doe', 'tis.some.nonesense@gmail.com', '999', '$2y$10$lZBaq62SvLDuGcUqa6FNkuZiEre.lQMQGjklUONJPKXE0BD2q/d6q', 'customer', NULL, NULL, NULL, '2026-05-28 13:43:45', 1, NULL),
(8, 'NAVIN', 'navin@graduate.utm.my', '0119876543', '202cb962ac59075b964b07152d234b70', 'owner', NULL, NULL, NULL, '2026-05-28 14:46:51', 1, NULL),
(9, 'Hadif Parker', 'muhammaddanishhadifmohdhazuan@gmail.com', '0174545008', '$2y$10$EncUoUjM9yG9OqTXjOSDwusZgpJ9I85a63MtERwAfCxXQGhGtp1q6', 'customer', NULL, NULL, NULL, '2026-05-29 00:44:24', 1, NULL),
(13, 'Muhammad Nazim', 'nazim06@graduate.utm.my', '0146526757', '$2y$10$TNJadP/4ouLQEeHAUD.5hu3mwenFKreoWxXdhxswdpj138eT.xVWG', 'owner', 8, NULL, NULL, '2026-06-07 10:13:27', 1, NULL),
(14, 'John Wick', 'nazimaaabbb@gmail.com', NULL, '$2y$10$LhYy/m/8r2LtXDHvDjOd2ugaXByhdZoH/jfl659JZPOYjjCyiYdOe', 'staff', 7, NULL, NULL, '2026-06-07 10:20:47', 1, NULL),
(16, 'Jane Doe', 'theamazingemailz256@gmail.com', NULL, '$2y$10$YNcdgg/ehOC2eFtBmG5AweJ.nkrW0/2xEFaeYFJvX5vwjd5cTnbAy', 'staff', 8, NULL, NULL, '2026-06-07 12:34:49', 1, NULL),
(18, 'Nasi Lemak Antarabangsa', 'navinramu22@gmail.com', '0195302144', '5be057accb25758101fa5eadbbd79503', 'owner', NULL, NULL, '2026-06-08 12:01:04', '2026-06-07 16:01:05', 0, '87e08dfebc7f987ae6c5ba7330a39b21efb568e764db0942c1512fd7f29ec769'),
(21, 'Danish Hadif', 'hadifhazuan06@gmail.com', '0123456789', '$2y$10$5o3gOzBzBJWzyK55QYi6JuTemGAa1OzgPWJFFAtO11kQ8wh89L38W', 'owner', 16, NULL, NULL, '2026-06-10 06:13:25', 1, NULL),
(22, 'James', 'buddieshh@gmail.com', NULL, '$2y$10$ulh/NXn0EyLjbC.aAizCg.pplf0BlFghinYY46/S/lT.uww.iz4oO', 'staff', 13, NULL, NULL, '2026-06-10 06:20:51', 1, NULL),
(35, 'Abu', 'difqie06@gmail.com', NULL, '$2y$10$Ao3xEfJ3UVBrYEotMcsSS.s3pKS4yevzCVuk4/pg/8a/X4pLemUX2', 'staff', 14, NULL, NULL, '2026-06-11 07:42:20', 1, NULL),
(41, 'TianManStaff', 'ngyueyang@graduate.utm.my', NULL, '$2y$10$dw5bEnWTSIOK7uB9qU.qEOPWOeBuCJFk1a/DO4X4iwGgCzI/4Awvm', 'staff', 2, NULL, NULL, '2026-06-11 20:56:19', 1, NULL),
(43, 'Ling Vanessa Hee', 'lingsan0602@gmail.com', '01136654463', '$2y$10$ZfxCEq7aRt61f08YBp5UB.y2YAfyVVrd2u7tXoILmpibAXw2UqFJa', 'customer', NULL, NULL, NULL, '2026-06-12 02:06:16', 1, NULL),
(44, 'Norleez Fhatihah', 'norleezf@gmail.com', '0174751206', '$2y$10$t5BKCgm3AOADi0r5sp.nbOJwLg.w8p5CXkll3TapVPhwr6LhvECW2', 'customer', NULL, NULL, NULL, '2026-06-12 02:06:59', 1, NULL),
(47, 'Yasmin cool', 'yasminjeffry0611@gmail.com', '01162461672', '$2y$10$rZJmYzzayAjyRgEm9IHTguGO93IC7aCCHOYGawP5OTR.aHX7Ov6Bu', 'customer', NULL, NULL, NULL, '2026-06-12 02:08:37', 1, NULL),
(48, 'marcusss', 'laimakesi@gmail.com', '0177653342', '$2y$10$34drbRoFapwYvpDNKOjXg.DFfqCIDQsplEC5eTlfoKLem4iRxssku', 'customer', NULL, NULL, '2026-06-12 22:09:25', '2026-06-12 02:09:25', 0, '98b1e7a799e98cb2c6142e10e879f794778c026748ceb2d0347d25aaccb0d502'),
(49, 'Izzatun', 'nurfazlli490@gmail.com', '0102550706', '$2y$10$Tim.XzOA0oCsz0kg8xIBLeo.AjEsQ1cnYPx74w1k5z6H7k0KjpYS6', 'customer', NULL, NULL, NULL, '2026-06-12 02:11:08', 1, NULL),
(50, 'LEE JIAN HUI', 'jhlee1136@gmail.com', '0196945321', '$2y$10$h8vEPysdl.hd49LjYdpsB.nIGqLRAl4Kwmtk/ZXxmqxTZzE06o.Uq', 'customer', NULL, NULL, NULL, '2026-06-12 02:12:57', 1, NULL),
(51, 'cusss', 'cuslai398@gmail.com', '0177653342', '$2y$10$v1cRJ.aBSk.4QZxbAWJYYeIf.tzPcfcxF1vJFA3p6IrgM2/9qkMSG', 'customer', NULL, NULL, '2026-06-12 22:13:24', '2026-06-12 02:13:24', 0, '050a9219caa4b83e577223bf04b9d4a89072dd56264034ae5a9dbb6fdaa8fb80'),
(52, 'LEE jh', 'jh1ee393.06@gmail.com', '0196945321', '$2y$10$TWTmFengziBVgn3g2kALVer3CyMhrg97IBIQWStmsJMAzHzHR5w7a', 'customer', NULL, NULL, NULL, '2026-06-12 02:14:51', 1, NULL),
(55, 'MARCUS LAI WEI ZE', 'marcuslaiweize@gmail.com', '0177653342', '$2y$10$SjH3d9h9tDo5JpmAZUiecuVVsRQVYOsL5CtSLswKBYXiQl2JDP/vS', 'customer', NULL, NULL, NULL, '2026-06-12 02:16:29', 1, NULL),
(60, 'Izzatun', 'nurizzatunnadhirah@graduate.utm.my', '0102550706', '$2y$10$h1ziscn2u6ekjF1FhoDdRumtBRCqVIHmSY2xnvO9jv5wEMOr9gdy.', 'owner', 17, NULL, NULL, '2026-06-12 02:19:32', 1, NULL),
(62, 'LEE JIAN HUI', 'leejianhui@graduate.utm.my', '0196945321', '$2y$10$emHHj7om7EpIBse9IpD88uGcU419XVhZbEsBH5SqAuXvgyPvb4W46', 'customer', NULL, NULL, '2026-06-12 22:54:41', '2026-06-12 02:54:41', 0, '7ad65b2c21f872110fcac215f2050492807af1c8f4b67039fc8cea8b63e5c19d'),
(63, 'Ng Yue Yang', 'ngyueyang.316@gmail.com', '016-9330771', '$2y$10$ICab5hQvlPb2mjyqe0znV.cnjDHYMRPD2TFRPzbMhdYisZEkLOP8i', 'customer', NULL, NULL, NULL, '2026-06-14 08:28:44', 1, NULL),
(64, 'Engku Afif', 'engkuijat06@gmail.com', '0139380409', '$2y$10$b/JRHB2rbA0ugW8fi.BMjOtWo48rt4UwjoPva.lxUW.SQxr6ednN.', 'customer', NULL, NULL, NULL, '2026-06-14 12:46:43', 1, NULL),
(66, 'Taarunesh Ramasamy', 'ramsamtaarunesh@gmail.com', '179792847', '$2y$10$QqNMWQIWfdPWadb6JoWEZOFsgQ12ASX2Jd1Y2/CAF8v.fr1lhSH16', 'customer', NULL, NULL, '2026-06-15 20:30:07', '2026-06-15 00:30:06', 0, 'f42ecb61dacfa067473048873163d6b9fe0c6923db52d160ce6cf34d9aa1779e'),
(70, 'i food corner', 'navinramu2501@gmail.com', '0195302144', '$2y$10$LgCdYFioCaGaMdHj9YC90ejJ4r/w53KMKqlIOFDrMops/IQ.cJ5zu', 'owner', NULL, NULL, NULL, '2026-06-15 03:34:53', 1, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `restaurant_id` (`restaurant_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Indexes for table `owner_applications`
--
ALTER TABLE `owner_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `restaurant_id` (`restaurant_id`),
  ADD KEY `idx_status_created` (`status`,`created_at`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=213;

--
-- AUTO_INCREMENT for table `owner_applications`
--
ALTER TABLE `owner_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`);

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`);

--
-- Constraints for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD CONSTRAINT `restaurants_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
