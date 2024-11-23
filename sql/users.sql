-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 21, 2024 at 04:50 AM
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
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` enum('online','offline') NOT NULL DEFAULT 'offline',
  `typing` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `middle_initial`, `last_name`, `suffix`, `contact_number`, `email`, `address`, `zip_code`, `username`, `password_hash`, `role`, `created_at`, `updated_at`, `deleted_at`, `status`, `typing`) VALUES
(1, 'Mark Laurence', 'l', 'caringal', 'jr', '09345678901', 'ha1@gmail.com', 'place', '1234', 'mcaringal', '$2y$10$U5xRKzJxyW0Lgia.SYeMZ.KK/vtWuOuwngG5M1qMsyw8jrxO.eeXy', 'General User', '2024-11-07 00:59:59', '2024-11-07 00:59:59', NULL, 'offline', 0),
(3, 'Mark Laurence', 'l', 'caringal', 'wq', '09234567891', 'ha22@gmail.com', 'place', '1234', 'mcaringal3', '$2y$10$CT/oMDS884MILfbF3/2BwOolbz1R7EIMKzZAnGPcrauQX9X5.R9RO', 'General User', '2024-11-07 01:00:59', '2024-11-07 01:00:59', NULL, 'offline', 0),
(6, 'Jack', 'l', 'caringal', 'wq', '09234567891', 'mk@gmail.com', 'place', '1234', 'jcaringal', '$2y$10$jVll8K2S6EsrZKPsWUo.POe1lv32lSQncJqkT9567wS78ZrAV3tde', 'Owner', '2024-11-07 01:04:04', '2024-11-08 08:36:02', NULL, 'online', 0),
(7, 'Mark Laurence', 'l', 'caringal1', 'wq', '09345678901', 'g@gmail.com', 'place', '1234', 'mcaringal1', '$2y$10$xXj.kj406T.ZM571znX1qeVKsAAEWVh94M0AOBaonnZvbla2nMQ8e', 'General User', '2024-11-07 01:11:42', '2024-11-08 10:59:27', NULL, 'online', 0),
(9, 'jack12', '1', 'jason', '', '09876543462', 'Jack@gmail.com', '1221', '', '123455', '$2y$10$Dk5YSAHL2G02USrQo4KateYe0q4wm9hvq8ywVD7fzWDg4dkJplC.O', 'Staff', '2024-11-09 11:46:29', '2024-11-20 01:11:33', NULL, 'online', 0),
(10, 'Mark Jack12', 'N', 'caringal', '', '09234567891', 'ha23@gmail.com', 'place12', '1231', 'Mark12', '$2y$10$ZT9OElJF0bEAfKn0Do5/gObuP0HPnetJJQs4v.n0y1XDd322G7uhe', 'Owner', '2024-11-14 02:40:10', '2024-11-19 03:35:51', NULL, 'offline', 0),
(11, 'Mark LAsdaas', 's', 'sad', 'sad', '09876543212', 'ha111@gmail.com', 'sad', '1234', 'msad', '$2y$10$vwToazZbZ20Cj5btCf54PuCGE9Cs0SSoGAxJVPgIUiuUX.mpBoZiW', 'General User', '2024-11-14 02:53:34', '2024-11-14 02:53:45', NULL, 'online', 0),
(12, 'jack12', NULL, 'sa', NULL, '09875643221', 'ha223@gmail.com', NULL, NULL, 'Mark', '$2y$10$5xl0RKLYBpYhxwop.ZwGheXANyEzVRCyhvQylAWUIm1R8MLrj7l.S', 'Admin', '2024-11-18 08:13:24', '2024-11-18 08:13:24', NULL, 'offline', 0),
(16, 'Mark', '', 'saddadasd', '', '09978671234', 'ha22123@gmail.com', 'place', '4534', 'msaddadasd', '$2y$10$ZfNHGvgboymbSUEDz5Vda.pyfPU9BxDlGx.UhepbG0xURBxLr4MR6', 'General User', '2024-11-21 03:11:06', '2024-11-21 03:12:06', NULL, 'offline', 0),
(17, 'Mark', 'a', 'aads', 'asd', '09978671234', 'ha2312@gmail.com', 'asd', '1234', 'maads', '$2y$10$3E0BvnpPfgB9IhwCOKDIOOVKY3Uq3H7N/HDN2lyGG/jVGEpsplNxe', 'General User', '2024-11-21 03:44:55', '2024-11-21 03:44:55', NULL, 'offline', 0),
(18, '1234', 'l', 'saddadasd', 'AS', '09978671234', '', 'place', '1222', '1saddadasd', '$2y$10$20s2UTZqShj4ks8iXcPxYeUOXg0Ylr5johE1g7H3XGQmPIyqUj/MG', 'General User', '2024-11-21 03:47:49', '2024-11-21 03:47:49', NULL, 'offline', 0);

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
