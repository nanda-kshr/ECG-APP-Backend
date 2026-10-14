-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 07, 2026 at 06:51 AM
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
-- Database: `ecg_app_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `doctor_pg`
--

CREATE TABLE `doctor_pg` (
  `id` int(11) NOT NULL,
  `d_id` int(11) NOT NULL,
  `pg_id` int(11) NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `duty_roster`
--

CREATE TABLE `duty_roster` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `duty_date` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ecg_images`
--

CREATE TABLE `ecg_images` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `image_name` varchar(255) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `mime_type` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `task_id` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `status` varchar(15) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ecg_images`
--

INSERT INTO `ecg_images` (`id`, `patient_id`, `user_id`, `image_path`, `image_name`, `file_size`, `mime_type`, `created_at`, `task_id`, `notes`, `comment`, `status`) VALUES
(122, 30, NULL, 'uploads/ecg_images/TASK141_1770186172_1.jpg', 'scaled_13277841-5c4b-4941-ae70-2773fc6ea3b31788928663345888650.jpg', 1990921, 'image/jpeg', '2026-02-04 06:22:52', 141, NULL, NULL, 'closed'),
(123, 31, NULL, 'uploads/ecg_images/TASK142_1770362792_1.jpg', 'scaled_6425f511-4cab-4f4c-8702-3c19c36daf9b8178738885721096443.jpg', 1941117, 'image/jpeg', '2026-02-06 07:26:32', 142, NULL, 'test', 'completed'),
(124, 33, NULL, 'uploads/ecg_images/TASK146_1776228778_1.jpg', 'scaled_d6c0ff85-541c-442b-bd5e-519b07d17ce05436731271113305477.jpg', 3066719, 'image/jpeg', '2026-04-15 04:52:58', 146, NULL, 'This is healthy.', 'completed'),
(125, 33, NULL, 'uploads/ecg_images/TASK148_1776230160_1.jpg', 'scaled_3939d193-1720-4746-a231-92483719b8b67048312974740578594.jpg', 2223443, 'image/jpeg', '2026-04-15 05:16:00', 148, NULL, NULL, 'closed'),
(126, 33, NULL, 'uploads/ecg_images/TASK149_1776235736_1.jpeg', 'WhatsApp Image 2026-04-15 at 10.00.45 (1).jpeg', 44144, 'image/jpeg', '2026-04-15 06:48:56', 149, NULL, NULL, 'closed'),
(127, 33, NULL, 'uploads/ecg_images/TASK150_1776236701_1.jpg', 'scaled_78378c91-064d-4c4a-b269-f4956173f03b8728809281709914009.jpg', 1744775, 'image/jpeg', '2026-04-15 07:05:01', 150, NULL, NULL, 'closed'),
(128, 33, NULL, 'uploads/ecg_images/TASK152_1776236917_1.jpg', 'scaled_e80c229d-fe7a-4387-acf4-31c693bc214b1597577107017453994.jpg', 2592984, 'image/jpeg', '2026-04-15 07:08:37', 152, NULL, NULL, 'closed'),
(129, 34, NULL, 'uploads/ecg_images/TASK153_1776239600_1.jpg', 'scaled_97ba76ea-9902-4809-a3ca-9b1a6562b5886285672585544988250.jpg', 1387120, 'image/jpeg', '2026-04-15 07:53:20', 153, NULL, NULL, 'closed'),
(130, 33, NULL, 'uploads/ecg_images/TASK154_1776243273_1.jpg', 'scaled_9ceab183-e734-4f4a-9701-7b9115e974a81353731546132916499.jpg', 1560484, 'image/jpeg', '2026-04-15 08:54:33', 154, NULL, 'good', 'completed'),
(131, 37, NULL, 'uploads/ecg_images/TASK155_1776668295_1.jpg', 'scaled_9ca0d539-6ddd-4d3e-9e6d-d3255a186de95138773204453894720.jpg', 1066950, 'image/jpeg', '2026-04-20 06:58:15', 155, NULL, 'nstemi', 'completed'),
(132, 33, NULL, 'uploads/ecg_images/TASK157_1777088640_1.jpg', 'Election.jpg', 31962, 'image/jpeg', '2026-04-25 03:44:00', 157, NULL, NULL, 'closed'),
(133, 33, NULL, 'uploads/ecg_images/TASK158_1777090838_1.jpeg', 'WhatsApp Image 2026-04-15 at 10.00.45 (1).jpeg', 44144, 'image/jpeg', '2026-04-25 04:20:38', 158, NULL, NULL, 'closed'),
(134, 33, NULL, 'uploads/ecg_images/TASK159_1777090914_1.jpeg', 'WhatsApp Image 2026-04-15 at 10.00.45 (1).jpeg', 44144, 'image/jpeg', '2026-04-25 04:21:54', 159, NULL, NULL, 'closed'),
(135, 38, NULL, 'uploads/ecg_images/TASK160_1778052908_1.jpg', 'scaled_42c2c901-b193-44c0-a51e-cfba9303eb0b3218110489314295932.jpg', 3714656, 'image/jpeg', '2026-05-06 07:35:08', 160, NULL, NULL, 'closed'),
(136, 39, 61, 'uploads/ecg_images/TASK161_1778123404_1.jpg', 'scaled_9168083c-03df-4ff1-8c33-232795f2d0df7291401006131261815.jpg', 3884997, 'image/jpeg', '2026-05-07 03:10:04', 161, NULL, 'Its not a big thing..!', 'completed'),
(137, 43, 71, 'uploads/ecg_images/TASK162_1780389994_1.jpg', 'scaled_90dc4613-4968-481e-8ad7-484692613ef38752520665409399289.jpg', 985860, 'image/jpeg', '2026-06-02 08:46:34', 162, NULL, 'normal', 'completed'),
(138, 45, 65, 'uploads/ecg_images/TASK163_1780467468_1.jpg', 'scaled_368980d6-aca5-43a4-80d6-90088fad7bdc227281759431476788.jpg', 491224, 'image/jpeg', '2026-06-03 06:17:48', 163, NULL, 'normal', 'completed'),
(139, 46, 65, 'uploads/ecg_images/TASK164_1780467562_1.jpg', 'scaled_d8580ac4-777e-4644-81a9-e07a8a4cf8da6585323620291568531.jpg', 479431, 'image/jpeg', '2026-06-03 06:19:22', 164, NULL, 'normal', 'completed'),
(140, 47, 65, 'uploads/ecg_images/TASK165_1780467636_1.jpg', 'scaled_47337e3f-e2c5-46c1-b03e-66a77389d1de5217892913060518225.jpg', 597507, 'image/jpeg', '2026-06-03 06:20:36', 165, NULL, 'good', 'completed'),
(141, 48, 67, 'uploads/ecg_images/TASK166_1780469116_1.jpg', 'scaled_bc505245-dab7-45aa-af7a-bb64819d276c3791441685842628003.jpg', 1333659, 'image/jpeg', '2026-06-03 06:45:16', 166, NULL, 'good', 'completed'),
(142, 49, 83, 'uploads/ecg_images/TASK167_1780470725_1.jpg', 'scaled_42379d1c-cc9a-4e37-9481-6fec8d12bc6e8752457192748011696.jpg', 1949823, 'image/jpeg', '2026-06-03 07:12:05', 167, NULL, 'this is pretty normal', 'completed'),
(143, 50, 86, 'uploads/ecg_images/TASK168_1781592406_1.jpg', 'scaled_662bc70a-2f8f-4d0e-bbec-4e34b7c2e26d5704066524646796925.jpg', 2793105, 'image/jpeg', '2026-06-16 06:46:46', 168, NULL, NULL, 'closed'),
(144, 51, 86, 'uploads/ecg_images/TASK169_1784613828_1.jpg', 'scaled_bbbb362a-8524-408c-9255-56f17711a9165619225200453056824.jpg', 2699594, 'image/jpeg', '2026-07-21 06:03:48', 169, NULL, 'LVH With Deep T inversions ? HCM', 'completed'),
(145, 54, 90, 'uploads/ecg_images/TASK170_1785907097_1.jpg', 'scaled_dc054534-4ba7-4809-b4ef-4a1b981ffb0b8964973532911376295.jpg', 1627293, 'image/jpeg', '2026-08-05 05:18:17', 170, NULL, 'hi', 'completed'),
(146, 54, 90, 'uploads/ecg_images/TASK171_1785989993_1.jpg', 'scaled_1a4249dc-c044-4eee-9385-e0f37c16637b3062803505615030133.jpg', 1757506, 'image/jpeg', '2026-08-06 04:19:53', 171, NULL, 'normal testing', 'completed'),
(147, 54, 90, 'uploads/ecg_images/TASK172_1785990443_1.jpg', 'scaled_c6ce7154-543a-4ff9-981a-11acfb1188903759429874508957479.jpg', 1960877, 'image/jpeg', '2026-08-06 04:27:23', 172, NULL, 'test', 'completed'),
(148, 54, 90, 'uploads/ecg_images/TASK173_1785990828_1.jpg', 'scaled_15f0a53c-2a6d-4955-a85f-dd7f161c48ea3081326169970607680.jpg', 1973892, 'image/jpeg', '2026-08-06 04:33:48', 173, NULL, 'hi', 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` varchar(36) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'scheduled',
  `attempt_count` int(11) NOT NULL DEFAULT 0,
  `last_error` text DEFAULT NULL,
  `created_at` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `updated_at` datetime(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6),
  `type` varchar(255) NOT NULL,
  `target_url` varchar(255) NOT NULL,
  `target_method` varchar(255) NOT NULL,
  `headers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`headers`)),
  `params` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`params`)),
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`body`)),
  `scheduling` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`scheduling`)),
  `retries` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`retries`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `status`, `attempt_count`, `last_error`, `created_at`, `updated_at`, `type`, `target_url`, `target_method`, `headers`, `params`, `payload`, `body`, `scheduling`, `retries`) VALUES
('127a1854-c52f-40f5-9a4c-de41c8d7814a', 'completed', 1, NULL, '2026-06-03 11:50:37.310640', '2026-06-03 12:05:37.000000', 'oneoff', 'http://180.235.121.249:8081/saveetha_cardio/api/close_task.php', 'POST', NULL, NULL, NULL, NULL, NULL, NULL),
('34b93c55-5797-4137-b496-66ea4a1a44ee', 'completed', 1, NULL, '2026-05-06 13:05:09.659209', '2026-05-06 13:20:12.000000', 'oneoff', 'http://180.235.121.249:8081/saveetha_cardio/api/close_task.php', 'POST', NULL, NULL, NULL, NULL, NULL, NULL),
('7d2d202c-b25f-4ffc-b712-b1cc696eeccd', 'completed', 1, NULL, '2026-06-03 12:42:05.884106', '2026-06-03 12:57:05.000000', 'oneoff', 'http://180.235.121.249:8081/saveetha_cardio/api/close_task.php', 'POST', NULL, NULL, NULL, NULL, NULL, NULL),
('879ed5f6-f065-43a6-8c57-447bc30673cd', 'completed', 1, NULL, '2026-06-03 11:49:23.331295', '2026-06-03 12:04:23.000000', 'oneoff', 'http://180.235.121.249:8081/saveetha_cardio/api/close_task.php', 'POST', NULL, NULL, NULL, NULL, NULL, NULL),
('8e06c3cf-bca4-47c6-8d62-6f8640c43e63', 'completed', 1, NULL, '2026-06-03 11:47:48.637739', '2026-06-03 12:02:48.000000', 'oneoff', 'http://180.235.121.249:8081/saveetha_cardio/api/close_task.php', 'POST', NULL, NULL, NULL, NULL, NULL, NULL),
('9e0284c9-0bcf-4259-bc5c-8e1d18245a25', 'completed', 1, NULL, '2026-04-25 09:52:03.508525', '2026-04-25 10:06:54.000000', 'oneoff', 'http://180.235.121.249:8081/saveetha_cardio/api/close_task.php', 'POST', NULL, NULL, NULL, NULL, NULL, NULL),
('b4c14a96-1720-4802-ab77-6fb5d37873e6', 'scheduled', 1, 'Error', '2025-12-22 10:58:44.173666', '2026-04-15 12:41:05.467011', 'recurring', 'http://180.235.121.249:8081/saveetha_cardio/api/notify_pending_tasks.php', 'GET', NULL, NULL, NULL, NULL, NULL, NULL),
('c2f84514-4ac6-451f-b5b5-6f5aefbf4479', 'completed', 1, NULL, '2026-05-07 08:40:04.413507', '2026-05-07 08:55:04.000000', 'oneoff', 'http://180.235.121.249:8081/saveetha_cardio/api/close_task.php', 'POST', NULL, NULL, NULL, NULL, NULL, NULL),
('c6a71a50-312a-4cbb-89a2-b74ee57cb09f', 'completed', 1, NULL, '2026-06-02 14:16:34.764877', '2026-06-02 14:31:34.000000', 'oneoff', 'http://180.235.121.249:8081/saveetha_cardio/api/close_task.php', 'POST', NULL, NULL, NULL, NULL, NULL, NULL),
('f87e9870-2c02-404b-a905-469ea775c142', 'completed', 1, NULL, '2026-06-03 12:15:16.730807', '2026-06-03 12:30:16.000000', 'oneoff', 'http://180.235.121.249:8081/saveetha_cardio/api/close_task.php', 'POST', NULL, NULL, NULL, NULL, NULL, NULL),
('fd56143a-0660-4cdf-87b1-0965f44b005b', 'completed', 1, NULL, '2026-06-16 12:16:48.217742', '2026-06-16 12:31:48.000000', 'oneoff', 'http://180.235.121.249:8081/saveetha_cardio/api/close_task.php', 'POST', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `patient_id` varchar(64) NOT NULL,
  `name` varchar(191) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `assigned_doctor_id` int(11) DEFAULT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `patient_id`, `name`, `age`, `gender`, `assigned_doctor_id`, `status`, `created_at`, `phone`) VALUES
(30, 'PAT20260204001', 'Test Patient', NULL, 'male', NULL, 'in_progress', '2026-02-04 06:22:01', '+911234567890'),
(31, 'PAT20260206001', 'sathish', NULL, 'male', NULL, 'in_progress', '2026-02-06 07:26:27', '+919500008938'),
(32, 'PAT20260415001', 'Nandakishore P', NULL, 'male', NULL, 'pending', '2026-04-15 04:40:29', '+9188488376628'),
(33, 'PAT20260415002', 'Nandakishore P', NULL, 'male', NULL, 'in_progress', '2026-04-15 04:40:54', '+9188372662789'),
(34, 'PAT20260415003', 'suryakumar', NULL, 'male', NULL, 'in_progress', '2026-04-15 07:53:06', '+918681958196'),
(35, 'PAT20260420001', 'murugan', NULL, 'male', NULL, 'pending', '2026-04-20 06:55:40', '+919876543244'),
(36, 'PAT20260420002', 'murugan', NULL, 'male', NULL, 'pending', '2026-04-20 06:56:29', '+9198765373788'),
(37, 'PAT20260420003', 'murugan', NULL, 'male', NULL, 'in_progress', '2026-04-20 06:57:53', '+91983637883883'),
(38, 'PAT20260506001', 'lavanya', NULL, 'male', NULL, 'in_progress', '2026-05-06 07:34:18', '+919361389613'),
(39, 'PAT20260507001', 'Test Patient', NULL, 'male', 60, 'in_progress', '2026-05-07 03:06:20', '+917657657769'),
(40, 'PAT20260508001', 'maha', NULL, 'male', NULL, 'pending', '2026-05-08 04:47:43', '+9195674468987'),
(41, 'PAT20260508002', 'dhana lakshmi', NULL, 'male', NULL, 'pending', '2026-05-08 04:50:02', '+919353456734'),
(42, 'PAT20260508003', 'dhana', NULL, 'male', NULL, 'pending', '2026-05-08 04:50:46', '+919678547899'),
(43, 'PAT20260602001', 'aravind', NULL, 'male', 80, 'in_progress', '2026-06-02 08:46:21', '+919597124900'),
(44, 'PAT20260602002', 'aravind', NULL, 'male', NULL, 'pending', '2026-06-02 08:46:56', '+919597124900'),
(45, 'PAT20260603001', 'siva', NULL, 'male', 81, 'in_progress', '2026-06-03 06:17:42', '+919876543210'),
(46, 'PAT20260603002', 'siva', NULL, 'male', 81, 'in_progress', '2026-06-03 06:19:13', '+919876543210'),
(47, 'PAT20260603003', 'siva', NULL, 'male', 81, 'in_progress', '2026-06-03 06:20:30', '+919876543210'),
(48, 'PAT20260603004', 'Siva', NULL, 'male', 81, 'in_progress', '2026-06-03 06:45:05', '+919876543210'),
(49, 'PAT20260603005', 'Rama', NULL, 'male', 81, 'in_progress', '2026-06-03 07:10:53', '+918712345678'),
(50, 'PAT20260616001', 'Balan', NULL, 'male', 81, 'in_progress', '2026-06-16 06:46:20', '+919876543210'),
(51, 'PAT20260721001', 'Narasama', NULL, 'male', 81, 'in_progress', '2026-07-21 06:02:20', '+917904557729'),
(52, 'PAT20260805001', 'Dhatchanamoorthy', NULL, 'male', NULL, 'pending', '2026-08-05 04:52:32', '+919159725049'),
(53, 'PAT20260805002', 'Dhatchanamoorthy', NULL, 'male', NULL, 'pending', '2026-08-05 04:52:59', '+919159725049'),
(54, 'PAT20260805003', 'Nandakishore', NULL, 'male', 81, 'in_progress', '2026-08-05 05:18:01', '+911234567890'),
(55, 'PAT20260805004', 'prem', NULL, 'male', NULL, 'pending', '2026-08-05 05:24:13', '+919876543210'),
(56, 'PAT20260806001', 'prem', NULL, 'male', NULL, 'pending', '2026-08-06 04:23:28', '+919876543210');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `assigned_doctor_id` int(11) DEFAULT NULL,
  `status` enum('pending','assigned','in_progress','completed') NOT NULL DEFAULT 'pending',
  `priority` enum('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
  `user_notes` text DEFAULT NULL,
  `assigned_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `comment` text DEFAULT NULL,
  `assigned_pg_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `patient_id`, `user_id`, `assigned_doctor_id`, `status`, `priority`, `user_notes`, `assigned_at`, `created_at`, `comment`, `assigned_pg_id`) VALUES
(141, 30, NULL, NULL, 'pending', 'normal', '', '2026-02-04 11:52:52', '2026-02-04 06:22:52', NULL, NULL),
(142, 31, NULL, NULL, 'pending', 'normal', 'test', '2026-02-06 12:56:32', '2026-02-06 07:26:32', 'test', NULL),
(146, 33, NULL, NULL, 'pending', 'normal', '', '2026-04-15 10:22:58', '2026-04-15 04:52:58', 'This is healthy.', NULL),
(148, 33, NULL, NULL, 'pending', 'normal', '', '2026-04-15 10:46:00', '2026-04-15 05:16:00', NULL, NULL),
(149, 33, NULL, NULL, 'pending', 'normal', '', '2026-04-15 12:18:56', '2026-04-15 06:48:56', NULL, NULL),
(150, 33, NULL, NULL, 'pending', 'normal', '', '2026-04-15 12:35:01', '2026-04-15 07:05:01', NULL, NULL),
(152, 33, NULL, NULL, 'pending', 'normal', '', '2026-04-15 12:38:37', '2026-04-15 07:08:37', NULL, NULL),
(153, 34, NULL, NULL, 'pending', 'normal', '38, male, no comorbs now came with chest pain for past 1 week', '2026-04-15 13:23:20', '2026-04-15 07:53:20', NULL, NULL),
(154, 33, NULL, NULL, 'pending', 'normal', '', '2026-04-15 14:24:33', '2026-04-15 08:54:33', 'good', NULL),
(155, 37, NULL, NULL, 'pending', 'normal', '', '2026-04-20 12:28:15', '2026-04-20 06:58:15', 'nstemi', NULL),
(157, 33, NULL, NULL, 'pending', 'normal', '', '2026-04-25 09:14:00', '2026-04-25 03:44:00', NULL, NULL),
(158, 33, NULL, NULL, 'pending', 'normal', '', '2026-04-25 09:50:38', '2026-04-25 04:20:38', NULL, NULL),
(159, 33, NULL, NULL, 'pending', 'normal', '', '2026-04-25 09:51:54', '2026-04-25 04:21:54', NULL, NULL),
(160, 38, NULL, NULL, 'pending', 'normal', 'anc', '2026-05-06 13:05:08', '2026-05-06 07:35:08', NULL, NULL),
(161, 39, 61, 60, 'pending', 'normal', 'is there anything to worry about?', '2026-05-07 08:40:04', '2026-05-07 03:10:04', 'Its not a big thing..!', NULL),
(162, 43, 71, 80, 'pending', 'normal', 'test', '2026-06-02 14:16:34', '2026-06-02 08:46:34', 'normal', NULL),
(163, 45, 65, 81, 'pending', 'normal', '', '2026-06-03 11:47:48', '2026-06-03 06:17:48', 'normal', NULL),
(164, 46, 65, 81, 'pending', 'normal', '', '2026-06-03 11:49:22', '2026-06-03 06:19:22', 'normal', NULL),
(165, 47, 65, 81, 'pending', 'normal', '', '2026-06-03 11:50:36', '2026-06-03 06:20:36', 'good', NULL),
(166, 48, 67, 81, 'pending', 'normal', '', '2026-06-03 12:15:16', '2026-06-03 06:45:16', 'good', NULL),
(167, 49, 83, 81, 'pending', 'normal', 'please check this report', '2026-06-03 12:42:05', '2026-06-03 07:12:05', 'this is pretty normal', NULL),
(168, 50, 86, 81, 'pending', 'normal', 'came for cardiac evaluation.', '2026-06-16 12:16:46', '2026-06-16 06:46:46', NULL, NULL),
(169, 51, 86, 81, 'pending', 'normal', 'c/o chest pain (old CAD)', '2026-07-21 11:33:48', '2026-07-21 06:03:48', 'LVH With Deep T inversions ? HCM', NULL),
(170, 54, 90, 91, 'pending', 'normal', '', '2026-08-05 10:48:17', '2026-08-05 05:18:17', 'hi', NULL),
(171, 54, 90, 81, 'pending', 'normal', 'test', '2026-08-06 09:49:53', '2026-08-06 04:19:53', 'normal testing', NULL),
(172, 54, 90, 81, 'pending', 'normal', '', '2026-08-06 09:57:23', '2026-08-06 04:27:23', 'test', NULL),
(173, 54, 90, 81, 'pending', 'normal', '', '2026-08-06 10:03:48', '2026-08-06 04:33:48', 'hi', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `task_history`
--

CREATE TABLE `task_history` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `changed_by` int(11) DEFAULT NULL,
  `old_status` varchar(32) DEFAULT NULL,
  `new_status` varchar(32) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task_history`
