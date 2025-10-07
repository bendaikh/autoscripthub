-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 02, 2024 at 12:00 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fickrr`
--

-- --------------------------------------------------------

--
-- Table structure for table `custom_settings`
--

CREATE TABLE `custom_settings` (
  `cs_id` int(11) NOT NULL,
  `pr_head_bg_color` varchar(50) DEFAULT NULL,
  `pr_head_color` varchar(50) DEFAULT NULL,
  `pr_promo_title_one` varchar(191) DEFAULT NULL,
  `pr_promo_title_two` varchar(191) DEFAULT NULL,
  `pr_promo_date` date DEFAULT NULL,
  `pr_promo_button_link` text DEFAULT NULL,
  `pr_promo_heading` varchar(191) DEFAULT NULL,
  `promotion_popup` int(11) DEFAULT 0,
  `promotion_bg_image` varchar(50) DEFAULT NULL,
  `user_license` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `custom_settings`
--

INSERT INTO `custom_settings` (`cs_id`, `pr_head_bg_color`, `pr_head_color`, `pr_promo_title_one`, `pr_promo_title_two`, `pr_promo_date`, `pr_promo_button_link`, `pr_promo_heading`, `promotion_popup`, `promotion_bg_image`, `user_license`) VALUES
(1, '#232F3E', '#FFFFFF', '30% off', 'Big Deal Offer', '2024-03-31', 'https://demo.demoworks.in/fickrr/shop', 'Special Offer Promotion', 1, '1709298316jpg', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `custom_settings`
--
ALTER TABLE `custom_settings`
  ADD PRIMARY KEY (`cs_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `custom_settings`
--
ALTER TABLE `custom_settings`
  MODIFY `cs_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
