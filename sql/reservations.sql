-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2024 at 10:20 AM
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
(1, 7, 1, '2024-11-19', '07:00:00', 'Paid', 'ert', NULL, '2024-11-19 12:41:59', '2024-11-19 12:42:42'),
(2, 7, 1, '2024-11-19', '08:15:00', 'Paid', 'ssdf', NULL, '2024-11-19 12:43:23', '2024-11-19 12:43:54'),
(3, 7, 1, '2024-11-19', '09:30:00', 'Paid', 'ERR', NULL, '2024-11-19 12:58:07', '2024-11-20 05:50:19'),
(4, 7, 1, '2024-11-20', '07:00:00', 'Paid', '', NULL, '2024-11-20 00:49:38', '2024-11-20 01:00:21'),
(8, 7, 1, '2024-11-20', '14:00:00', 'Rescheduled', 'asd', NULL, '2024-11-20 05:47:25', '2024-11-20 06:00:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
