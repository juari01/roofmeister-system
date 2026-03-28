-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 07, 2022 at 06:17 AM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `legacyleadsusa`
--

-- --------------------------------------------------------

--
-- Table structure for table `group`
--

CREATE TABLE `group` (
  `group_id` int(10) UNSIGNED NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `group` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `group`
--

INSERT INTO `group` (`group_id`, `created`, `updated`, `active`, `group`) VALUES
(1, '2021-06-06 21:54:07', '2021-06-06 21:54:07', 1, 'Admins'),
(2, '2021-07-11 06:17:15', '2021-07-11 06:17:15', 1, 'Test Group'),
(3, '2021-07-11 06:17:20', '2021-07-11 06:22:54', 1, 'Test Group3');

-- --------------------------------------------------------

--
-- Table structure for table `security`
--

CREATE TABLE `security` (
  `security_id` int(10) UNSIGNED NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `code` varchar(32) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `description` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `security`
--

INSERT INTO `security` (`security_id`, `created`, `updated`, `active`, `code`, `name`, `description`) VALUES
(1, '2021-06-06 21:45:02', '2021-06-06 21:45:02', 1, 'admin', 'Admin', 'Full application access.');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `username` varchar(64) DEFAULT NULL,
  `first_name` varchar(64) DEFAULT NULL,
  `last_name` varchar(64) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(64) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `hash` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `created`, `updated`, `username`, `first_name`, `last_name`, `email`, `password`, `active`, `last_login`, `hash`) VALUES
(1, '2021-06-06 21:46:25', '2022-09-07 00:28:16', 'hwadmin', 'Hyperion', 'Admin', 'nick.coons@hyperionworks.com', '$2y$10$Q9x.qPkzr7layQSjC2nJlOkf23aZVVL5k0Y2s18Rgar2igU9qqMIK', 1, '2022-09-07 02:28:16', 'e5099e6fe0b0db59df96f4de2275a8d7'),
(5, '2021-06-14 05:00:29', '2021-06-14 05:00:29', 'brad.prince', 'Brad', 'Prince', 'brad@brad.com', '$2y$10$egknf5237dR39LccRe5whu6YtrlJs.Y8WOYGgHjj2DYxMPCqXtVSe', 1, NULL, NULL),
(6, '2021-06-14 05:01:09', '2021-06-14 05:01:09', 'brad.prince1', 'Brad', 'Prince', 'brad@brad.com', '$2y$10$dL8N/Tn/..XrGDh2OIYnF.idtYJAnHIryiPvsfJplvgJmDf9qBrmC', 1, NULL, NULL),
(7, '2021-06-14 05:06:14', '2021-06-14 06:04:03', 'user1', 'First', 'Last', 'email', '$2y$10$Ib6fsAWhEpWSOXPTQh2TZe62Lo7brbgEZa3TKRaRgTugisZspbaPK', 1, '2021-06-13 23:04:03', '52c97df93b2176bd80576f2ff9f2ef82'),
(8, '2022-09-04 07:02:52', '2022-09-07 04:11:50', 'ian', 'Ian', 'Juario', 'test@gmail.com', '$2y$10$mpmUtViS8fUn4TMbi4BF7uMpgzIcdJxMDbJJJ1k/84YGBPraTmSGW', 1, '2022-09-07 06:11:50', 'f852ca5d219cdb9867aa3b867670b9d1');

-- --------------------------------------------------------

--
-- Table structure for table `xref_group_security`
--

CREATE TABLE `xref_group_security` (
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `group_id` int(10) UNSIGNED NOT NULL,
  `security_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `xref_group_security`
--

INSERT INTO `xref_group_security` (`created`, `updated`, `group_id`, `security_id`) VALUES
('2021-06-06 21:54:46', '2021-06-06 21:54:46', 1, 1),
('2021-07-11 06:22:54', '2021-07-11 06:22:54', 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `xref_user_group`
--

CREATE TABLE `xref_user_group` (
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(10) UNSIGNED NOT NULL,
  `group_id` int(10) UNSIGNED NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `xref_user_group`
--

INSERT INTO `xref_user_group` (`created`, `user_id`, `group_id`, `updated`) VALUES
('2021-06-06 21:54:21', 1, 1, '2021-06-06 21:54:21'),
('2021-06-14 05:00:29', 5, 1, '2021-06-14 05:00:29'),
('2021-06-14 05:01:09', 6, 1, '2021-06-14 05:01:09'),
('2022-09-04 07:02:52', 8, 1, '2022-09-04 07:02:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `group`
--
ALTER TABLE `group`
  ADD PRIMARY KEY (`group_id`),
  ADD KEY `created` (`created`),
  ADD KEY `updated` (`updated`),
  ADD KEY `active` (`active`);

--
-- Indexes for table `security`
--
ALTER TABLE `security`
  ADD PRIMARY KEY (`security_id`),
  ADD KEY `created` (`created`),
  ADD KEY `updated` (`updated`),
  ADD KEY `active` (`active`),
  ADD KEY `code` (`code`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `updated` (`updated`),
  ADD KEY `created` (`created`);

--
-- Indexes for table `xref_group_security`
--
ALTER TABLE `xref_group_security`
  ADD KEY `created` (`created`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `security_id` (`security_id`);

--
-- Indexes for table `xref_user_group`
--
ALTER TABLE `xref_user_group`
  ADD KEY `created` (`created`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `group_id` (`group_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `group`
--
ALTER TABLE `group`
  MODIFY `group_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `security`
--
ALTER TABLE `security`
  MODIFY `security_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `xref_group_security`
--
ALTER TABLE `xref_group_security`
  ADD CONSTRAINT `xref_group_security_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `group` (`group_id`),
  ADD CONSTRAINT `xref_group_security_ibfk_2` FOREIGN KEY (`security_id`) REFERENCES `security` (`security_id`);

--
-- Constraints for table `xref_user_group`
--
ALTER TABLE `xref_user_group`
  ADD CONSTRAINT `xref_user_group_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `xref_user_group_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `group` (`group_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
