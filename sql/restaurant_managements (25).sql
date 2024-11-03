-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 03, 2024 at 01:50 AM
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
-- Database: `dine-watch`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `log_id` int(11) NOT NULL,
  `action_by` int(11) DEFAULT NULL,
  `action_type` enum('Login','Logout','Create Reservation','Update Reservation','Cancel Reservation','Order Placed','Order Updated','Order Canceled','Add Product','Update Product','Delete Product') DEFAULT NULL,
  `action_details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`log_id`, `action_by`, `action_type`, `action_details`, `created_at`) VALUES
(291, 5, 'Login', 'mlast3 logged in', '2024-11-01 22:25:29'),
(292, 5, 'Logout', 'mlast3 logged out', '2024-11-01 22:28:44'),
(301, 31, 'Login', 'jdsaa logged in', '2024-11-01 22:39:17'),
(302, 31, 'Login', 'jdsaa logged in', '2024-11-01 22:40:51'),
(303, 31, 'Logout', 'jdsaa logged out', '2024-11-01 22:41:21'),
(304, 31, 'Login', 'jdsaa logged in', '2024-11-01 22:42:36'),
(305, 31, 'Login', 'jdsaa logged in', '2024-11-01 22:46:02'),
(306, 31, 'Logout', 'jdsaa logged out', '2024-11-01 22:46:27'),
(307, 31, 'Login', 'jdsaa logged in', '2024-11-01 22:46:30'),
(308, 31, 'Logout', 'jdsaa logged out', '2024-11-01 22:51:47'),
(309, 31, 'Login', 'jdsaa logged in', '2024-11-01 22:51:49'),
(310, 30, 'Login', 'hala s. dsaa sad logged in', '2024-11-01 22:55:42'),
(311, 30, 'Logout', 'hala s. dsaa sad logged out', '2024-11-01 22:55:57'),
(312, 30, 'Login', 'hala s. dsaa sad logged in', '2024-11-01 22:55:59'),
(313, 30, 'Logout', 'hala s. dsaa sad logged out', '2024-11-01 22:58:02'),
(314, 30, 'Login', 'hala s. dsaa sad logged in', '2024-11-01 22:58:05'),
(315, 30, 'Logout', 'hala s. dsaa sad logged out', '2024-11-01 23:06:29'),
(316, 30, 'Login', 'hala s. dsaa sad logged in', '2024-11-01 23:06:31'),
(317, 30, 'Logout', 'hala s. dsaa sad logged out', '2024-11-01 23:07:52'),
(318, 30, 'Login', 'hala s. dsaa sad logged in', '2024-11-01 23:07:53'),
(319, 30, 'Logout', 'hala s. dsaa sad logged out', '2024-11-01 23:09:46'),
(320, 30, 'Login', 'hala s. dsaa sad logged in', '2024-11-01 23:09:48'),
(321, 30, 'Logout', 'hala s. dsaa sad logged out', '2024-11-01 23:21:06'),
(322, 30, 'Login', 'hala s. dsaa sad logged in', '2024-11-01 23:21:08'),
(323, 30, 'Logout', 'hdsaa logged out', '2024-11-01 23:26:40'),
(324, 30, 'Login', 'hala s. dsaa sad logged in', '2024-11-01 23:26:42'),
(325, 30, 'Logout', 'hdsaa logged out', '2024-11-02 00:00:03'),
(326, 31, 'Login', 'jack j. dsaa sad logged in', '2024-11-02 00:00:07'),
(327, 31, 'Logout', 'jdsaa logged out', '2024-11-02 01:40:21'),
(328, 31, 'Login', 'jack j. dsaa sad logged in', '2024-11-02 01:40:28'),
(329, 30, 'Login', 'hala s. dsaa sad logged in', '2024-11-02 01:40:31'),
(330, 30, 'Logout', 'hala s. dsaa sad logged out', '2024-11-02 02:05:22'),
(331, 30, 'Login', 'hala s. dsaa sad logged in', '2024-11-02 02:05:24'),
(332, 30, 'Logout', 'hdsaa logged out', '2024-11-02 02:38:17'),
(333, 30, 'Login', 'hala s. dsaa sad logged in', '2024-11-02 02:38:33'),
(334, 31, 'Login', 'jack j. dsaa sad logged in', '2024-11-02 02:38:38'),
(335, 31, 'Logout', 'jack j. dsaa sad logged out', '2024-11-02 05:07:32'),
(336, 5, 'Login', 'mlast3 logged in', '2024-11-02 05:08:14'),
(337, 5, '', 'Updated table ID: 1 with table number: 9', '2024-11-02 05:08:42'),
(338, 31, 'Login', 'jack j. dsaa sad logged in', '2024-11-02 06:48:33'),
(339, 31, 'Login', 'jack j. dsaa sad logged in', '2024-11-02 06:50:34'),
(340, 31, 'Login', 'jack j. dsaa sad logged in', '2024-11-02 06:52:31'),
(341, 31, 'Logout', 'jack j. dsaa sad logged out', '2024-11-02 09:37:16'),
(342, 30, 'Login', 'hala s. dsaa sad logged in', '2024-11-02 09:37:20'),
(343, 30, 'Logout', 'hdsaa logged out', '2024-11-02 10:30:24'),
(344, 30, 'Login', 'hala s. dsaa sad logged in', '2024-11-02 10:30:26'),
(345, 30, 'Logout', 'hdsaa logged out', '2024-11-02 10:30:32'),
(346, 31, 'Login', 'jack j. dsaa sad logged in', '2024-11-02 10:30:36'),
(347, 31, 'Login', 'jack j. dsaa sad logged in', '2024-11-03 00:09:37');

-- --------------------------------------------------------

--
-- Table structure for table `data_reservations`
--

CREATE TABLE `data_reservations` (
  `reservation_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `table_id` int(11) DEFAULT NULL,
  `reservation_date` date DEFAULT NULL,
  `reservation_time` time DEFAULT NULL,
  `status` enum('Pending','Complete','Canceled','Rescheduled','Paid') DEFAULT 'Pending',
  `custom_note` varchar(255) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_reservations`
--

INSERT INTO `data_reservations` (`reservation_id`, `user_id`, `table_id`, `reservation_date`, `reservation_time`, `status`, `custom_note`, `feedback`, `created_at`, `updated_at`) VALUES
(16, 31, 1, '2024-11-03', '13:15:00', 'Pending', '', NULL, '2024-11-03 00:22:20', '2024-11-03 00:22:20');

-- --------------------------------------------------------

--
-- Table structure for table `error_logs`
--

CREATE TABLE `error_logs` (
  `log_id` int(11) NOT NULL,
  `error_message` text DEFAULT NULL,
  `error_type` varchar(100) DEFAULT NULL,
  `occurred_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `reservation_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `feedback_text` text DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reservation_id` int(11) DEFAULT NULL,
  `order_details` text DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `order_time` datetime DEFAULT current_timestamp(),
  `status` enum('Pending','In-Progress','Completed','Canceled','paid in advance') DEFAULT 'Pending',
  `feedback` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `payment_method` varchar(50) DEFAULT 'Credit Card'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `reservation_id`, `order_details`, `total_amount`, `order_time`, `status`, `feedback`, `created_at`, `updated_at`, `payment_method`) VALUES
