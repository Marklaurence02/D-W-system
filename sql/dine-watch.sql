-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 24, 2024 at 03:08 PM
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
  `action_type` enum('Login','Logout','Create Reservation','Update Reservation','Cancel Reservation','Order Placed','Order Updated','Order Canceled','Add Product','Update Product','Delete Product','Add Admin','Delete Admin','Add Category','Update Category','Add Table','Update Table','Delete Table','Update Profile','Delete Category','Update Admin','Update Status') DEFAULT NULL,
  `action_details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`log_id`, `action_by`, `action_type`, `action_details`, `created_at`) VALUES
(1, 10, 'Delete Category', 'Category with ID 4 was deleted by user 10.', '2024-11-23 14:30:18'),
(2, 10, 'Delete Admin', 'Admin \'jnsssz\' (ID: 36) was deleted by user \'Mark12\' (ID: 10).', '2024-11-23 14:40:15'),
(3, 10, 'Delete Category', 'Category with ID 4 was deleted by user 10.', '2024-11-23 14:43:52'),
(4, 10, 'Delete Category', 'Category with ID 4 was deleted by user \'Mark12\' (ID: 10).', '2024-11-23 14:44:34'),
(5, 10, 'Update Admin', 'Admin (ID: 10, Username: 12321) updated user (ID: 34).', '2024-11-23 14:47:06'),
(6, 10, 'Update Admin', 'Admin (ID: 10, Username: 12321) updated user (ID: 34).', '2024-11-23 14:50:50'),
(7, 10, 'Update Category', 'Category (ID: 4) updated by user (ID: 10, Username: Mark12) to \'asad2\'.', '2024-11-23 14:52:52'),
(8, 10, 'Update Category', 'Category (ID: 4) updated by user (ID: 10, Username: Mark12) to \'asad22\'.', '2024-11-23 14:53:19'),
(9, 10, 'Update Category', 'Category (ID: 1) updated by user (ID: 10, Username: Mark12) to \'pizza32\'.', '2024-11-23 14:55:21'),
(10, 10, 'Update Product', 'Updated product: Pizzasdf21 (Category ID: 1)', '2024-11-23 15:03:59'),
(11, 10, 'Update Admin', 'Admin (ID: 10, Username: 12321) updated user (ID: 34).', '2024-11-23 15:06:24'),
(12, 10, '', 'Order ID 1 status changed to \'Completed\' by user ID 10 (Username: Mark12).', '2024-11-23 15:16:26'),
(13, 10, '', 'Order ID 1 status changed to \'Completed\' by user ID 10 (Username: Mark12).', '2024-11-23 15:16:31'),
(14, 10, '', 'Order ID 1 status changed to \'Completed\' by user ID 10 (Username: Mark12).', '2024-11-23 15:18:25'),
(15, 10, '', 'Order ID 1 status changed to \'Completed\' by user ID 10 (Username: Mark12).', '2024-11-23 15:18:29'),
(16, 10, 'Update Status', 'Order ID 1 status changed to \'Paid in advance\' by user ID 10 (Username: Mark12).', '2024-11-23 15:21:24'),
(17, 10, 'Update Status', 'Order ID 1 status changed to \'Completed\' by user ID 10 (Username: Mark12).', '2024-11-23 15:25:44'),
(18, 10, 'Update Status', 'Order ID 1 status changed to \'Paid in advance\' by user ID 10 (Username: Mark12).', '2024-11-23 15:26:27'),
(19, 10, 'Update Status', 'Order ID 1 status changed to \'Completed\' by user ID 10 (Username: Mark12).', '2024-11-23 15:27:19'),
(20, 10, 'Update Status', 'Order ID 1 status changed to \'In-Progress\' by user ID 10 (Username: Mark12).', '2024-11-23 15:27:29'),
(21, 10, 'Update Status', 'Order ID 1 status changed to \'Canceled\' by user ID 10 (Username: Mark12).', '2024-11-23 15:30:55'),
(22, 10, 'Update Status', 'Order ID 1 status changed to \'Paid in advance\' by user ID 10 (Username: Mark12).', '2024-11-23 15:31:04'),
(23, 10, 'Add Admin', '121312 added a new admin/staff member: 12112 1212', '2024-11-23 16:01:51'),
(24, 10, 'Update Admin', 'Admin (ID: 10, Username: 121312) updated user (ID: 41).', '2024-11-23 16:02:25'),
(25, 10, 'Update Table', 'Updated table ID: 1 with table number: 1', '2024-11-23 16:06:47'),
(26, 10, 'Update Category', 'Category (ID: 1) updated by user (ID: 10, Username: Mark12) to \'pizza322\'.', '2024-11-23 16:46:55'),
(27, 10, 'Update Category', 'Category (ID: 4) updated by user (ID: 10, Username: Mark12) to \'asad222\'.', '2024-11-23 17:06:42'),
(28, 10, 'Update Category', 'Category (ID: 4) updated by user (ID: 10, Username: Mark12) to \'asad2223\'.', '2024-11-23 17:06:56'),
(29, 10, 'Update Category', 'Category (ID: 4) updated by user (ID: 10, Username: Mark12) to \'asad222321\'.', '2024-11-23 17:08:41'),
(30, 10, 'Update Category', 'Category (ID: 4) updated by user (ID: 10, Username: Mark12) to \'asad222321qw\'.', '2024-11-23 17:09:56'),
(31, 10, 'Logout', 'Mark12 logged out', '2024-11-23 17:32:19'),
(32, 10, 'Logout', 'Mark12 logged out', '2024-11-23 23:37:33'),
(33, 10, 'Login', 'User Mark12 logged in from IP: ::1', '2024-11-23 23:37:46'),
(34, 10, 'Logout', 'Mark12 logged out', '2024-11-23 23:39:34'),
(35, 10, 'Login', 'User Mark12 Login ', '2024-11-23 23:39:42'),
(36, 10, 'Logout', 'Mark12 logged out', '2024-11-23 23:45:00'),
(37, 10, 'Login', 'User Mark122 Login ', '2024-11-23 23:45:09'),
(38, 10, 'Update Profile', 'User Mark1222 updated their profile.', '2024-11-23 23:45:29'),
(39, 10, 'Update Profile', 'User Mark1222 updated their profile.', '2024-11-23 23:47:51'),
(40, 10, 'Update Profile', 'User Mark1222 updated their profile.', '2024-11-23 23:48:03'),
(41, 10, 'Update Profile', 'User Mark1222 updated their profile.', '2024-11-23 23:48:58'),
(42, 10, 'Logout', 'Mark122 logged out', '2024-11-23 23:51:26'),
(43, 42, 'Login', 'User mcaringal9 Login ', '2024-11-23 23:51:51'),
(44, 42, 'Update Profile', 'User mcaringal2 updated their profile.', '2024-11-23 23:52:11'),
(45, 42, 'Logout', 'mcaringal9 logged out', '2024-11-24 00:30:32'),
(46, 9, 'Login', 'User 123455 Login ', '2024-11-24 00:30:41'),
(47, 9, 'Logout', '123455 logged out', '2024-11-24 00:57:02'),
(48, 10, 'Login', 'User Mark1222 Login ', '2024-11-24 00:57:09'),
(49, 10, 'Logout', 'Mark1222 logged out', '2024-11-24 00:58:46'),
(50, 9, 'Login', 'User 123455 Login ', '2024-11-24 00:59:01'),
(51, 9, 'Logout', '123455 logged out', '2024-11-24 01:09:16'),
(52, 9, 'Login', 'User 123455 Login ', '2024-11-24 01:09:24'),
(53, 9, 'Logout', '123455 logged out', '2024-11-24 01:41:59'),
(54, 33, 'Login', 'User 1213564786 Login ', '2024-11-24 01:42:06'),
(55, 33, 'Logout', '1213564786 logged out', '2024-11-24 02:27:39'),
(56, 43, 'Update Profile', 'User mcaringal10 updated their profile.', '2024-11-24 08:39:40'),
(57, 10, 'Login', 'User Mark1222 Login ', '2024-11-24 09:40:48'),
(58, 10, 'Logout', 'Mark1222 logged out', '2024-11-24 09:46:24'),
(59, 42, 'Login', 'User mcaringal2 Login ', '2024-11-24 10:50:29'),
(60, 42, 'Logout', 'mcaringal2 logged out', '2024-11-24 11:34:12'),
(61, 45, 'Update Profile', 'User mas7 updated their profile.', '2024-11-24 11:56:10'),
(62, 45, 'Update Profile', 'User mas7 updated their profile.', '2024-11-24 11:58:36'),
(63, 45, 'Update Profile', 'User mas7 updated their profile.', '2024-11-24 12:59:03'),
(64, 45, 'Update Profile', 'User mas7 updated their profile.', '2024-11-24 13:30:39');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_group_message` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `sender_id`, `receiver_id`, `message`, `timestamp`, `is_group_message`) VALUES
(1, 9, 7, 'sa', '2024-11-24 01:35:20', 0),
(2, 9, 27, 'sasa', '2024-11-24 01:35:25', 0),
(3, 9, 12, 'zZ', '2024-11-24 01:35:31', 0),
(4, 9, 33, 'zxzx', '2024-11-24 01:35:46', 0),
(5, 7, 9, 'qw', '2024-11-24 05:39:04', 0),
(6, 43, 9, 's,.ahkv j', '2024-11-24 07:37:24', 0),
(7, 43, 9, 'sadja', '2024-11-24 07:44:51', 0);

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
(26, 44, 1, '2024-11-25', '08:30:00', 'Paid', 'debugging nalalng at yung active/deactive hahah', NULL, '2024-11-24 09:15:53', '2024-11-24 09:16:20');

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

