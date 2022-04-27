-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2022 at 07:59 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `graphic-lead-upwork`
--

-- --------------------------------------------------------

--
-- Table structure for table `imagequeue`
--

CREATE TABLE `imagequeue` (
  `imageid` char(36) NOT NULL,
  `userid` int(11) NOT NULL,
  `contenttype` varchar(256) DEFAULT NULL,
  `submitted` datetime DEFAULT NULL,
  `processed` datetime DEFAULT NULL,
  `ttl` datetime DEFAULT NULL,
  `status` varchar(256) DEFAULT NULL,
  `callback_success` varchar(191) DEFAULT NULL,
  `callback_failure` varchar(191) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `imagequeue`
--

INSERT INTO `imagequeue` (`imageid`, `userid`, `contenttype`, `submitted`, `processed`, `ttl`, `status`, `callback_success`, `callback_failure`) VALUES
('fc47ae86-665a-40de-92e1-b4bf58915978', 3, 'image/png', '2022-04-20 06:54:40', NULL, '2022-04-21 06:54:40', 'processing', 'http://aditya.com/image?success', 'http://aditya.com/image?failure'),
('4162bcc4-da0b-4fe3-ac76-db996957db4a', 3, 'image/jpg', '2022-04-20 06:56:57', NULL, '2022-04-25 23:02:37', 'Failed to retrieve image from backend server', 'http://aditya.com/image?success', 'http://aditya.com/image?failure'),
('4e661c69-d89c-43d4-8e45-2e56d15ce8ac', 3, NULL, NULL, NULL, '2022-04-23 00:02:55', 'waiting for upload', NULL, NULL),
('5ee82bc2-f437-4d88-ad57-a2d190464bf1', 3, NULL, NULL, NULL, '2022-04-23 00:08:31', 'waiting for upload', NULL, NULL),
('ed3056cc-941e-4b9f-98b1-33418b97d5a1', 3, NULL, NULL, NULL, '2022-04-23 03:22:49', 'Failed', NULL, NULL),
('eb5f034b-f951-4761-9fa0-6a75de18d7a4', 3, 'image/png', '2022-04-22 11:01:30', NULL, '2022-04-23 11:01:30', 'processing', NULL, NULL),
('23309ce7-1a96-42cf-9519-82301ea6cb2b', 3, 'image/png', '2022-04-22 11:05:40', NULL, '2022-04-23 11:05:40', 'processing', NULL, NULL),
('707cb788-2043-4303-a217-d1c954962c7a', 3, 'image/png', '2022-04-22 11:40:02', NULL, '2022-04-23 11:40:02', 'processing', NULL, NULL),
('50e26be6-7ed5-4b5a-932b-6ef8deaad0fe', 3, 'image/jpg', '2022-04-23 01:11:23', '2022-04-22 12:03:48', '2022-04-24 01:11:23', 'Failed to send image backend', NULL, NULL),
('35a5d2df-44a1-4c2c-acaf-e42ce7fb3124', 3, 'image/png', '2022-04-23 00:56:53', NULL, '2022-04-24 00:56:53', 'processing', NULL, NULL),
('3caab869-5499-4ebf-9f8e-96106b181ce2', 3, 'image/png', '2022-04-23 01:02:25', NULL, '2022-04-24 01:02:25', 'processing', NULL, NULL),
('d74b8e72-a9e9-482f-a30e-d3781645f341', 3, 'image/png', '2022-04-23 01:08:59', NULL, '2022-04-24 01:08:59', 'Failed to send image backend', NULL, NULL),
('f02cd3e5-47dc-4b6e-85f1-649355ef9537', 3, 'image/png', '2022-04-23 01:13:33', NULL, '2022-04-24 01:13:33', 'processing', NULL, NULL),
('1ffbf1a9-877c-43f6-8c2e-0d3ecbc2edbe', 3, 'image/png', '2022-04-23 01:14:27', NULL, '2022-04-24 01:14:27', 'Failed to send image backend', NULL, NULL),
('ed7c9a77-860c-48f2-bf89-0635518645dd', 3, 'image/png', '2022-04-24 23:26:43', NULL, '2022-04-25 23:26:43', 'ssh: Could not resolve hostname stage1-1.intranet.graphiclead.com: No such host is known. \r\nlost connection\n', NULL, NULL),
('5fc79b9a-b4fd-4aee-b3ff-d91e2207608a', 3, 'image/png', '2022-04-24 23:28:58', NULL, '2022-04-25 23:28:58', 'ssh: Could not resolve hostname stage1-1.intranet.graphiclead.com: No such host is known. \r\nlost connection\n', NULL, NULL),
('d2dbd530-25fd-413e-b883-64e68b8a83f1', 3, 'image/png', '2022-04-25 03:22:47', NULL, '2022-04-26 03:22:47', 'ssh: Could not resolve hostname stage1-1.intranet.graphiclead.com: No such host is known. \r\nlost connection\n', NULL, NULL),
('bec822fb-c1b2-4893-8d86-7bf2ca32ab9e', 3, 'image/jpg', '2022-04-25 03:23:41', NULL, '2022-04-26 03:23:41', 'ssh: Could not resolve hostname stage1-1.intranet.graphiclead.com: No such host is known. \r\nlost connection\n', NULL, NULL),
('5ff3a564-23b0-47cf-8fc2-08e6b0483995', 3, NULL, NULL, NULL, '2022-04-27 04:06:34', 'waiting for upload', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_12_14_000001_create_personal_access_tokens_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userid` int(11) NOT NULL,
  `password` varchar(256) NOT NULL,
  `email` varchar(256) NOT NULL,
  `username` varchar(191) DEFAULT NULL,
  `firstname` varchar(256) NOT NULL,
  `lastname` varchar(256) NOT NULL,
  `company` varchar(256) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `lastlogin` datetime NOT NULL,
  `codecount` int(11) NOT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `modified` timestamp NULL DEFAULT NULL,
  `groups` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userid`, `password`, `email`, `username`, `firstname`, `lastname`, `company`, `active`, `lastlogin`, `codecount`, `created`, `modified`, `groups`) VALUES
(1, '$2y$10$r8GAf.hpKYEkT6qF75/5z.itraRnBU98PEP.Tr34QPUxxXLgzYug2', 'test@test.com', NULL, 'test', 'test-last', 'graphic-lead', 1, '2022-03-28 11:09:22', 1, '2022-03-28 02:19:58', '2022-03-28 05:39:22', 1),
(3, '$2y$10$DPTP7c/l.KwWLRif04w1Mu3bUL47DUbSKdfZH4ev/5af8T5NIDTZm', 'peter@test.com', 'peter50', 'peter', 'peter-last', 'graphic-lead', 1, '2022-04-27 01:38:41', 1, '2022-03-28 17:01:11', '2022-04-26 20:08:41', 1),
(4, '$2y$10$D43QfkTCAoL6FGIzw83BmuhnHJoTUrRHxmmWvfTIPRpelPygzLktW', 'te@test.com', 'peter90', 'peter', 'peter-last', 'graphic-lead', 1, '2022-04-21 07:55:17', 1, '2022-04-21 02:25:18', '2022-04-21 02:25:18', 0),
(5, '$2y$10$yk.j2v0xYJOuuypTFRnDyud2nzSWnoAyub7.vBsdyyD3BkJc0GE.m', 'kendra@test.com', 'kendra', 'peter', 'peter-last', 'graphic-lead', 1, '2022-04-27 01:45:31', 1, '2022-04-26 20:15:31', '2022-04-26 20:15:31', 0),
(6, '$2y$10$HYlV9K3swFyX4srr93IxleIeZv9O3PPNSbEKCeIum/JVwRdXw0kGi', 'lamar@test.com', 'lamar', 'peter', 'peter-last', 'graphic-lead', 1, '2022-04-27 01:46:32', 1, '2022-04-26 20:16:32', '2022-04-26 20:16:32', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
