-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 08, 2025 at 03:10 AM
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
-- Database: `mysql_table`
--

-- --------------------------------------------------------

--
-- Table structure for table `clinics`
--

CREATE TABLE `clinics` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clinics`
--

INSERT INTO `clinics` (`id`, `name`, `address`) VALUES
(1, 'Radha Little Steps Pediatrics', 'clinic tata motors hatkesh Hatkesh Udhog Nagar, kankan Division, Maharashtra, india, 401107');
(2, 'Hatkesh Steps Pediatrics', 'clinic tata motors hatkesh Hatkesh Udhog Nagar, kankan Division, Maharashtra, india, 401107');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `doctor_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `image` varchar(100) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`doctor_id`, `name`, `image`, `description`) VALUES
(1, 'DR KAPIL SHUKLA', 'doctor_image.png', 'MBBS, MD pediatrician');

-- --------------------------------------------------------

--
-- Table structure for table `fees`
--

CREATE TABLE `fees` (
  `id` int(11) NOT NULL,
  `first_visit_fee` varchar(100) DEFAULT NULL,
  `follow_up_fee` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fees`
--

INSERT INTO `fees` (`id`, `first_visit_fee`, `follow_up_fee`) VALUES
(1, 'First Visit Fee $600 Pay at Clinic', 'Follow Up Fee $300 Pay at Clinic');

-- --------------------------------------------------------

--
-- Table structure for table `slots`
--

CREATE TABLE `slots` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `is_booked` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `slots`
--

INSERT INTO `slots` (`id`, `doctor_id`, `date`, `time`, `is_booked`) VALUES
(1, 1, '2025-07-16', '07:15:00', 1),
(2, 1, '2025-07-16', '07:00:00', 0),
(3, 1, '2025-07-16', '07:45:00', 1),
(4, 1, '2025-07-16', '08:15:00', 0),
(5, 1, '2025-07-16', '08:30:00', 0),
(6, 1, '2025-07-22', '06:45:00', 0),
(7, 1, '2025-07-22', '07:00:00', 1),
(8, 1, '2025-07-22', '07:15:00', 1),
(9, 1, '2025-07-22', '06:30:00', 0),
(10, 1, '2025-07-22', '08:00:00', 0),
(11, 1, '2025-07-30', '07:00:00', 0),
(12, 1, '2025-07-30', '07:45:00', 0),
(13, 1, '2025-07-30', '07:30:00', 0),
(14, 1, '2025-07-30', '06:15:00', 0),
(15, 1, '2025-07-30', '08:45:00', 0),
(16, 1, '2025-07-21', '07:30:00', 0),
(17, 1, '2025-07-21', '08:15:00', 0),
(18, 1, '2025-07-21', '07:00:00', 0),
(19, 1, '2025-07-21', '07:15:00', 0),
(20, 1, '2025-07-21', '08:45:00', 0),
(21, NULL, NULL, '06:00:00', 0),
(22, NULL, NULL, '06:15:00', 0),
(23, NULL, NULL, '07:15:00', 0),
(24, NULL, NULL, '08:15:00', 0),
(25, NULL, NULL, '06:30:00', 0),
(26, NULL, NULL, '07:45:00', 0),
(27, NULL, NULL, '06:45:00', 0),
(28, NULL, NULL, '07:00:00', 0),
(29, NULL, NULL, '06:00:00', 0),
(30, NULL, NULL, '08:45:00', 0),
(31, 1, NULL, '08:00:00', 0),
(32, 1, NULL, '08:30:00', 0),
(33, 1, NULL, '06:45:00', 0),
(34, 1, NULL, '07:15:00', 0),
(35, 1, NULL, '06:00:00', 0),
(36, NULL, NULL, '06:00:00', 0),
(37, NULL, NULL, '07:00:00', 0),
(38, NULL, NULL, '06:15:00', 0),
(39, NULL, NULL, '08:45:00', 0),
(40, NULL, NULL, '09:00:00', 0),
(41, 1, NULL, '06:45:00', 0),
(42, 1, NULL, '07:00:00', 0),
(43, 1, NULL, '08:15:00', 0),
(44, 1, NULL, '06:15:00', 0),
(45, 1, NULL, '07:30:00', 0),
(46, NULL, NULL, '06:15:00', 0),
(47, NULL, NULL, '08:30:00', 0),
(48, NULL, NULL, '07:15:00', 0),
(49, NULL, NULL, '06:45:00', 0),
(50, NULL, NULL, '09:00:00', 0),
(51, 1, NULL, '08:45:00', 0),
(52, 1, NULL, '07:45:00', 0),
(53, 1, NULL, '06:15:00', 0),
(54, 1, NULL, '06:00:00', 0),
(55, 1, NULL, '06:45:00', 0),
(56, NULL, NULL, '07:15:00', 0),
(57, NULL, NULL, '08:45:00', 0),
(58, NULL, NULL, '08:15:00', 0),
(59, NULL, NULL, '08:00:00', 0),
(60, NULL, NULL, '06:00:00', 0),
(61, 1, '2025-07-08', '07:30:00', 0),
(62, 1, '2025-07-08', '07:45:00', 0),
(63, 1, '2025-07-08', '08:15:00', 0),
(64, 1, '2025-07-08', '06:45:00', 0),
(65, 1, '2025-07-08', '07:00:00', 0),
(66, 1, '2025-07-09', '09:00:00', 0),
(67, 1, '2025-07-09', '08:15:00', 0),
(68, 1, '2025-07-09', '08:00:00', 0),
(69, 1, '2025-07-09', '07:30:00', 0),
(70, 1, '2025-07-09', '07:45:00', 0),
(71, 1, '2025-07-17', '08:15:00', 0),
(72, 1, '2025-07-17', '09:00:00', 0),
(73, 1, '2025-07-17', '07:30:00', 0),
(74, 1, '2025-07-17', '06:30:00', 0),
(75, 1, '2025-07-17', '06:45:00', 0),
(76, 1, '2025-07-24', '07:15:00', 0),
(77, 1, '2025-07-24', '06:00:00', 0),
(78, 1, '2025-07-24', '06:45:00', 0),
(79, 1, '2025-07-24', '08:15:00', 0),
(80, 1, '2025-07-24', '08:45:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp` varchar(6) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `otp`, `otp_expiry`, `created_at`) VALUES
(1, 'odukoyasheriff@gmail.com', '299436', '2025-07-08 02:26:45', '2025-07-08 00:21:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clinics`
--
ALTER TABLE `clinics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`doctor_id`);

--
-- Indexes for table `fees`
--
ALTER TABLE `fees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `slots`
--
ALTER TABLE `slots`
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
-- AUTO_INCREMENT for table `clinics`
--
ALTER TABLE `clinics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `doctor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fees`
--
ALTER TABLE `fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `slots`
--
ALTER TABLE `slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
