-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 06, 2023 at 08:34 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

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
-- Table structure for table `pwa_settings`
--

CREATE TABLE `pwa_settings` (
  `sno` int(11) NOT NULL,
  `app_name` varchar(100) DEFAULT NULL,
  `short_name` varchar(100) DEFAULT NULL,
  `background_color` varchar(50) DEFAULT NULL,
  `theme_color` varchar(50) DEFAULT NULL,
  `pwa_icon1` varchar(50) DEFAULT NULL,
  `pwa_icon2` varchar(50) DEFAULT NULL,
  `pwa_icon3` varchar(50) DEFAULT NULL,
  `pwa_icon4` varchar(50) DEFAULT NULL,
  `pwa_icon5` varchar(50) DEFAULT NULL,
  `pwa_icon6` varchar(50) DEFAULT NULL,
  `pwa_icon7` varchar(50) DEFAULT NULL,
  `pwa_icon8` varchar(50) DEFAULT NULL,
  `pwa_splash1` varchar(50) DEFAULT NULL,
  `pwa_splash2` varchar(50) DEFAULT NULL,
  `pwa_splash3` varchar(50) DEFAULT NULL,
  `pwa_splash4` varchar(50) DEFAULT NULL,
  `pwa_splash5` varchar(50) DEFAULT NULL,
  `pwa_splash6` varchar(50) DEFAULT NULL,
  `pwa_splash7` varchar(50) DEFAULT NULL,
  `pwa_splash8` varchar(50) DEFAULT NULL,
  `pwa_splash9` varchar(50) DEFAULT NULL,
  `pwa_splash10` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pwa_settings`
--

INSERT INTO `pwa_settings` (`sno`, `app_name`, `short_name`, `background_color`, `theme_color`, `pwa_icon1`, `pwa_icon2`, `pwa_icon3`, `pwa_icon4`, `pwa_icon5`, `pwa_icon6`, `pwa_icon7`, `pwa_icon8`, `pwa_splash1`, `pwa_splash2`, `pwa_splash3`, `pwa_splash4`, `pwa_splash5`, `pwa_splash6`, `pwa_splash7`, `pwa_splash8`, `pwa_splash9`, `pwa_splash10`) VALUES
(1, 'Fickrr Marketplace', 'Selling Items', '#213E66', '#FF5A5F', '16886251141.png', '16886251142.png', '16886251143.png', '16886251144.png', '16886251145.png', '16886251146.png', '16886251147.png', '16886251148.png', '16886251149.png', '168862511410.png', '168862511411.png', '168862511412.png', '168862511413.png', '168862511414.png', '168862511415.png', '168862511416.png', '168862511417.png', '168862511418.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pwa_settings`
--
ALTER TABLE `pwa_settings`
  ADD PRIMARY KEY (`sno`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pwa_settings`
--
ALTER TABLE `pwa_settings`
  MODIFY `sno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