(1, 31, 1, 'Product ID: 1 | Quantity: 1 | Price: 60.00', 60.00, '2024-11-02 20:55:17', 'paid in advance', NULL, '2024-11-02 12:55:17', '2024-11-02 12:55:17', 'Credit Card'),
(2, 31, 3, 'Product ID: 1 | Quantity: 2 | Price: 120.00', 120.00, '2024-11-02 21:01:28', 'paid in advance', NULL, '2024-11-02 13:01:28', '2024-11-02 13:01:28', 'Credit Card'),
(3, 31, 4, 'Product ID: 1 | Quantity: 1 | Price: 60.00', 60.00, '2024-11-02 21:04:01', 'paid in advance', NULL, '2024-11-02 13:04:01', '2024-11-02 13:04:01', 'Credit Card'),
(4, 31, 5, 'Product ID: 1 | Quantity: 1 | Price: 60.00', 60.00, '2024-11-02 21:11:44', 'paid in advance', NULL, '2024-11-02 13:11:44', '2024-11-02 13:11:44', 'Credit Card'),
(5, 31, 6, 'Product ID: 1 | Quantity: 1 | Price: 60.00', 60.00, '2024-11-02 21:16:05', 'paid in advance', NULL, '2024-11-02 13:16:05', '2024-11-02 13:16:05', 'Credit Card'),
(6, 31, 7, 'Product ID: 5 | Quantity: 1 | Price: 290.00', 290.00, '2024-11-02 21:19:42', 'paid in advance', NULL, '2024-11-02 13:19:42', '2024-11-02 13:19:42', 'Credit Card'),
(7, 31, 8, 'Product ID: 1 | Quantity: 1 | Price: 60.00', 60.00, '2024-11-02 21:23:43', 'paid in advance', NULL, '2024-11-02 13:23:43', '2024-11-02 13:23:43', 'Credit Card'),
(8, 31, 9, 'Product ID: 1 | Quantity: 1 | Price: 60.00', 60.00, '2024-11-02 21:26:27', 'paid in advance', NULL, '2024-11-02 13:26:27', '2024-11-02 13:26:27', 'Credit Card'),
(9, 31, 10, 'Product ID: 1 | Quantity: 1 | Price: 60.00', 60.00, '2024-11-02 21:29:01', 'paid in advance', NULL, '2024-11-02 13:29:01', '2024-11-02 13:29:01', 'Credit Card'),
(10, 31, 11, 'Product ID: 1 | Quantity: 1 | Price: 60.00', 60.00, '2024-11-02 21:40:59', 'paid in advance', NULL, '2024-11-02 13:40:59', '2024-11-02 13:40:59', 'Credit Card'),
(11, 31, 12, 'Product ID: 1 | Quantity: 1 | Price: 60.00', 60.00, '2024-11-02 21:43:29', 'paid in advance', NULL, '2024-11-02 13:43:29', '2024-11-02 13:43:29', 'Credit Card'),
(12, 31, 13, 'Product ID: 5 | Quantity: 1 | Price: 290.00', 290.00, '2024-11-02 21:45:40', 'paid in advance', NULL, '2024-11-02 13:45:40', '2024-11-02 13:45:40', 'Credit Card'),
(13, 31, 14, 'Product ID: 1 | Quantity: 1 | Price: 60.00', 60.00, '2024-11-02 21:48:19', 'paid in advance', NULL, '2024-11-02 13:48:19', '2024-11-02 13:48:19', 'Credit Card'),
(14, 31, 15, 'Product ID: 1 | Quantity: 1 | Price: 60.00', 60.00, '2024-11-02 21:50:25', 'paid in advance', NULL, '2024-11-02 13:50:25', '2024-11-02 13:50:25', 'Credit Card');

