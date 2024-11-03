-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 03, 2024 at 03:32 AM
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
-- Database: `hoa_records`
--

-- --------------------------------------------------------

--
-- Table structure for table `hoas`
--

CREATE TABLE `hoas` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hoas`
--

INSERT INTO `hoas` (`id`, `name`) VALUES
(1, 'Hamorawon'),
(2, 'Caramel'),
(3, 'Aguit-Itan'),
(19002, 'Test');

-- --------------------------------------------------------

--
-- Table structure for table `login_history`
--

CREATE TABLE `login_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `id` int(11) NOT NULL,
  `lot_no` varchar(255) NOT NULL,
  `hoa` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `member_id` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `membership_date` date DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `role` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`id`, `lot_no`, `hoa`, `name`, `member_id`, `address`, `phone_number`, `email`, `membership_date`, `status`, `role`, `notes`) VALUES
(1, 'TS-18', 'Home Shelter Association', 'Raven Apilado', '21-01177', 'NONe', '09123456789', 'Raven@gmail.xom', NULL, 'active', NULL, NULL),
(2, 'TS-17', 'Home Shelter Association', 'Julius Progella', '21-9799', 'None', '098765431234', 'Julius@gmail.com', NULL, 'active', NULL, NULL),
(3, 'TS-12', 'Home Shelter Association', 'test', '1241235', '135', '15137257248', 'test@gmail.com', '2024-11-03', 'active', 'none', ''),
(4, 'TS-16', 'Home Shelter Association', 'ravy', '345678', '4567', '345678', 'ravy@gmail.com', '2024-11-03', 'active', 'N/A', '');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `hoa_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `member_id` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `membership_date` date DEFAULT NULL,
  `status` enum('Active','Inactive','Suspended') DEFAULT 'Active',
  `role` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `hoa_id`, `name`, `member_id`, `address`, `phone_number`, `email`, `membership_date`, `status`, `role`, `notes`) VALUES
(1, 2, 'raven apilado', '1920392', 'Calbayog', '0934562325', 'raven@gmail.com', '2012-12-12', 'Active', 'Executive', 'test2'),
(2, 2, 'Julius ', '1920323', 'Brgy, Hamorawon', '098812788', 'julius@gmail.com', '0000-00-00', 'Active', 'Member', 'null'),
(4, 2, 'Daniel Ryan', '1920045', NULL, NULL, NULL, NULL, 'Active', NULL, NULL),
(5, 2, 'Daniel Ryan', '19203925', NULL, NULL, NULL, NULL, 'Active', NULL, NULL),
(6, 1, 'Juan Carlos', '1', NULL, NULL, NULL, NULL, 'Active', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `polygons`
--

CREATE TABLE `polygons` (
  `id` int(11) NOT NULL,
  `district` varchar(255) DEFAULT NULL,
  `barangay` varchar(255) DEFAULT NULL,
  `hoa` varchar(255) DEFAULT NULL,
  `lot_no` varchar(50) NOT NULL,
  `geojson` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`geojson`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `polygons`
--

INSERT INTO `polygons` (`id`, `district`, `barangay`, `hoa`, `lot_no`, `geojson`) VALUES
(1, 'Calbayog', 'San Policarpo', 'Home Shelter Association', 'TS-12', '{\"type\":\"Feature\",\"properties\":[],\"geometry\":{\"type\":\"Polygon\",\"coordinates\":[[[124.574131,12.067826],[124.574039,12.06783],[124.574051,12.067903],[124.574162,12.067886],[124.57415,12.06781],[124.574039,12.06783],[124.574131,12.067826]]]}}'),
(2, '1', '1', '1', 'TS-13', '{\"type\":\"Feature\",\"properties\":[],\"geometry\":{\"type\":\"Polygon\",\"coordinates\":[[[124.571181,12.068256],[124.571089,12.06826],[124.571101,12.068333],[124.571212,12.068316],[124.571199,12.068241],[124.571089,12.06826],[124.571181,12.068256]]]}}'),
(3, '1', '1', '1', 'TS-14', '{\"type\":\"Feature\",\"properties\":[],\"geometry\":{\"type\":\"Polygon\",\"coordinates\":[[[124.594434,12.067262],[124.57121,12.06785],[124.571112,12.06786],[124.57105,12.06783],[124.571061,12.06797],[124.57092,12.067989],[124.570998,12.068448],[124.571295,12.068417],[124.571201,12.067851],[124.594434,12.067262]]]}}'),
(4, '2', '2', '2', 'TS-15', '{\"type\":\"Feature\",\"properties\":[],\"geometry\":{\"type\":\"Polygon\",\"coordinates\":[[[124.125657,12.145],[124.125723,12.145063],[124.125657,12.145],[124.125723,12.145063],[124.125657,12.145]]]}}'),
(5, 'Test', 'Test1', 'Test', 'TS-16', '{\"type\":\"Feature\",\"properties\":[],\"geometry\":{\"type\":\"Polygon\",\"coordinates\":[[[124.574131,12.067826],[124.574147,12.067914],[124.574128,12.067845],[124.574097,12.06789],[124.574056,12.06797],[124.574131,12.067826]]]}}'),
(6, 'Calbayog', 'San Policarpo', 'Home Shelter Association', 'TS-17', '{\"type\":\"Feature\",\"properties\":[],\"geometry\":{\"type\":\"Polygon\",\"coordinates\":[[[126.574345,12.06778],[126.574392,12.067857],[126.574438,12.06778],[126.574392,12.067857],[126.574345,12.06778]]]}}'),
(7, 'Calbayog', 'San Policarpo', 'Home Shelter Association', 'TS-18', '{\"type\":\"Feature\",\"properties\":[],\"geometry\":{\"type\":\"Polygon\",\"coordinates\":[[[124.574131,12.067826],[124.574131,12.067826],[124.574143,12.067899],[124.574254,12.067882],[124.574242,12.067806],[124.574131,12.067826],[124.574131,12.067826]]]}}'),
(8, 'Test', 'Test', 'Test', 'TS-12345', '{\"type\":\"Feature\",\"properties\":[],\"geometry\":{\"type\":\"Polygon\",\"coordinates\":[[[124.574131,12.067826],[124.574131,12.067826],[124.574198,12.067764],[124.574182,12.067676],[124.574022,12.067765],[124.574131,12.067826]]]}}');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `employeeid` varchar(255) DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `DOB` date DEFAULT NULL,
  `role` enum('Admin','Employee') DEFAULT NULL,
  `last_login_time` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `employeeid`, `firstname`, `lastname`, `DOB`, `role`, `last_login_time`, `created_at`) VALUES
(2, 'raven', '$2y$10$id9xNoUIn6Qn/kPq6jJjP.pPYVrABm/VP0BjgfTmFuNhdZrQ1ZILe', '21-01177', 'Raven', 'Apilado', '2002-12-07', 'Admin', '2024-11-03 02:30:46', '2024-09-19 16:41:22'),
(5, 'ravire', '$2y$10$cBpf3Lbygm/fJjeLPN0lEOn8w7ZVD/lpj3JcIrEbWoDqGIY2ME7ra', NULL, NULL, NULL, NULL, 'Admin', '2024-09-23 20:19:12', '2024-09-19 18:26:27'),
(6, NULL, '$2y$10$FGfXFdilKYRQg98wL9M5meqvYccDEXFC7iN37uRdg3GCarkMAgEZC', '21-01178', 'near', 'far', '2004-07-12', '', '2024-10-30 20:34:13', '2024-10-30 20:33:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hoas`
--
ALTER TABLE `hoas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_history`
--
ALTER TABLE `login_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `member_id` (`member_id`),
  ADD KEY `lot_no` (`lot_no`),
  ADD KEY `hoa` (`hoa`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hoa_id` (`hoa_id`);

--
-- Indexes for table `polygons`
--
ALTER TABLE `polygons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lot_no_2` (`lot_no`),
  ADD UNIQUE KEY `lot_no_3` (`lot_no`),
  ADD KEY `lot_no` (`lot_no`),
  ADD KEY `hoa` (`hoa`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hoas`
--
ALTER TABLE `hoas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19003;

--
-- AUTO_INCREMENT for table `login_history`
--
ALTER TABLE `login_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `member`
--
ALTER TABLE `member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `polygons`
--
ALTER TABLE `polygons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `login_history`
--
ALTER TABLE `login_history`
  ADD CONSTRAINT `login_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `member`
--
ALTER TABLE `member`
  ADD CONSTRAINT `member_ibfk_1` FOREIGN KEY (`lot_no`) REFERENCES `polygons` (`lot_no`),
  ADD CONSTRAINT `member_ibfk_2` FOREIGN KEY (`hoa`) REFERENCES `polygons` (`hoa`);

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_ibfk_1` FOREIGN KEY (`hoa_id`) REFERENCES `hoas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
