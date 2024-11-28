-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 28, 2024 at 10:41 AM
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
(1, 1, 'Add Product', 'Added a new product: 12 (Category ID: 1)', '2024-11-27 11:05:22'),
(2, 1, 'Add Product', 'Added a new product: 12 (Category ID: 1)', '2024-11-27 11:06:53'),
(3, 1, 'Add Table', 'Added table number 1 in Indoor area with seating capacity of 12.', '2024-11-27 11:07:14'),
(4, 1, 'Logout', 'mcaringal logged out', '2024-11-27 11:08:20'),
(5, 19, 'Login', 'User 32exc Login ', '2024-11-27 11:08:26'),
(6, 19, 'Logout', '32exc logged out', '2024-11-27 11:08:33'),
(7, 19, 'Login', 'User 32exc Login ', '2024-11-27 11:08:38'),
(8, 19, 'Logout', '32exc logged out', '2024-11-27 11:08:42'),
(9, 19, 'Login', 'User 32exc Login ', '2024-11-27 11:08:54'),
(10, 19, 'Logout', '32exc logged out', '2024-11-27 11:10:01'),
(11, 17, 'Login', 'User 123456789 Login ', '2024-11-27 11:10:41'),
(12, 17, 'Logout', '123456789 logged out', '2024-11-27 11:22:33'),
(13, 1, 'Login', 'User mcaringal Login ', '2024-11-27 11:22:46'),
(14, 1, 'Logout', 'mcaringal logged out', '2024-11-27 11:23:43'),
(15, 17, 'Login', 'User 123456789 Login ', '2024-11-27 11:24:03'),
(16, 17, 'Logout', '123456789 logged out', '2024-11-27 11:40:07'),
(17, 1, 'Login', 'User mcaringal Login ', '2024-11-27 11:40:14'),
(18, 1, 'Logout', 'mcaringal logged out', '2024-11-27 11:44:35'),
(19, 17, 'Login', 'User 123456789 Login ', '2024-11-27 11:44:48'),
(20, 17, 'Logout', '123456789 logged out', '2024-11-27 11:46:39'),
(21, 1, 'Login', 'User mcaringal Login ', '2024-11-27 11:46:56'),
(22, 1, 'Add Product', 'Added a new product: 21 (Category ID: 1)', '2024-11-27 11:50:49'),
(23, 1, 'Update Product', 'Updated product: 12 (Category ID: 1)', '2024-11-27 11:52:55'),
(24, 1, 'Update Table', 'Updated table ID: 1 with table number: 1', '2024-11-27 11:53:05'),
(25, 1, 'Add Table', 'Added table number 1 in Outdoor area with seating capacity of 12.', '2024-11-27 11:53:18'),
(26, 1, 'Logout', 'mcaringal logged out', '2024-11-27 11:53:46'),
(27, 15, 'Update Profile', 'User mcaringal4 updated their profile.', '2024-11-27 11:59:37'),
(28, 15, 'Update Profile', 'User mcaringal4 updated their profile.', '2024-11-27 12:01:15'),
(29, 1, 'Login', 'User mcaringal Login ', '2024-11-27 12:02:03'),
(30, 1, 'Login', 'User mcaringal Login ', '2024-11-28 06:12:21'),
(31, 1, 'Add Product', 'Added a new product: ghj (Category ID: 2)', '2024-11-28 06:13:16'),
(32, 1, 'Add Product', 'Added a new product: kb (Category ID: 1)', '2024-11-28 06:13:40'),
(33, 1, 'Add Product', 'Added a new product: kb (Category ID: 1)', '2024-11-28 06:13:58'),
(34, 1, 'Add Product', 'Added a new product: 12 (Category ID: 1)', '2024-11-28 06:14:14'),
(35, 1, 'Add Product', 'Added a new product: 1222 (Category ID: 1)', '2024-11-28 06:14:35'),
(36, 1, 'Add Product', 'Added a new product: 123 (Category ID: 1)', '2024-11-28 06:14:54'),
(37, 1, 'Add Product', 'Added a new product: 123 (Category ID: 2)', '2024-11-28 06:15:11');

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
(6, 13, 1, '2024-12-05', '07:30:00', 'Pending', 'es', NULL, '2024-11-28 06:48:05', '2024-11-28 06:48:05');

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
(3, 1, 15, '12', 5, '2024-11-27 12:23:29');

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
(1, 15, 1, 'Product Name: 12 | Quantity: 1 | Price: 21.00', 21.00, '2024-11-27 20:00:16', 'paid in advance', NULL, '2024-11-27 12:00:16', '2024-11-27 12:00:16', 'Credit Card'),
(2, 15, 2, 'Product Name: 12 | Quantity: 1 | Price: 21.00', 21.00, '2024-11-27 20:28:47', 'paid in advance', NULL, '2024-11-27 12:28:47', '2024-11-27 12:28:47', 'Credit Card'),
(3, 15, 3, 'Product Name: 12 | Quantity: 1 | Price: 21.00', 21.00, '2024-11-27 20:30:56', 'paid in advance', NULL, '2024-11-27 12:30:56', '2024-11-27 12:30:56', 'Credit Card');

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
(5, 1, 0, 13, 1, 3, 63.00);

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
(1, 'pizzaz', 'active'),
(2, 'BACK', 'active');

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
(1, '12', 21.00, '21', '../Uploads/67470817bee27_istockphoto-1371910917-612x612.jpg', '2024-11-27 11:05:22', '2024-11-27 12:30:56', 10, 1, 'active'),
(2, '12', 21.00, '21', '../Uploads/6746fd4d21566_istockphoto-1371910917-612x612.jpg', '2024-11-27 11:06:53', '2024-11-27 11:06:53', 21, 1, 'active'),
(3, '21', 12.00, '21', '../Uploads/674707992fc31_community-activities-teensnarrow.jpg', '2024-11-27 11:50:49', '2024-11-27 11:50:49', 21, 1, 'active'),
(4, 'ghj', 12.00, '231', '../Uploads/674809fcddfda_png-clipart-2015-united-nations-climate-change-conference-presidential-climate-action-plan-global-warming-others-miscellaneous-globe.png', '2024-11-28 06:13:16', '2024-11-28 06:13:16', 5, 2, 'active'),
(5, 'kb', 4.00, '123', '../Uploads/67480a1434d02_credit_gorodenkoff.jpg', '2024-11-28 06:13:40', '2024-11-28 06:13:40', 12, 1, 'active'),
(6, 'kb', 43.00, '423', '../Uploads/67480a26d79c9_istockphoto-1371910917-612x612.jpg', '2024-11-28 06:13:58', '2024-11-28 06:13:58', 23, 1, 'active'),
(7, '12', 12.00, '12', '../Uploads/67480a36e8db1_dinewatchlogo (1).png', '2024-11-28 06:14:14', '2024-11-28 06:14:14', 123, 1, 'active'),
(8, '1222', 1221.00, '12', '../Uploads/67480a4b22d34_dinewatchlogo (1).png', '2024-11-28 06:14:35', '2024-11-28 06:14:35', 12, 1, 'active'),
(9, '123', 121.00, '123231', '../Uploads/67480a5e9f1ef_images (1).jpg', '2024-11-28 06:14:54', '2024-11-28 06:14:54', 213, 1, 'active'),
(10, '123', 123.00, '123', '../Uploads/67480a6f74e72_dinewatchlogo (1).png', '2024-11-28 06:15:11', '2024-11-28 06:15:11', 12, 2, 'active');

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
(1, 1, 15, 21.00, '2024-11-27 12:00:16', 'Credit Card'),
(2, 2, 15, 21.00, '2024-11-27 12:28:47', 'Credit Card'),
(3, 3, 15, 21.00, '2024-11-27 12:30:56', 'Credit Card');

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
(1, 1, 1, 15, 1, 1, 21.00),
(2, 2, 2, 15, 1, 1, 21.00),
(3, 3, 3, 15, 1, 1, 21.00);

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
(1, 15, 1, '2024-11-29', '09:30:00', 'Complete', '12', '2024-11-27 11:58:37', '2024-11-27 12:20:22'),
(2, 15, 2, '2024-11-28', '07:00:00', 'Paid', '21', '2024-11-27 12:28:23', '2024-11-27 12:28:47'),
(3, 15, 2, '2024-11-28', '08:30:00', 'Paid', '2131', '2024-11-27 12:30:33', '2024-11-27 12:30:56');

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
(10, 1, '4faafa66ddaecf4641e2709575b6017d4bbb8c67551893feab6ffe3506a97263', '2024-11-27 12:02:03', '2024-11-27 14:02:03'),
(11, 1, 'b47dc028a8942682bbb865728000dd45b31d027246f4715b713b7a3db70ebd12', '2024-11-28 06:12:21', '2024-11-28 08:12:18');

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
(1, 1, 12, 1, 'Indoor', 'active'),
(2, 1, 12, 1, 'Outdoor', 'active');

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
(1, 1, '../uploads/674708216244e_community-activities-teensnarrow.jpg', '2024-11-27 11:07:14', 'front view'),
(2, 1, '../uploads/6746fd62e033d_istockphoto-1371910917-612x612.jpg', '2024-11-27 11:07:14', 'right view'),
(3, 2, '../uploads/6747082e12a96_credit_gorodenkoff.jpg', '2024-11-27 11:53:18', 'front view');

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
(1, 'Mark Laurence', 'l', 'caringal', '', '09234567891', '22-35748@g.batstate-u.edu.ph', 'place', '1234q', 'mcaringal', '$2y$10$byiRbax9lfXhncotS6v62..jInN.89LxAnCl3rxafFBBtgcdx2EUu', 'Owner', '2024-11-24 23:31:19', '2024-11-27 12:02:03', 'active', 'online'),
(2, 'Mark Laurence', 'l', 'caringal', 'jr', '09234567891', 'Jack@gmail.com', 'place12', '1234', 'mcaringal2', '$2y$10$Fkj1MwbX/qRqSqzGBJ.z3.naGpjNfMje36iNpJdgxc75083rZTl9e', 'Admin', '2024-11-24 23:47:10', '2024-11-27 02:25:11', 'active', 'offline'),
(3, 'sample', 's', 'sample', '', '09011111111', 'benzoncarl010@gmail.com', 'sample', 'sampl', 'ssample', '$2y$10$G8ubvFvSPqVDtjpHRuTPVyMExQN2bvLyzuBfqkTSSnYZKG3hkwUVe', 'General User', '2024-11-25 03:26:48', '2024-11-25 06:45:20', 'active', 'online'),
(4, '12', NULL, '31', NULL, '09984262708', '123@gmail.com', NULL, NULL, '13', '$2y$10$HPzIsIliTWd2v8WrQmbnLuXnG3XQ5Gu1jnx7e0x6P/ABxK8Ls9mgK', 'Staff', '2024-11-26 08:24:30', '2024-11-26 23:18:41', 'active', 'online'),
(13, 'Mark Laurence', 'm', 'Caringal', '12', '09984262708', 'blackking0123456789@gmail.com', 'noe', '4322', 'mcaringal3', '$2y$10$ibdI/I5.JwCW3MnqfWVM.uLAdNRTjUMNq5I8SMEJvPUcXiQMBFmQa', 'General User', '2024-11-26 11:18:37', '2024-11-28 05:37:10', 'active', 'online'),
(15, 'Mark Laurence1', '2', 'Caringal', '32', '09984262708', 'flameking23456@gmail.com', 'noe', '4322', 'mcaringal4', '$2y$10$vUSMwm0WirqmszvO.V.1meIxOeIpwsOosA7txF8B/Jj9o7vDUekVy', 'General User', '2024-11-26 11:30:38', '2024-11-28 05:36:57', 'active', 'online'),
(16, 'Mark Laurence', 'L', 'Caringal', 'jr', '09984262708', 'mark_lnce_caringal@bec.edu.ph', 'noe', '4322', 'mcaringal5', '$2y$10$E8GcFgkDj.KwQN7/O.mksuky7T5LCYGGJpttY2ILqSrt1f7G.XKZ2', 'General User', '2024-11-26 12:06:27', '2024-11-26 13:00:17', 'active', 'offline'),
(17, 'jack12', NULL, 'sa', NULL, '09876543212', 'Jac12k@gmail.com', NULL, NULL, '123456789', '$2y$10$3lvOl8DTPVyMExQN2bvLyzuBfqkTSSnYZKG3hkwUVCaiVvJhsIja6', 'Staff', '2024-11-26 23:06:22', '2024-11-27 11:46:39', 'active', 'offline'),
(19, 'Mark Laurence', '', 'caringal', NULL, '09234567891', 'as@gmail.com', '', NULL, '32exc', '$2y$10$tobTUaMJjWyK.K23IYkVJOEQhdcXWOdGUTn6/T0aX39SbI6/HYjti', 'Admin', '2024-11-27 02:36:21', '2024-11-27 11:10:01', 'active', 'offline');

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
(15, 4),
(13, 17);

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
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `data_reservations`
--
ALTER TABLE `data_reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order_cancellations`
--
ALTER TABLE `order_cancellations`
  MODIFY `cancellation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `product_items`
--
ALTER TABLE `product_items`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `receipt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `receipt_items`
--
ALTER TABLE `receipt_items`
  MODIFY `receipt_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reservation_reschedule`
--
ALTER TABLE `reservation_reschedule`
  MODIFY `reschedule_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `table_images`
--
ALTER TABLE `table_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