-- --------------------------------------------------------

--
-- Table structure for table `order_cancellations`
--

CREATE TABLE `order_cancellations` (
  `cancellation_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `canceled_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `totalprice` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `user_id`, `product_id`, `quantity`, `totalprice`) VALUES
(15, 1, 31, 1, 1, 60.00);

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`category_id`, `category_name`) VALUES
(1, 'Meal'),
(2, 'Drink'),
(3, 'Pizza'),
(4, 'addons');

-- --------------------------------------------------------

--
-- Table structure for table `product_items`
--

CREATE TABLE `product_items` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `special_instructions` text DEFAULT NULL,
  `product_image` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `quantity` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_items`
--

INSERT INTO `product_items` (`product_id`, `product_name`, `price`, `special_instructions`, `product_image`, `created_at`, `updated_at`, `quantity`, `category_id`) VALUES
(1, 'Black Coffee', 60.00, 'Coffee brewed in a large batch using hot water and ground KAPENG BARAKO beans.', '../Uploads/671458170c418_images.jpg', '2024-10-07 10:35:00', '2024-10-23 03:45:42', 20, 2),
(2, 'Bacon Cheeseburger The BCB (M)', 249.00, 'House marinara, mozzarella cheese, special cheese sauce, bell pepper, onions, ground beef, and bacon.', '../Uploads/671458be593cc_images (1).jpg', '2024-10-07 10:35:00', '2024-10-19 17:27:25', 20, 2),
(3, 'Pizzasdf', 12.00, '', '../Uploads/67205684a0d61_marvelous-city-skyline.jpg', '2024-10-29 03:29:08', '2024-10-29 03:29:08', 1234567, 1),
(4, 'Black Coffee (S)', 20.00, 'matapang', '../Uploads/672060b50f618_images.jpg', '2024-10-29 04:12:37', '2024-10-29 04:12:37', 3, 4),
(5, 'Pizzasd', 290.00, '290', '../Uploads/6720614004731_images (2).jpg', '2024-10-29 04:14:56', '2024-10-29 04:14:56', 5, 2);

-- --------------------------------------------------------

--
-- Table structure for table `receipts`
--

CREATE TABLE `receipts` (
  `receipt_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `receipt_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` varchar(50) DEFAULT 'Credit Card'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receipts`
--

INSERT INTO `receipts` (`receipt_id`, `order_id`, `user_id`, `total_amount`, `receipt_date`, `payment_method`) VALUES
(1, 1, 31, 60.00, '2024-11-02 12:55:17', 'Credit Card'),
(2, 1, 31, 120.00, '2024-11-02 13:01:28', 'Credit Card'),
(3, 2, 31, 120.00, '2024-11-02 13:01:28', 'Credit Card'),
(5, 1, 31, 120.00, '2024-11-02 13:01:41', 'Credit Card'),
(6, 2, 31, 120.00, '2024-11-02 13:01:41', 'Credit Card'),
(8, 1, 31, 120.00, '2024-11-02 13:01:48', 'Credit Card'),
(9, 2, 31, 120.00, '2024-11-02 13:01:48', 'Credit Card'),
(11, 1, 31, 60.00, '2024-11-02 13:04:01', 'Credit Card'),
(12, 2, 31, 60.00, '2024-11-02 13:04:01', 'Credit Card'),
(13, 3, 31, 60.00, '2024-11-02 13:04:01', 'Credit Card'),
(14, 1, 31, 60.00, '2024-11-02 13:11:44', 'Credit Card'),
(15, 2, 31, 60.00, '2024-11-02 13:11:44', 'Credit Card'),
(16, 3, 31, 60.00, '2024-11-02 13:11:44', 'Credit Card'),
(17, 4, 31, 60.00, '2024-11-02 13:11:44', 'Credit Card'),
(21, 1, 31, 60.00, '2024-11-02 13:16:05', 'Credit Card'),
(22, 2, 31, 60.00, '2024-11-02 13:16:05', 'Credit Card'),
(23, 3, 31, 60.00, '2024-11-02 13:16:05', 'Credit Card'),
(24, 4, 31, 60.00, '2024-11-02 13:16:05', 'Credit Card'),
(25, 5, 31, 60.00, '2024-11-02 13:16:05', 'Credit Card'),
(28, 1, 31, 290.00, '2024-11-02 13:19:42', 'Credit Card'),
(29, 2, 31, 290.00, '2024-11-02 13:19:42', 'Credit Card'),
(30, 3, 31, 290.00, '2024-11-02 13:19:42', 'Credit Card'),
(31, 4, 31, 290.00, '2024-11-02 13:19:42', 'Credit Card'),
(32, 5, 31, 290.00, '2024-11-02 13:19:42', 'Credit Card'),
(33, 6, 31, 290.00, '2024-11-02 13:19:42', 'Credit Card'),
(35, 1, 31, 60.00, '2024-11-02 13:23:43', 'Credit Card'),
(36, 2, 31, 60.00, '2024-11-02 13:23:43', 'Credit Card'),
(37, 3, 31, 60.00, '2024-11-02 13:23:43', 'Credit Card'),
(38, 4, 31, 60.00, '2024-11-02 13:23:43', 'Credit Card'),
(39, 5, 31, 60.00, '2024-11-02 13:23:43', 'Credit Card'),
(40, 6, 31, 60.00, '2024-11-02 13:23:43', 'Credit Card'),
(41, 7, 31, 60.00, '2024-11-02 13:23:43', 'Credit Card'),
(42, 1, 31, 60.00, '2024-11-02 13:26:27', 'Credit Card'),
(43, 2, 31, 60.00, '2024-11-02 13:26:27', 'Credit Card'),
(44, 3, 31, 60.00, '2024-11-02 13:26:27', 'Credit Card'),
(45, 4, 31, 60.00, '2024-11-02 13:26:27', 'Credit Card'),
(46, 5, 31, 60.00, '2024-11-02 13:26:27', 'Credit Card'),
(47, 6, 31, 60.00, '2024-11-02 13:26:27', 'Credit Card'),
(48, 7, 31, 60.00, '2024-11-02 13:26:27', 'Credit Card'),
(49, 8, 31, 60.00, '2024-11-02 13:26:27', 'Credit Card'),
(57, 1, 31, 60.00, '2024-11-02 13:29:01', 'Credit Card'),
(58, 2, 31, 60.00, '2024-11-02 13:29:01', 'Credit Card'),
(59, 3, 31, 60.00, '2024-11-02 13:29:01', 'Credit Card'),
(60, 4, 31, 60.00, '2024-11-02 13:29:01', 'Credit Card'),
(61, 5, 31, 60.00, '2024-11-02 13:29:01', 'Credit Card'),
(62, 6, 31, 60.00, '2024-11-02 13:29:01', 'Credit Card'),
(63, 7, 31, 60.00, '2024-11-02 13:29:01', 'Credit Card'),
(64, 8, 31, 60.00, '2024-11-02 13:29:01', 'Credit Card'),
(65, 9, 31, 60.00, '2024-11-02 13:29:01', 'Credit Card'),
(72, 1, 31, 60.00, '2024-11-02 13:40:59', 'Credit Card'),
(73, 2, 31, 60.00, '2024-11-02 13:40:59', 'Credit Card'),
(74, 3, 31, 60.00, '2024-11-02 13:40:59', 'Credit Card'),
(75, 4, 31, 60.00, '2024-11-02 13:40:59', 'Credit Card'),
(76, 5, 31, 60.00, '2024-11-02 13:40:59', 'Credit Card'),
(77, 6, 31, 60.00, '2024-11-02 13:40:59', 'Credit Card'),
(78, 7, 31, 60.00, '2024-11-02 13:40:59', 'Credit Card'),
(79, 8, 31, 60.00, '2024-11-02 13:40:59', 'Credit Card'),
(80, 9, 31, 60.00, '2024-11-02 13:40:59', 'Credit Card'),
(81, 10, 31, 60.00, '2024-11-02 13:40:59', 'Credit Card'),
(87, 1, 31, 60.00, '2024-11-02 13:43:29', 'Credit Card'),
(88, 2, 31, 60.00, '2024-11-02 13:43:29', 'Credit Card'),
(89, 3, 31, 60.00, '2024-11-02 13:43:29', 'Credit Card'),
(90, 4, 31, 60.00, '2024-11-02 13:43:29', 'Credit Card'),
(91, 5, 31, 60.00, '2024-11-02 13:43:29', 'Credit Card'),
(92, 6, 31, 60.00, '2024-11-02 13:43:29', 'Credit Card'),
(93, 7, 31, 60.00, '2024-11-02 13:43:29', 'Credit Card'),
(94, 8, 31, 60.00, '2024-11-02 13:43:29', 'Credit Card'),
(95, 9, 31, 60.00, '2024-11-02 13:43:29', 'Credit Card'),
(96, 10, 31, 60.00, '2024-11-02 13:43:29', 'Credit Card'),
(97, 11, 31, 60.00, '2024-11-02 13:43:29', 'Credit Card'),
(102, 1, 31, 290.00, '2024-11-02 13:45:40', 'Credit Card'),
(103, 2, 31, 290.00, '2024-11-02 13:45:40', 'Credit Card'),
(104, 3, 31, 290.00, '2024-11-02 13:45:40', 'Credit Card'),
(105, 4, 31, 290.00, '2024-11-02 13:45:40', 'Credit Card'),
(106, 5, 31, 290.00, '2024-11-02 13:45:40', 'Credit Card'),
(107, 6, 31, 290.00, '2024-11-02 13:45:40', 'Credit Card'),
(108, 7, 31, 290.00, '2024-11-02 13:45:40', 'Credit Card'),
(109, 8, 31, 290.00, '2024-11-02 13:45:40', 'Credit Card'),
(110, 9, 31, 290.00, '2024-11-02 13:45:40', 'Credit Card'),
(111, 10, 31, 290.00, '2024-11-02 13:45:40', 'Credit Card'),
(112, 11, 31, 290.00, '2024-11-02 13:45:40', 'Credit Card'),
(113, 12, 31, 290.00, '2024-11-02 13:45:40', 'Credit Card'),
(117, 1, 31, 60.00, '2024-11-02 13:48:19', 'Credit Card'),
(118, 2, 31, 60.00, '2024-11-02 13:48:19', 'Credit Card'),
(119, 3, 31, 60.00, '2024-11-02 13:48:19', 'Credit Card'),
(120, 4, 31, 60.00, '2024-11-02 13:48:19', 'Credit Card'),
(121, 5, 31, 60.00, '2024-11-02 13:48:19', 'Credit Card'),
(122, 6, 31, 60.00, '2024-11-02 13:48:19', 'Credit Card'),
(123, 7, 31, 60.00, '2024-11-02 13:48:19', 'Credit Card'),
(124, 8, 31, 60.00, '2024-11-02 13:48:19', 'Credit Card'),
(125, 9, 31, 60.00, '2024-11-02 13:48:19', 'Credit Card'),
(126, 10, 31, 60.00, '2024-11-02 13:48:19', 'Credit Card'),
(127, 11, 31, 60.00, '2024-11-02 13:48:19', 'Credit Card'),
(128, 12, 31, 60.00, '2024-11-02 13:48:19', 'Credit Card'),
(129, 13, 31, 60.00, '2024-11-02 13:48:19', 'Credit Card'),
(132, 1, 31, 60.00, '2024-11-02 13:50:25', 'Credit Card'),
(133, 2, 31, 60.00, '2024-11-02 13:50:25', 'Credit Card'),
(134, 3, 31, 60.00, '2024-11-02 13:50:25', 'Credit Card'),
(135, 4, 31, 60.00, '2024-11-02 13:50:25', 'Credit Card'),
(136, 5, 31, 60.00, '2024-11-02 13:50:25', 'Credit Card'),
(137, 6, 31, 60.00, '2024-11-02 13:50:25', 'Credit Card'),
(138, 7, 31, 60.00, '2024-11-02 13:50:25', 'Credit Card'),
(139, 8, 31, 60.00, '2024-11-02 13:50:25', 'Credit Card'),
(140, 9, 31, 60.00, '2024-11-02 13:50:25', 'Credit Card'),
(141, 10, 31, 60.00, '2024-11-02 13:50:25', 'Credit Card'),
(142, 11, 31, 60.00, '2024-11-02 13:50:25', 'Credit Card'),
(143, 12, 31, 60.00, '2024-11-02 13:50:25', 'Credit Card'),
(144, 13, 31, 60.00, '2024-11-02 13:50:25', 'Credit Card'),
(145, 14, 31, 60.00, '2024-11-02 13:50:25', 'Credit Card');

-- --------------------------------------------------------

--
-- Table structure for table `receipt_items`
--

CREATE TABLE `receipt_items` (
  `receipt_item_id` int(11) NOT NULL,
  `receipt_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `item_total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receipt_items`