--

INSERT INTO `task_history` (`id`, `task_id`, `changed_by`, `old_status`, `new_status`, `comment`, `created_at`) VALUES
(461, 141, NULL, NULL, 'pending', 'ECG created manually', '2026-02-04 06:22:52'),
(462, 141, NULL, NULL, 'assigned', 'Auto-assigned duty doctor', '2026-02-04 06:22:52'),
(463, 141, NULL, 'pending', 'closed', 'Image closed via API', '2026-02-04 06:37:52'),
(464, 142, NULL, NULL, 'pending', 'ECG created manually', '2026-02-06 07:26:32'),
(465, 142, NULL, NULL, 'assigned', 'Auto-assigned duty doctor', '2026-02-06 07:26:32'),
(466, 142, 1, NULL, 'completed', 'All images completed (Bulk Update)', '2026-02-06 07:28:36'),
(467, 142, 1, NULL, 'image_completed', 'Opinion: test', '2026-02-06 07:28:36'),
(471, 146, NULL, NULL, 'pending', 'ECG created manually', '2026-04-15 04:52:58'),
(472, 146, NULL, NULL, 'assigned', 'Auto-assigned duty doctor', '2026-04-15 04:52:58'),
(473, 146, NULL, NULL, 'completed', 'All images completed (Bulk Update)', '2026-04-15 04:58:29'),
(474, 146, NULL, NULL, 'image_completed', 'Opinion: This is healthy.', '2026-04-15 04:58:29'),
(476, 148, NULL, NULL, 'pending', 'ECG created manually', '2026-04-15 05:16:00'),
(477, 148, NULL, NULL, 'assigned', 'Auto-assigned duty doctor', '2026-04-15 05:16:00'),
(478, 149, NULL, NULL, 'pending', 'ECG created manually', '2026-04-15 06:48:56'),
(479, 149, NULL, NULL, 'assigned', 'Auto-assigned duty doctor', '2026-04-15 06:48:56'),
(480, 149, NULL, 'pending', 'closed', 'Image closed via API', '2026-04-15 06:58:40'),
(481, 150, NULL, NULL, 'pending', 'ECG created manually', '2026-04-15 07:05:01'),
(482, 150, NULL, NULL, 'assigned', 'Auto-assigned duty doctor', '2026-04-15 07:05:01'),
(483, 150, NULL, 'pending', 'closed', 'Image closed via API', '2026-04-15 07:05:41'),
(485, 152, NULL, NULL, 'pending', 'ECG created manually', '2026-04-15 07:08:37'),
(486, 152, NULL, NULL, 'assigned', 'Auto-assigned duty doctor', '2026-04-15 07:08:37'),
(487, 152, NULL, 'pending', 'closed', 'Image closed via API', '2026-04-15 07:25:28'),
(488, 153, NULL, NULL, 'pending', 'ECG created manually', '2026-04-15 07:53:20'),
(489, 153, NULL, NULL, 'assigned', 'Auto-assigned duty doctor', '2026-04-15 07:53:20'),
(490, 153, NULL, 'pending', 'closed', 'Image closed via API', '2026-04-15 08:53:17'),
(491, 154, NULL, NULL, 'pending', 'ECG created manually', '2026-04-15 08:54:33'),
(492, 154, NULL, NULL, 'assigned', 'Auto-assigned duty doctor', '2026-04-15 08:54:33'),
(493, 154, 1, NULL, 'completed', 'All images completed (Bulk Update)', '2026-04-20 06:57:08'),
(494, 154, 1, NULL, 'image_completed', 'Opinion: good', '2026-04-20 06:57:08'),
(495, 155, NULL, NULL, 'pending', 'ECG created manually', '2026-04-20 06:58:15'),
(496, 155, NULL, NULL, 'assigned', 'Auto-assigned duty doctor', '2026-04-20 06:58:15'),
(497, 155, 1, NULL, 'completed', 'All images completed (Bulk Update)', '2026-04-20 07:01:46'),
(498, 155, 1, NULL, 'image_completed', 'Opinion: nstemi', '2026-04-20 07:01:46'),
(500, 157, NULL, NULL, 'pending', 'ECG created manually', '2026-04-25 03:44:00'),
(501, 157, NULL, NULL, 'assigned', 'Auto-assigned duty doctor', '2026-04-25 03:44:00'),
(502, 157, NULL, 'pending', 'closed', 'Image closed via API', '2026-04-25 04:20:24'),
(503, 158, NULL, NULL, 'pending', 'ECG created manually', '2026-04-25 04:20:38'),
(504, 158, NULL, NULL, 'assigned', 'Auto-assigned duty doctor', '2026-04-25 04:20:38'),
(505, 158, NULL, 'pending', 'closed', 'Image closed via API', '2026-04-25 04:21:05'),
(506, 159, NULL, NULL, 'pending', 'ECG created manually', '2026-04-25 04:21:54'),
(507, 159, NULL, NULL, 'assigned', 'Auto-assigned duty doctor', '2026-04-25 04:21:54'),
(508, 159, NULL, 'pending', 'closed', 'Image closed via API', '2026-04-25 04:36:54'),
(509, 160, NULL, NULL, 'pending', 'ECG created manually', '2026-05-06 07:35:08'),
(510, 160, NULL, NULL, 'assigned', 'Auto-assigned duty doctor', '2026-05-06 07:35:08'),
(511, 160, NULL, 'pending', 'closed', 'Image closed via API', '2026-05-06 07:50:09'),
(512, 161, 61, NULL, 'pending', 'ECG created manually', '2026-05-07 03:10:04'),
(513, 161, 61, NULL, 'assigned', 'Auto-assigned duty doctor', '2026-05-07 03:10:04'),
(514, 161, 60, NULL, 'completed', 'All images completed (Bulk Update)', '2026-05-07 03:11:45'),
(515, 161, 60, NULL, 'image_completed', 'Opinion: Its not a big thing..!', '2026-05-07 03:11:45'),
(516, 162, 71, NULL, 'pending', 'ECG created manually', '2026-06-02 08:46:34'),
(517, 162, 71, NULL, 'assigned', 'Auto-assigned duty doctor', '2026-06-02 08:46:34'),
(518, 162, 80, NULL, 'completed', 'All images completed (Bulk Update)', '2026-06-02 08:49:02'),
(519, 162, 80, NULL, 'image_completed', 'Opinion: normal', '2026-06-02 08:49:02'),
(520, 163, 65, NULL, 'pending', 'ECG created manually', '2026-06-03 06:17:48'),
(521, 163, 65, NULL, 'assigned', 'Auto-assigned duty doctor', '2026-06-03 06:17:48'),
(522, 163, 81, NULL, 'completed', 'All images completed (Bulk Update)', '2026-06-03 06:18:19'),
(523, 163, 81, NULL, 'image_completed', 'Opinion: normal', '2026-06-03 06:18:19'),
(524, 164, 65, NULL, 'pending', 'ECG created manually', '2026-06-03 06:19:22'),
(525, 164, 65, NULL, 'assigned', 'Auto-assigned duty doctor', '2026-06-03 06:19:22'),
(526, 164, 81, NULL, 'completed', 'All images completed (Bulk Update)', '2026-06-03 06:19:52'),
(527, 164, 81, NULL, 'image_completed', 'Opinion: normal', '2026-06-03 06:19:52'),
(528, 165, 65, NULL, 'pending', 'ECG created manually', '2026-06-03 06:20:36'),
(529, 165, 65, NULL, 'assigned', 'Auto-assigned duty doctor', '2026-06-03 06:20:36'),
(530, 165, 81, NULL, 'completed', 'All images completed (Bulk Update)', '2026-06-03 06:22:14'),
(531, 165, 81, NULL, 'image_completed', 'Opinion: good', '2026-06-03 06:22:14'),
(532, 166, 67, NULL, 'pending', 'ECG created manually', '2026-06-03 06:45:16'),
(533, 166, 67, NULL, 'assigned', 'Auto-assigned duty doctor', '2026-06-03 06:45:16'),
(534, 166, 81, NULL, 'completed', 'All images completed (Bulk Update)', '2026-06-03 06:45:44'),
(535, 166, 81, NULL, 'image_completed', 'Opinion: good', '2026-06-03 06:45:44'),
(536, 167, 83, NULL, 'pending', 'ECG created manually', '2026-06-03 07:12:05'),
(537, 167, 83, NULL, 'assigned', 'Auto-assigned duty doctor', '2026-06-03 07:12:05'),
(538, 167, 81, NULL, 'completed', 'All images completed (Bulk Update)', '2026-06-03 07:15:13'),
(539, 167, 81, NULL, 'image_completed', 'Opinion: this is pretty normal', '2026-06-03 07:15:13'),
(540, 168, 86, NULL, 'pending', 'ECG created manually', '2026-06-16 06:46:46'),
(541, 168, 86, NULL, 'assigned', 'Auto-assigned duty doctor', '2026-06-16 06:46:46'),
(542, 168, NULL, 'pending', 'closed', 'Image closed via API', '2026-06-16 07:01:48'),
(543, 169, 86, NULL, 'pending', 'ECG created manually', '2026-07-21 06:03:48'),
(544, 169, 86, NULL, 'assigned', 'Auto-assigned duty doctor', '2026-07-21 06:03:48'),
(545, 169, 81, NULL, 'completed', 'All images completed (Bulk Update)', '2026-07-21 12:04:19'),
(546, 169, 81, NULL, 'image_completed', 'Opinion: LVH With Deep T inversions ? HCM', '2026-07-21 12:04:19'),
(547, 170, 90, NULL, 'pending', 'ECG created manually', '2026-08-05 05:18:17'),
(548, 170, 90, NULL, 'assigned', 'Auto-assigned duty doctor', '2026-08-05 05:18:17'),
(549, 170, 91, NULL, 'completed', 'All images completed (Bulk Update)', '2026-08-06 04:18:04'),
(550, 170, 91, NULL, 'image_completed', 'Opinion: hi', '2026-08-06 04:18:04'),
(551, 171, 90, NULL, 'pending', 'ECG created manually', '2026-08-06 04:19:53'),
(552, 171, 90, NULL, 'assigned', 'Auto-assigned duty doctor', '2026-08-06 04:19:53'),
(553, 171, 81, NULL, 'completed', 'All images completed (Bulk Update)', '2026-08-06 04:21:23'),
(554, 171, 81, NULL, 'image_completed', 'Opinion: normal testing', '2026-08-06 04:21:23'),
(555, 172, 90, NULL, 'pending', 'ECG created manually', '2026-08-06 04:27:23'),
(556, 172, 90, NULL, 'assigned', 'Auto-assigned duty doctor', '2026-08-06 04:27:23'),
(557, 172, 81, NULL, 'completed', 'All images completed (Bulk Update)', '2026-08-06 04:28:16'),
(558, 172, 81, NULL, 'image_completed', 'Opinion: test', '2026-08-06 04:28:16'),
(559, 173, 90, NULL, 'pending', 'ECG created manually', '2026-08-06 04:33:48'),
(560, 173, 90, NULL, 'assigned', 'Auto-assigned duty doctor', '2026-08-06 04:33:48'),
(561, 173, 1, NULL, 'completed', 'All images completed (Bulk Update)', '2026-08-07 04:35:14'),
(562, 173, 1, NULL, 'image_completed', 'Opinion: hi', '2026-08-07 04:35:14');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(191) DEFAULT NULL,
  `email` varchar(191) NOT NULL,
  `phone` text DEFAULT NULL,
  `is_duty` tinyint(1) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','doctor','technician','pg','user') NOT NULL DEFAULT 'technician',
  `fcm_token` varchar(512) DEFAULT NULL,
  `blocked` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `is_duty`, `active`, `created_at`, `password_hash`, `role`, `fcm_token`, `blocked`) VALUES
