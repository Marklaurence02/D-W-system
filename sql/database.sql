-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 30, 2024 at 05:06 PM
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
(37, 1, 'Add Product', 'Added a new product: 123 (Category ID: 2)', '2024-11-28 06:15:11'),
(38, 1, 'Logout', 'mcaringal logged out', '2024-11-28 09:58:08'),
(39, 19, 'Login', 'User 32exc Login ', '2024-11-28 09:58:13'),
(40, 19, 'Add Product', 'Added a new product: we (Category ID: 2)', '2024-11-28 10:36:27'),
(41, 19, 'Logout', '32exc logged out', '2024-11-28 11:07:00'),
(42, 1, 'Login', 'User mcaringal Login ', '2024-11-28 11:07:20'),
(43, 1, 'Add Product', 'Added a new product: 211 (Category ID: 1)', '2024-11-28 11:24:31'),
(44, 1, 'Update Status', 'Order ID 1 status changed to \'Completed\' by user ID 1 (Username: mcaringal).', '2024-11-28 11:26:44'),
(45, 1, 'Update Status', 'Order ID 1 status changed to \'Completed\' by user ID 1 (Username: mcaringal).', '2024-11-28 11:26:44'),
(46, 1, 'Update Status', 'Order ID 1 status changed to \'Completed\' by user ID 1 (Username: mcaringal).', '2024-11-28 11:26:44'),
(47, 1, 'Update Status', 'Order ID 1 status changed to \'Completed\' by user ID 1 (Username: mcaringal).', '2024-11-28 11:26:44'),
(48, 1, 'Update Status', 'Order ID 1 status changed to \'Completed\' by user ID 1 (Username: mcaringal).', '2024-11-28 11:26:44'),
(49, 1, 'Update Status', 'Order ID 1 status changed to \'Completed\' by user ID 1 (Username: mcaringal).', '2024-11-28 11:26:44'),
(50, 1, 'Update Status', 'Order ID 1 status changed to \'Canceled\' by user ID 1 (Username: mcaringal).', '2024-11-28 11:26:52'),
(51, 1, 'Update Status', 'Order ID 1 status changed to \'Canceled\' by user ID 1 (Username: mcaringal).', '2024-11-28 11:26:52'),
(52, 1, 'Update Status', 'Order ID 1 status changed to \'Canceled\' by user ID 1 (Username: mcaringal).', '2024-11-28 11:26:52'),
(53, 1, 'Update Status', 'Order ID 1 status changed to \'Canceled\' by user ID 1 (Username: mcaringal).', '2024-11-28 11:26:52'),
(54, 1, 'Update Status', 'Order ID 1 status changed to \'Paid in advance\' by user ID 1 (Username: mcaringal).', '2024-11-28 11:27:54'),
(55, 1, 'Update Status', 'Order ID 1 status changed to \'Paid in advance\' by user ID 1 (Username: mcaringal).', '2024-11-28 11:27:54'),
(56, 1, 'Logout', 'mcaringal logged out', '2024-11-28 11:34:16'),
(57, 19, 'Login', 'User 32exc Login ', '2024-11-28 11:34:32'),
(58, 19, 'Add Product', 'Added a new product: 12 (Category ID: 1)', '2024-11-28 11:37:00'),
(59, 19, 'Logout', '32exc logged out', '2024-11-28 11:37:10'),
(60, 1, 'Login', 'User mcaringal Login ', '2024-11-28 11:37:45'),
(61, 1, 'Update Product', 'Updated product: 12 (Category ID: 1)', '2024-11-28 12:06:17'),
(62, 1, 'Update Status', 'Order ID 1 status changed to \'Paid in advance\' by user ID 1 (Username: mcaringal).', '2024-11-28 12:09:53'),
(63, 1, 'Update Status', 'Order ID 1 status changed to \'Paid in advance\' by user ID 1 (Username: mcaringal).', '2024-11-28 12:09:53'),
(64, 1, 'Update Status', 'Order ID 1 status changed to \'Completed\' by user ID 1 (Username: mcaringal).', '2024-11-28 12:10:03'),
(65, 1, 'Update Status', 'Order ID 1 status changed to \'Completed\' by user ID 1 (Username: mcaringal).', '2024-11-28 12:10:03'),
(66, 1, 'Update Status', 'Order ID 1 status changed to \'Completed\' by user ID 1 (Username: mcaringal).', '2024-11-28 12:10:03'),
(67, 1, 'Update Status', 'Order ID 2 status changed to \'In-Progress\' by user ID 1 (Username: mcaringal).', '2024-11-28 12:10:11'),
(68, 1, 'Update Status', 'Order ID 2 status changed to \'In-Progress\' by user ID 1 (Username: mcaringal).', '2024-11-28 12:10:11'),
(69, 1, 'Update Status', 'Order ID 2 status changed to \'In-Progress\' by user ID 1 (Username: mcaringal).', '2024-11-28 12:10:11'),
(70, 1, 'Update Status', 'Order ID 2 status changed to \'In-Progress\' by user ID 1 (Username: mcaringal).', '2024-11-28 12:10:11'),
(71, 1, 'Update Status', 'Order ID 3 status changed to \'Pending\' by user ID 1 (Username: mcaringal).', '2024-11-28 12:10:16'),
(72, 1, 'Update Status', 'Order ID 3 status changed to \'Pending\' by user ID 1 (Username: mcaringal).', '2024-11-28 12:10:16'),
(73, 1, 'Update Status', 'Order ID 3 status changed to \'Pending\' by user ID 1 (Username: mcaringal).', '2024-11-28 12:10:16'),
(74, 1, 'Update Status', 'Order ID 3 status changed to \'Pending\' by user ID 1 (Username: mcaringal).', '2024-11-28 12:10:16'),
(75, 1, 'Update Status', 'Order ID 3 status changed to \'Pending\' by user ID 1 (Username: mcaringal).', '2024-11-28 12:10:16'),
(76, 1, 'Update Status', 'Order ID 3 status changed to \'Canceled\' by user ID 1 (Username: mcaringal).', '2024-11-28 12:10:31'),
(77, 1, 'Update Status', 'Order ID 3 status changed to \'Canceled\' by user ID 1 (Username: mcaringal).', '2024-11-28 12:10:31'),
(78, 1, 'Update Status', 'Order ID 3 status changed to \'Canceled\' by user ID 1 (Username: mcaringal).', '2024-11-28 12:10:31'),
(79, 1, 'Update Status', 'Order ID 3 status changed to \'Canceled\' by user ID 1 (Username: mcaringal).', '2024-11-28 12:10:31'),
(80, 1, 'Update Status', 'Order ID 3 status changed to \'Canceled\' by user ID 1 (Username: mcaringal).', '2024-11-28 12:10:31'),
(81, 1, 'Update Status', 'Order ID 3 status changed to \'Canceled\' by user ID 1 (Username: mcaringal).', '2024-11-28 12:10:31'),
(82, 1, 'Update Category', 'Category (ID: 1) updated by user (ID: 1, Username: mcaringal) to \'12\'.', '2024-11-28 12:16:28'),
(83, 1, 'Add Product', 'Added a new product: 12 (Category ID: 1)', '2024-11-28 12:18:32'),
(84, 1, 'Update Product', 'Updated product: 12 (Category ID: 1)', '2024-11-28 12:19:10'),
(85, 1, 'Delete Product', 'Deleted product: 12', '2024-11-28 12:19:31'),
(86, 1, 'Add Table', 'Added table number 2 in Outdoor area with seating capacity of 2.', '2024-11-28 12:20:53'),
(87, 1, 'Update Table', 'Updated table ID: 1 with table number: 1', '2024-11-28 12:21:43'),
(88, 1, 'Delete Table', 'Deleted table number: 2', '2024-11-28 12:22:00'),
(89, 1, 'Add Admin', '2323 added a new admin/staff member: Mark Laurence caringal', '2024-11-28 12:23:15'),
(90, 1, 'Update Admin', 'Admin (ID: 1, Username: mcaringal2) updated user (ID: 2).', '2024-11-28 12:24:18'),
(91, 1, 'Delete Admin', 'Admin \'2323\' (ID: 21) was deleted by user \'mcaringal\' (ID: 1).', '2024-11-28 12:25:01'),
(92, 1, 'Logout', 'mcaringal logged out', '2024-11-28 12:26:44'),
(93, 1, 'Login', 'User mcaringal Login ', '2024-11-28 23:42:25'),
(94, 1, 'Update Status', 'Order ID 1 status changed to \'Paid in advance\' by user ID 1 (Username: mcaringal).', '2024-11-28 23:53:14'),
(95, 1, 'Update Status', 'Order ID 1 status changed to \'Paid in advance\' by user ID 1 (Username: mcaringal).', '2024-11-28 23:53:14'),
(96, 1, 'Logout', 'mcaringal logged out', '2024-11-28 23:59:27'),
(97, 17, 'Login', 'User 123456789 Login ', '2024-11-29 00:04:26'),
(98, 17, 'Logout', '123456789 logged out', '2024-11-29 00:53:22'),
(99, 1, 'Login', 'User mcaringal Login ', '2024-11-29 00:53:29'),
(100, 1, 'Logout', 'mcaringal logged out', '2024-11-29 01:01:58'),
(101, 17, 'Login', 'User 123456789 Login ', '2024-11-29 01:02:16'),
(102, 19, 'Login', 'User 32exc Login ', '2024-11-29 05:03:49'),
(103, 1, 'Login', 'User mcaringal Login ', '2024-11-29 05:03:58'),
(104, 19, 'Logout', '32exc logged out', '2024-11-29 05:06:08'),
(105, 1, 'Logout', 'mcaringal logged out', '2024-11-29 05:06:54'),
(106, 17, 'Login', 'User 123456789 Login ', '2024-11-29 05:07:09'),
(107, 17, 'Logout', '123456789 logged out', '2024-11-29 05:11:33'),
(108, 17, 'Login', 'User 123456789 Login ', '2024-11-29 05:14:17'),
(109, 17, 'Logout', '123456789 logged out', '2024-11-29 06:06:43'),
(110, 1, 'Login', 'User mcaringal Login ', '2024-11-30 00:50:03'),
(111, 15, 'Login', 'User mcaringal4 Login ', '2024-11-30 10:57:25'),
(112, 17, 'Login', 'User 123456789 Login ', '2024-11-30 15:05:49'),
(113, 17, 'Logout', '123456789 logged out', '2024-11-30 15:07:54'),
(114, 1, 'Login', 'User mcaringal Login ', '2024-11-30 15:08:01'),
(115, 1, 'Logout', 'mcaringal logged out', '2024-11-30 15:08:20'),
(116, 1, 'Login', 'User mcaringal Login ', '2024-11-30 15:09:11'),
(117, 1, 'Update Profile', 'User mcaringal updated their profile.', '2024-11-30 15:17:29'),
(118, 1, 'Logout', 'mcaringal logged out', '2024-11-30 15:18:35'),
(119, 1, 'Login', 'User mcaringal Login ', '2024-11-30 15:18:41'),
(120, 1, 'Update Profile', 'User mcaringal updated their profile.', '2024-11-30 15:19:22'),
(121, 1, 'Update Profile', 'User mcaringal updated their profile.', '2024-11-30 15:22:10'),
(122, 1, 'Update Profile', 'User mcaringal updated their profile.', '2024-11-30 15:23:02'),
(123, 1, 'Update Profile', 'User mcaringal updated their profile.', '2024-11-30 15:23:26'),
(124, 1, 'Update Profile', 'User mcaringal updated their profile.', '2024-11-30 15:24:55'),
(125, 1, 'Update Profile', 'User mcaringal updated their profile.', '2024-11-30 15:26:06'),
(126, 1, 'Update Profile', 'User mcaringal updated their profile.', '2024-11-30 15:27:17'),
(127, 1, 'Update Profile', 'User mcaringal updated their profile.', '2024-11-30 15:27:58'),
(128, 1, 'Update Profile', 'User mcaringal updated their profile.', '2024-11-30 15:28:09'),
(129, 1, 'Update Profile', 'User mcaringal updated their profile.', '2024-11-30 15:28:31'),
(130, 15, 'Update Profile', 'User mcaringal4 updated their profile.', '2024-11-30 15:29:03'),
(131, 1, 'Update Profile', 'User mcaringal updated their profile.', '2024-11-30 15:32:33'),
(132, 1, 'Update Profile', 'User mcaringal updated their profile.', '2024-11-30 15:35:15'),
(133, 15, 'Update Profile', 'User mcaringal4 updated their profile.', '2024-11-30 15:44:17'),
(134, 15, 'Update Profile', 'User mcaringal4 updated their profile.', '2024-11-30 15:45:41'),
(135, 15, 'Update Profile', 'User mcaringal4 updated their profile.', '2024-11-30 15:45:53'),
(136, 15, 'Update Profile', 'User mcaringal4 updated their profile.', '2024-11-30 15:59:52'),
(137, 15, 'Update Profile', 'User mcaringal4 updated their profile.', '2024-11-30 16:04:56');

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
(66, 17, 13, 'sa', '2024-11-29 01:32:53', 0),
(67, 13, 17, 'last', '2024-11-29 01:33:07', 0),
(68, 1, 2, 'YOW', '2024-11-29 05:04:10', 0),
(69, 1, 19, 'WHATS UP', '2024-11-29 05:04:23', 0),
(70, 19, 1, 'YES', '2024-11-29 05:04:30', 0),
(71, 19, 1, 'ISEE', '2024-11-29 05:04:35', 0),
(72, 1, 19, 'O YEAH', '2024-11-29 05:04:40', 0),
(73, 13, 17, 'puta', '2024-11-29 05:07:25', 0),
(74, 13, 17, 'fuck', '2024-11-29 05:07:32', 0),
(75, 13, 17, 'yow', '2024-11-29 05:11:18', 0),
(76, 13, 17, 'No Refund Policy?', '2024-11-29 05:15:55', 0),
(77, 13, 17, 'No Refund Policy?', '2024-11-29 05:17:02', 0),
(78, 13, 17, 'What time Dine&Watch Open', '2024-11-29 05:17:06', 0),
(79, 13, 17, 'No Refund Policy?', '2024-11-29 05:17:50', 0),
(80, 13, 17, '122', '2024-11-29 05:21:06', 0),
(81, 13, 17, '21', '2024-11-29 05:21:11', 0),
(82, 13, 17, 'da', '2024-11-29 05:22:49', 0),
(83, 13, 17, '23', '2024-11-29 05:23:49', 0),
(84, 13, 17, 'zxc', '2024-11-29 05:26:04', 0),
(85, 17, 13, 'awa', '2024-11-29 05:30:44', 0),
(86, 13, 17, 'ads', '2024-11-29 05:36:40', 0),
(87, 13, 17, 'hello', '2024-11-29 05:41:01', 0),
(88, 13, 17, 'What time Dine&Watch Open', '2024-11-29 05:56:08', 0),
(89, 13, 17, 'No Refund Policy?', '2024-11-29 05:57:05', 0),
(90, 13, 17, 'What time Dine&Watch Open', '2024-11-29 05:57:12', 0),
(91, 13, 17, 'FAQ', '2024-11-29 05:57:14', 0),
(92, 13, 17, 'No Refund Policy?', '2024-11-29 05:58:49', 0),
(93, 13, 17, 'yow', '2024-11-29 06:00:47', 0),
(94, 17, 13, 'yes', '2024-11-29 06:01:07', 0),
(95, 17, 13, 'what the problem customer', '2024-11-29 06:01:22', 0),
(96, 13, 17, 'u see iwn to add order', '2024-11-29 06:01:44', 0),
(97, 17, 13, 'for adding order is for the front disk of the store', '2024-11-29 06:02:44', 0),
(98, 17, 13, 'ok', '2024-11-29 06:05:24', 0),
(99, 13, 17, 'hello', '2024-11-29 06:06:57', 0),
(100, 15, 4, 'hello', '2024-11-30 15:06:45', 0);

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
(1, 1, 15, 'wqeq', 5, '2024-11-30 11:28:04'),
(2, 2, 15, '123', 5, '2024-11-30 11:28:09');

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
(1, 15, 1, 'Product Name: 12 | Quantity: 1 | Price: 21.00', 21.00, '2024-11-27 20:00:16', 'paid in advance', NULL, '2024-11-27 12:00:16', '2024-11-28 23:53:14', 'Credit Card'),
(2, 15, 2, 'Product Name: 12 | Quantity: 1 | Price: 21.00', 21.00, '2024-11-27 20:28:47', 'In-Progress', NULL, '2024-11-27 12:28:47', '2024-11-28 12:10:11', 'Credit Card'),
(3, 15, 3, 'Product Name: 12 | Quantity: 1 | Price: 21.00', 21.00, '2024-11-27 20:30:56', 'Canceled', NULL, '2024-11-27 12:30:56', '2024-11-28 12:10:31', 'Credit Card'),
(4, 15, 7, 'Product Name: 12 | Quantity: 6 | Price: 126.00', 126.00, '2024-11-30 19:11:17', 'paid in advance', NULL, '2024-11-30 11:11:17', '2024-11-30 11:11:17', 'Credit Card');

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
(1, '12', 'active'),
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
(1, '12', 21.00, '21', '../Uploads/67485fbe0f3e4_credit_gorodenkoff.jpg', '2024-11-27 11:05:22', '2024-11-30 11:11:17', 4, 1, 'active'),
(2, '12', 21.00, '21', '../Uploads/6746fd4d21566_istockphoto-1371910917-612x612.jpg', '2024-11-27 11:06:53', '2024-11-27 11:06:53', 21, 1, 'active'),
(3, '21', 12.00, '21', '../Uploads/674707992fc31_community-activities-teensnarrow.jpg', '2024-11-27 11:50:49', '2024-11-27 11:50:49', 21, 1, 'active'),
(4, 'ghj', 12.00, '231', '../Uploads/674809fcddfda_png-clipart-2015-united-nations-climate-change-conference-presidential-climate-action-plan-global-warming-others-miscellaneous-globe.png', '2024-11-28 06:13:16', '2024-11-28 06:13:16', 5, 2, 'active'),
(5, 'kb', 4.00, '123', '../Uploads/67480a1434d02_credit_gorodenkoff.jpg', '2024-11-28 06:13:40', '2024-11-28 06:13:40', 12, 1, 'active'),
(6, 'kb', 43.00, '423', '../Uploads/67480a26d79c9_istockphoto-1371910917-612x612.jpg', '2024-11-28 06:13:58', '2024-11-28 06:13:58', 23, 1, 'active'),
(7, '12', 12.00, '12', '../Uploads/67480a36e8db1_dinewatchlogo (1).png', '2024-11-28 06:14:14', '2024-11-28 06:14:14', 123, 1, 'active'),
(8, '1222', 1221.00, '12', '../Uploads/67480a4b22d34_dinewatchlogo (1).png', '2024-11-28 06:14:35', '2024-11-28 06:14:35', 12, 1, 'active'),
(9, '123', 121.00, '123231', '../Uploads/67480a5e9f1ef_images (1).jpg', '2024-11-28 06:14:54', '2024-11-28 06:14:54', 213, 1, 'active'),
(10, '123', 123.00, '123', '../Uploads/67480a6f74e72_dinewatchlogo (1).png', '2024-11-28 06:15:11', '2024-11-28 06:15:11', 12, 2, 'active'),
(11, 'we', 23.00, '23', '../Uploads/674847abad8bc_community-activities-teensnarrow.jpg', '2024-11-28 10:36:27', '2024-11-28 10:36:27', 3, 2, 'active'),
(12, '211', 12.00, '', '../Uploads/674852efbab11_dinewatchlogo (1).png', '2024-11-28 11:24:31', '2024-11-28 11:24:31', 21, 1, 'active'),
(14, '12', 12.00, '23', '../Uploads/67485f98485ca_community-activities-teensnarrow.jpg', '2024-11-28 12:18:32', '2024-11-28 12:18:32', 42, 1, 'active');

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
(3, 3, 15, 21.00, '2024-11-27 12:30:56', 'Credit Card'),
(4, 4, 15, 126.00, '2024-11-30 11:11:17', 'Credit Card');

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
(3, 3, 3, 15, 1, 1, 21.00),
(4, 4, 7, 15, 1, 6, 126.00);

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
(1, 15, 1, '2024-11-29', '09:30:00', 'Complete', '12', '2024-11-27 11:58:37', '2024-11-30 00:50:37'),
(2, 15, 2, '2024-11-28', '07:00:00', 'Complete', '21', '2024-11-27 12:28:23', '2024-11-28 12:11:32'),
(3, 15, 2, '2024-11-28', '08:30:00', 'Rescheduled', '2131', '2024-11-27 12:30:33', '2024-11-28 12:11:55'),
(7, 15, 1, '2024-12-01', '07:00:00', 'Paid', 'qwee', '2024-11-30 11:02:23', '2024-11-30 11:11:17');

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
(19, 17, '93dde463443b0cc3f1ada806ef622b3375d958442c67252e3130b84b48d59b47', '2024-11-29 01:02:16', '2024-11-29 03:02:16'),
(24, 1, 'aeff682fcc9747aedc81e0f7c99eac83742329dacae7f4ae9a1ef0def9c22273', '2024-11-30 00:50:03', '2024-11-30 02:50:03'),
(25, 15, '75fc4d93af1e55a228955a58b99a07dcb03a3ba733f730b3f37b17da836d6f17', '2024-11-30 10:57:25', '2024-11-30 12:57:25'),
(29, 1, '4be98c4b2c5aa7940cf6a8e1b96a0decec8fd7084ec8656cb0edadf98388e640', '2024-11-30 15:18:41', '2024-11-30 17:18:41');

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
(2, 1, '../uploads/67486057a6bbf_credit_gorodenkoff.jpg', '2024-11-27 11:07:14', 'right view'),
(3, 2, '../uploads/6747082e12a96_credit_gorodenkoff.jpg', '2024-11-27 11:53:18', 'front view'),
(5, 1, '../uploads/67486057a3f68_istockphoto-1371910917-612x612.jpg', '2024-11-28 12:21:43', 'back view');

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
(1, 'Mark Laurence', 'l', 'caringal', '', '09234567891', '22-35748@g.batstate-u.edu.ph', 'place', '1234q', 'mcaringal', '$2y$10$ZLej2dwDOaHilpnwVse4gephAqxwdatR0MfhrSLblkv/.brmKanzm', 'Owner', '2024-11-24 23:31:19', '2024-11-30 15:35:15', 'active', 'online'),
(2, 'Mark Laurence', 'l', 'caringal', 'jr', '09234567891', 'Jack@gmail.com', 'place12', '1234', 'mcaringal2', '$2y$10$Fkj1MwbX/qRqSqzGBJ.z3.naGpjNfMje36iNpJdgxc75083rZTl9e', 'Admin', '2024-11-24 23:47:10', '2024-11-28 12:24:18', 'active', 'offline'),
(3, 'sample', 's', 'sample', '', '09011111111', 'benzoncarl010@gmail.com', 'sample', 'sampl', 'ssample', '$2y$10$G8ubvFvSPqVDtjpHRuTPVyMExQN2bvLyzuBfqkTSSnYZKG3hkwUVe', 'General User', '2024-11-25 03:26:48', '2024-11-25 06:45:20', 'active', 'online'),
(4, '12', NULL, '31', NULL, '09984262708', '123@gmail.com', NULL, NULL, '13', '$2y$10$HPzIsIliTWd2v8WrQmbnLuXnG3XQ5Gu1jnx7e0x6P/ABxK8Ls9mgK', 'Staff', '2024-11-26 08:24:30', '2024-11-26 23:18:41', 'active', 'online'),
(13, 'Mark Laurence', 'm', 'Caringal', '12', '09984262708', 'blackking0123456789@gmail.com', 'noe', '4322', 'mcaringal3', '$2y$10$ibdI/I5.JwCW3MnqfWVM.uLAdNRTjUMNq5I8SMEJvPUcXiQMBFmQa', 'General User', '2024-11-26 11:18:37', '2024-11-28 05:37:10', 'active', 'online'),
(15, 'Mark Laurence1', '2', 'Caringal', '32', '09984262708', 'flameking23456@gmail.com', 'noe', '4322', 'mcaringal4', '$2y$10$wp0kYJXApgla52JBNqcI8.wotyhEvP6xm1zWu9iM2iexs8Z.1yWCi', 'General User', '2024-11-26 11:30:38', '2024-11-30 16:04:56', 'active', 'online'),
(16, 'Mark Laurence', 'L', 'Caringal', 'jr', '09984262708', 'mark_lnce_caringal@bec.edu.ph', 'noe', '4322', 'mcaringal5', '$2y$10$E8GcFgkDj.KwQN7/O.mksuky7T5LCYGGJpttY2ILqSrt1f7G.XKZ2', 'General User', '2024-11-26 12:06:27', '2024-11-26 13:00:17', 'active', 'offline'),
(17, 'jack12', NULL, 'sa', NULL, '09876543212', 'Jac12k@gmail.com', NULL, NULL, '123456789', '$2y$10$3lvOl8DTPVyMExQN2bvLyzuBfqkTSSnYZKG3hkwUVCaiVvJhsIja6', 'Staff', '2024-11-26 23:06:22', '2024-11-30 15:07:54', 'active', 'offline'),
(19, 'Mark Laurence', '', 'caringal', NULL, '09234567891', 'as@gmail.com', '', NULL, '32exc', '$2y$10$tobTUaMJjWyK.K23IYkVJOEQhdcXWOdGUTn6/T0aX39SbI6/HYjti', 'Admin', '2024-11-27 02:36:21', '2024-11-29 05:06:08', 'active', 'offline');

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
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `data_reservations`
--
ALTER TABLE `data_reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_cancellations`
--
ALTER TABLE `order_cancellations`
  MODIFY `cancellation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `product_items`
--
ALTER TABLE `product_items`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `receipt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `receipt_items`
--
ALTER TABLE `receipt_items`
  MODIFY `receipt_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `reservation_reschedule`
--
ALTER TABLE `reservation_reschedule`
  MODIFY `reschedule_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `table_images`
--
ALTER TABLE `table_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

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