CREATE TABLE `feedbacks` (
  `feedback_id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `feedback_text` text NOT NULL,
  `rating` int(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedbacks`
--

INSERT INTO `feedbacks` (`feedback_id`, `reservation_id`, `user_id`, `feedback_text`, `rating`, `created_at`) VALUES
(1, 1, 7, 'asa', 5, '2024-11-23 15:54:57');

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
(1, 7, 1, 'Product Name: Pizzasdf21 | Quantity: 1 | Price: 20.00', 20.00, '2024-11-23 20:52:21', 'paid in advance', NULL, '2024-11-23 12:52:21', '2024-11-23 15:31:04', 'Credit Card'),
(2, 7, 22, 'Product Name: pepsi | Quantity: 1 | Price: 213.00', 213.00, '2024-11-24 16:16:41', 'paid in advance', NULL, '2024-11-24 08:16:41', '2024-11-24 08:16:41', 'Credit Card'),
(3, 7, 23, 'Product Name: pepsi | Quantity: 1 | Price: 213.00', 213.00, '2024-11-24 16:23:05', 'paid in advance', NULL, '2024-11-24 08:23:05', '2024-11-24 08:23:05', 'Credit Card'),
(4, 44, 24, 'Product Name: pepsi | Quantity: 3 | Price: 1278.00; Product Name: Pizzasdf | Quantity: 4 | Price: 84.00; Product Name: Pizzasdf21 | Quantity: 5 | Price: 100.00', 823.00, '2024-11-24 16:54:58', 'paid in advance', NULL, '2024-11-24 08:54:58', '2024-11-24 08:54:58', 'Credit Card'),
(5, 7, 27, 'Product Name: pepsi | Quantity: 6 | Price: 1278.00; Product Name: Pizzasdf | Quantity: 2 | Price: 42.00; Product Name: Pizzasdf21 | Quantity: 1 | Price: 20.00', 1340.00, '2024-11-24 19:27:26', 'paid in advance', NULL, '2024-11-24 11:27:26', '2024-11-24 11:27:26', 'Credit Card'),
(6, 43, 28, 'Product Name: Pizzasdf | Quantity: 3 | Price: 63.00; Product Name: pepsi | Quantity: 4 | Price: 852.00; Product Name: Pizzasdf21 | Quantity: 4 | Price: 20.00', 995.00, '2024-11-24 19:35:27', 'paid in advance', NULL, '2024-11-24 11:35:27', '2024-11-24 11:35:27', 'Credit Card'),
(7, 45, 29, 'Product Name: Pizzasdf21 | Quantity: 5 | Price: 100.00', 100.00, '2024-11-24 19:54:21', 'paid in advance', NULL, '2024-11-24 11:54:21', '2024-11-24 11:54:21', 'Credit Card'),
(8, 45, 30, 'Product Name: Pizzasdf | Quantity: 1 | Price: 21.00; Product Name: Pizzasdf21 | Quantity: 1 | Price: 20.00', 41.00, '2024-11-24 20:00:21', 'paid in advance', NULL, '2024-11-24 12:00:21', '2024-11-24 12:00:21', 'Credit Card'),
(9, 45, 31, 'Product Name: Pizzasdf21 | Quantity: 10 | Price: 200.00; Product Name: Pizzasdf | Quantity: 1 | Price: 21.00', 221.00, '2024-11-24 20:12:09', 'paid in advance', NULL, '2024-11-24 12:12:09', '2024-11-24 12:12:09', 'Credit Card'),
(12, 45, 32, 'Product Name: Pizzasdf21 | Quantity: 6 | Price: 120.00', 120.00, '2024-11-24 20:31:12', 'paid in advance', NULL, '2024-11-24 12:31:12', '2024-11-24 12:31:12', 'Credit Card'),
(13, 45, 33, 'Product Name: Pizzasdf21 | Quantity: 6 | Price: 20.00', 120.00, '2024-11-24 20:38:47', 'paid in advance', NULL, '2024-11-24 12:38:47', '2024-11-24 12:38:47', 'Credit Card'),
(14, 43, 35, 'Product Name: Pizzasdf21 | Quantity: 2 | Price: 40.00; Product Name: pepsi | Quantity: 1 | Price: 213.00', 253.00, '2024-11-24 21:03:57', 'paid in advance', NULL, '2024-11-24 13:03:57', '2024-11-24 13:03:57', 'Credit Card'),
(15, 45, 34, 'Product Name: Pizzasdf21 | Quantity: 4 | Price: 80.00', 80.00, '2024-11-24 21:09:11', 'paid in advance', NULL, '2024-11-24 13:09:11', '2024-11-24 13:09:11', 'Credit Card'),
(18, 45, 36, 'Product Name: pepsi | Quantity: 3 | Price: 639.00', 639.00, '2024-11-24 21:22:41', 'paid in advance', NULL, '2024-11-24 13:22:41', '2024-11-24 13:22:41', 'Credit Card'),
(19, 43, 37, 'Product Name: Pizzasdf21 | Quantity: 4 | Price: 80.00', 80.00, '2024-11-24 21:44:55', 'paid in advance', NULL, '2024-11-24 13:44:55', '2024-11-24 13:44:55', 'Credit Card'),
(20, 45, 38, 'Product Name: pepsi | Quantity: 1 | Price: 213.00', 213.00, '2024-11-24 21:45:20', 'paid in advance', NULL, '2024-11-24 13:45:20', '2024-11-24 13:45:20', 'Credit Card'),
(22, 45, 39, 'Product Name: Pizzasdf | Quantity: 5 | Price: 105.00; Product Name: pepsi | Quantity: 1 | Price: 639.00', 318.00, '2024-11-24 21:50:12', 'paid in advance', NULL, '2024-11-24 13:50:12', '2024-11-24 13:50:12', 'Credit Card'),
(23, 45, 40, 'Product Name: pepsi | Quantity: 1 | Price: 213.00', 213.00, '2024-11-24 21:55:15', 'paid in advance', NULL, '2024-11-24 13:55:15', '2024-11-24 13:55:15', 'Credit Card'),
(24, 45, 41, 'Product Name: Pizzasdf21 | Quantity: 2 | Price: 40.00', 40.00, '2024-11-24 21:57:05', 'paid in advance', NULL, '2024-11-24 13:57:05', '2024-11-24 13:57:05', 'Credit Card'),
(25, 45, 42, 'Product Name: pepsi | Quantity: 1 | Price: 213.00', 213.00, '2024-11-24 22:00:06', 'paid in advance', NULL, '2024-11-24 14:00:06', '2024-11-24 14:00:06', 'Credit Card'),
(26, 45, 43, 'Product Name: pepsi | Quantity: 1 | Price: 213.00', 213.00, '2024-11-24 22:06:56', 'paid in advance', NULL, '2024-11-24 14:06:56', '2024-11-24 14:06:56', 'Credit Card');

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
  `reservation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `totalprice` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `reservation_id`, `user_id`, `product_id`, `quantity`, `totalprice`) VALUES
(14, 1, 0, 44, 2, 4, 426.00),
(15, 1, 0, 44, 3, 8, 168.00),
(16, 1, 0, 44, 1, 3, 60.00),
(36, 1, 0, 43, 1, 1, 20.00);

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`category_id`, `category_name`, `status`) VALUES
(1, 'pizza322', 'active'),
(2, 'drinks', 'active'),
(3, 'pizza 1', 'active'),
(4, 'asad222321qw', 'active');

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
  `category_id` int(11) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_items`
--

INSERT INTO `product_items` (`product_id`, `product_name`, `price`, `special_instructions`, `product_image`, `created_at`, `updated_at`, `quantity`, `category_id`, `status`) VALUES
(1, 'Pizzasdf21', 20.00, '12112sa', '../Uploads/673b51ace3901_istockphoto-1371910917-612x612.jpg', '2024-11-07 01:06:11', '2024-11-24 12:12:09', 215, 1, 'active'),
(2, 'pepsi', 213.00, '234', '../Uploads/672c12a5ee9a4_images.jpg', '2024-11-07 01:06:45', '2024-11-24 13:22:41', 1, 2, 'active'),
(3, 'Pizzasdf', 21.00, '1231', '../Uploads/673df2ac3f3bf_istockphoto-1371910917-612x612.jpg', '2024-11-20 14:31:08', '2024-11-24 12:12:09', 19, 1, 'active');

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
(1, 1, 7, 20.00, '2024-11-23 12:52:21', 'Credit Card'),
(2, 2, 7, 213.00, '2024-11-24 08:16:41', 'Credit Card'),
(3, 3, 7, 213.00, '2024-11-24 08:23:05', 'Credit Card'),
(4, 4, 44, 823.00, '2024-11-24 08:54:58', 'Credit Card'),
(5, 5, 7, 1340.00, '2024-11-24 11:27:26', 'Credit Card'),
(6, 6, 43, 995.00, '2024-11-24 11:35:27', 'Credit Card'),
(7, 7, 45, 100.00, '2024-11-24 11:54:21', 'Credit Card'),
(8, 8, 45, 41.00, '2024-11-24 12:00:21', 'Credit Card'),
(9, 9, 45, 221.00, '2024-11-24 12:12:09', 'Credit Card'),
(12, 12, 45, 120.00, '2024-11-24 12:31:12', 'Credit Card'),
(13, 13, 45, 120.00, '2024-11-24 12:38:47', 'Credit Card'),
(14, 14, 43, 253.00, '2024-11-24 13:03:57', 'Credit Card'),
(15, 15, 45, 80.00, '2024-11-24 13:09:11', 'Credit Card'),
(18, 18, 45, 639.00, '2024-11-24 13:22:41', 'Credit Card'),
(19, 19, 43, 80.00, '2024-11-24 13:44:55', 'Credit Card'),
(20, 20, 45, 213.00, '2024-11-24 13:45:20', 'Credit Card'),
(22, 22, 45, 318.00, '2024-11-24 13:50:12', 'Credit Card'),
(23, 23, 45, 213.00, '2024-11-24 13:55:15', 'Credit Card'),
(24, 24, 45, 40.00, '2024-11-24 13:57:05', 'Credit Card'),
(25, 25, 45, 213.00, '2024-11-24 14:00:06', 'Credit Card'),
(26, 26, 45, 213.00, '2024-11-24 14:06:56', 'Credit Card');

-- --------------------------------------------------------

--
-- Table structure for table `receipt_items`
--

CREATE TABLE `receipt_items` (
  `receipt_item_id` int(11) NOT NULL,
  `receipt_id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `item_total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receipt_items`
--

INSERT INTO `receipt_items` (`receipt_item_id`, `receipt_id`, `reservation_id`, `user_id`, `product_id`, `quantity`, `item_total_price`) VALUES
(1, 1, 1, 7, 1, 1, 20.00),
(2, 2, 22, 7, 2, 1, 213.00),
(3, 3, 23, 7, 2, 1, 213.00),
(4, 4, 24, 44, 1, 5, 100.00),
(5, 4, 24, 44, 3, 4, 84.00),
(6, 4, 24, 44, 2, 3, 1278.00),
(7, 5, 27, 7, 1, 1, 20.00),
(8, 5, 27, 7, 3, 2, 42.00),
(9, 5, 27, 7, 2, 6, 1278.00),
(10, 6, 28, 43, 1, 4, 20.00),
(11, 6, 28, 43, 2, 4, 852.00),
(12, 6, 28, 43, 3, 3, 63.00),
(13, 7, 29, 45, 1, 5, 100.00),
(14, 8, 30, 45, 1, 1, 20.00),
(15, 8, 30, 45, 3, 1, 21.00),
(17, 9, 31, 45, 3, 1, 21.00),
(18, 9, 31, 45, 1, 10, 200.00),
(22, 12, 32, 45, 1, 6, 120.00),
(23, 13, 33, 45, 1, 6, 20.00),
(24, 14, 35, 43, 2, 1, 213.00),
(25, 14, 35, 43, 1, 2, 40.00),
(27, 15, 34, 45, 1, 4, 80.00),
(30, 18, 36, 45, 2, 3, 639.00),
(31, 19, 37, 43, 1, 4, 80.00),
(32, 20, 38, 45, 2, 1, 213.00),
(36, 22, 39, 45, 2, 1, 639.00),
(37, 22, 39, 45, 3, 5, 105.00),
(39, 23, 40, 45, 2, 1, 213.00),
(40, 24, 41, 45, 1, 2, 40.00),
(41, 25, 42, 45, 2, 1, 213.00),
(42, 26, 43, 45, 2, 1, 213.00);

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`reservation_id`, `user_id`, `table_id`, `reservation_date`, `reservation_time`, `status`, `custom_note`, `created_at`, `updated_at`) VALUES
(1, 7, 1, '2024-11-24', '07:00:00', 'Complete', 'aSDA', '2024-11-23 12:51:38', '2024-11-23 15:53:17'),
(22, 7, 1, '2024-11-25', '07:00:00', 'Paid', '', '2024-11-24 08:07:07', '2024-11-24 08:16:41'),
(23, 7, 4, '2024-11-25', '07:00:00', 'Paid', 'sasd', '2024-11-24 08:21:47', '2024-11-24 08:23:04'),
(24, 44, 3, '2024-11-25', '07:15:00', 'Paid', '1234', '2024-11-24 08:49:37', '2024-11-24 08:54:58'),
(27, 7, 1, '2024-11-25', '08:15:00', 'Paid', '', '2024-11-24 11:20:39', '2024-11-24 11:21:12'),
(28, 43, 1, '2024-11-25', '09:45:00', 'Paid', 'sdf', '2024-11-24 11:34:59', '2024-11-24 11:35:27'),
(29, 45, 6, '2024-11-25', '07:15:00', 'Paid', 'sdfgvhb', '2024-11-24 11:53:37', '2024-11-24 11:54:21'),
(30, 45, 4, '2024-11-25', '08:30:00', 'Paid', 'rdty', '2024-11-24 11:59:54', '2024-11-24 12:00:21'),
(31, 45, 4, '2024-11-25', '08:15:00', 'Paid', 'qwsdcxva', '2024-11-24 12:04:26', '2024-11-24 12:04:50'),
(32, 45, 4, '2024-11-25', '09:45:00', 'Paid', '21', '2024-11-24 12:19:09', '2024-11-24 12:31:12'),
(33, 45, 6, '2024-11-25', '07:00:00', 'Paid', 'qwsds', '2024-11-24 12:38:26', '2024-11-24 12:38:47'),
(34, 45, 4, '2024-11-25', '11:15:00', 'Paid', '', '2024-11-24 12:40:57', '2024-11-24 13:09:11'),
(35, 43, 13, '2024-11-25', '07:15:00', 'Paid', 'wqsadasa', '2024-11-24 12:56:51', '2024-11-24 13:03:57'),
(36, 45, 6, '2024-11-29', '07:15:00', 'Paid', '', '2024-11-24 13:12:17', '2024-11-24 13:22:41'),
(37, 43, 12, '2024-11-25', '07:15:00', 'Paid', 'saaas', '2024-11-24 13:15:38', '2024-11-24 13:44:55'),
(38, 45, 4, '2024-11-25', '11:00:00', 'Paid', 'aSzdfd', '2024-11-24 13:37:53', '2024-11-24 13:45:20'),
(39, 45, 15, '2024-11-26', '07:00:00', 'Paid', 'qweesd', '2024-11-24 13:48:53', '2024-11-24 13:50:12'),
(40, 45, 6, '2024-11-25', '08:30:00', 'Paid', 'asdaasd', '2024-11-24 13:53:15', '2024-11-24 13:55:15'),
(41, 45, 6, '2024-11-25', '09:45:00', 'Paid', 'waszx', '2024-11-24 13:56:43', '2024-11-24 13:57:05'),
(42, 45, 6, '2024-11-25', '11:15:00', 'Paid', 'srty', '2024-11-24 13:59:36', '2024-11-24 14:00:06'),
(43, 45, 12, '2024-11-25', '08:30:00', 'Paid', 'zxc', '2024-11-24 14:06:23', '2024-11-24 14:06:56');

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
(5, 10, '2106ea9bc61ef5c39fba7a916e6cdb99bb9448dce4eea8959113d143ddee57bd', '2024-11-23 13:06:02', '2024-11-23 15:06:02'),
(6, 10, 'ccea21428e9fcc7b74ccc87a7da5378367595501bac6d030e83acde2f2ade949', '2024-11-23 13:21:20', '2024-11-23 15:21:20'),
(7, 10, 'ab7d18df84f0904e993550ce2f362f087ce6033250431fa56c37b0ce5c64b95b', '2024-11-23 15:43:41', '2024-11-23 17:43:41'),
(8, 42, '6a47dc254470bd395d257223614b7272dc30ef9ce88fe9c4b13e07b93d4c00d4', '2024-11-23 17:46:48', '2024-11-23 19:46:48'),
(20, 42, '89a16fe922e679a0e5745f8c98a59ce0c25012fe96fa176500b97cf082f52765', '2024-11-24 10:50:29', '2024-11-24 12:50:29');

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

CREATE TABLE `tables` (
  `table_id` int(11) NOT NULL,
  `table_number` int(11) NOT NULL,
  `seating_capacity` int(11) NOT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `area` enum('Indoor','Outdoor') NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`table_id`, `table_number`, `seating_capacity`, `is_available`, `area`, `status`) VALUES
(1, 1, 11, 1, 'Outdoor', 'active'),
(2, 1, 0, 0, 'Indoor', 'active'),
(3, 12, 3, 1, 'Indoor', 'active'),
(4, 12, 0, 1, 'Indoor', 'active'),
(6, 2, 2, 1, 'Indoor', 'active'),
(8, 13, 2, 1, 'Outdoor', 'active'),
(11, 14, 12, 1, 'Outdoor', 'active'),
(12, 5, 4, 1, 'Outdoor', 'active'),
(13, 1221, 12, 1, 'Indoor', 'active'),
(14, 12112, 2, 1, 'Indoor', 'active'),
(15, 21, 1, 1, 'Outdoor', 'active');

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
(1, 1, '../uploads/672c12c9aad00_image.jpg', '2024-11-07 01:07:21', 'front view'),
(2, 1, '../uploads/673b599945aa8_1676263252681.jpg', '2024-11-07 01:07:21', 'back view'),
(3, 1, '../uploads/672c12c9ac18d_image.jpg', '2024-11-07 01:07:21', 'left view'),
(4, 2, '../uploads/672c12e59a8f7_marvelous-city-skyline.jpg', '2024-11-07 01:07:49', 'front view'),
(5, 2, '../uploads/672c12e59b9e1_dramatic-river-views.jpg', '2024-11-07 01:07:49', 'back view'),
(6, 3, '../uploads/672c8f8b5f26a_l-intro-1678787918.jpg', '2024-11-07 09:59:39', 'front view'),
(7, 4, '../uploads/672c9049a0252_images (3).jpg', '2024-11-07 10:02:49', 'front view'),
(9, 3, '../uploads/673b612461f97_1684320192420.png', '2024-11-18 15:45:40', 'left view'),
(10, 4, '../uploads/673b61944410d_istockphoto-1371910917-612x612.jpg', '2024-11-18 15:47:32', 'back view'),
(11, 3, '../uploads/673bd7c9456ab_istockphoto-1371910917-612x612.jpg', '2024-11-19 00:11:53', 'back view'),
(12, 1, '../uploads/673be31fa3ebf_1684320192420.png', '2024-11-19 01:00:15', 'right view'),
(17, 6, '../uploads/673f1da78dd7b_istockphoto-1371910917-612x612.jpg', '2024-11-21 11:46:47', 'back view'),
(18, 8, '../uploads/6741d72881795_community-activities-teensnarrow.jpg', '2024-11-23 13:22:48', 'front view');

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
  `username` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('Owner','Admin','Staff','General User') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `account_status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `status` enum('online','offline') NOT NULL DEFAULT 'offline'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `middle_initial`, `last_name`, `suffix`, `contact_number`, `email`, `address`, `zip_code`, `username`, `password_hash`, `role`, `created_at`, `updated_at`, `account_status`, `status`) VALUES
(1, 'Mark Laurence', 'l', 'caringal', 'jr', '09345678901', 'ha1@gmail.com', 'place', '1234', 'mcaringal', '$2y$10$U5xRKzJxyW0Lgia.SYeMZ.KK/vtWuOuwngG5M1qMsyw8jrxO.eeXy', 'General User', '2024-11-07 00:59:59', '2024-11-07 00:59:59', 'active', 'offline'),
(3, 'Mark Laurence', 'l', 'caringal', 'wq', '09234567891', 'ha22@gmail.com', 'place', '1234', 'mcaringal3', '$2y$10$CT/oMDS884MILfbF3/2BwOolbz1R7EIMKzZAnGPcrauQX9X5.R9RO', 'General User', '2024-11-07 01:00:59', '2024-11-07 01:00:59', 'active', 'offline'),
(6, 'Jack', 'l', 'caringal', 'wq', '09234567891', 'mk@gmail.com', 'place', '1234', 'jcaringal', '$2y$10$jVll8K2S6EsrZKPsWUo.POe1lv32lSQncJqkT9567wS78ZrAV3tde', 'Owner', '2024-11-07 01:04:04', '2024-11-08 08:36:02', 'active', 'offline'),
(7, 'Mark Laurence', 'l', 'caringal1', 'wq', '09345678901', 'g@gmail.com', 'place', '1234', 'mcaringal1', '$2y$10$xXj.kj406T.ZM571znX1qeVKsAAEWVh94M0AOBaonnZvbla2nMQ8e', 'General User', '2024-11-07 01:11:42', '2024-11-24 11:20:11', 'active', 'online'),
(9, 'jack12', '1', 'jason', '', '09876543462', 'Jack@gmail.com', '1221', '1231', '123455', '$2y$10$Dk5YSAHL2G02USrQo4KateYe0q4wm9hvq8ywVD7fzWDg4dkJplC.O', 'Staff', '2024-11-09 11:46:29', '2024-11-22 14:38:44', 'active', 'offline'),
(10, 'Mark Jack1323', 'N', 'caringal2', '', '09234567891', 'ha23@gmail.com', 'place12', '1231', 'Mark1222', '$2y$10$ZT9OElJF0bEAfKn0Do5/gObuP0HPnetJJQs4v.n0y1XDd322G7uhe', 'Owner', '2024-11-14 02:40:10', '2024-11-23 23:50:07', 'active', 'offline'),
(11, 'Mark LAsdaas', 's', 'sad', 'sad', '09876543212', 'ha111@gmail.com', 'sad', '1234', 'msad', '$2y$10$vwToazZbZ20Cj5btCf54PuCGE9Cs0SSoGAxJVPgIUiuUX.mpBoZiW', 'General User', '2024-11-14 02:53:34', '2024-11-14 02:53:45', 'active', 'offline'),
(12, 'jack12', NULL, 'sa', NULL, '09875643221', 'ha223@gmail.com', NULL, NULL, 'Mark', '$2y$10$5xl0RKLYBpYhxwop.ZwGheXANyEzVRCyhvQylAWUIm1R8MLrj7l.S', 'Admin', '2024-11-18 08:13:24', '2024-11-18 08:13:24', 'active', 'offline'),
(16, 'Mark', '', 'saddadasd', '', '09978671234', 'ha22123@gmail.com', 'place', '4534', 'msaddadasd', '$2y$10$ZfNHGvgboymbSUEDz5Vda.pyfPU9BxDlGx.UhepbG0xURBxLr4MR6', 'General User', '2024-11-21 03:11:06', '2024-11-21 03:12:06', 'active', 'offline'),
(17, 'Mark', 'a', 'aads', 'asd', '09978671234', 'ha2312@gmail.com', 'asd', '1234', 'maads', '$2y$10$3E0BvnpPfgB9IhwCOKDIOOVKY3Uq3H7N/HDN2lyGG/jVGEpsplNxe', 'General User', '2024-11-21 03:44:55', '2024-11-21 03:44:55', 'active', 'offline'),
(19, 'Mark', 'l', 'As', 'AS', '09978671234', 'g@g12mail.com', 'place', '1234', 'mas', '$2y$10$xANHDehRsrOEEoXqfFQhB.emmw69WF/0.USypIoCmJiyrXb4O4rr6', 'General User', '2024-11-21 04:29:46', '2024-11-21 04:29:46', 'active', 'offline'),
(20, 'Mark', 'l', 'As', 'AS', '09978671234', 'g@g122mail.com', 'place', '1234', 'mas2', '$2y$10$kmt.UFtKS5acmrNGu8NPXeqgsRf0B3KKBF5ypXFSk1gisK3e9cTSy', 'General User', '2024-11-21 04:30:37', '2024-11-21 04:30:37', 'active', 'offline'),
(21, 'Mark', 'l', 'As', 'AS', '09978671234', 'g@g1212mail.com', 'place', '1234', 'mas3', '$2y$10$TvDSZxfBrhbgAlk4F.LRkudHD/Ri/9e5YqAYSxJLUMDknPOzoQ7eq', 'General User', '2024-11-21 04:31:24', '2024-11-21 04:31:24', 'active', 'offline'),
(22, 'Mark', 'l', 'As', 'AS', '09978671234', 'g@g121212mail.com', 'place', '1234', 'mas4', '$2y$10$ZVWGsnjceq/PP0qFJqmDhOajdpHE6AQKZqnqDhmYo7uSX4XZzAIlG', 'General User', '2024-11-21 04:33:16', '2024-11-21 04:33:16', 'active', 'offline'),
(23, '', '', '', '', '', 'jen112231@gmail.com', '', '', '15', '$2y$10$Y9fe6S9cpBRyn0eOUD31.OTcDbVkq6Urb97vp3uszX5u8LWQdfx4u', 'General User', '2024-11-21 04:34:50', '2024-11-21 04:34:50', 'active', 'offline'),
(24, '12', '1', '12', '12', '09984262708', 'jen121121@gmail.com', '12', '4322', '112', '$2y$10$EPq8e93hr4OjVc9llMfG/e3OYt9AKRgBIRwo7jPezBiWii9tHurb6', 'General User', '2024-11-21 04:36:03', '2024-11-21 04:36:03', 'active', 'offline'),
(25, '12', '1', '12', '12', '09984262708', 'jen121121211@gmail.com', '12', '4322', '1122', '$2y$10$KkHkr6Hs9JIES8ZMQvc9M.ILfI6e/Rf6VgInlEDf.ULXRsHXrpGx.', 'General User', '2024-11-21 04:37:40', '2024-11-21 04:37:40', 'active', 'offline'),
(26, 'Mark Laurence', '', 'Caringal', '', '09984262708', 'jen112212@gmail.com', 'noe', '4322', 'mcaringal4', '$2y$10$Nny8i0HVfnju7de6ZxbqqeWRZ7EToYsV/0ut9gMZjCeXCh8/FCvju', 'General User', '2024-11-21 05:44:08', '2024-11-21 05:44:08', 'active', 'offline'),
(27, 'Mark Laurence', '', 'Caringal', '', '09984262708', 'jen1121@gmail.com', 'noe', '4322', 'mcaringal5', '$2y$10$.c8gxt8Cte0P.0K1AyA91uDllQztr3jmmSJqcfWRQDzAAbQZ3/oOu', 'General User', '2024-11-21 05:44:31', '2024-11-21 11:31:23', 'active', 'offline'),
(28, 'Mark Laurence', '', 'Caringal', '', '09984262708', 'kijen112121@gmail.com', 'noe', '4322', 'mcaringal6', '$2y$10$WamRLunwTEch3q3o4NbW6.ECMcDIj5YQcmcQTKF/Q4mj/usq60Ay6', 'General User', '2024-11-21 05:48:54', '2024-11-21 05:48:54', 'active', 'offline'),
(29, 'Mark', 'l', 'As', 'AS', '09978671234', 'g@g12121212mail.com', 'place', '1234', 'mas5', '$2y$10$3P9JJ0GvVIe3gnHZe.YrfOrvZWUgj6AZguqlg4uguNWas77tzlAyK', 'General User', '2024-11-21 05:51:50', '2024-11-21 05:51:50', 'active', 'offline'),
(30, 'Mark james', 'l', 'As', 'asd', '09978671234', 'ha2jhg3@gmail.com', '1221', '4534', 'mas6', '$2y$10$0EuNYGBYm0wynYNzIfpoWOGtLPWwvJapOWtYQBbhD5joir7iUw6/O', 'General User', '2024-11-21 05:52:29', '2024-11-21 05:52:29', 'active', 'offline'),
(31, 'Mark Laurence', '', 'Caringal', '', '09984262708', 'jen112112@gmail.com', 'noe', '4322', 'mcaringal7', '$2y$10$ZN/3yCv/H6bfmNa8IH4CA.DLAeTaD9DquhxWPhAIHqzh6G9Ss4JH.', 'General User', '2024-11-21 07:01:20', '2024-11-21 07:01:20', 'active', 'offline'),
(32, 'Mark Laurence', '', 'Caringal', '', '09984262708', 'jen112qw1@gmail.com', 'noe', '4322', 'mcaringal8', '$2y$10$AVO4lcScvlVgo.J.DFhg1OcdoDuNb1gAmbrlwxtIBkVJEqW6bPk4S', 'General User', '2024-11-21 07:05:34', '2024-11-21 07:05:34', 'active', 'offline'),
(33, 'Mark LauRENCE', 'k', 'caringal', NULL, '09876543462', 'admin@gmail.com', '12', '21', '1213564786', '$2y$10$mxkwOv80C5jQh5ekDDpwG.GJt7Vg0Ny5tQeIjn5WNH8RdMh1o/8XS', 'Admin', '2024-11-23 01:41:53', '2024-11-23 08:39:57', 'active', 'offline'),
(34, 'Mark Laurence', '1', 'Caringal', NULL, '09984262708', '123@gmail.com', '122121', NULL, '12321', '$2y$10$qhxVweg0WOYVmIn7F/asa.PQc2/fgw2Dw0F3cY2XPd6BBMKRXzL5W', 'Admin', '2024-11-23 12:57:05', '2024-11-23 15:06:24', 'active', 'offline'),
(41, '12112', '', '1212', NULL, '09345678895', '21@gmail.com', 'place', NULL, '121312', '$2y$10$4HeYivuDPKBevsnJfYBtUuE2IYJhdmQz9fD2U6XhASM6/n2OERBHm', 'Admin', '2024-11-23 16:01:51', '2024-11-23 16:02:25', 'active', 'offline'),
(42, 'Mark Laurence', 'l', 'caringal', 'jr', '09234567891', '22-35748@g.batstate-u.edu.ph', '12212', '1234', 'mcaringal2', '$2y$10$IMfX9xyC1KC3PdeWc2Y2U..v/JxJwzhPqRFtyaIXeYEOy.FSuEdY.', 'Owner', '2024-11-23 17:32:45', '2024-11-24 10:43:36', 'active', 'offline'),
(43, 'Mark Laurence3242', '', 'Caringal', '', '09984262708', 'andreiagasopa@gmail.com', 'noe', '4322', 'mcaringal10', '$2y$10$1RX45Jp0z0cTFp7U7KiZ3.d57BnNB4ptASUG1JeMwJQCXnUffrbHC', 'General User', '2024-11-24 00:17:24', '2024-11-24 11:33:11', 'active', 'online'),
(44, 'kyle', 'm', 'calingasan', '', '09984262708', 'calingasankylematthew@gmail.com', 'noe', '4322', 'kcalingasan', '$2y$10$USFxQfBbyGgitn6DR/RpeOt6gbVmR2HlG882BA.SKhvc4k20w7orS', 'General User', '2024-11-24 08:48:11', '2024-11-24 08:48:20', 'active', 'offline'),
(45, 'Mark', 'c', 'As', 'asd', '09978671234', 'benzoncarl010@gmail.com', 'place', '4534', 'mas7', '$2y$10$kbcRdFGHily5LkaLPo.NRO67Q7FbT7MECPOGLutXOryvIer6kPNJq', 'General User', '2024-11-24 11:45:24', '2024-11-24 13:30:39', 'active', 'online');

-- --------------------------------------------------------

--
-- Table structure for table `user_staff_assignments`
--

CREATE TABLE `user_staff_assignments` (
  `user_id` int(11) NOT NULL,
  `assigned_staff_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_staff_assignments`
--

INSERT INTO `user_staff_assignments` (`user_id`, `assigned_staff_id`) VALUES
(7, 9),
(27, 9),
(43, 9),
(44, 9),
(45, 9);

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
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_reservations`
--
ALTER TABLE `data_reservations`
  ADD PRIMARY KEY (`reservation_id`);

--
-- Indexes for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `reservation_id` (`reservation_id`),
  ADD KEY `user_id` (`user_id`);

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
  ADD KEY `product_id` (`product_id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
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
  ADD KEY `user_id` (`user_id`),
  ADD KEY `session_token` (`session_token`);

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
-- Indexes for table `user_staff_assignments`
--
ALTER TABLE `user_staff_assignments`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `assigned_staff_id` (`assigned_staff_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `data_reservations`
--
ALTER TABLE `data_reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `order_cancellations`
--
ALTER TABLE `order_cancellations`
  MODIFY `cancellation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `product_items`
--
ALTER TABLE `product_items`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `receipt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `receipt_items`
--
ALTER TABLE `receipt_items`
  MODIFY `receipt_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `reservation_reschedule`
--
ALTER TABLE `reservation_reschedule`
  MODIFY `reschedule_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `table_images`
--
ALTER TABLE `table_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`action_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD CONSTRAINT `feedbacks_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`reservation_id`),
  ADD CONSTRAINT `feedbacks_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

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
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `receipt_items_ibfk_1` FOREIGN KEY (`receipt_id`) REFERENCES `receipts` (`receipt_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `receipt_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product_items` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_staff_assignments`
--
ALTER TABLE `user_staff_assignments`
  ADD CONSTRAINT `user_staff_assignments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_staff_assignments_ibfk_2` FOREIGN KEY (`assigned_staff_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
