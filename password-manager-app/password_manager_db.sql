-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 10, 2025 at 03:39 PM
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
-- Database: `password_manager_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `display_option`
--

CREATE TABLE `display_option` (
  `id` varchar(250) NOT NULL,
  `option` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `display_option`
--

INSERT INTO `display_option` (`id`, `option`) VALUES
('d451fd0d04c62ea543de70d48fc436c83e998995c6d85fb2ccedc6b8a0febce4/27160c038fd64bcb2e4533f224b55c3933ceb2d9d244f8536585355e993d66b1', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_account`
--

CREATE TABLE `tbl_account` (
  `id` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'admin',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `access_level` int(11) DEFAULT 1,
  `phone` varchar(20) DEFAULT NULL
) ;

--
-- Dumping data for table `tbl_account`
--

INSERT INTO `tbl_account` (`id`, `username`, `password`, `email`, `first_name`, `last_name`, `role`, `is_active`, `created_at`, `last_login`, `access_level`, `phone`) VALUES
('e71736c971c3451ae162fb330fada675b20a0b6b5f2091263f9f810613a0d3f7/09be13863632c8b3810c5bb1444ac1a9502', 'cj2003', '$2y$10$odSj8lN143Z7UKnw3cF4Pul45YA5LPRkbmsBvxTPbFNOlLJGFlZjW', 'johnchristianfariola@gmail.com', 'John Christian', 'Larayos', 'admin', 1, '2025-03-08 11:13:37', '2025-03-10 11:38:33', 5, '0909643776');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_folder`
--

CREATE TABLE `tbl_folder` (
  `folder_id` varchar(255) NOT NULL,
  `folder_name` varchar(255) NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_folder`
--

INSERT INTO `tbl_folder` (`folder_id`, `folder_name`, `created_date`, `modified_date`) VALUES
('285fc19d-7a06-42f8-b0c3-9e54b37d81f2', 'Work', '2025-03-08 23:23:59', '2025-03-08 23:23:59'),
('6c91d0e4-3297-4b18-9fa7-812469eb5360', 'Productivity & Work Tool', '2025-03-08 23:23:59', '2025-03-09 20:35:34'),
('a7f3e912-d5b8-4c6f-9e38-1d56f8a2c45e', 'Social Media & Communication', '2025-03-08 23:23:59', '2025-03-09 09:45:27');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_save_passwords`
--

CREATE TABLE `tbl_save_passwords` (
  `id` varchar(255) NOT NULL,
  `website_name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `notes` text NOT NULL,
  `folder` varchar(250) DEFAULT NULL,
  `icon_image` longblob DEFAULT NULL,
  `icon_file_name` varchar(255) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `icon_option` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_save_passwords`
--

INSERT INTO `tbl_save_passwords` (`id`, `website_name`, `username`, `password`, `website_url`, `notes`, `folder`, `icon_image`, `icon_file_name`, `date_created`, `date_modified`, `icon_option`) VALUES
('00d99406-fc88-11ef-a3a3-088fc3566126', 'Google', 'johnchristianfariola@gmail.com', 'john@(^09631142759^)&chri', 'https://gmail.com/', '', 'a7f3e912-d5b8-4c6f-9e38-1d56f8a2c45e', NULL, 'google', '2025-03-09 01:44:13', '2025-03-10 13:41:12', 2),
('0cd71e65-fc33-11ef-abae-088fc3566126', 'Facebook', 'johnchristianfariola@gmail.com', 'BlueSky#24!Fb2025', 'https://www.facebook.com/', '', 'a7f3e912-d5b8-4c6f-9e38-1d56f8a2c45e', NULL, 'facebook', '2025-03-08 15:36:05', '2025-03-09 15:17:23', 2),
('1edecd88-fce3-11ef-a3a3-088fc3566126', 'Github', 'us1071591@gmail.com', 'kYWV7GFBh$1w', 'https://github.com/', '', '6c91d0e4-3297-4b18-9fa7-812469eb5360', NULL, 'github', '2025-03-09 12:36:27', '2025-03-09 15:17:25', 2),
('7f313bca-fc89-11ef-a3a3-088fc3566126', 'Google', 'us1071591@gmail.com', 'J0hnChr1st!@nF@r10l', 'https://gmail.com/', '', 'a7f3e912-d5b8-4c6f-9e38-1d56f8a2c45e', NULL, 'google', '2025-03-09 01:54:54', '2025-03-09 15:17:28', 2),
('840d348d-fce5-11ef-a3a3-088fc3566126', 'Github', 'johnchristianfariola@gmail.com', 'Code@2025!Hub', 'https://github.com/', 'this is a github account', '6c91d0e4-3297-4b18-9fa7-812469eb5360', NULL, 'github', '2025-03-09 12:53:36', '2025-03-09 15:17:30', 2),
('97032135-fda4-11ef-9a7e-088fc3566126', 'MS365', 'johnchristianfariola2003@outlook.com', 'CloudPower#88!MS365', 'https://m365.cloud.microsoft/', '', '6c91d0e4-3297-4b18-9fa7-812469eb5360', NULL, 'browser-edge', '2025-03-10 11:41:21', '2025-03-10 11:41:45', 2),
('ce5333e7-fd4a-11ef-9a7e-088fc3566126', 'WordPress', 'johnchristianfariola@gmail.com', '1gH&QOJmvs@3Kf*%Y)gY4G9i', ' https://goodland-a1b9f1.ingress-earth.ewp.live/wp-login.php', 'This wordpress if for the GoodLand website', '6c91d0e4-3297-4b18-9fa7-812469eb5360', NULL, 'wordpress', '2025-03-10 00:58:40', '2025-03-10 00:59:27', 2),
('d1f0ced1-fcf8-11ef-a3a3-088fc3566126', 'MS365 ', 'johnchristian.fariola@mcclawis.edu.ph', 'john@(^09061040467^)&chri', 'https://m365.cloud.microsoft/', '2FA Enabled', '6c91d0e4-3297-4b18-9fa7-812469eb5360', NULL, 'browser-edge', '2025-03-09 15:11:49', '2025-03-09 15:11:49', 2),
('da97136d-fc9c-11ef-a3a3-088fc3566126', 'Google', 'montgomeryaurelia06@gmail.com', 'P@ssAurelia2003!', 'https://gmail.com/', '', 'a7f3e912-d5b8-4c6f-9e38-1d56f8a2c45e', NULL, 'google', '2025-03-09 04:13:28', '2025-03-09 15:17:33', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `tbl_user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone_number` varchar(255) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`tbl_user_id`, `name`, `phone_number`, `email_address`, `username`, `password`) VALUES
(2, 'Lorem Ipsum', '09123456678', 'loremipsum@gmail.com', 'admin', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `display_option`
--
ALTER TABLE `display_option`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_account`
--
ALTER TABLE `tbl_account`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `tbl_folder`
--
ALTER TABLE `tbl_folder`
  ADD PRIMARY KEY (`folder_id`);

--
-- Indexes for table `tbl_save_passwords`
--
ALTER TABLE `tbl_save_passwords`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`tbl_user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `tbl_user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
