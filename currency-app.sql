-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 22, 2024 at 08:09 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `currency-app`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `message`, `created_at`) VALUES
(1, 'patrick', 'admin@martin.com', 'eldknksjbckzd', '2024-11-22 04:32:12'),
(2, 'mpamo', 'mpamo@gmail.com', 'ndagukunda chr', '2024-11-22 04:51:27');

-- --------------------------------------------------------

--
-- Table structure for table `exchange_rates`
--

CREATE TABLE `exchange_rates` (
  `id` int(11) NOT NULL,
  `from_currency` varchar(10) NOT NULL,
  `to_currency` varchar(10) NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exchange_rates`
--

INSERT INTO `exchange_rates` (`id`, `from_currency`, `to_currency`, `rate`, `updated_at`) VALUES
(1, 'INR', 'FRW', 13.20, '2024-11-21 09:50:47'),
(2, 'FRW', 'INR', 0.08, '2024-11-21 09:50:47');

-- --------------------------------------------------------

--
-- Table structure for table `exchange_requests`
--

CREATE TABLE `exchange_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `from_currency` varchar(10) NOT NULL,
  `to_currency` varchar(10) NOT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_screenshot` varchar(255) DEFAULT NULL,
  `admin_screenshot` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `finalized` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exchange_requests`
--

INSERT INTO `exchange_requests` (`id`, `user_id`, `amount`, `from_currency`, `to_currency`, `payment_method`, `payment_screenshot`, `admin_screenshot`, `status`, `created_at`, `finalized`) VALUES
(5, 2, 5000.00, 'FRW', 'INR', 'PayPal', '1732198064-faceook.jpg', 'screenshot_673f75ee9cdf0.PNG', 'Approved', '2024-11-21 14:07:44', 1),
(6, 2, 6000.00, 'INR', 'INR', 'Cash', '1732211607-ys log.PNG', 'screenshot_673f75e6bb93c.PNG', 'Approved', '2024-11-21 17:53:27', 1),
(7, 8, 4000.00, 'FRW', 'INR', 'Cash', '1732212619-Capture.PNG', 'screenshot_673f77ae35279.jpg', 'Approved', '2024-11-21 18:10:19', 1),
(8, 8, 5668.00, 'FRW', 'INR', 'PayPal', '1732213564-Capture-removebg-preview.png', NULL, 'Rejected', '2024-11-21 18:26:04', 1),
(9, 8, 1000.00, 'FRW', 'INR', 'Bank Transfer', '1732250527-OIP.jpg', NULL, 'Approved', '2024-11-22 04:42:07', 1),
(10, 8, 1000.00, 'FRW', 'INR', 'Bank Transfer', '1732252856-OIP.jpg', NULL, 'Approved', '2024-11-22 05:20:56', 1),
(11, 8, 10.00, 'INR', 'FRW', 'Bank Transfer', '1732256568-OIP.jpg', NULL, 'Pending', '2024-11-22 06:22:48', 0);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `sender_type` enum('user','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `user_id`, `admin_id`, `message`, `sender_type`, `created_at`) VALUES
(1, 5, NULL, 'hello', 'user', '2024-11-22 06:38:05'),
(2, 5, NULL, 'hello', 'user', '2024-11-22 06:38:18'),
(3, 8, NULL, 'we', 'user', '2024-11-22 06:39:47');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `username` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `created_at`, `username`, `role`) VALUES
(2, 'websdeveloper2023@gmail.com', '$2y$10$NYh8JWz4SFML/5zKJzbmH.nreqc2kyibl7kBwd4Xdd6lLOstM4S02', '2024-11-21 10:20:15', 'patrick', 'user'),
(5, 'admin@martin.com', '$2y$10$0hbWzePHDagXDuuKZCyuDuxUpWR97cCMHT6Vr3GBo06xuJsUkJM6S', '2024-11-21 10:52:37', 'MARTIN EXCHANGER', 'admin'),
(8, 'king@gmail.com', '$2y$10$TYTJCuNEqtPdy6WFbUB71exomM3l31bZ6Yv6fs7BArrgf69mODXtK', '2024-11-21 18:09:40', 'king', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exchange_rates`
--
ALTER TABLE `exchange_rates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `from_currency` (`from_currency`,`to_currency`);

--
-- Indexes for table `exchange_requests`
--
ALTER TABLE `exchange_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `exchange_rates`
--
ALTER TABLE `exchange_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `exchange_requests`
--
ALTER TABLE `exchange_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `exchange_requests`
--
ALTER TABLE `exchange_requests`
  ADD CONSTRAINT `exchange_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
