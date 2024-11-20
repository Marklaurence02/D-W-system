-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2024 at 11:10 AM
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
(3, 1, 'Login', 'Mark Laurence l. caringal jr logged in', '2024-11-07 01:01:05'),
(4, 6, 'Login', 'jcaringal logged in', '2024-11-07 01:04:49'),
(5, 6, 'Add Product', 'Added a new product: Pizzasdf (Category ID: 1)', '2024-11-07 01:06:11'),
(6, 6, 'Add Product', 'Added a new product: pepsi (Category ID: 2)', '2024-11-07 01:06:45'),
(7, 6, 'Logout', 'jcaringal logged out', '2024-11-07 01:11:11'),
(8, 7, 'Login', 'Mark Laurence l. caringal1 wq logged in', '2024-11-07 01:11:50'),
(9, 7, 'Logout', 'mcaringal1 logged out', '2024-11-07 01:21:03'),
(10, 6, 'Login', 'jcaringal logged in', '2024-11-07 01:21:31'),
(11, 6, 'Logout', 'jcaringal logged out', '2024-11-07 01:22:29'),
(12, 6, 'Login', 'jcaringal logged in', '2024-11-07 01:26:29'),
(14, 6, 'Logout', 'jcaringal logged out', '2024-11-07 05:05:07'),
(15, 6, 'Login', 'jcaringal logged in', '2024-11-07 05:05:36'),
(16, 6, 'Logout', 'jcaringal logged out', '2024-11-07 05:15:02'),
(17, 6, 'Login', 'jcaringal logged in', '2024-11-07 05:15:12'),
(18, 7, 'Login', 'Mark Laurence l. caringal1 wq logged in', '2024-11-07 05:19:29'),
(19, 7, 'Login', 'Mark Laurence l. caringal1 wq logged in', '2024-11-07 06:05:17'),
(20, 7, 'Logout', 'mcaringal1 logged out', '2024-11-07 06:07:30'),
(21, 7, 'Logout', 'mcaringal1 logged out', '2024-11-07 06:07:46'),
(22, 6, 'Login', 'jcaringal logged in', '2024-11-07 06:08:24'),
(23, 7, 'Login', 'Mark Laurence l. caringal1 wq logged in', '2024-11-07 06:08:33'),
(24, 6, 'Login', 'jcaringal logged in', '2024-11-07 06:12:47'),
(25, 7, 'Login', 'Mark Laurence l. caringal1 wq logged in', '2024-11-07 06:12:57'),
(26, 6, 'Login', 'jcaringal logged in', '2024-11-07 06:15:28'),
(27, 7, 'Login', 'Mark Laurence l. caringal1 wq logged in', '2024-11-07 06:15:49'),
(28, 7, 'Logout', 'mcaringal1 logged out', '2024-11-07 06:16:50'),
(29, 6, 'Login', 'jcaringal logged in', '2024-11-07 06:17:05'),
(30, 7, 'Logout', 'mcaringal1 logged out', '2024-11-07 06:20:25'),
(31, 6, 'Logout', 'jcaringal logged out', '2024-11-07 06:25:49'),
(32, 6, 'Login', 'jcaringal logged in', '2024-11-07 06:28:38'),
(33, 6, 'Logout', 'jcaringal logged out', '2024-11-07 06:34:11'),
(34, 6, 'Login', 'jcaringal logged in', '2024-11-07 06:34:31'),
(35, 6, 'Logout', 'jcaringal logged out', '2024-11-07 06:43:35'),
(36, 6, 'Login', 'jcaringal logged in', '2024-11-07 06:43:39'),
(37, 6, 'Logout', 'jcaringal logged out', '2024-11-07 06:43:50'),
(38, 6, 'Login', 'jcaringal logged in', '2024-11-07 06:44:32'),
(39, 6, 'Logout', 'jcaringal logged out', '2024-11-07 06:45:49'),
(40, 6, 'Login', 'jcaringal logged in', '2024-11-07 06:45:53'),
(41, 6, 'Logout', 'jcaringal logged out', '2024-11-07 06:46:28'),
(42, 6, 'Login', 'jcaringal logged in', '2024-11-07 06:46:42'),
(43, 6, 'Logout', 'jcaringal logged out', '2024-11-07 06:50:17'),
(44, 6, 'Login', 'jcaringal logged in', '2024-11-07 06:50:22'),
(45, 6, 'Login', 'jcaringal logged in', '2024-11-07 06:54:39'),
(46, 6, 'Login', 'jcaringal logged in', '2024-11-07 07:37:38'),
(47, 6, 'Add Product', 'Added a new product: da (Category ID: 1)', '2024-11-07 09:28:21'),
(48, 6, 'Login', 'jcaringal logged in', '2024-11-08 08:36:02'),
(52, 6, 'Login', 'jcaringal logged in', '2024-11-08 08:51:27'),
(59, 6, 'Login', 'jcaringal logged in', '2024-11-08 09:54:13'),
(60, 6, 'Login', 'jcaringal logged in', '2024-11-08 11:19:54'),
(61, 6, 'Add Product', 'Added a new product: 123 (Category ID: 1)', '2024-11-08 11:27:58'),
(62, 6, 'Login', 'jcaringal logged in', '2024-11-08 11:44:44'),
(63, 6, 'Login', 'jcaringal logged in', '2024-11-09 11:45:37'),
(64, 9, 'Login', '123455 logged in', '2024-11-09 14:15:15'),
(65, 9, 'Login', '123455 logged in', '2024-11-09 14:21:02'),
(66, 9, 'Login', '123455 logged in', '2024-11-09 14:25:23'),
(67, 9, 'Login', '123455 logged in', '2024-11-09 14:26:22'),
(68, 9, 'Login', '123455 logged in', '2024-11-09 14:29:41'),
(69, 6, 'Login', 'jcaringal logged in', '2024-11-09 14:31:17'),
(70, 9, 'Login', '123455 logged in', '2024-11-09 14:31:26'),
(71, 9, 'Login', '123455 logged in', '2024-11-10 23:29:33'),
(72, 9, 'Login', '123455 logged in', '2024-11-10 23:34:25'),
(73, 9, 'Login', '123455 logged in', '2024-11-10 23:36:23'),
(74, 6, 'Login', 'jcaringal logged in', '2024-11-10 23:44:18'),
(75, 6, 'Login', 'jcaringal logged in', '2024-11-11 00:30:14'),
(76, 6, 'Login', 'jcaringal logged in', '2024-11-11 00:30:36'),
(77, 6, 'Login', 'jcaringal logged in', '2024-11-11 00:44:59'),
(78, 6, 'Login', 'jcaringal logged in', '2024-11-11 04:56:20'),
(79, 6, 'Login', 'jcaringal logged in', '2024-11-11 07:58:39'),
(80, 9, 'Login', '123455 logged in', '2024-11-13 01:02:39'),
(81, 9, 'Login', '123455 logged in', '2024-11-13 01:05:51'),
(82, 9, 'Login', '123455 logged in', '2024-11-13 01:10:37'),
(83, 9, 'Login', '123455 logged in', '2024-11-13 01:11:22'),
(84, 9, 'Login', '123455 logged in', '2024-11-13 01:13:27'),
(85, 6, 'Login', 'jcaringal logged in', '2024-11-13 01:14:24'),
(86, 9, 'Login', '123455 logged in', '2024-11-13 01:20:07'),
(87, 9, 'Login', '123455 logged in', '2024-11-13 01:22:30'),
(88, 9, 'Login', '123455 logged in', '2024-11-13 01:25:36'),
(89, 9, 'Login', '123455 logged in', '2024-11-13 01:33:52'),
(90, 9, 'Login', '123455 logged in', '2024-11-13 01:35:53'),
(91, 9, 'Login', '123455 logged in', '2024-11-13 01:36:04'),
(92, 9, 'Login', '123455 logged in', '2024-11-13 01:38:59'),
(93, 9, 'Login', '123455 logged in', '2024-11-13 01:50:27'),
(94, 9, 'Login', '123455 logged in', '2024-11-13 01:51:00'),
(95, 9, 'Login', '123455 logged in', '2024-11-13 01:52:51'),
(96, 6, 'Login', 'jcaringal logged in', '2024-11-13 01:53:58'),
(97, 9, 'Login', '123455 logged in', '2024-11-13 02:06:46'),
(98, 9, 'Login', '123455 logged in', '2024-11-13 23:47:23'),
(99, 9, 'Login', '123455 logged in', '2024-11-13 23:52:04'),
(100, 9, 'Login', '123455 logged in', '2024-11-13 23:52:29'),
(101, 9, 'Login', '123455 logged in', '2024-11-13 23:53:05'),
(102, 6, 'Login', 'jcaringal logged in', '2024-11-14 00:10:43'),
(106, 6, 'Login', 'jcaringal logged in', '2024-11-14 00:30:10'),
(107, 6, 'Login', 'jcaringal logged in', '2024-11-14 00:40:36'),
(113, 6, 'Login', 'jcaringal logged in', '2024-11-14 02:37:59'),
(114, 10, 'Login', 'mcaringal4 logged in', '2024-11-14 02:41:37'),
(115, 9, 'Login', '123455 logged in', '2024-11-14 03:12:28'),
(116, 10, 'Login', 'mcaringal4 logged in', '2024-11-14 03:31:37'),
(117, 9, 'Login', '123455 logged in', '2024-11-14 04:08:30'),
(118, 10, 'Login', 'mcaringal4 logged in', '2024-11-14 13:10:30'),
(119, 10, 'Login', 'mcaringal4 logged in', '2024-11-14 14:21:52'),
(120, 10, 'Login', 'mcaringal4 logged in', '2024-11-15 09:07:41'),
(121, 10, 'Login', 'mcaringal4 logged in', '2024-11-15 10:11:03'),
(122, 10, 'Login', 'mcaringal4 logged in', '2024-11-15 11:16:32'),
(123, 10, 'Login', 'mcaringal4 logged in', '2024-11-15 23:35:54'),
(124, 10, 'Login', 'mcaringal4 logged in', '2024-11-16 23:37:30'),
(125, 10, 'Login', 'mcaringal4 logged in', '2024-11-17 03:05:19'),
(126, 10, 'Login', 'mcaringal4 logged in', '2024-11-17 03:07:43'),
(127, 10, 'Logout', 'mcaringal4 logged out', '2024-11-17 03:08:02'),
(128, 10, 'Login', 'mcaringal4 logged in', '2024-11-17 03:08:13'),
(129, 10, 'Logout', 'mcaringal4 logged out', '2024-11-17 03:08:35'),
(130, 10, 'Login', 'mcaringal4 logged in', '2024-11-17 03:08:46'),
(131, 9, 'Login', '123455 logged in', '2024-11-17 03:09:20'),
(132, 9, 'Logout', '123455 logged out', '2024-11-17 03:09:44'),
(133, 10, 'Login', 'mcaringal4 logged in', '2024-11-17 03:12:00'),
(134, 10, 'Logout', 'mcaringal4 logged out', '2024-11-17 03:24:45'),
(135, NULL, 'Delete Product', 'Deleted product: da', '2024-11-17 10:33:33'),
(136, NULL, 'Update Product', 'Updated product: Pizzasdf (Category ID: 1)', '2024-11-17 13:31:29'),
(137, NULL, 'Update Product', 'Updated product: Pizzasdf (Category ID: 1)', '2024-11-17 13:37:28'),
(138, NULL, 'Update Product', 'Updated product: Pizzasdf (Category ID: 1)', '2024-11-17 14:29:37'),
(139, NULL, 'Update Product', 'Updated product: Pizzasdf (Category ID: 1)', '2024-11-17 14:29:45'),
(140, NULL, 'Update Product', 'Updated product: Pizzasdf (Category ID: 1)', '2024-11-17 15:08:36'),
(141, NULL, 'Update Product', 'Updated product: Pizzasdf (Category ID: 1)', '2024-11-17 15:47:30'),
(142, NULL, 'Update Product', 'Updated product: Pizzasdf (Category ID: 1)', '2024-11-18 01:11:46'),
(143, NULL, 'Update Product', 'Updated product: Pizzasdf2 (Category ID: 1)', '2024-11-18 01:13:12'),
(144, NULL, 'Update Product', 'Updated product: Pizzasdf2 (Category ID: 1)', '2024-11-18 01:29:31'),
(145, NULL, 'Update Product', 'Updated product: Pizzasdf2 (Category ID: 1)', '2024-11-18 01:29:46'),
(146, NULL, 'Update Product', 'Updated product: Pizzasdf2 (Category ID: 1)', '2024-11-18 01:31:11'),
(147, 10, 'Logout', 'mcaringal4 logged out', '2024-11-18 02:56:38'),
(148, 10, 'Logout', 'mcaringal4 logged out', '2024-11-18 09:00:44'),
(149, NULL, 'Update Product', 'Updated product: Pizzasdf2 (Category ID: 1)', '2024-11-18 14:39:04'),
(150, NULL, 'Update Product', 'Updated product: Pizzasdf212 (Category ID: 1)', '2024-11-18 14:39:13'),
(151, NULL, 'Update Product', 'Updated product: Pizzasdf2122112221 (Category ID: 1)', '2024-11-18 14:39:23'),
(152, NULL, 'Update Product', 'Updated product: Pizzasdf2122112221 (Category ID: 1)', '2024-11-18 14:39:40'),
(153, NULL, 'Update Product', 'Updated product: Pizzasdf2122112221 (Category ID: 1)', '2024-11-18 14:41:35'),
(154, NULL, 'Update Product', 'Updated product: Pizzasdf2122112221 (Category ID: 1)', '2024-11-18 14:43:18'),
(155, NULL, 'Update Product', 'Updated product: pepsi (Category ID: 2)', '2024-11-18 14:44:33'),
(156, NULL, 'Update Product', 'Updated product: Pizzasdf21 (Category ID: 1)', '2024-11-18 14:46:04'),
(157, NULL, 'Update Product', 'Updated product: Pizzasdf21 (Category ID: 1)', '2024-11-18 14:49:47'),
(158, NULL, '', 'Deleted table number: 4', '2024-11-18 15:10:45'),
(159, NULL, '', 'Updated table ID: 1 with table number: 0', '2024-11-18 15:13:10'),
(160, NULL, '', 'Updated table ID: 1 with table number: 0', '2024-11-18 15:13:29'),
(161, NULL, '', 'Updated table ID: 3 with table number: 0', '2024-11-18 15:45:40'),
(162, NULL, '', 'Updated table ID: 4 with table number: 0', '2024-11-18 15:47:32'),
(163, NULL, '', 'Updated table ID: 1 with table number: 0', '2024-11-19 00:11:21'),
(164, NULL, '', 'Updated table ID: 1 with table number: 0', '2024-11-19 00:11:36'),
(165, NULL, '', 'Updated table ID: 3 with table number: 0', '2024-11-19 00:11:53'),
(166, NULL, '', 'Updated table ID: 1 with table number: 0', '2024-11-19 00:12:31'),
(167, NULL, '', 'Updated table ID: 6 with table number: 0', '2024-11-19 00:15:25'),
(168, NULL, '', 'Updated table ID: 6 with table number: 0', '2024-11-19 00:15:36'),
(169, NULL, '', 'Updated table ID: 6 with table number: 0', '2024-11-19 00:15:45'),
(170, NULL, '', 'Updated table ID: 2 with table number: 0', '2024-11-19 00:15:54'),
(171, NULL, '', 'Updated table ID: 1 with table number: 0', '2024-11-19 00:27:23'),
(172, NULL, '', 'Updated table ID: 1 with table number: 0', '2024-11-19 00:27:31'),
(173, NULL, '', 'Updated table ID: 1 with table number: 0', '2024-11-19 00:34:09'),
(174, NULL, '', 'Updated table ID: 1 with table number: 0', '2024-11-19 00:35:05'),
(175, NULL, '', 'Updated table ID: 1 with table number: 0', '2024-11-19 00:35:13'),
(176, NULL, '', 'Updated table ID: 1 with table number: 0', '2024-11-19 00:35:38'),
(177, NULL, '', 'Updated table ID: 1 with table number: 0', '2024-11-19 00:35:57'),
(178, NULL, '', 'Updated table ID: 1 with table number: 0', '2024-11-19 00:36:18'),
(179, NULL, '', 'Updated table ID: 2 with table number: 0', '2024-11-19 00:38:03'),
(180, NULL, '', 'Updated table ID: 1 with table number: 0', '2024-11-19 00:38:10'),
(181, NULL, '', 'Updated table ID: 1 with table number: 0', '2024-11-19 00:38:34'),
(182, NULL, '', 'Updated table ID: 1 with table number: 0', '2024-11-19 00:38:39'),
(183, NULL, '', 'Updated table ID: 1 with table number: 0', '2024-11-19 00:43:33'),
(184, NULL, '', 'Updated table ID: 1 with table number: 0', '2024-11-19 00:43:41'),
(185, NULL, '', 'Updated table ID: 1 with table number: 0', '2024-11-19 00:44:28'),
(186, NULL, '', 'Updated table ID: 1 with table number: 0', '2024-11-19 00:44:39'),
(187, NULL, '', 'Updated table ID: 1 with table number: 0', '2024-11-19 00:49:54'),
(188, NULL, '', 'Updated table ID: 1 with table number: 0', '2024-11-19 00:51:21'),
(189, NULL, '', 'Updated table ID: 1 with table number: 12', '2024-11-19 00:59:44'),
(190, NULL, '', 'Updated table ID: 1 with table number: 12', '2024-11-19 01:00:15'),
(191, NULL, '', 'Updated table ID: 1 with table number: 12', '2024-11-19 01:00:29'),
(192, 10, 'Logout', 'mcaringal4 logged out', '2024-11-19 05:05:00'),
(193, 10, 'Logout', 'Mark12 logged out', '2024-11-19 14:52:54'),
(194, 10, 'Logout', 'Mark12 logged out', '2024-11-20 04:15:09'),
(195, 10, 'Logout', 'Mark12 logged out', '2024-11-20 05:50:47');

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=196;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`action_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