--

INSERT INTO `receipt_items` (`receipt_item_id`, `receipt_id`, `product_id`, `quantity`, `item_total_price`) VALUES
(1, 1, 1, 1, 60.00),
(2, 2, 1, 2, 120.00),
(3, 11, 1, 1, 60.00),
(4, 14, 1, 1, 60.00),
(5, 21, 1, 1, 60.00),
(6, 28, 5, 1, 290.00),
(7, 35, 1, 1, 60.00),
(8, 42, 1, 1, 60.00),
(9, 57, 1, 1, 60.00),
(10, 72, 1, 1, 60.00),
(11, 87, 1, 1, 60.00),
(12, 102, 5, 1, 290.00),
(13, 117, 1, 1, 60.00),
(14, 132, 1, 1, 60.00);

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `reservation_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `table_id` int(11) DEFAULT NULL,
  `reservation_date` date DEFAULT NULL,
  `reservation_time` time DEFAULT NULL,
  `status` enum('Pending','Complete','Canceled','Rescheduled','Paid') DEFAULT 'Pending',
  `custom_note` varchar(255) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`reservation_id`, `user_id`, `table_id`, `reservation_date`, `reservation_time`, `status`, `custom_note`, `feedback`, `created_at`, `updated_at`) VALUES
(1, 31, 1, '2024-11-02', '08:15:00', 'Paid', '', NULL, '2024-11-02 12:54:35', '2024-11-02 12:55:17'),
(2, 31, 1, '2024-11-15', '07:15:00', 'Paid', '12', NULL, '2024-11-02 13:00:54', '2024-11-02 13:01:28'),
(3, 31, 1, '2024-11-27', '07:30:00', 'Paid', '1233', NULL, '2024-11-02 13:03:33', '2024-11-02 13:04:01'),
(4, 31, 1, '2024-11-02', '07:30:00', 'Paid', '123', NULL, '2024-11-02 13:08:53', '2024-11-02 13:09:16'),
(5, 31, 1, '2024-11-03', '07:30:00', 'Paid', '1243', NULL, '2024-11-02 13:15:12', '2024-11-02 13:16:05'),
(6, 31, 1, '2024-11-02', '07:00:00', 'Paid', '231', NULL, '2024-11-02 13:19:15', '2024-11-02 13:19:42'),
(7, 31, 1, '2024-11-03', '08:45:00', 'Paid', '', NULL, '2024-11-02 13:23:06', '2024-11-02 13:23:43'),
(8, 31, 1, '2024-11-03', '07:15:00', 'Paid', '123', NULL, '2024-11-02 13:26:00', '2024-11-02 13:26:27'),
(9, 31, 1, '2024-11-03', '07:00:00', 'Paid', '', NULL, '2024-11-02 13:28:08', '2024-11-02 13:29:01'),
(10, 31, 1, '2024-11-03', '10:00:00', 'Paid', '123', NULL, '2024-11-02 13:39:52', '2024-11-02 13:40:59'),
(11, 31, 3, '2024-11-02', '07:15:00', 'Paid', '12321', NULL, '2024-11-02 13:42:49', '2024-11-02 13:43:29'),
(12, 31, 1, '2024-11-02', '09:30:00', 'Paid', '', NULL, '2024-11-02 13:44:29', '2024-11-02 13:45:40'),
(13, 31, 1, '2024-11-03', '12:00:00', 'Paid', '1312', NULL, '2024-11-02 13:47:39', '2024-11-02 13:48:19'),
(14, 31, 1, '2024-11-03', '11:15:00', 'Paid', '2131', NULL, '2024-11-02 13:49:46', '2024-11-02 13:50:25');

-- --------------------------------------------------------

--
-- Table structure for table `reservation_ids`
--

CREATE TABLE `reservation_ids` (
  `reservation_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservation_reschedule`
--

CREATE TABLE `reservation_reschedule` (
  `reschedule_id` int(11) NOT NULL,
  `reservation_id` int(11) DEFAULT NULL,
  `old_reservation_time` datetime DEFAULT NULL,
  `new_reservation_time` datetime DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `session_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`session_id`, `user_id`, `session_token`, `created_at`, `expires_at`) VALUES