(1, 'Admin User', 'admin@example.com', NULL, 0, 1, '2025-11-10 07:21:36', '$2y$10$T.Og.1aiccB5LwJDnvGPGOZfRObzk0VNPC3ACbgE6vNGl17dMmUWG', 'admin', 'fuXORD6VQj21WVHB6EjVA_:APA91bFLOUhObAw8eVehHsJH95YOITokHjmZDtL4tywbFye10dfTSSJCwWoTivu0eUbKt678no5BreZVyNK4lukQxLbPPHMS8NiteeLrupTKM8l_8Z9Z_bQ', 0),
(60, 'Test Doctor', 'test@doctor.com', NULL, 0, 1, '2026-05-07 03:01:33', '$2y$10$gKC/MMpJapFF/CkpuNAdBesoBQjUvEf4MKwcxMdinim6mH8.0HdkS', 'doctor', 'egeksIegQ5iptALJbknYUN:APA91bH1x6ZwFxY5Tfeb4cnYldNfZIaHEXblnWh_XbDWjK5d3HIgThQG4U3ZYM4PkLLeQmzg6RBbpcMB1PoBEmCVYTr15TCni3twlDtYqzmLYjuWbHkGWoM', 0),
(61, 'clinic test doctor', 'clinic@doctor.com', '1234567891', 0, 1, '2026-05-07 03:04:01', '$2y$10$sSQjuZsF3Mo2GG8DV5BwmuNHAlUrBRypxk.Roegj0j7UddaQZZd9O', 'user', 'egeksIegQ5iptALJbknYUN:APA91bH1x6ZwFxY5Tfeb4cnYldNfZIaHEXblnWh_XbDWjK5d3HIgThQG4U3ZYM4PkLLeQmzg6RBbpcMB1PoBEmCVYTr15TCni3twlDtYqzmLYjuWbHkGWoM', 0),
(62, 'Dharshini', 'dharshinisivakumar42@gmail.com', '9080195540', 0, 1, '2026-05-14 03:59:33', '$2y$10$3ZxYjm.69pOjPdes1i0Ekuh34asnlxIYko8SeCLjXxkqJp8Lxu042', 'user', 'exkoSYjvSEuOIDlcfA6WpL:APA91bGwVqx126fxtsqVS3ikKZh8LQ2py4Y9SzMpCWAHgTODxyhzYd75UhDM9d_xfJFIlRJUi1Wfa2vqrfayjo0I7po7YV4C0_l9T0U-y9Lf-RvlcANx7IE', 0),
(63, 'Negha', 'rnegha2710@gmail.com', '9361967623', 0, 1, '2026-05-14 04:01:19', '$2y$10$4e8c8tWiP4v.Gwf.YE99RO6v44zAAAjbZr5j8/Pc0yN2Z6dE8pLCq', 'user', 'fU2FCcidRMygU8wfQFSBMl:APA91bHYR65aiIBn7GEhNpMgZHYdxuHX8sYycSrkbeRx78paax8zpTMkNJHLGUAjWVzBxxcBBaovkJX517SG0ELg2gabz-vPnkZDR_h3gh4yUDhyUl5aos4', 0),
(64, 'Nandhini.j', 'nandhujaya005@gmail.com', '7200355812', 0, 1, '2026-05-14 04:03:53', '$2y$10$bNP7XvSten9NnMHqzespCOGjYQONFVUpyOjShq4hIaSPrt/wh1.RG', 'user', NULL, 0),
(65, 'Soniya.N', 'soniyan2310@gmail.com', '9384216502', 0, 1, '2026-05-14 04:06:06', '$2y$10$WdYwoYeLnG/SHmY8N/raput2O9H4FVE9M/WaGHwuFh8NdWB.zyu5G', 'user', 'dVuS_JDbRxWPTGqYDzpzqW:APA91bGXtiDTCSh6QqM7SbGUVQtxK9OMFUIEsaG0aig6fp1aNmsiCdPXYmG0zsnEhNLSiEOusPhUsFjWNGaxd0nr3DoEwcLt6PSozVWUqjeRXMH9GAEVvNE', 0),
(66, 'kaviya', 'kavisaim2005@gmail.com', '9176960796', 0, 1, '2026-05-14 04:06:32', '$2y$10$U4r9lWLBKteC7Nt9R9BBAu.7doS9HIFw7TSAPsT6.e4d29woXM/4a', 'user', NULL, 0),
(67, 'kaviya', 'kaviya170325@gmail.com', '9176960796', 0, 1, '2026-05-14 04:08:15', '$2y$10$gaScEKvC0vR8XkZCSTNJxOXqgoJ6WKIyvWQU.LPwVpcWUs4VtV2zS', 'user', 'dAI3Iu1cQPm0me7h3bTaXj:APA91bGvdiNPc-lAZmXP9yD1i6EoVAkH65YNIJRL2EMXl5_7aSvFjsYyc0hqxK-GgJmZel_TWZZUembhlBEuuo8GB2aeMplvT98PFjG7JGxTy2Y64GsGHLI', 0),
(68, 'Vinitha', 'vinitha.s24122004@gmail.com', '7200247747', 0, 1, '2026-05-14 04:08:58', '$2y$10$vUALJdsQQPHcgvkPo3o3ReYV1Q9nFHbf8C88diFE7Cb6b5wyV0aq2', 'user', 'eZCnEnQHQaW8IGABamy08T:APA91bG6BISTe3wMLUbh80AJseGn3RWoqYjzd3MvmXa6n82u1WEZpMkabOqiqboSF8BPpQitYwBf3ZmojXY3DnZlVduSo0OEKbigJP4myCQRzg4xiTyV7jA', 0),
(69, 'Gopika', 'gopika3005k@gmail.com', '8778133692', 0, 1, '2026-05-14 04:22:33', '$2y$10$aNXZmTX0Wo2Ewc847AIQaeLvnzo2xXzaSFCztPfm5hWYGmqGot5WK', 'user', NULL, 0),
(70, 'Saranya.R', 'saranyanishwin2004@gmail.com', '9894889974', 0, 1, '2026-05-14 05:07:07', '$2y$10$HCIQrRb8V35AlBE0za6ckeKX/pnu8tmh1EubPxPUKTfbufMn2k/b6', 'user', 'dtQFwmJKTz-SM9UJmgmTMv:APA91bHa9nq9Ky53lsQRfUiAwtJLJrGW6hLxqyem44tovsL7tQLlbVEhVpKJZBZWw2OIEG_voOEZafrvPSj2bZE3KUHK1c22zlQQO-oAB1ybYD6udUderc8', 0),
(71, 'oviya.N', 'oviyanarasiman@gmail.com', '7845945176', 0, 1, '2026-05-14 06:37:24', '$2y$10$95bXjYNWadc.UxN9leLndePXH9In0.Nufb0vcH468WWGZp3icPa1K', 'user', 'dNnt2c6GQ1aAxwAX0KPezw:APA91bHAvgvU7bPAO-oR2kAyGqWvaMTEN2jdVB25hZA6xeaFfqmX9M88gqbn9qawqJ7hfWxQv8rBFTg3WbvJSC57N88F6sHh2VMANNFkxtybVJpkk3N_h10', 0),
(73, 'Ayushi', 'mungad05@gmail.com', NULL, 0, 1, '2026-05-15 03:23:54', '$2y$10$7Ydipr3ZIqN.5uHojCyKF.1BlXmTsOxgWndgdfRjPIhAAYhsfS6s6', 'doctor', NULL, 0),
(74, 'Rishab', '000000000@email.com', NULL, 0, 1, '2026-05-15 03:33:30', '$2y$10$FuCBu6ZqXiawouVJBqTAz.hdsEW6mC9cXJh5fhr9eOjfWrLmesSFa', 'doctor', NULL, 0),
(75, 'Govaradhan', 'fjhadfjhadvbfjhv@fakhfhbad.acojdnf', NULL, 0, 1, '2026-05-15 03:42:16', '$2y$10$z8pX8TR/NWM3kbHVfJmNRe.11YmIzZ/Oce9YYN6y0iLspSpvoJFNG', 'doctor', NULL, 0),
(76, 'muhammed', 'muhammed95387@gmail.com', NULL, 0, 1, '2026-05-15 03:52:57', '$2y$10$6jKLc58vAur4tNUkZryGFO2stmRMJYHZ3otke.7zXGfacThPlv9J6', 'doctor', NULL, 0),
(77, 'Nadhakumar', 'drnandhakumar1@gmail.com', NULL, 0, 1, '2026-05-15 03:53:35', '$2y$10$ke1ckaAR6k8konxT72yLr.je6wKl9oSuypuEBrv1SRJvgN5lPTbt6', 'doctor', NULL, 0),
(78, 'Vignesh', 'cheyyarvignesh@gmail.com', NULL, 0, 1, '2026-05-19 05:31:11', '$2y$10$oBLNZ4fHoVNst6ISfEy3r./4SELtnT29KG0I5QOe4Q4L7gQgRgnyq', 'doctor', NULL, 0),
(79, 'Lavanya', 'lavanyasamyu@gmail.com', '9677071262', 0, 1, '2026-06-02 04:48:20', '$2y$10$/ZdvEzFmkYB7cHv6YAlukOMIsm.E5YSZTqH1J2QbszA3fWdEkJlMi', 'user', NULL, 0),
(80, 'Aravind R', 'aravind9349rajendiran@gmail.com', NULL, 0, 1, '2026-06-02 08:44:01', '$2y$10$FcPNX.SCrb7Ko88N9E6jquhYQv0Iux4SDhpvoJF9zk6ZoUpdKaZzC', 'doctor', NULL, 0),
(81, 'Aravind Ganesh', 'draravindcardiology@gmail.com', NULL, 1, 1, '2026-06-03 06:15:17', '$2y$10$EofQ78JORF1bz6liKaosv.yib6Cwi6gRxHgjFMN.U/CziyW8T8ufm', 'doctor', 'eQVpsK7DSYCRIxuKsZPV-i:APA91bE__HFQPlmOzjs8Hko8lJj_zaGhXOM-tpYMfzYEEyO6eun00TFp_dK1twrtXQTPvsiv5jHgwcrWOd1xxQ9Zr3Ai9ImA6iBYMdiWAC0NnNWQaai-dgQ', 0),
(82, 'Angel Sheeba R', 'luvlyangel21@gmail.com', '9900252715', 0, 1, '2026-06-03 07:06:45', '$2y$10$fgNm0w9pckGIFYqh4lMS2OouFdCOnhkIVzfkSw2cdHPpL1V5CGiyu', 'user', NULL, 0),
(83, 'aravind', 'arvind1695@gmail.com', '9597124900', 0, 1, '2026-06-03 07:07:32', '$2y$10$UYbHiKqkIHK5U6e0c9FyHuhMX7Jv5Qr.dVLxD0SZC4QPM1dODTzfi', 'user', 'eQVpsK7DSYCRIxuKsZPV-i:APA91bE__HFQPlmOzjs8Hko8lJj_zaGhXOM-tpYMfzYEEyO6eun00TFp_dK1twrtXQTPvsiv5jHgwcrWOd1xxQ9Zr3Ai9ImA6iBYMdiWAC0NnNWQaai-dgQ', 0),
(84, 'Gowtham', 'gotham431@gmail.com', '9677769775', 0, 1, '2026-06-03 07:19:54', '$2y$10$VKZ8/Pkj.eK9UwaI.Pva/ekcvMntro0OhpdAvYerQLdUYAaC5aGEe', 'user', NULL, 0),
(85, 'Nandakishore', 'nandakishorep212@gmail.com', '1234567891', 0, 1, '2026-06-03 07:23:12', '$2y$10$utye/Mj.b3SeRtXH1ETw2elRVOwV/Gq0VaJsoC2TZZxbLUJsQNTbm', 'user', 'dUYj35j1QRGbCprhkgYTT7:APA91bEBO3dXk9Uj2BRLezU68T9WG3j5clI6G6LnQDbE6wDXydu4yX5DPuqr1cngxuFmEfO020IYZlLrnElNO5OxqCIYiCdnFcigJsS-bgfHQu-0O9gSjVc', 0),
(86, 'sivakumar', 'stkholmes953@gmail.com', '7904557728', 0, 1, '2026-06-13 09:00:09', '$2y$10$JN7/4rUVQImr1QaVUNQG4eZ/LTvuGMayDOQc2C3LK9AFAP/DwQBSi', 'user', 'fdNTFgEhRUCRGtBICW-483:APA91bHZnjb9vMNsKH8KbRPQnKCs9FCCJM3H9gSWS_2YPi3If4eYnDtFfni3clDAGEzJPnLs31-S67IupXA007pfdiirGy_zFHIExW3esligWtXyeT1Dwno', 0),
(87, 'Udhav', 'udhavsatish4@gmail.com', '8903030303', 0, 1, '2026-07-16 03:51:53', '$2y$10$ZKBZ/HBW2EXG080IlSJwLeU4nnGatn1PxM6XR8DZxoSiA4LDWl0py', 'user', NULL, 0),
(88, 'monishkumar', 'monishkumar@gmail.com', '9361230121', 0, 1, '2026-07-20 10:08:45', '$2y$10$nXK1w/r0IqlF6FoCzR/Fb.yqQLQiYcsuazVAzM15Hwl/vNaCB9KkS', 'user', NULL, 0),
(89, 'Premkumar', 'premkumarspk2k@gmail.com', '8667051163', 0, 1, '2026-08-05 04:50:33', '$2y$10$DwmNaUeH0PRNlPfnthYVdekQZ7ge.V397nqPESqw3iLRhmpg4VaCW', 'user', NULL, 0),
(90, 'sathish@doc.com', 'sathish@doc.com', '1234567890', 0, 1, '2026-08-05 05:16:12', '$2y$10$./KSqXcPUNsOypLzPQEHVe78Rucz/aq8vuyGrrSFlnUYtRIpxll7q', 'user', 'dUYj35j1QRGbCprhkgYTT7:APA91bEBO3dXk9Uj2BRLezU68T9WG3j5clI6G6LnQDbE6wDXydu4yX5DPuqr1cngxuFmEfO020IYZlLrnElNO5OxqCIYiCdnFcigJsS-bgfHQu-0O9gSjVc', 0),
(91, 'doc@clinic.com', 'doc@clinic.com', NULL, 0, 1, '2026-08-05 05:17:19', '$2y$10$RvReLZBm4O3DODA.vZXpoO9C4fSrw.VXgenLvmuOVxWLmjgDkccDy', 'doctor', 'fuXORD6VQj21WVHB6EjVA_:APA91bFLOUhObAw8eVehHsJH95YOITokHjmZDtL4tywbFye10dfTSSJCwWoTivu0eUbKt678no5BreZVyNK4lukQxLbPPHMS8NiteeLrupTKM8l_8Z9Z_bQ', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_devices`
--

CREATE TABLE `user_devices` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(512) NOT NULL,
  `platform` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `doctor_pg`
--
ALTER TABLE `doctor_pg`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `duty_roster`
--
ALTER TABLE `duty_roster`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_duty_user` (`user_id`),
  ADD KEY `idx_duty_date` (`duty_date`);

--
-- Indexes for table `ecg_images`
--
ALTER TABLE `ecg_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ecg_images_patient` (`patient_id`),
  ADD KEY `fk_ecg_images_tech` (`user_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_patient_patientid` (`patient_id`),
  ADD KEY `idx_patients_assigned_doctor` (`assigned_doctor_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tasks_patient` (`patient_id`),
  ADD KEY `idx_tasks_assigned_doctor` (`assigned_doctor_id`),
  ADD KEY `fk_tasks_technician` (`user_id`);

--
-- Indexes for table `task_history`
--
ALTER TABLE `task_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_task_history_task` (`task_id`),
  ADD KEY `fk_task_history_user` (`changed_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_email` (`email`),
  ADD KEY `idx_users_is_duty` (`is_duty`);

--
-- Indexes for table `user_devices`
--
ALTER TABLE `user_devices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_token` (`user_id`,`token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `doctor_pg`
--
ALTER TABLE `doctor_pg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `duty_roster`
--
ALTER TABLE `duty_roster`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ecg_images`
--
ALTER TABLE `ecg_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=174;

--
-- AUTO_INCREMENT for table `task_history`
--
ALTER TABLE `task_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=563;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `user_devices`
--
ALTER TABLE `user_devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `duty_roster`
--
ALTER TABLE `duty_roster`
  ADD CONSTRAINT `fk_duty_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ecg_images`
--
ALTER TABLE `ecg_images`
  ADD CONSTRAINT `fk_ecg_images_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ecg_images_tech` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `fk_patients_assigned_doctor` FOREIGN KEY (`assigned_doctor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `fk_tasks_assigned_doctor` FOREIGN KEY (`assigned_doctor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tasks_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tasks_technician` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `task_history`
--
ALTER TABLE `task_history`
  ADD CONSTRAINT `fk_task_history_task` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_task_history_user` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
