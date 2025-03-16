-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 15, 2025 at 12:11 PM
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
('d451fd0d04c62ea543de70d48fc436c83e998995c6d85fb2ccedc6b8a0febce4/27160c038fd64bcb2e4533f224b55c3933ceb2d9d244f8536585355e993d66b1', 1);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_account`
--

INSERT INTO `tbl_account` (`id`, `username`, `password`, `email`, `first_name`, `last_name`, `role`, `is_active`, `created_at`, `last_login`, `access_level`, `phone`) VALUES
('e71736c971c3451ae162fb330fada675b20a0b6b5f2091263f9f810613a0d3f7/09be13863632c8b3810c5bb1444ac1a9502', 'cj2003', '$2y$10$odSj8lN143Z7UKnw3cF4Pul45YA5LPRkbmsBvxTPbFNOlLJGFlZjW', 'johnchristianfariola@gmail.com', 'John Christian', 'Larayos', 'admin', 1, '2025-03-08 11:13:37', '2025-03-11 13:38:10', 5, '0909643776');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_favorites`
--

CREATE TABLE `tbl_favorites` (
  `id` varchar(250) NOT NULL,
  `password_id` varchar(250) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_favorites`
--

INSERT INTO `tbl_favorites` (`id`, `password_id`, `created_at`, `updated_at`) VALUES
('05ba50c6241c57b60dc0cf7f503e86c7514a18ff400b24aebd0c057295dc5112', 'd1f0ced1-fcf8-11ef-a3a3-088fc3566126', '2025-03-15', '2025-03-15');

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
('7ae8d5a1-0186-11f0-8cdb-088fc3566126', 'Social', '2025-03-15 18:15:54', '2025-03-15 18:15:54'),
('85c70d17-0186-11f0-8cdb-088fc3566126', 'Email', '2025-03-15 18:16:12', '2025-03-15 18:16:12'),
('8edfce30-0186-11f0-8cdb-088fc3566126', 'Work Tools', '2025-03-15 18:16:28', '2025-03-15 18:16:28'),
('93080c0a-0186-11f0-8cdb-088fc3566126', 'Media', '2025-03-15 18:16:35', '2025-03-15 18:16:35'),
('97afde94-0186-11f0-8cdb-088fc3566126', 'Finance', '2025-03-15 18:16:42', '2025-03-15 18:16:42'),
('9e0f71a1-0186-11f0-8cdb-088fc3566126', 'Internet', '2025-03-15 18:16:53', '2025-03-15 18:16:53');

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
('15fb083c-018b-11f0-8cdb-088fc3566126', 'Github', 'us1071591@gmail.com', 'kYWV7GFBh$1w ', 'https://github.com/', '', '8edfce30-0186-11f0-8cdb-088fc3566126', NULL, 'github', '2025-03-15 10:48:52', '2025-03-15 10:48:52', 2),
('2296b69c-018c-11f0-8cdb-088fc3566126', 'UnSplash', 'johnchristianfariola@gmail.com', '6Pg@rqLi2@2JJ!w', 'https://unsplash.com/', '', '93080c0a-0186-11f0-8cdb-088fc3566126', NULL, 'image-alt', '2025-03-15 10:56:23', '2025-03-15 10:56:23', 2),
('246611de-018d-11f0-8cdb-088fc3566126', 'Maya', '+639950367559', 'johnchristian@2003', 'https://play.google.com/store/search?q=Maya&c=apps&hl=en', 'ATM PIN: 032920', '97afde94-0186-11f0-8cdb-088fc3566126', NULL, 'android', '2025-03-15 11:03:35', '2025-03-15 11:03:56', 2),
('27a46f11-018e-11f0-8cdb-088fc3566126', 'Wifi', 'Yumis_Wifi', 'NNGgtzng%9', '', '', '9e0f71a1-0186-11f0-8cdb-088fc3566126', NULL, 'wifi', '2025-03-15 11:10:50', '2025-03-15 11:10:56', 2),
('30177b78-018b-11f0-8cdb-088fc3566126', 'Github', 'johnchristianfariola@gmail.com', 'Code@2025!Hub ', 'https://github.com/', '', '8edfce30-0186-11f0-8cdb-088fc3566126', NULL, 'github', '2025-03-15 10:49:36', '2025-03-15 10:49:36', 2),
('3d7efbf7-0189-11f0-8cdb-088fc3566126', 'Spotify', 'johnchristianfariola@gmail.com', 'E+k2Wnw<V_%G/:V ', 'https://open.spotify.com/', '', '7ae8d5a1-0186-11f0-8cdb-088fc3566126', NULL, 'music-note-beamed', '2025-03-15 10:35:40', '2025-03-15 10:35:40', 2),
('576cecda-018c-11f0-8cdb-088fc3566126', 'Manga Zone', 'johnchristianfariola@gmail.com', 'john123&chri', '', '', '93080c0a-0186-11f0-8cdb-088fc3566126', NULL, 'book', '2025-03-15 10:57:52', '2025-03-15 10:57:52', 2),
('5b4374aa-018d-11f0-8cdb-088fc3566126', 'SmartHome GateWay', 'user', 'admin', 'http://192.168.1.1/', '', '9e0f71a1-0186-11f0-8cdb-088fc3566126', NULL, 'wifi', '2025-03-15 11:05:07', '2025-03-15 11:05:34', 2),
('68308e22-018b-11f0-8cdb-088fc3566126', 'MCC-LRC ', ' 2021-1485', '2#QsUKgSD7kC', 'https://mcc-lrc.com/', '', '8edfce30-0186-11f0-8cdb-088fc3566126', NULL, 'book', '2025-03-15 10:51:10', '2025-03-15 10:51:10', 2),
('6a938e48-0187-11f0-8cdb-088fc3566126', 'Facebook', 'johnchristianfariola@gmail.com', 'john@(^09631142759^)&chr', 'https://www.facebook.com/', 'Recovery Email ➜ +639950367559 • johnchristianfariola@gmail.com \r\n', '7ae8d5a1-0186-11f0-8cdb-088fc3566126', NULL, 'facebook', '2025-03-15 10:22:36', '2025-03-15 10:22:36', 2),
('6d01daf5-0189-11f0-8cdb-088fc3566126', 'Gmail', 'johnchristianfariola@gmail.com', 'john@(^09631142759^)&chri ', 'https://gmail.com/', '', '85c70d17-0186-11f0-8cdb-088fc3566126', NULL, 'google', '2025-03-15 10:36:59', '2025-03-15 10:36:59', 2),
('8d7e4c16-0189-11f0-8cdb-088fc3566126', 'Gmail', 'us1071591@gmail.com', 'J0hnChr1st!@nF@r10l', 'https://gmail.com/', '2FA Enabled ➜ Recovery Email • johnchristianfariola@gmail.com • 0995 036 7559 ', '85c70d17-0186-11f0-8cdb-088fc3566126', NULL, 'google', '2025-03-15 10:37:54', '2025-03-15 10:37:54', 2),
('ace8a783-018a-11f0-8cdb-088fc3566126', 'MS365', 'johnchristian.fariola@mcclawis.edu.ph ', 'john@(^09061040467^)&chr', 'https://m365.cloud.microsoft/', '', '85c70d17-0186-11f0-8cdb-088fc3566126', NULL, 'browser-edge', '2025-03-15 10:45:56', '2025-03-15 10:45:56', 2),
('b2dc8d01-0189-11f0-8cdb-088fc3566126', 'Gmail', 'montgomeryaurelia06@gmail.com ', 'P@ssAurelia2003!', 'https://gmail.com/', '2FA Enabled \r\n\r\n', '85c70d17-0186-11f0-8cdb-088fc3566126', NULL, 'google', '2025-03-15 10:38:56', '2025-03-15 10:38:56', 2),
('c3545711-018b-11f0-8cdb-088fc3566126', 'UnityHub', 'johnchristian2003', 'john@(^09631142759^)&Chri ', '', '', '8edfce30-0186-11f0-8cdb-088fc3566126', NULL, 'stack', '2025-03-15 10:53:43', '2025-03-15 10:53:43', 2),
('cb0fd6eb-0188-11f0-8cdb-088fc3566126', 'YouGov', 'johnchristianfariola@gmail.com', 'N/A', 'https://account.yougov.com/ph-fil/account', 'Verification Code required to sign-i', '7ae8d5a1-0186-11f0-8cdb-088fc3566126', NULL, 'question-circle-fill', '2025-03-15 10:32:28', '2025-03-15 10:33:27', 2),
('e6c11365-018a-11f0-8cdb-088fc3566126', 'MS365', 'johnchristianfariola2003@outlook.com', 'CloudPower#88!MS365', 'https://m365.cloud.microsoft/', '', '85c70d17-0186-11f0-8cdb-088fc3566126', NULL, 'browser-edge', '2025-03-15 10:47:33', '2025-03-15 10:47:33', 2),
('ebbc05d6-018c-11f0-8cdb-088fc3566126', 'GCash', '+639950367559', '2920', 'https://play.google.com/store/apps/details?id=com.globe.gcash.android&hl=en', '', '97afde94-0186-11f0-8cdb-088fc3566126', NULL, 'android', '2025-03-15 11:02:00', '2025-03-15 11:02:00', 2);

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
-- Indexes for table `tbl_favorites`
--
ALTER TABLE `tbl_favorites`
  ADD PRIMARY KEY (`id`);

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