(1, 2, 'random_secure_token_123', '2024-10-09 08:13:26', '2024-12-10 19:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

CREATE TABLE `tables` (
  `table_id` int(11) NOT NULL,
  `table_number` int(11) NOT NULL,
  `seating_capacity` int(11) NOT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `area` enum('Indoor','Outdoor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`table_id`, `table_number`, `seating_capacity`, `is_available`, `area`) VALUES
(1, 9, 4, 1, 'Outdoor'),
(2, 2, 6, 0, 'Outdoor'),
(3, 3, 2, 1, 'Indoor'),
(27, 1, 2, 1, 'Outdoor'),
(28, 3, 1, 1, 'Outdoor');

-- --------------------------------------------------------

--
-- Table structure for table `table_images`
--

CREATE TABLE `table_images` (
  `image_id` int(11) NOT NULL,
  `table_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `position` enum('back view','left view','right view','front view') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `table_images`
--

INSERT INTO `table_images` (`image_id`, `table_id`, `image_path`, `uploaded_at`, `position`) VALUES
(1, 1, '../uploads/67161da98506b_images (2).jpg', '2024-10-18 11:00:41', 'back view'),
(2, 1, '../uploads/671871c0da28a_images (2).jpg', '2024-10-18 11:00:41', 'front view'),
(3, 2, '../uploads/671871dfb1037_images (2).jpg', '2024-10-18 11:00:41', 'back view'),
(33, 1, '../uploads/6716192d7c308_image.jpg', '2024-10-21 08:43:41', 'left view'),
(34, 2, '../uploads/671e06a31346a_GyulaiSausage.jpg', '2024-10-27 09:21:25', 'left view'),
(35, 2, '../uploads/671e069749121_images.jpg', '2024-10-27 09:23:35', 'right view'),
(36, 3, '../uploads/672317b33d1ef_marvelous-city-skyline.jpg', '2024-10-31 05:37:55', 'back view'),
(37, 27, '../uploads/672317bd527e1_marvelous-city-skyline.jpg', '2024-10-31 05:38:05', 'back view'),
(38, 28, '../uploads/672317c95c4da_images (2).jpg', '2024-10-31 05:38:17', 'back view');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_initial` char(1) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `email` varchar(191) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `zip_code` char(5) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('Owner','Admin','Staff','General User') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `middle_initial`, `last_name`, `suffix`, `contact_number`, `email`, `address`, `zip_code`, `username`, `password_hash`, `role`, `created_at`, `updated_at`, `deleted_at`) VALUES
(4, 'Jhon Carl1', '', 'last2', '', '09999999999', 'as22@gmail.com', '', '', 'jlast2', '$2y$10$xc3ncPQ6b73XHDhGRhUhJOtG3VpdieA9wxWJWjKI4QOlZ53XI9oHi', 'Staff', '2024-10-19 09:44:43', '2024-10-25 09:52:11', NULL),
(5, 'Mark james', 'l', 'last3', '', '12345678908', 'haha1@gmail.com', 'place', '45345', 'mlast3', '$2y$10$mMacmmxWCYvFDcnJWrRh.Oka5uaIcGGWaDIQBdcf9hMbJw.QjYIWW', 'Owner', '2024-10-19 11:04:08', '2024-11-01 22:24:54', NULL),
(30, 'hala', 's', 'dsaa', 'sad', '09876543231', 'jen@gmail.com', 'asd', '1231', 'hdsaa', '$2y$10$9zf8CID.Nvx/dUMjL9uxFOcopU1X5U23shPX9br1o81LLUjBE0AkC', 'General User', '2024-11-01 22:38:40', '2024-11-01 22:38:40', NULL),
(31, 'jack', 'j', 'dsaa', 'sad', '09876543231', 'jen1@gmail.com', 'asd', '1231', 'jdsaa', '$2y$10$jpp79JoLLFKxmu16/bzPSe36M1v.SVXEGZkWvVLt9YFRtPIQHgnl2', 'General User', '2024-11-01 22:39:11', '2024-11-01 22:39:11', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `action_by` (`action_by`);

--
-- Indexes for table `data_reservations`
--
ALTER TABLE `data_reservations`
  ADD PRIMARY KEY (`reservation_id`);

--
-- Indexes for table `error_logs`
--
ALTER TABLE `error_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `reservation_id` (`reservation_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_cancellations`
--
ALTER TABLE `order_cancellations`
  ADD PRIMARY KEY (`cancellation_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `product_items`
--
ALTER TABLE `product_items`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `fk_category` (`category_id`);

--
-- Indexes for table `receipts`
--
ALTER TABLE `receipts`
  ADD PRIMARY KEY (`receipt_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `receipt_items`
--
ALTER TABLE `receipt_items`
  ADD PRIMARY KEY (`receipt_item_id`),
  ADD KEY `receipt_id` (`receipt_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`);

--
-- Indexes for table `reservation_ids`
--
ALTER TABLE `reservation_ids`
  ADD PRIMARY KEY (`reservation_id`);

--
-- Indexes for table `reservation_reschedule`
--
ALTER TABLE `reservation_reschedule`
  ADD PRIMARY KEY (`reschedule_id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`table_id`);

--
-- Indexes for table `table_images`
--
ALTER TABLE `table_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `table_id` (`table_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=348;

--
-- AUTO_INCREMENT for table `data_reservations`
--
ALTER TABLE `data_reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `error_logs`
--
ALTER TABLE `error_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `order_cancellations`
--
ALTER TABLE `order_cancellations`
  MODIFY `cancellation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `product_items`
--
ALTER TABLE `product_items`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `receipt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT for table `receipt_items`
--
ALTER TABLE `receipt_items`
  MODIFY `receipt_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `reservation_reschedule`
--
ALTER TABLE `reservation_reschedule`
  MODIFY `reschedule_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `table_images`
--
ALTER TABLE `table_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`action_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE SET NULL;

--
-- Constraints for table `order_cancellations`
--
ALTER TABLE `order_cancellations`
  ADD CONSTRAINT `order_cancellations_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_items`
--
ALTER TABLE `product_items`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`category_id`) ON DELETE CASCADE;

--
-- Constraints for table `receipts`
--
ALTER TABLE `receipts`
  ADD CONSTRAINT `receipts_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `receipts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `receipt_items`
--
ALTER TABLE `receipt_items`
  ADD CONSTRAINT `receipt_items_ibfk_1` FOREIGN KEY (`receipt_id`) REFERENCES `receipts` (`receipt_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `receipt_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product_items` (`product_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
