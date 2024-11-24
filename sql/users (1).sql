-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 24, 2024 at 07:35 PM
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
  `account_status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `status` enum('online','offline') NOT NULL DEFAULT 'offline'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `middle_initial`, `last_name`, `suffix`, `contact_number`, `email`, `address`, `zip_code`, `username`, `password_hash`, `role`, `created_at`, `updated_at`, `account_status`, `status`) VALUES
(1, 'Mark Laurence', 'm', 'place', '12', '09984262708', '22-35748@g.batstate-u.edu.ph', 'no12', '4322', 'mplace', '$2y$10$bDYvMhcvIwhJtqIJmUMOH.lQynkhq4lRM5cWedzZkzDGfxC3cmGeG', 'Owner', '2024-11-24 17:34:13', '2024-11-24 17:54:03', 'active', 'online'),
(2, 'Mark Laurence', '', 'Caringal', NULL, '09984262708', 'Jack@gmail.com', '', NULL, 'admin1231', '$2y$10$tAKS6w8lNipmVL1UIb.80OZGxXvGZaVHAqz261JEneL14gmW26CCy', 'Admin', '2024-11-24 17:51:05', '2024-11-24 17:51:22', 'active', 'offline'),
(3, 'Mark Laurence2', NULL, 'Caringal', NULL, '09984262708', 'jen1@gmail.com', NULL, NULL, 'admin2', '$2y$10$ECRrZUIabx2mi.WLvsKtkuLK/CrNXuMBr12UK6PPuoa56ioi3pbqG', 'Staff', '2024-11-24 17:51:50', '2024-11-24 18:33:35', 'active', 'online'),
(4, 'Mark Laurence', 'w', 'Caringal', 'qqwq', '09984262708', 'becelatoddy@hotmail.com', 'noe', '4322', 'mcaringal', '$2y$10$DrkT5mw86Tt17ZAhiSAec.QmhuHwK8DwMPredP/CO9KRo56E8GfoS', 'General User', '2024-11-24 17:55:55', '2024-11-24 17:56:05', 'active', 'online'),
(5, 'jack12', '', 'As', NULL, '09978671234', '21@gmail.com', '', NULL, '12', '$2y$10$0OZS2E962A6kWIjhr8vYmOiP1MK86tGm/DHcMyRZhA7/buC2U6PW6', 'Staff', '2024-11-24 18:07:37', '2024-11-24 18:32:59', 'active', 'online');

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
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
