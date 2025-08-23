-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 23, 2025 at 07:33 PM
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
-- Database: `qr_oms`
--

-- --------------------------------------------------------

--
-- Table structure for table `item_details`
--

CREATE TABLE `item_details` (
  `Item_Id` int(11) NOT NULL,
  `Item_Name` varchar(100) NOT NULL,
  `Item_Price` decimal(10,2) NOT NULL,
  `Item_Category` varchar(50) NOT NULL,
  `Item_Image` varchar(255) DEFAULT NULL,
  `Item_Status` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_details`
--

INSERT INTO `item_details` (`Item_Id`, `Item_Name`, `Item_Price`, `Item_Category`, `Item_Image`, `Item_Status`) VALUES
(4, 'kottu full', 255.00, 'special', 'uploads/68a8c57ed45fd_beefK.jpg', 'Active'),
(5, 'cheese', 500.00, 'special', 'uploads/68a8c5a22ee4a_cheeseK.jpg', 'Active'),
(6, 'fired rice', 450.00, 'special', 'uploads/68a8c5c8cf3ba_chickF2.jpg', 'Active'),
(7, 'chicken rice', 200.00, 'buckets', 'uploads/68a8c5edbd5fe_chickB2.jpg', 'Active'),
(8, 'kottu full', 255.00, 'buckets', 'uploads/68a8c60a42413_beefK.jpg', 'Inactive');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `Order_Id` int(11) NOT NULL,
  `Order_Items` text NOT NULL,
  `Total_Price` decimal(10,2) NOT NULL,
  `Table_Number` int(11) NOT NULL,
  `Order_Time` timestamp NOT NULL DEFAULT current_timestamp(),
  `Status` enum('Pending','Preparing','Completed','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`Order_Id`, `Order_Items`, `Total_Price`, `Table_Number`, `Order_Time`, `Status`) VALUES
(1, '[{\"id\":\"special-2\",\"name\":\"Myone\",\"price\":777,\"img\":\"http:\\/\\/192.168.43.111\\/qr_oms\\/uploads\\/68a8771e00f7e_istockphoto-2124610776-612x612.jpg\",\"quantity\":1}]', 854.70, 0, '2025-08-22 10:27:51', 'Pending'),
(2, '[{\"id\":\"special-2\",\"name\":\"Myone\",\"price\":777,\"img\":\"http:\\/\\/192.168.43.111\\/qr_oms\\/uploads\\/68a8771e00f7e_istockphoto-2124610776-612x612.jpg\",\"quantity\":1}]', 854.70, 0, '2025-08-22 10:27:58', 'Pending'),
(3, '[{\"id\":\"special-2\",\"name\":\"Myone\",\"price\":777,\"img\":\"http:\\/\\/192.168.43.111\\/qr_oms\\/uploads\\/68a8771e00f7e_istockphoto-2124610776-612x612.jpg\",\"quantity\":1}]', 854.70, 0, '2025-08-22 10:41:58', 'Pending'),
(4, '[{\"id\":\"special-2\",\"name\":\"Myone\",\"price\":777,\"img\":\"http:\\/\\/localhost\\/qr_oms\\/uploads\\/68a8771e00f7e_istockphoto-2124610776-612x612.jpg\",\"quantity\":1},{\"id\":\"special-1\",\"name\":\"iyfiyfiyfiiy\",\"price\":888,\"img\":\"http:\\/\\/localhost\\/qr_oms\\/uploads\\/68a876feddb37_Black%20Modern%20A%20letter%20Logo.png\",\"quantity\":1}]', 1831.50, 0, '2025-08-22 12:37:55', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

CREATE TABLE `tables` (
  `Table_Id` varchar(10) NOT NULL,
  `Table_Name` varchar(100) NOT NULL,
  `Status` enum('Available','Occupied','Reserved','Cleaning') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`Table_Id`, `Table_Name`, `Status`) VALUES
('T01', '', 'Available'),
('T02', '', 'Available'),
('T03', '', 'Available'),
('T04', '', 'Available'),
('T05', '', 'Available');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `item_details`
--
ALTER TABLE `item_details`
  ADD PRIMARY KEY (`Item_Id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`Order_Id`);

--
-- Indexes for table `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`Table_Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `item_details`
--
ALTER TABLE `item_details`
  MODIFY `Item_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `Order_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
