-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 10, 2022 at 02:31 PM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 7.4.25

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
-- Table structure for table `withdrawal_methods`
--

CREATE TABLE `withdrawal_methods` (
  `wm_id` int(11) NOT NULL,
  `withdrawal_name` varchar(500) NOT NULL,
  `withdrawal_key` varchar(100) NOT NULL,
  `withdrawal_order` int(50) NOT NULL,
  `withdrawal_status` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `withdrawal_methods`
--

INSERT INTO `withdrawal_methods` (`wm_id`, `withdrawal_name`, `withdrawal_key`, `withdrawal_order`, `withdrawal_status`) VALUES
(2, 'Stripe', 'stripe', 2, 1),
(3, 'Paypal', 'paypal', 1, 1),
(4, 'Paystack', 'paystack', 3, 1),
(5, 'Local Bank', 'localbank', 4, 1),
(6, 'PayFast', 'payfast', 5, 1),
(7, 'Paytm', 'paytm', 6, 1),
(8, 'UPI', 'UPI', 7, 1),
(9, 'Skrill', 'skrill', 8, 1),
(10, 'Manual Payment', 'crypto', 9, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `withdrawal_methods`
--
ALTER TABLE `withdrawal_methods`
  ADD PRIMARY KEY (`wm_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `withdrawal_methods`
--
ALTER TABLE `withdrawal_methods`
  MODIFY `wm_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
