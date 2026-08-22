-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 15, 2026 at 09:52 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `web_onlinefea`
--

-- --------------------------------------------------------

--
-- Table structure for table `active_sessions`
--

CREATE TABLE `active_sessions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `browser` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platform` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_activity` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `active_sessions`
--

INSERT INTO `active_sessions` (`id`, `user_id`, `session_id`, `device_id`, `ip_address`, `user_agent`, `browser`, `platform`, `device_name`, `is_active`, `last_activity`, `created_at`, `updated_at`) VALUES
(1, 1, 'wCzCfuCGAbM7ngflAurU1heKW90wG2DST3YSUypk', '36cd38c2f07ae22a4ae1272f86671e22', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'Chrome', 'Windows', 'Desktop', 1, '2026-08-07 11:41:30', '2026-08-07 11:41:30', '2026-08-07 11:41:30'),
(2, 2, 'V9etBFdHT3ZDIXVKHSnOzALTMSNFRml5yPGUqD6j', '36cd38c2f07ae22a4ae1272f86671e22', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'Chrome', 'Windows', 'Desktop', 1, '2026-08-10 01:58:59', '2026-08-07 11:43:47', '2026-08-10 01:58:59'),
(3, 3, 'vsrdJHMOGnR5qAKze4oaV6ByI7uTVNIEpUk5G37T', '36cd38c2f07ae22a4ae1272f86671e22', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'Chrome', 'Windows', 'Desktop', 1, '2026-08-10 10:05:59', '2026-08-07 11:48:09', '2026-08-10 10:05:59'),
(4, 3, 'xgl0CGEEo5sZyFADrztMpvCO5enu1WZhlW01w6ED', '36cd38c2f07ae22a4ae1272f86671e22', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'Chrome', 'Windows', 'Desktop', 0, '2026-08-07 19:10:21', '2026-08-07 17:57:35', '2026-08-10 02:42:52'),
(5, 2, 'z4d7rlah7rScP0cIKjFm4QRMV1vYHS6vWITOqav0', '36cd38c2f07ae22a4ae1272f86671e22', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'Chrome', 'Windows', 'Desktop', 0, '2026-08-07 17:59:43', '2026-08-07 17:59:43', '2026-08-10 01:55:42'),
(6, 3, '76R26iF1W6t6kB90iH4MhLtjMcLylHJm70wHzukZ', '36cd38c2f07ae22a4ae1272f86671e22', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'Chrome', 'Windows', 'Desktop', 0, '2026-08-07 21:20:17', '2026-08-07 21:20:17', '2026-08-10 02:42:52'),
(7, 2, '62aL3khriytyf2LRZHpYeDdwbnEQ7qoX0K6toZQO', '36cd38c2f07ae22a4ae1272f86671e22', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'Chrome', 'Windows', 'Desktop', 0, '2026-08-10 01:55:42', '2026-08-10 01:55:42', '2026-08-10 01:55:42'),
(8, 3, 'pEhpXDlDd3SEbTuqQJu7ztowmlL2leczBPiiH9VI', '36cd38c2f07ae22a4ae1272f86671e22', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'Chrome', 'Windows', 'Desktop', 0, '2026-08-10 02:02:15', '2026-08-10 02:02:15', '2026-08-10 02:42:52'),
(9, 3, '4TItPYnh8z1rQHRFGy8HYiYN492dBtJNSyeNhacE', '36cd38c2f07ae22a4ae1272f86671e22', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'Chrome', 'Windows', 'Desktop', 0, '2026-08-10 02:25:12', '2026-08-10 02:22:57', '2026-08-10 02:42:52'),
(10, 3, 'ly7xM5x3EukgbdIvEZB6PcgjdypbXJDpZklOf3dy', '36cd38c2f07ae22a4ae1272f86671e22', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'Chrome', 'Windows', 'Desktop', 0, '2026-08-10 02:25:46', '2026-08-10 02:25:46', '2026-08-10 02:42:52'),
(11, 3, 'zobGnXTdkur14mTsjLX7SW7tHFlwQQ0JrdK07MRq', '36cd38c2f07ae22a4ae1272f86671e22', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'Chrome', 'Windows', 'Desktop', 0, '2026-08-10 02:26:23', '2026-08-10 02:26:23', '2026-08-10 02:42:52'),
(12, 3, 'mj8K0ojaqVnZ2xy8RnyiT0D6QHLw8pxhommKeTHd', '36cd38c2f07ae22a4ae1272f86671e22', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'Chrome', 'Windows', 'Desktop', 0, '2026-08-10 02:26:44', '2026-08-10 02:26:44', '2026-08-10 02:42:52'),
(13, 3, 'TV2StEhj4Zbd2HavAuWAQTQZGbltWx6qAHTRtW6b', '36cd38c2f07ae22a4ae1272f86671e22', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'Chrome', 'Windows', 'Desktop', 0, '2026-08-10 02:28:23', '2026-08-10 02:27:21', '2026-08-10 02:42:52'),
(14, 3, '8DijHSDfSROT0l8fnAqjmHJSPdT2A73pXbpWOuUp', '36cd38c2f07ae22a4ae1272f86671e22', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'Chrome', 'Windows', 'Desktop', 0, '2026-08-10 02:42:52', '2026-08-10 02:42:52', '2026-08-10 02:42:52');

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_id` bigint UNSIGNED DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `properties` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `model_type`, `model_id`, `description`, `properties`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 1, 'register', 'App\\Models\\User', 1, NULL, '{\"role\": \"student\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 11:41:30', '2026-08-07 11:41:30'),
(2, 1, 'logout', 'App\\Models\\User', 1, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 11:42:24', '2026-08-07 11:42:24'),
(3, 2, 'register', 'App\\Models\\User', 2, NULL, '{\"role\": \"instructor\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 11:43:47', '2026-08-07 11:43:47'),
(4, 3, 'login', 'App\\Models\\User', 3, NULL, '{\"remember\": true}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 11:48:09', '2026-08-07 11:48:09'),
(5, 3, 'approve_instructor', 'App\\Models\\User', 2, NULL, '[]', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 11:48:29', '2026-08-07 11:48:29'),
(6, 3, 'create_category', 'App\\Models\\Category', 1, NULL, '{\"slug\": \"bst-san-dien\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 12:32:06', '2026-08-07 12:32:06'),
(7, 2, 'login', 'App\\Models\\User', 2, NULL, '{\"remember\": false}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 17:59:43', '2026-08-07 17:59:43'),
(8, 2, 'login', 'App\\Models\\User', 2, NULL, '{\"remember\": false}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 01:55:42', '2026-08-10 01:55:42'),
(9, 2, 'logout', 'App\\Models\\User', 2, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 01:59:19', '2026-08-10 01:59:19'),
(10, 3, 'login', 'App\\Models\\User', 3, NULL, '{\"remember\": false}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 02:02:15', '2026-08-10 02:02:15'),
(11, 3, 'logout', 'App\\Models\\User', 3, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 02:22:51', '2026-08-10 02:22:51'),
(12, 3, 'login', 'App\\Models\\User', 3, NULL, '{\"remember\": false}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 02:25:46', '2026-08-10 02:25:46'),
(13, 3, 'login', 'App\\Models\\User', 3, NULL, '{\"remember\": false}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 02:26:44', '2026-08-10 02:26:44'),
(14, 3, 'login', 'App\\Models\\User', 3, NULL, '{\"remember\": false}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 02:42:52', '2026-08-10 02:42:52');

-- --------------------------------------------------------

--
-- Table structure for table `ai_chat_messages`
--

CREATE TABLE `ai_chat_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `lesson_id` bigint UNSIGNED DEFAULT NULL,
  `role` enum('user','assistant') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_summaries`
--

CREATE TABLE `ai_summaries` (
  `id` bigint UNSIGNED NOT NULL,
  `lesson_id` bigint UNSIGNED NOT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'vi',
  `source_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED DEFAULT NULL,
  `lesson_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `instructions` text COLLATE utf8mb4_unicode_ci,
  `due_date` timestamp NULL DEFAULT NULL,
  `max_score` int UNSIGNED NOT NULL DEFAULT '100',
  `passing_score` int UNSIGNED NOT NULL DEFAULT '70',
  `due_days` int UNSIGNED DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT '1',
  `allowed_file_types` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pdf,doc,docx,zip',
  `maximum_file_size` int UNSIGNED NOT NULL DEFAULT '10240',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `badges`
--

CREATE TABLE `badges` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `points_required` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-123viet@gmail.com|127.0.0.1', 'i:1;', 1786352523),
('laravel-cache-123viet@gmail.com|127.0.0.1:timer', 'i:1786352523;', 1786352523),
('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba', 'i:1;', 1786355032),
('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba:timer', 'i:1786355032;', 1786355032),
('laravel-cache-admin2006@gmail.com|127.0.0.1', 'i:1;', 1786352507),
('laravel-cache-admin2006@gmail.com|127.0.0.1:timer', 'i:1786352507;', 1786352507),
('laravel-cache-instructor@example.com|127.0.0.1', 'i:1;', 1786352160),
('laravel-cache-instructor@example.com|127.0.0.1:timer', 'i:1786352160;', 1786352160),
('laravel-cache-khachhangfea@gmail.com|127.0.0.1', 'i:1;', 1786352446),
('laravel-cache-khachhangfea@gmail.com|127.0.0.1:timer', 'i:1786352446;', 1786352446),
('laravel-cache-nguyenducv2006@gmail.com|127.0.0.1', 'i:2;', 1786150767),
('laravel-cache-nguyenducv2006@gmail.com|127.0.0.1:timer', 'i:1786150767;', 1786150767),
('laravel-cache-nhanviena@gmail.com|127.0.0.1', 'i:1;', 1786352170),
('laravel-cache-nhanviena@gmail.com|127.0.0.1:timer', 'i:1786352170;', 1786352170),
('laravel-cache-system_setting_default_commission_rate', 'd:20;', 1786355809),
('laravel-cache-vietnam_banks_list', 'a:65:{i:0;a:5:{s:4:\"code\";s:3:\"ICB\";s:9:\"shortName\";s:10:\"VietinBank\";s:4:\"name\";s:42:\"Ngân hàng TMCP Công thương Việt Nam\";s:3:\"bin\";s:6:\"970415\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/ICB.png\";}i:1;a:5:{s:4:\"code\";s:3:\"VCB\";s:9:\"shortName\";s:11:\"Vietcombank\";s:4:\"name\";s:46:\"Ngân hàng TMCP Ngoại Thương Việt Nam\";s:3:\"bin\";s:6:\"970436\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/VCB.png\";}i:2;a:5:{s:4:\"code\";s:4:\"BIDV\";s:9:\"shortName\";s:4:\"BIDV\";s:4:\"name\";s:56:\"Ngân hàng TMCP Đầu tư và Phát triển Việt Nam\";s:3:\"bin\";s:6:\"970418\";s:4:\"logo\";s:34:\"https://cdn.vietqr.io/img/BIDV.png\";}i:3;a:5:{s:4:\"code\";s:3:\"VBA\";s:9:\"shortName\";s:8:\"Agribank\";s:4:\"name\";s:67:\"Ngân hàng Nông nghiệp và Phát triển Nông thôn Việt Nam\";s:3:\"bin\";s:6:\"970405\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/VBA.png\";}i:4;a:5:{s:4:\"code\";s:3:\"OCB\";s:9:\"shortName\";s:3:\"OCB\";s:4:\"name\";s:32:\"Ngân hàng TMCP Phương Đông\";s:3:\"bin\";s:6:\"970448\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/OCB.png\";}i:5;a:5:{s:4:\"code\";s:2:\"MB\";s:9:\"shortName\";s:6:\"MBBank\";s:4:\"name\";s:29:\"Ngân hàng TMCP Quân đội\";s:3:\"bin\";s:6:\"970422\";s:4:\"logo\";s:32:\"https://cdn.vietqr.io/img/MB.png\";}i:6;a:5:{s:4:\"code\";s:3:\"TCB\";s:9:\"shortName\";s:11:\"Techcombank\";s:4:\"name\";s:41:\"Ngân hàng TMCP Kỹ thương Việt Nam\";s:3:\"bin\";s:6:\"970407\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/TCB.png\";}i:7;a:5:{s:4:\"code\";s:3:\"ACB\";s:9:\"shortName\";s:3:\"ACB\";s:4:\"name\";s:25:\"Ngân hàng TMCP Á Châu\";s:3:\"bin\";s:6:\"970416\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/ACB.png\";}i:8;a:5:{s:4:\"code\";s:3:\"VPB\";s:9:\"shortName\";s:6:\"VPBank\";s:4:\"name\";s:44:\"Ngân hàng TMCP Việt Nam Thịnh Vượng\";s:3:\"bin\";s:6:\"970432\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/VPB.png\";}i:9;a:5:{s:4:\"code\";s:3:\"TPB\";s:9:\"shortName\";s:6:\"TPBank\";s:4:\"name\";s:28:\"Ngân hàng TMCP Tiên Phong\";s:3:\"bin\";s:6:\"970423\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/TPB.png\";}i:10;a:5:{s:4:\"code\";s:3:\"STB\";s:9:\"shortName\";s:9:\"Sacombank\";s:4:\"name\";s:40:\"Ngân hàng TMCP Sài Gòn Thương Tín\";s:3:\"bin\";s:6:\"970403\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/STB.png\";}i:11;a:5:{s:4:\"code\";s:3:\"HDB\";s:9:\"shortName\";s:6:\"HDBank\";s:4:\"name\";s:58:\"Ngân hàng TMCP Phát triển Thành phố Hồ Chí Minh\";s:3:\"bin\";s:6:\"970437\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/HDB.png\";}i:12;a:5:{s:4:\"code\";s:4:\"VCCB\";s:9:\"shortName\";s:15:\"VietCapitalBank\";s:4:\"name\";s:31:\"Ngân hàng TMCP Bản Việt\";s:3:\"bin\";s:6:\"970454\";s:4:\"logo\";s:34:\"https://cdn.vietqr.io/img/VCCB.png\";}i:13;a:5:{s:4:\"code\";s:3:\"SCB\";s:9:\"shortName\";s:3:\"SCB\";s:4:\"name\";s:26:\"Ngân hàng TMCP Sài Gòn\";s:3:\"bin\";s:6:\"970429\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/SCB.png\";}i:14;a:5:{s:4:\"code\";s:3:\"VIB\";s:9:\"shortName\";s:3:\"VIB\";s:4:\"name\";s:40:\"Ngân hàng TMCP Quốc tế Việt Nam\";s:3:\"bin\";s:6:\"970441\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/VIB.png\";}i:15;a:5:{s:4:\"code\";s:3:\"SHB\";s:9:\"shortName\";s:3:\"SHB\";s:4:\"name\";s:38:\"Ngân hàng TMCP Sài Gòn - Hà Nội\";s:3:\"bin\";s:6:\"970443\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/SHB.png\";}i:16;a:5:{s:4:\"code\";s:3:\"EIB\";s:9:\"shortName\";s:8:\"Eximbank\";s:4:\"name\";s:48:\"Ngân hàng TMCP Xuất Nhập khẩu Việt Nam\";s:3:\"bin\";s:6:\"970431\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/EIB.png\";}i:17;a:5:{s:4:\"code\";s:3:\"MSB\";s:9:\"shortName\";s:3:\"MSB\";s:4:\"name\";s:41:\"Ngân hàng TMCP Hàng Hải Việt Nam\";s:3:\"bin\";s:6:\"970426\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/MSB.png\";}i:18;a:5:{s:4:\"code\";s:4:\"CAKE\";s:9:\"shortName\";s:4:\"CAKE\";s:4:\"name\";s:66:\"TMCP Việt Nam Thịnh Vượng - Ngân hàng số CAKE by VPBank\";s:3:\"bin\";s:6:\"546034\";s:4:\"logo\";s:34:\"https://cdn.vietqr.io/img/CAKE.png\";}i:19;a:5:{s:4:\"code\";s:5:\"Ubank\";s:9:\"shortName\";s:5:\"Ubank\";s:4:\"name\";s:67:\"TMCP Việt Nam Thịnh Vượng - Ngân hàng số Ubank by VPBank\";s:3:\"bin\";s:6:\"546035\";s:4:\"logo\";s:35:\"https://cdn.vietqr.io/img/UBANK.png\";}i:20;a:5:{s:4:\"code\";s:8:\"VTLMONEY\";s:9:\"shortName\";s:12:\"ViettelMoney\";s:4:\"name\";s:108:\"Tổng Công ty Dịch vụ số Viettel - Chi nhánh tập đoàn công nghiệp viễn thông Quân Đội\";s:3:\"bin\";s:6:\"971005\";s:4:\"logo\";s:42:\"https://cdn.vietqr.io/img/VIETTELMONEY.png\";}i:21;a:5:{s:4:\"code\";s:4:\"TIMO\";s:9:\"shortName\";s:4:\"Timo\";s:4:\"name\";s:62:\"Ngân hàng số Timo by Ban Viet Bank (Timo by Ban Viet Bank)\";s:3:\"bin\";s:6:\"963388\";s:4:\"logo\";s:58:\"https://vietqr.net/portal-service/resources/icons/TIMO.png\";}i:22;a:5:{s:4:\"code\";s:9:\"VNPTMONEY\";s:9:\"shortName\";s:9:\"VNPTMoney\";s:4:\"name\";s:10:\"VNPT Money\";s:3:\"bin\";s:6:\"971011\";s:4:\"logo\";s:39:\"https://cdn.vietqr.io/img/VNPTMONEY.png\";}i:23;a:5:{s:4:\"code\";s:5:\"SGICB\";s:9:\"shortName\";s:10:\"SaigonBank\";s:4:\"name\";s:41:\"Ngân hàng TMCP Sài Gòn Công Thương\";s:3:\"bin\";s:6:\"970400\";s:4:\"logo\";s:35:\"https://cdn.vietqr.io/img/SGICB.png\";}i:24;a:5:{s:4:\"code\";s:3:\"BAB\";s:9:\"shortName\";s:8:\"BacABank\";s:4:\"name\";s:25:\"Ngân hàng TMCP Bắc Á\";s:3:\"bin\";s:6:\"970409\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/BAB.png\";}i:25;a:5:{s:4:\"code\";s:4:\"momo\";s:9:\"shortName\";s:4:\"MoMo\";s:4:\"name\";s:42:\"CTCP Dịch Vụ Di Động Trực Tuyến\";s:3:\"bin\";s:6:\"971025\";s:4:\"logo\";s:34:\"https://cdn.vietqr.io/img/momo.png\";}i:26;a:5:{s:4:\"code\";s:4:\"PVDB\";s:9:\"shortName\";s:13:\"PVcomBank Pay\";s:4:\"name\";s:61:\"Ngân hàng TMCP Đại Chúng Việt Nam Ngân hàng số\";s:3:\"bin\";s:6:\"971133\";s:4:\"logo\";s:34:\"https://cdn.vietqr.io/img/PVCB.png\";}i:27;a:5:{s:4:\"code\";s:4:\"PVCB\";s:9:\"shortName\";s:9:\"PVcomBank\";s:4:\"name\";s:44:\"Ngân hàng TMCP Đại Chúng Việt Nam\";s:3:\"bin\";s:6:\"970412\";s:4:\"logo\";s:34:\"https://cdn.vietqr.io/img/PVCB.png\";}i:28;a:5:{s:4:\"code\";s:3:\"MBV\";s:9:\"shortName\";s:3:\"MBV\";s:4:\"name\";s:45:\"Ngân hàng TNHH MTV Việt Nam Hiện Đại\";s:3:\"bin\";s:6:\"970414\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/MBV.png\";}i:29;a:5:{s:4:\"code\";s:3:\"NCB\";s:9:\"shortName\";s:3:\"NCB\";s:4:\"name\";s:28:\"Ngân hàng TMCP Quốc Dân\";s:3:\"bin\";s:6:\"970419\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/NCB.png\";}i:30;a:5:{s:4:\"code\";s:5:\"SHBVN\";s:9:\"shortName\";s:11:\"ShinhanBank\";s:4:\"name\";s:39:\"Ngân hàng TNHH MTV Shinhan Việt Nam\";s:3:\"bin\";s:6:\"970424\";s:4:\"logo\";s:35:\"https://cdn.vietqr.io/img/SHBVN.png\";}i:31;a:5:{s:4:\"code\";s:3:\"ABB\";s:9:\"shortName\";s:6:\"ABBANK\";s:4:\"name\";s:25:\"Ngân hàng TMCP An Bình\";s:3:\"bin\";s:6:\"970425\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/ABB.png\";}i:32;a:5:{s:4:\"code\";s:3:\"VAB\";s:9:\"shortName\";s:9:\"VietABank\";s:4:\"name\";s:26:\"Ngân hàng TMCP Việt Á\";s:3:\"bin\";s:6:\"970427\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/VAB.png\";}i:33;a:5:{s:4:\"code\";s:3:\"NAB\";s:9:\"shortName\";s:8:\"NamABank\";s:4:\"name\";s:23:\"Ngân hàng TMCP Nam Á\";s:3:\"bin\";s:6:\"970428\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/NAB.png\";}i:34;a:5:{s:4:\"code\";s:3:\"PGB\";s:9:\"shortName\";s:6:\"PGBank\";s:4:\"name\";s:51:\"Ngân hàng TMCP Thịnh vượng và Phát triển\";s:3:\"bin\";s:6:\"970430\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/PGB.png\";}i:35;a:5:{s:4:\"code\";s:8:\"VIETBANK\";s:9:\"shortName\";s:8:\"VietBank\";s:4:\"name\";s:41:\"Ngân hàng TMCP Việt Nam Thương Tín\";s:3:\"bin\";s:6:\"970433\";s:4:\"logo\";s:38:\"https://cdn.vietqr.io/img/VIETBANK.png\";}i:36;a:5:{s:4:\"code\";s:3:\"BVB\";s:9:\"shortName\";s:11:\"BaoVietBank\";s:4:\"name\";s:29:\"Ngân hàng TMCP Bảo Việt\";s:3:\"bin\";s:6:\"970438\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/BVB.png\";}i:37;a:5:{s:4:\"code\";s:4:\"SEAB\";s:9:\"shortName\";s:7:\"SeABank\";s:4:\"name\";s:32:\"Ngân hàng TMCP Đông Nam Á\";s:3:\"bin\";s:6:\"970440\";s:4:\"logo\";s:34:\"https://cdn.vietqr.io/img/SEAB.png\";}i:38;a:5:{s:4:\"code\";s:8:\"COOPBANK\";s:9:\"shortName\";s:8:\"COOPBANK\";s:4:\"name\";s:37:\"Ngân hàng Hợp tác xã Việt Nam\";s:3:\"bin\";s:6:\"970446\";s:4:\"logo\";s:38:\"https://cdn.vietqr.io/img/COOPBANK.png\";}i:39;a:5:{s:4:\"code\";s:3:\"LPB\";s:9:\"shortName\";s:6:\"LPBank\";s:4:\"name\";s:39:\"Ngân hàng TMCP Lộc Phát Việt Nam\";s:3:\"bin\";s:6:\"970449\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/LPB.png\";}i:40;a:5:{s:4:\"code\";s:3:\"KLB\";s:9:\"shortName\";s:12:\"KienLongBank\";s:4:\"name\";s:27:\"Ngân hàng TMCP Kiên Long\";s:3:\"bin\";s:6:\"970452\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/KLB.png\";}i:41;a:5:{s:4:\"code\";s:5:\"KBank\";s:9:\"shortName\";s:5:\"KBank\";s:4:\"name\";s:43:\"Ngân hàng Đại chúng TNHH Kasikornbank\";s:3:\"bin\";s:6:\"668888\";s:4:\"logo\";s:35:\"https://cdn.vietqr.io/img/KBANK.png\";}i:42;a:5:{s:4:\"code\";s:4:\"MAFC\";s:9:\"shortName\";s:4:\"MAFC\";s:4:\"name\";s:55:\"Công ty Tài chính TNHH MTV Mirae Asset (Việt Nam) \";s:3:\"bin\";s:6:\"977777\";s:4:\"logo\";s:34:\"https://cdn.vietqr.io/img/MAFC.png\";}i:43;a:5:{s:4:\"code\";s:5:\"HLBVN\";s:9:\"shortName\";s:9:\"HongLeong\";s:4:\"name\";s:42:\"Ngân hàng TNHH MTV Hong Leong Việt Nam\";s:3:\"bin\";s:6:\"970442\";s:4:\"logo\";s:35:\"https://cdn.vietqr.io/img/HLBVN.png\";}i:44;a:5:{s:4:\"code\";s:9:\"KEBHANAHN\";s:9:\"shortName\";s:9:\"KEBHANAHN\";s:4:\"name\";s:45:\"Ngân hàng KEB Hana – Chi nhánh Hà Nội\";s:3:\"bin\";s:6:\"970467\";s:4:\"logo\";s:39:\"https://cdn.vietqr.io/img/KEBHANAHN.png\";}i:45;a:5:{s:4:\"code\";s:10:\"KEBHANAHCM\";s:9:\"shortName\";s:10:\"KEBHanaHCM\";s:4:\"name\";s:63:\"Ngân hàng KEB Hana – Chi nhánh Thành phố Hồ Chí Minh\";s:3:\"bin\";s:6:\"970466\";s:4:\"logo\";s:40:\"https://cdn.vietqr.io/img/KEBHANAHCM.png\";}i:46;a:5:{s:4:\"code\";s:8:\"CITIBANK\";s:9:\"shortName\";s:8:\"Citibank\";s:4:\"name\";s:49:\"Ngân hàng Citibank, N.A. - Chi nhánh Hà Nội\";s:3:\"bin\";s:6:\"533948\";s:4:\"logo\";s:38:\"https://cdn.vietqr.io/img/CITIBANK.png\";}i:47;a:5:{s:4:\"code\";s:3:\"CBB\";s:9:\"shortName\";s:6:\"CBBank\";s:4:\"name\";s:58:\"Ngân hàng Thương mại TNHH MTV Xây dựng Việt Nam\";s:3:\"bin\";s:6:\"970444\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/CBB.png\";}i:48;a:5:{s:4:\"code\";s:4:\"CIMB\";s:9:\"shortName\";s:4:\"CIMB\";s:4:\"name\";s:36:\"Ngân hàng TNHH MTV CIMB Việt Nam\";s:3:\"bin\";s:6:\"422589\";s:4:\"logo\";s:34:\"https://cdn.vietqr.io/img/CIMB.png\";}i:49;a:5:{s:4:\"code\";s:3:\"DBS\";s:9:\"shortName\";s:7:\"DBSBank\";s:4:\"name\";s:53:\"DBS Bank Ltd - Chi nhánh Thành phố Hồ Chí Minh\";s:3:\"bin\";s:6:\"796500\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/DBS.png\";}i:50;a:5:{s:4:\"code\";s:5:\"Vikki\";s:9:\"shortName\";s:5:\"Vikki\";s:4:\"name\";s:31:\"Ngân hàng TNHH MTV Số Vikki\";s:3:\"bin\";s:6:\"970406\";s:4:\"logo\";s:35:\"https://cdn.vietqr.io/img/Vikki.png\";}i:51;a:5:{s:4:\"code\";s:4:\"VBSP\";s:9:\"shortName\";s:4:\"VBSP\";s:4:\"name\";s:34:\"Ngân hàng Chính sách Xã hội\";s:3:\"bin\";s:6:\"999888\";s:4:\"logo\";s:34:\"https://cdn.vietqr.io/img/VBSP.png\";}i:52;a:5:{s:4:\"code\";s:3:\"GPB\";s:9:\"shortName\";s:6:\"GPBank\";s:4:\"name\";s:58:\"Ngân hàng Thương mại TNHH MTV Dầu Khí Toàn Cầu\";s:3:\"bin\";s:6:\"970408\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/GPB.png\";}i:53;a:5:{s:4:\"code\";s:5:\"KBHCM\";s:9:\"shortName\";s:10:\"KookminHCM\";s:4:\"name\";s:60:\"Ngân hàng Kookmin - Chi nhánh Thành phố Hồ Chí Minh\";s:3:\"bin\";s:6:\"970463\";s:4:\"logo\";s:35:\"https://cdn.vietqr.io/img/KBHCM.png\";}i:54;a:5:{s:4:\"code\";s:4:\"KBHN\";s:9:\"shortName\";s:9:\"KookminHN\";s:4:\"name\";s:42:\"Ngân hàng Kookmin - Chi nhánh Hà Nội\";s:3:\"bin\";s:6:\"970462\";s:4:\"logo\";s:34:\"https://cdn.vietqr.io/img/KBHN.png\";}i:55;a:5:{s:4:\"code\";s:3:\"WVN\";s:9:\"shortName\";s:5:\"Woori\";s:4:\"name\";s:37:\"Ngân hàng TNHH MTV Woori Việt Nam\";s:3:\"bin\";s:6:\"970457\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/WVN.png\";}i:56;a:5:{s:4:\"code\";s:3:\"VRB\";s:9:\"shortName\";s:3:\"VRB\";s:4:\"name\";s:36:\"Ngân hàng Liên doanh Việt - Nga\";s:3:\"bin\";s:6:\"970421\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/VRB.png\";}i:57;a:5:{s:4:\"code\";s:4:\"HSBC\";s:9:\"shortName\";s:4:\"HSBC\";s:4:\"name\";s:38:\"Ngân hàng TNHH MTV HSBC (Việt Nam)\";s:3:\"bin\";s:6:\"458761\";s:4:\"logo\";s:34:\"https://cdn.vietqr.io/img/HSBC.png\";}i:58;a:5:{s:4:\"code\";s:8:\"IBK - HN\";s:9:\"shortName\";s:5:\"IBKHN\";s:4:\"name\";s:68:\"Ngân hàng Công nghiệp Hàn Quốc - Chi nhánh Hà Nội\";s:3:\"bin\";s:6:\"970455\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/IBK.png\";}i:59;a:5:{s:4:\"code\";s:9:\"IBK - HCM\";s:9:\"shortName\";s:6:\"IBKHCM\";s:4:\"name\";s:77:\"Ngân hàng Công nghiệp Hàn Quốc - Chi nhánh TP. Hồ Chí Minh\";s:3:\"bin\";s:6:\"970456\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/IBK.png\";}i:60;a:5:{s:4:\"code\";s:3:\"IVB\";s:9:\"shortName\";s:12:\"IndovinaBank\";s:4:\"name\";s:25:\"Ngân hàng TNHH Indovina\";s:3:\"bin\";s:6:\"970434\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/IVB.png\";}i:61;a:5:{s:4:\"code\";s:3:\"UOB\";s:9:\"shortName\";s:14:\"UnitedOverseas\";s:4:\"name\";s:59:\"Ngân hàng United Overseas - Chi nhánh TP. Hồ Chí Minh\";s:3:\"bin\";s:6:\"970458\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/UOB.png\";}i:62;a:5:{s:4:\"code\";s:6:\"NHB HN\";s:9:\"shortName\";s:8:\"Nonghyup\";s:4:\"name\";s:43:\"Ngân hàng Nonghyup - Chi nhánh Hà Nội\";s:3:\"bin\";s:6:\"801011\";s:4:\"logo\";s:33:\"https://cdn.vietqr.io/img/NHB.png\";}i:63;a:5:{s:4:\"code\";s:4:\"SCVN\";s:9:\"shortName\";s:17:\"StandardChartered\";s:4:\"name\";s:55:\"Ngân hàng TNHH MTV Standard Chartered Bank Việt Nam\";s:3:\"bin\";s:6:\"970410\";s:4:\"logo\";s:34:\"https://cdn.vietqr.io/img/SCVN.png\";}i:64;a:5:{s:4:\"code\";s:4:\"PBVN\";s:9:\"shortName\";s:10:\"PublicBank\";s:4:\"name\";s:38:\"Ngân hàng TNHH MTV Public Việt Nam\";s:3:\"bin\";s:6:\"970439\";s:4:\"logo\";s:34:\"https://cdn.vietqr.io/img/PBVN.png\";}}', 1786438614);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-08-07 11:41:31', '2026-08-07 11:41:31');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` bigint UNSIGNED NOT NULL,
  `cart_id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `description`, `icon`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Nguyễn Đức Việt', 'bst-san-dien', 'ỳew', 'code', 1, 0, '2026-08-07 12:32:06', '2026-08-07 12:32:06');

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `certificate_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issued_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chapters`
--

CREATE TABLE `chapters` (
  `id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `content_updates`
--

CREATE TABLE `content_updates` (
  `id` bigint UNSIGNED NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` bigint UNSIGNED DEFAULT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` json NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED NOT NULL,
  `reviewed_by` bigint UNSIGNED DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('percent','fixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percent',
  `value` decimal(12,2) NOT NULL DEFAULT '0.00',
  `min_order_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `max_uses` int UNSIGNED DEFAULT NULL,
  `used_count` int UNSIGNED NOT NULL DEFAULT '0',
  `starts_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` bigint UNSIGNED NOT NULL,
  `instructor_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_description` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `objectives` text COLLATE utf8mb4_unicode_ci,
  `target_audience` text COLLATE utf8mb4_unicode_ci,
  `requirements` text COLLATE utf8mb4_unicode_ci,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preview_video` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` enum('beginner','intermediate','advanced') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'vi',
  `price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_price` decimal(12,2) DEFAULT NULL,
  `sale_price` decimal(12,2) DEFAULT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `reject_reason` text COLLATE utf8mb4_unicode_ci,
  `rating_avg` decimal(3,2) NOT NULL DEFAULT '0.00',
  `rating_count` int UNSIGNED NOT NULL DEFAULT '0',
  `enrollment_count` int UNSIGNED NOT NULL DEFAULT '0',
  `duration_minutes` int UNSIGNED NOT NULL DEFAULT '0',
  `tags` json DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `published_at` timestamp NULL DEFAULT NULL,
  `suspended_at` timestamp NULL DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `submission_count` int UNSIGNED NOT NULL DEFAULT '0',
  `required_video_percent` tinyint UNSIGNED DEFAULT NULL,
  `required_lesson_percent` tinyint UNSIGNED DEFAULT NULL,
  `minimum_quiz_score` tinyint UNSIGNED DEFAULT NULL,
  `require_all_quizzes` tinyint(1) NOT NULL DEFAULT '1',
  `require_all_assignments` tinyint(1) NOT NULL DEFAULT '1',
  `certificate_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `copyright_agreed` tinyint(1) NOT NULL DEFAULT '0',
  `copyright_agreed_at` timestamp NULL DEFAULT NULL,
  `copyright_agreed_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `instructor_id`, `category_id`, `title`, `slug`, `short_description`, `description`, `objectives`, `target_audience`, `requirements`, `thumbnail`, `preview_video`, `level`, `language`, `price`, `discount_price`, `sale_price`, `status`, `is_published`, `reject_reason`, `rating_avg`, `rating_count`, `enrollment_count`, `duration_minutes`, `tags`, `is_featured`, `published_at`, `suspended_at`, `submitted_at`, `approved_at`, `submission_count`, `required_video_percent`, `required_lesson_percent`, `minimum_quiz_score`, `require_all_quizzes`, `require_all_assignments`, `certificate_enabled`, `copyright_agreed`, `copyright_agreed_at`, `copyright_agreed_by`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'njjb', 'njjb', '676r6', '76r7', 'fđgfdg', NULL, NULL, NULL, NULL, 'beginner', 'vi', 5000000.00, 4000000.00, 4000000.00, 'draft', 0, NULL, 0.00, 0, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 1, 1, 0, NULL, NULL, '2026-08-07 13:28:41', '2026-08-07 13:28:41'),
(2, 2, 1, 'HTML BASIC', 'html-basic', 'Đây là video Bloopers, tức các cảnh lỗi, nói nhầm hoặc đoạn quay vui trong quá trình sản xuất series HTML của W3Schools.', 'ABC', 'Nâng cao trình độ lập trình', NULL, NULL, 'course-thumbnails/tb1eo9Jrlcpah73qZHINkznpHQYetpRx76MuAC9c.webp', NULL, 'beginner', 'vi', 200000.00, 150000.00, 150000.00, 'draft', 0, NULL, 0.00, 0, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 1, 1, 0, NULL, NULL, '2026-08-07 19:36:46', '2026-08-07 19:37:18');

-- --------------------------------------------------------

--
-- Table structure for table `course_reviews`
--

CREATE TABLE `course_reviews` (
  `id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `reviewer_id` bigint UNSIGNED DEFAULT NULL,
  `submission_number` int UNSIGNED NOT NULL DEFAULT '1',
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `comment` text COLLATE utf8mb4_unicode_ci,
  `checklist_json` json DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_sections`
--

CREATE TABLE `course_sections` (
  `id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_sections`
--

INSERT INTO `course_sections` (`id`, `course_id`, `title`, `description`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 2, 'Chương 1: HTML', 'Cơ bản HTML', 0, '2026-08-07 19:38:16', '2026-08-07 19:38:16'),
(2, 2, 'Chương 2: HTML Cao', 'Giới thiệu HTML là viết tắt của HyperText Markup Language. Đây là ngôn ngữ đánh dấu tiêu chuẩn được sử dụng để tạo và mô tả cấu trúc của các trang web. HTML sử dụng các phần tử và', 1, '2026-08-07 19:45:31', '2026-08-07 19:45:31');

-- --------------------------------------------------------

--
-- Table structure for table `discussions`
--

CREATE TABLE `discussions` (
  `id` bigint UNSIGNED NOT NULL,
  `lesson_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachment_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_resolved` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discussion_replies`
--

CREATE TABLE `discussion_replies` (
  `id` bigint UNSIGNED NOT NULL,
  `discussion_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachment_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_instructor_answer` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_helpful` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_verification_codes`
--

CREATE TABLE `email_verification_codes` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `code_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `attempt_count` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `last_sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `progress_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `completed_lessons` int UNSIGNED NOT NULL DEFAULT '0',
  `total_lessons` int UNSIGNED NOT NULL DEFAULT '0',
  `enrolled_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `last_accessed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `failed_jobs`
--

INSERT INTO `failed_jobs` (`id`, `uuid`, `connection`, `queue`, `payload`, `exception`, `failed_at`) VALUES
(1, '06602360-1514-4bc8-9b5e-e9d17ff9b1e1', 'database', 'default', '{\"uuid\":\"06602360-1514-4bc8-9b5e-e9d17ff9b1e1\",\"displayName\":\"App\\\\Jobs\\\\ConvertVideoToHLS\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":3600,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ConvertVideoToHLS\",\"command\":\"O:26:\\\"App\\\\Jobs\\\\ConvertVideoToHLS\\\":1:{s:6:\\\"lesson\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:17:\\\"App\\\\Models\\\\Lesson\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\",\"batchId\":null},\"createdAt\":1786156812,\"delay\":null}', 'Alchemy\\BinaryDriver\\Exception\\ExecutableNotFoundException: Executable not found, proposed : ffprobe in D:\\DATN_OnlineFea_WD14\\vendor\\php-ffmpeg\\php-ffmpeg\\src\\Alchemy\\BinaryDriver\\AbstractBinary.php:159\nStack trace:\n#0 D:\\DATN_OnlineFea_WD14\\vendor\\php-ffmpeg\\php-ffmpeg\\src\\FFMpeg\\Driver\\FFProbeDriver.php(50): Alchemy\\BinaryDriver\\AbstractBinary::load(Array, NULL, Object(Alchemy\\BinaryDriver\\Configuration))\n#1 D:\\DATN_OnlineFea_WD14\\vendor\\php-ffmpeg\\php-ffmpeg\\src\\FFMpeg\\FFProbe.php(220): FFMpeg\\Driver\\FFProbeDriver::create(Object(Alchemy\\BinaryDriver\\Configuration), NULL)\n#2 D:\\DATN_OnlineFea_WD14\\vendor\\php-ffmpeg\\php-ffmpeg\\src\\FFMpeg\\FFMpeg.php(130): FFMpeg\\FFProbe::create(Array, NULL, Object(Symfony\\Component\\Cache\\Adapter\\ArrayAdapter))\n#3 D:\\DATN_OnlineFea_WD14\\app\\Jobs\\ConvertVideoToHLS.php(52): FFMpeg\\FFMpeg::create(Array)\n#4 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): App\\Jobs\\ConvertVideoToHLS->handle()\n#5 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#6 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#7 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#8 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(799): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#9 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Bus\\Dispatcher.php(136): Illuminate\\Container\\Container->call(Array)\n#10 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(180): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}(Object(App\\Jobs\\ConvertVideoToHLS))\n#11 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(App\\Jobs\\ConvertVideoToHLS))\n#12 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Bus\\Dispatcher.php(140): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))\n#13 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(153): Illuminate\\Bus\\Dispatcher->dispatchNow(Object(App\\Jobs\\ConvertVideoToHLS), false)\n#14 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}(Object(App\\Jobs\\ConvertVideoToHLS))\n#15 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(App\\Jobs\\ConvertVideoToHLS))\n#16 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(146): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))\n#17 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(84): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(App\\Jobs\\ConvertVideoToHLS))\n#18 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Jobs\\Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Array)\n#19 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(553): Illuminate\\Queue\\Jobs\\Job->fire()\n#20 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(499): Illuminate\\Queue\\Worker->process(\'database\', Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(Illuminate\\Queue\\WorkerOptions))\n#21 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(412): Illuminate\\Queue\\Worker->runJob(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), \'database\', Object(Illuminate\\Queue\\WorkerOptions))\n#22 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(149): Illuminate\\Queue\\Worker->runNextJob(\'database\', \'default\', Object(Illuminate\\Queue\\WorkerOptions))\n#23 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(132): Illuminate\\Queue\\Console\\WorkCommand->runWorker(\'database\', \'default\')\n#24 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#25 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#26 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#27 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#28 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(799): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#29 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(280): Illuminate\\Container\\Container->call(Array)\n#30 D:\\DATN_OnlineFea_WD14\\vendor\\symfony\\console\\Command\\Command.php(341): Illuminate\\Console\\Command->execute(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#31 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(249): Symfony\\Component\\Console\\Command\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#32 D:\\DATN_OnlineFea_WD14\\vendor\\symfony\\console\\Application.php(1117): Illuminate\\Console\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#33 D:\\DATN_OnlineFea_WD14\\vendor\\symfony\\console\\Application.php(356): Symfony\\Component\\Console\\Application->doRunCommand(Object(Illuminate\\Queue\\Console\\WorkCommand), Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#34 D:\\DATN_OnlineFea_WD14\\vendor\\symfony\\console\\Application.php(195): Symfony\\Component\\Console\\Application->doRun(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#35 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Console\\Kernel.php(198): Symfony\\Component\\Console\\Application->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#36 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#37 D:\\DATN_OnlineFea_WD14\\artisan(16): Illuminate\\Foundation\\Application->handleCommand(Object(Symfony\\Component\\Console\\Input\\ArgvInput))\n#38 {main}\n\nNext FFMpeg\\Exception\\ExecutableNotFoundException: Unable to load FFProbe in D:\\DATN_OnlineFea_WD14\\vendor\\php-ffmpeg\\php-ffmpeg\\src\\FFMpeg\\Driver\\FFProbeDriver.php:52\nStack trace:\n#0 D:\\DATN_OnlineFea_WD14\\vendor\\php-ffmpeg\\php-ffmpeg\\src\\FFMpeg\\FFProbe.php(220): FFMpeg\\Driver\\FFProbeDriver::create(Object(Alchemy\\BinaryDriver\\Configuration), NULL)\n#1 D:\\DATN_OnlineFea_WD14\\vendor\\php-ffmpeg\\php-ffmpeg\\src\\FFMpeg\\FFMpeg.php(130): FFMpeg\\FFProbe::create(Array, NULL, Object(Symfony\\Component\\Cache\\Adapter\\ArrayAdapter))\n#2 D:\\DATN_OnlineFea_WD14\\app\\Jobs\\ConvertVideoToHLS.php(52): FFMpeg\\FFMpeg::create(Array)\n#3 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): App\\Jobs\\ConvertVideoToHLS->handle()\n#4 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#5 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#6 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#7 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(799): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#8 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Bus\\Dispatcher.php(136): Illuminate\\Container\\Container->call(Array)\n#9 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(180): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}(Object(App\\Jobs\\ConvertVideoToHLS))\n#10 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(App\\Jobs\\ConvertVideoToHLS))\n#11 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Bus\\Dispatcher.php(140): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))\n#12 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(153): Illuminate\\Bus\\Dispatcher->dispatchNow(Object(App\\Jobs\\ConvertVideoToHLS), false)\n#13 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}(Object(App\\Jobs\\ConvertVideoToHLS))\n#14 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(App\\Jobs\\ConvertVideoToHLS))\n#15 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(146): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))\n#16 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(84): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(App\\Jobs\\ConvertVideoToHLS))\n#17 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Jobs\\Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Array)\n#18 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(553): Illuminate\\Queue\\Jobs\\Job->fire()\n#19 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(499): Illuminate\\Queue\\Worker->process(\'database\', Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(Illuminate\\Queue\\WorkerOptions))\n#20 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(412): Illuminate\\Queue\\Worker->runJob(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), \'database\', Object(Illuminate\\Queue\\WorkerOptions))\n#21 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(149): Illuminate\\Queue\\Worker->runNextJob(\'database\', \'default\', Object(Illuminate\\Queue\\WorkerOptions))\n#22 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(132): Illuminate\\Queue\\Console\\WorkCommand->runWorker(\'database\', \'default\')\n#23 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#24 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#25 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#26 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#27 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(799): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#28 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(280): Illuminate\\Container\\Container->call(Array)\n#29 D:\\DATN_OnlineFea_WD14\\vendor\\symfony\\console\\Command\\Command.php(341): Illuminate\\Console\\Command->execute(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#30 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(249): Symfony\\Component\\Console\\Command\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#31 D:\\DATN_OnlineFea_WD14\\vendor\\symfony\\console\\Application.php(1117): Illuminate\\Console\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#32 D:\\DATN_OnlineFea_WD14\\vendor\\symfony\\console\\Application.php(356): Symfony\\Component\\Console\\Application->doRunCommand(Object(Illuminate\\Queue\\Console\\WorkCommand), Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#33 D:\\DATN_OnlineFea_WD14\\vendor\\symfony\\console\\Application.php(195): Symfony\\Component\\Console\\Application->doRun(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#34 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Console\\Kernel.php(198): Symfony\\Component\\Console\\Application->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#35 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#36 D:\\DATN_OnlineFea_WD14\\artisan(16): Illuminate\\Foundation\\Application->handleCommand(Object(Symfony\\Component\\Console\\Input\\ArgvInput))\n#37 {main}', '2026-08-07 19:40:20'),
(2, 'e56f1a86-9d8b-4380-abf0-5c4048e386d5', 'database', 'default', '{\"uuid\":\"e56f1a86-9d8b-4380-abf0-5c4048e386d5\",\"displayName\":\"App\\\\Jobs\\\\ConvertVideoToHLS\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":3600,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ConvertVideoToHLS\",\"command\":\"O:26:\\\"App\\\\Jobs\\\\ConvertVideoToHLS\\\":1:{s:6:\\\"lesson\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:17:\\\"App\\\\Models\\\\Lesson\\\";s:2:\\\"id\\\";i:2;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\",\"batchId\":null},\"createdAt\":1786156966,\"delay\":null}', 'Alchemy\\BinaryDriver\\Exception\\ExecutableNotFoundException: Executable not found, proposed : ffprobe in D:\\DATN_OnlineFea_WD14\\vendor\\php-ffmpeg\\php-ffmpeg\\src\\Alchemy\\BinaryDriver\\AbstractBinary.php:159\nStack trace:\n#0 D:\\DATN_OnlineFea_WD14\\vendor\\php-ffmpeg\\php-ffmpeg\\src\\FFMpeg\\Driver\\FFProbeDriver.php(50): Alchemy\\BinaryDriver\\AbstractBinary::load(Array, NULL, Object(Alchemy\\BinaryDriver\\Configuration))\n#1 D:\\DATN_OnlineFea_WD14\\vendor\\php-ffmpeg\\php-ffmpeg\\src\\FFMpeg\\FFProbe.php(220): FFMpeg\\Driver\\FFProbeDriver::create(Object(Alchemy\\BinaryDriver\\Configuration), NULL)\n#2 D:\\DATN_OnlineFea_WD14\\vendor\\php-ffmpeg\\php-ffmpeg\\src\\FFMpeg\\FFMpeg.php(130): FFMpeg\\FFProbe::create(Array, NULL, Object(Symfony\\Component\\Cache\\Adapter\\ArrayAdapter))\n#3 D:\\DATN_OnlineFea_WD14\\app\\Jobs\\ConvertVideoToHLS.php(52): FFMpeg\\FFMpeg::create(Array)\n#4 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): App\\Jobs\\ConvertVideoToHLS->handle()\n#5 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#6 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#7 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#8 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(799): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#9 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Bus\\Dispatcher.php(136): Illuminate\\Container\\Container->call(Array)\n#10 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(180): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}(Object(App\\Jobs\\ConvertVideoToHLS))\n#11 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(App\\Jobs\\ConvertVideoToHLS))\n#12 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Bus\\Dispatcher.php(140): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))\n#13 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(153): Illuminate\\Bus\\Dispatcher->dispatchNow(Object(App\\Jobs\\ConvertVideoToHLS), false)\n#14 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}(Object(App\\Jobs\\ConvertVideoToHLS))\n#15 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(App\\Jobs\\ConvertVideoToHLS))\n#16 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(146): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))\n#17 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(84): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(App\\Jobs\\ConvertVideoToHLS))\n#18 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Jobs\\Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Array)\n#19 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(553): Illuminate\\Queue\\Jobs\\Job->fire()\n#20 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(499): Illuminate\\Queue\\Worker->process(\'database\', Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(Illuminate\\Queue\\WorkerOptions))\n#21 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(412): Illuminate\\Queue\\Worker->runJob(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), \'database\', Object(Illuminate\\Queue\\WorkerOptions))\n#22 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(149): Illuminate\\Queue\\Worker->runNextJob(\'database\', \'default\', Object(Illuminate\\Queue\\WorkerOptions))\n#23 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(132): Illuminate\\Queue\\Console\\WorkCommand->runWorker(\'database\', \'default\')\n#24 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#25 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#26 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#27 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#28 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(799): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#29 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(280): Illuminate\\Container\\Container->call(Array)\n#30 D:\\DATN_OnlineFea_WD14\\vendor\\symfony\\console\\Command\\Command.php(341): Illuminate\\Console\\Command->execute(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#31 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(249): Symfony\\Component\\Console\\Command\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#32 D:\\DATN_OnlineFea_WD14\\vendor\\symfony\\console\\Application.php(1117): Illuminate\\Console\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#33 D:\\DATN_OnlineFea_WD14\\vendor\\symfony\\console\\Application.php(356): Symfony\\Component\\Console\\Application->doRunCommand(Object(Illuminate\\Queue\\Console\\WorkCommand), Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#34 D:\\DATN_OnlineFea_WD14\\vendor\\symfony\\console\\Application.php(195): Symfony\\Component\\Console\\Application->doRun(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#35 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Console\\Kernel.php(198): Symfony\\Component\\Console\\Application->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#36 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#37 D:\\DATN_OnlineFea_WD14\\artisan(16): Illuminate\\Foundation\\Application->handleCommand(Object(Symfony\\Component\\Console\\Input\\ArgvInput))\n#38 {main}\n\nNext FFMpeg\\Exception\\ExecutableNotFoundException: Unable to load FFProbe in D:\\DATN_OnlineFea_WD14\\vendor\\php-ffmpeg\\php-ffmpeg\\src\\FFMpeg\\Driver\\FFProbeDriver.php:52\nStack trace:\n#0 D:\\DATN_OnlineFea_WD14\\vendor\\php-ffmpeg\\php-ffmpeg\\src\\FFMpeg\\FFProbe.php(220): FFMpeg\\Driver\\FFProbeDriver::create(Object(Alchemy\\BinaryDriver\\Configuration), NULL)\n#1 D:\\DATN_OnlineFea_WD14\\vendor\\php-ffmpeg\\php-ffmpeg\\src\\FFMpeg\\FFMpeg.php(130): FFMpeg\\FFProbe::create(Array, NULL, Object(Symfony\\Component\\Cache\\Adapter\\ArrayAdapter))\n#2 D:\\DATN_OnlineFea_WD14\\app\\Jobs\\ConvertVideoToHLS.php(52): FFMpeg\\FFMpeg::create(Array)\n#3 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): App\\Jobs\\ConvertVideoToHLS->handle()\n#4 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#5 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#6 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#7 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(799): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#8 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Bus\\Dispatcher.php(136): Illuminate\\Container\\Container->call(Array)\n#9 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(180): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}(Object(App\\Jobs\\ConvertVideoToHLS))\n#10 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(App\\Jobs\\ConvertVideoToHLS))\n#11 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Bus\\Dispatcher.php(140): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))\n#12 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(153): Illuminate\\Bus\\Dispatcher->dispatchNow(Object(App\\Jobs\\ConvertVideoToHLS), false)\n#13 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}(Object(App\\Jobs\\ConvertVideoToHLS))\n#14 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(App\\Jobs\\ConvertVideoToHLS))\n#15 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(146): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))\n#16 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(84): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(App\\Jobs\\ConvertVideoToHLS))\n#17 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Jobs\\Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Array)\n#18 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(553): Illuminate\\Queue\\Jobs\\Job->fire()\n#19 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(499): Illuminate\\Queue\\Worker->process(\'database\', Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(Illuminate\\Queue\\WorkerOptions))\n#20 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(412): Illuminate\\Queue\\Worker->runJob(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), \'database\', Object(Illuminate\\Queue\\WorkerOptions))\n#21 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(149): Illuminate\\Queue\\Worker->runNextJob(\'database\', \'default\', Object(Illuminate\\Queue\\WorkerOptions))\n#22 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(132): Illuminate\\Queue\\Console\\WorkCommand->runWorker(\'database\', \'default\')\n#23 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#24 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#25 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#26 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#27 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(799): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#28 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(280): Illuminate\\Container\\Container->call(Array)\n#29 D:\\DATN_OnlineFea_WD14\\vendor\\symfony\\console\\Command\\Command.php(341): Illuminate\\Console\\Command->execute(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#30 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(249): Symfony\\Component\\Console\\Command\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#31 D:\\DATN_OnlineFea_WD14\\vendor\\symfony\\console\\Application.php(1117): Illuminate\\Console\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#32 D:\\DATN_OnlineFea_WD14\\vendor\\symfony\\console\\Application.php(356): Symfony\\Component\\Console\\Application->doRunCommand(Object(Illuminate\\Queue\\Console\\WorkCommand), Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#33 D:\\DATN_OnlineFea_WD14\\vendor\\symfony\\console\\Application.php(195): Symfony\\Component\\Console\\Application->doRun(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#34 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Console\\Kernel.php(198): Symfony\\Component\\Console\\Application->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#35 D:\\DATN_OnlineFea_WD14\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#36 D:\\DATN_OnlineFea_WD14\\artisan(16): Illuminate\\Foundation\\Application->handleCommand(Object(Symfony\\Component\\Console\\Input\\ArgvInput))\n#37 {main}', '2026-08-07 19:42:53');

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` bigint UNSIGNED NOT NULL,
  `question` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `homepage_settings`
--

CREATE TABLE `homepage_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `instructor_applications`
--

CREATE TABLE `instructor_applications` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `expertise` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `experience` text COLLATE utf8mb4_unicode_ci,
  `introduction` text COLLATE utf8mb4_unicode_ci,
  `cv_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `certificate_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `intro_video_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `reviewed_by` bigint UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `instructor_profiles`
--

CREATE TABLE `instructor_profiles` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `specialty` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `experience` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `linkedin_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `github_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cv` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agree_information` tinyint(1) NOT NULL DEFAULT '1',
  `agree_terms` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `instructor_profiles`
--

INSERT INTO `instructor_profiles` (`id`, `user_id`, `phone`, `specialty`, `experience`, `bio`, `linkedin_url`, `github_url`, `website_url`, `cv`, `agree_information`, `agree_terms`, `created_at`, `updated_at`) VALUES
(1, 2, '0987612345', 'Vip', '10 năm', 'ABC', NULL, NULL, NULL, 'instructor_cvs/PsqdPjtNmDYQfXBw9Bv1l1EE3ENioF2z73FVznow.pdf', 1, 1, '2026-08-07 11:43:47', '2026-08-07 11:43:47');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `learning_paths`
--

CREATE TABLE `learning_paths` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` enum('beginner','intermediate','advanced') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'beginner',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `learning_path_courses`
--

CREATE TABLE `learning_path_courses` (
  `id` bigint UNSIGNED NOT NULL,
  `learning_path_id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED DEFAULT NULL,
  `section_id` bigint UNSIGNED DEFAULT NULL,
  `chapter_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `document_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration` int UNSIGNED DEFAULT NULL,
  `type` enum('video','document','quiz','assignment') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'video',
  `video_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_original_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_size` bigint UNSIGNED NOT NULL DEFAULT '0',
  `video_mime` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hls_playlist` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hls_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hls_status` enum('pending','processing','completed','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `processing_error` text COLLATE utf8mb4_unicode_ci,
  `processed_at` timestamp NULL DEFAULT NULL,
  `duration_seconds` int UNSIGNED NOT NULL DEFAULT '0',
  `is_preview` tinyint(1) NOT NULL DEFAULT '0',
  `is_required` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `status` enum('draft','published') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `content_version` int UNSIGNED NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`id`, `course_id`, `section_id`, `chapter_id`, `title`, `content`, `document_file`, `duration`, `type`, `video_path`, `video_url`, `video_original_name`, `video_size`, `video_mime`, `hls_playlist`, `hls_path`, `hls_status`, `processing_error`, `processed_at`, `duration_seconds`, `is_preview`, `is_required`, `sort_order`, `status`, `content_version`, `created_at`, `updated_at`) VALUES
(1, 2, 1, NULL, 'HTML - Introduction', 'Giới thiệu\r\n\r\nHTML là viết tắt của HyperText Markup Language. Đây là ngôn ngữ đánh dấu tiêu chuẩn được sử dụng để tạo và mô tả cấu trúc của các trang web.\r\n\r\nHTML sử dụng các phần tử và thẻ để xác định từng loại nội dung trên một trang, chẳng hạn như tiêu đề, đoạn văn, bảng và nhiều thành phần khác.\r\n\r\nHTML hoạt động như thế nào?\r\n\r\nHTML sử dụng markup để mô tả cấu trúc của trang web.\r\n\r\nCác thành phần nội dung được đánh dấu bằng HTML Tags.\r\n\r\nVí dụ:\r\n\r\n<h1>This is a Heading</h1>\r\n<p>This is a paragraph.</p>\r\n\r\nTrong đó:\r\n\r\n<h1> cho biết nội dung là một tiêu đề.\r\n<p> cho biết nội dung là một đoạn văn.\r\n\r\nCác thẻ HTML không được trình duyệt hiển thị trực tiếp như văn bản. Trình duyệt sử dụng chúng để hiểu nội dung thuộc loại nào và cần hiển thị như thế nào.\r\n\r\nCấu trúc cơ bản của một trang HTML\r\n\r\nVí dụ:\r\n\r\n<!DOCTYPE html>\r\n<html>\r\n<head>\r\n    <title>Page Title</title>\r\n</head>\r\n<body>\r\n\r\n    <h1>This is a Heading</h1>\r\n    <p>This is a paragraph.</p>\r\n\r\n</body>\r\n</html>\r\n<!DOCTYPE html>\r\n<!DOCTYPE html>\r\n\r\nKhai báo tài liệu đang sử dụng HTML.\r\n\r\n<html>\r\n<html>\r\n...\r\n</html>\r\n\r\nĐây là phần tử gốc của một trang HTML.\r\n\r\nCác thành phần chính của tài liệu HTML được đặt bên trong phần tử này.\r\n\r\n<head>\r\n<head>\r\n...\r\n</head>\r\n\r\nPhần <head> chứa những thông tin về tài liệu HTML.\r\n\r\nThông tin này không phải nội dung chính được hiển thị bên trong trang web.\r\n\r\n<title>\r\n<title>Page Title</title>\r\n\r\nThẻ <title> xác định tiêu đề của tài liệu.\r\n\r\nTiêu đề này thường được hiển thị trên tab của trình duyệt.\r\n\r\n<body>\r\n<body>\r\n...\r\n</body>\r\n\r\nPhần <body> chứa nội dung được hiển thị cho người dùng trên trang web.\r\n\r\nVí dụ:\r\n\r\n<body>\r\n    <h1>This is a Heading</h1>\r\n    <p>This is a paragraph.</p>\r\n</body>\r\nHTML Tags\r\n\r\nHTML sử dụng các tag để xác định cấu trúc của nội dung.\r\n\r\nPhần lớn các tag có:\r\n\r\nStart Tag - thẻ mở.\r\nNội dung.\r\nEnd Tag - thẻ đóng.\r\n\r\nVí dụ:\r\n\r\n<p>This is a paragraph.</p>\r\n\r\nTrong đó:\r\n\r\n<p>\r\n\r\nlà Start Tag.\r\n\r\nThis is a paragraph.\r\n\r\nlà nội dung.\r\n\r\n</p>\r\n\r\nlà End Tag.\r\n\r\nThẻ đóng thường giống thẻ mở nhưng có thêm dấu /.\r\n\r\nCấu trúc chung:\r\n\r\n<tagname>Content</tagname>\r\n\r\nVí dụ:\r\n\r\n<h1>Hello World</h1>\r\nMột số Tag xuất hiện trong bài\r\nHeading\r\n<h1>This is a Heading</h1>\r\n\r\n<h1> được sử dụng để xác định một heading.\r\n\r\nParagraph\r\n<p>This is a paragraph.</p>\r\n\r\n<p> được sử dụng để xác định một đoạn văn.\r\n\r\nWeb Browser xử lý HTML\r\n\r\nCác trình duyệt web như Chrome, Edge, Firefox hoặc Safari đọc tài liệu HTML và chuyển cấu trúc HTML thành nội dung mà người dùng nhìn thấy trên màn hình.\r\n\r\nVí dụ mã HTML:\r\n\r\n<h1>Hello World</h1>\r\n<p>Welcome to my website.</p>\r\n\r\nTrình duyệt đọc:\r\n\r\n<h1> và hiểu rằng Hello World là heading.\r\n<p> và hiểu rằng Welcome to my website. là paragraph.\r\n\r\nTrình duyệt không hiển thị trực tiếp:\r\n\r\n<h1>\r\n<p>\r\n</p>\r\n</h1>\r\n\r\nmà sử dụng các tag đó để xác định cách cấu trúc nội dung.\r\n\r\nQuá trình có thể hiểu đơn giản như sau:\r\n\r\nHTML Code\r\n    ↓\r\nWeb Browser\r\n    ↓\r\nĐọc các HTML Tags\r\n    ↓\r\nHiểu cấu trúc nội dung\r\n    ↓\r\nHiển thị trang web\r\nKiến thức chính của Video 1\r\nHTML là viết tắt của HyperText Markup Language.\r\nHTML được sử dụng để xây dựng cấu trúc của trang web.\r\nHTML sử dụng markup và các tag để mô tả nội dung.\r\nHTML Tag giúp xác định nội dung là heading, paragraph hoặc loại nội dung khác.\r\nPhần lớn HTML Tag có thẻ mở và thẻ đóng.\r\n<html> là phần tử gốc của tài liệu HTML.\r\n<head> chứa thông tin về tài liệu.\r\n<title> xác định tiêu đề của tài liệu.\r\n<body> chứa nội dung hiển thị trên trang.\r\n<h1> tạo heading.\r\n<p> tạo paragraph.\r\nWeb browser đọc HTML và hiển thị kết quả thành trang web.', NULL, NULL, 'video', 'lesson-videos-mp4/ZFnudxlRfarr0qC0j43G1dX3P0FLhZ7wFo8eLeHy.mp4', NULL, 'YTSave_YouTube_HTML-Attributes-W3Schools-com_Media_yMX901oVtn8_004_360p.mp4', 4344718, 'video/mp4', NULL, NULL, 'pending', NULL, NULL, 0, 1, 1, 0, 'published', 1, '2026-08-07 19:40:12', '2026-08-07 19:40:12'),
(2, 2, 1, NULL, 'HTML - Editors', '# Video 2: HTML - Editors\r\n\r\n## Giới thiệu\r\n\r\nHTML có thể được viết và chỉnh sửa bằng nhiều loại trình soạn thảo khác nhau. Khi mới học HTML, có thể bắt đầu bằng một trình soạn thảo văn bản đơn giản để tập trung vào cách HTML hoạt động.\r\n\r\nTrên Windows có thể sử dụng **Notepad**, còn trên macOS có thể sử dụng **TextEdit**.\r\n\r\nQuá trình tạo một trang HTML cơ bản gồm 4 bước:\r\n\r\n1. Mở trình soạn thảo.\r\n2. Viết HTML.\r\n3. Lưu thành file HTML.\r\n4. Mở file bằng trình duyệt.\r\n\r\n## Bước 1: Mở HTML Editor\r\n\r\nTrước tiên cần một chương trình để viết mã HTML.\r\n\r\nVí dụ trên Windows:\r\n\r\n```text\r\nNotepad\r\n```\r\n\r\nTrên macOS:\r\n\r\n```text\r\nTextEdit\r\n```\r\n\r\nHTML thực chất được lưu dưới dạng văn bản, vì vậy không bắt buộc phải có một phần mềm lập trình phức tạp mới có thể tạo trang HTML.\r\n\r\nTrình soạn thảo được dùng để nhập và chỉnh sửa mã nguồn.\r\n\r\n## Bước 2: Viết HTML\r\n\r\nSau khi mở editor, nhập mã HTML vào tài liệu.\r\n\r\nVí dụ:\r\n\r\n```html\r\n<!DOCTYPE html>\r\n<html>\r\n<body>\r\n\r\n<h1>My First Heading</h1>\r\n\r\n<p>My first paragraph.</p>\r\n\r\n</body>\r\n</html>\r\n```\r\n\r\nTrong ví dụ:\r\n\r\n* `<!DOCTYPE html>` khai báo tài liệu HTML.\r\n* `<html>` chứa tài liệu HTML.\r\n* `<body>` chứa nội dung hiển thị trên trang.\r\n* `<h1>` tạo heading.\r\n* `<p>` tạo paragraph.\r\n\r\nEditor chỉ hiển thị mã nguồn.\r\n\r\nNội dung này chưa trở thành một trang web hiển thị hoàn chỉnh cho đến khi file được lưu và mở bằng trình duyệt.\r\n\r\n## Bước 3: Lưu thành trang HTML\r\n\r\nSau khi viết code, file cần được lưu với phần mở rộng HTML.\r\n\r\nVí dụ:\r\n\r\n```text\r\nindex.html\r\n```\r\n\r\nCó thể sử dụng:\r\n\r\n```text\r\n.html\r\n```\r\n\r\nhoặc:\r\n\r\n```text\r\n.htm\r\n```\r\n\r\nCả hai đều có thể được sử dụng cho tài liệu HTML.\r\n\r\nTên thường được dùng cho trang chính của một website là:\r\n\r\n```text\r\nindex.html\r\n```\r\n\r\nKhi lưu bằng Notepad có thể sử dụng:\r\n\r\n```text\r\nFile\r\n→ Save As\r\n→ index.html\r\n```\r\n\r\nEncoding nên đặt thành:\r\n\r\n```text\r\nUTF-8\r\n```\r\n\r\nUTF-8 là encoding phổ biến cho tài liệu HTML và cho phép lưu nhiều loại ký tự khác nhau.\r\n\r\nVí dụ cấu trúc thư mục:\r\n\r\n```text\r\nmy-website/\r\n│\r\n└── index.html\r\n```\r\n\r\n## Bước 4: Mở trang bằng trình duyệt\r\n\r\nSau khi lưu file HTML, có thể mở nó bằng web browser.\r\n\r\nVí dụ:\r\n\r\n* Google Chrome\r\n* Microsoft Edge\r\n* Firefox\r\n* Safari\r\n\r\nCó thể nhấp đúp vào:\r\n\r\n```text\r\nindex.html\r\n```\r\n\r\nhoặc:\r\n\r\n```text\r\nRight Click\r\n→ Open with\r\n→ Browser\r\n```\r\n\r\nNếu file chứa:\r\n\r\n```html\r\n<h1>My First Heading</h1>\r\n\r\n<p>My first paragraph.</p>\r\n```\r\n\r\ntrình duyệt sẽ đọc HTML và hiển thị một heading cùng một đoạn văn.\r\n\r\nEditor và Browser có hai nhiệm vụ khác nhau:\r\n\r\n```text\r\nHTML Editor\r\n    ↓\r\nViết và chỉnh sửa mã HTML\r\n    ↓\r\nLưu file .html\r\n    ↓\r\nWeb Browser\r\n    ↓\r\nĐọc HTML và hiển thị trang web\r\n```\r\n\r\n## Chỉnh sửa trang HTML\r\n\r\nNếu muốn thay đổi trang:\r\n\r\n1. Mở lại file HTML trong editor.\r\n2. Thay đổi code.\r\n3. Lưu file.\r\n4. Quay lại browser.\r\n5. Refresh trang.\r\n\r\nVí dụ ban đầu:\r\n\r\n```html\r\n<h1>Hello</h1>\r\n```\r\n\r\nSau khi sửa:\r\n\r\n```html\r\n<h1>Hello HTML</h1>\r\n```\r\n\r\nLưu file và refresh browser để xem kết quả mới.\r\n\r\n## Kiến thức chính của Video 2\r\n\r\n* HTML có thể được viết bằng một text editor.\r\n* Có thể dùng Notepad trên Windows hoặc TextEdit trên macOS.\r\n* HTML được viết dưới dạng mã nguồn văn bản.\r\n* File HTML thường có phần mở rộng `.html` hoặc `.htm`.\r\n* Một tên file phổ biến là `index.html`.\r\n* UTF-8 là encoding phù hợp để lưu tài liệu HTML.\r\n* Editor dùng để viết code.\r\n* Browser dùng để đọc và hiển thị HTML.\r\n* Quy trình cơ bản là: mở editor → viết HTML → lưu file → mở bằng browser.', NULL, NULL, 'video', 'lesson-videos-mp4/s4cGT30YAiPEl113DK27x6f5di3iCRzPi4piGnn4.mp4', NULL, 'YTSave_YouTube_HTML-Headings-W3Schools-com_Media_9gHPpwq6IaY_004_360p.mp4', 2740711, 'video/mp4', NULL, NULL, 'pending', NULL, NULL, 0, 0, 1, 1, 'published', 1, '2026-08-07 19:42:45', '2026-08-07 19:42:45');

-- --------------------------------------------------------

--
-- Table structure for table `lesson_ai_summaries`
--

CREATE TABLE `lesson_ai_summaries` (
  `id` bigint UNSIGNED NOT NULL,
  `lesson_id` bigint UNSIGNED NOT NULL,
  `summary` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `key_points` json DEFAULT NULL,
  `source_hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `generated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lesson_attachments`
--

CREATE TABLE `lesson_attachments` (
  `id` bigint UNSIGNED NOT NULL,
  `lesson_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lesson_notes`
--

CREATE TABLE `lesson_notes` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `lesson_id` bigint UNSIGNED NOT NULL,
  `timestamp_seconds` int UNSIGNED DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lesson_progress`
--

CREATE TABLE `lesson_progress` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `lesson_id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED DEFAULT NULL,
  `watched_seconds` int UNSIGNED NOT NULL DEFAULT '0',
  `duration_seconds` int UNSIGNED NOT NULL DEFAULT '0',
  `last_position_seconds` int UNSIGNED NOT NULL DEFAULT '0',
  `furthest_position_seconds` int UNSIGNED NOT NULL DEFAULT '0',
  `current_time` int UNSIGNED NOT NULL DEFAULT '0',
  `duration` int UNSIGNED NOT NULL DEFAULT '0',
  `progress_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `last_watched_at` timestamp NULL DEFAULT NULL,
  `last_client_updated_at` timestamp NULL DEFAULT NULL,
  `is_completed` tinyint(1) NOT NULL DEFAULT '0',
  `completed_at` timestamp NULL DEFAULT NULL,
  `last_viewed_content_version` int UNSIGNED NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lesson_subtitles`
--

CREATE TABLE `lesson_subtitles` (
  `id` bigint UNSIGNED NOT NULL,
  `lesson_id` bigint UNSIGNED NOT NULL,
  `language` enum('vi','en') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'vi',
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `live_sessions`
--

CREATE TABLE `live_sessions` (
  `id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `instructor_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `stream_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scheduled_at` timestamp NOT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `ended_at` timestamp NULL DEFAULT NULL,
  `status` enum('scheduled','live','ended','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_06_23_032329_create_categories_table', 1),
(5, '2026_06_23_032539_create_badges_table', 1),
(6, '2026_06_23_033251_create_coupons_table', 1),
(7, '2026_06_23_033833_create_faqs_table', 1),
(8, '2026_06_23_034826_create_courses_table', 1),
(9, '2026_06_23_040614_create_certificates_table', 1),
(10, '2026_06_23_040803_create_chapters_table', 1),
(11, '2026_06_23_041851_create_learning_paths_table', 1),
(12, '2026_06_23_042654_create_learning_path_courses_table', 1),
(13, '2026_06_23_042952_create_lessons_table', 1),
(14, '2026_06_23_043729_create_homepage_settings_table', 1),
(15, '2026_06_23_050306_create_activity_logs_table', 1),
(16, '2026_06_23_050417_create_user_badges_table', 1),
(17, '2026_06_23_050534_create_user_points_table', 1),
(18, '2026_06_23_050645_create_push_notifications_table', 1),
(19, '2026_06_23_050749_create_support_tickets_table', 1),
(20, '2026_06_23_050912_create_two_factor_codes_table', 1),
(21, '2026_06_23_051004_create_carts_table', 1),
(22, '2026_06_23_051105_create_orders_table', 1),
(23, '2026_06_23_051200_create_enrollments_table', 1),
(24, '2026_06_23_051435_create_wishlists_table', 1),
(25, '2026_06_23_051532_create_recently_viewed_courses_table', 1),
(26, '2026_06_23_051632_create_reviews_table', 1),
(27, '2026_06_23_051900_create_live_sessions_table', 1),
(28, '2026_06_23_052119_create_study_groups_table', 1),
(29, '2026_06_23_052218_create_cart_items_table', 1),
(30, '2026_06_23_052316_create_order_items_table', 1),
(31, '2026_06_23_052454_create_payments_table', 1),
(32, '2026_06_23_053213_create_support_ticket_messages_table', 1),
(33, '2026_06_23_053359_create_lesson_attachments_table', 1),
(34, '2026_06_23_053555_create_lesson_progress_table', 1),
(35, '2026_06_23_053808_create_lesson_subtitles_table', 1),
(36, '2026_06_23_053836_create_video_notes_table', 1),
(37, '2026_06_23_054111_create_assignments_table', 1),
(38, '2026_06_23_054234_create_discussions_table', 1),
(39, '2026_06_23_054454_create_quizzes_table', 1),
(40, '2026_06_23_054809_create_ai_chat_messages_table', 1),
(41, '2026_06_23_054903_create_ai_summaries_table', 1),
(42, '2026_06_23_055038_create_submissions_table', 1),
(43, '2026_06_23_055238_create_discussion_replies_table', 1),
(44, '2026_06_23_055937_create_quiz_questions_table', 1),
(45, '2026_06_23_060100_create_quiz_options_table', 1),
(46, '2026_06_23_060716_create_quiz_attempts_table', 1),
(47, '2026_06_27_000001_enhance_users_for_authentication', 1),
(48, '2026_06_27_000002_create_roles_and_permissions_tables', 1),
(49, '2026_06_29_000001_enhance_users_for_authentication', 1),
(50, '2026_06_29_000002_create_instructor_applications_table', 1),
(51, '2026_06_29_000003_create_permissions_tables', 1),
(52, '2026_07_01_000001_update_courses_for_instructor_management', 1),
(53, '2026_07_01_000002_create_course_sections_and_update_lessons', 1),
(54, '2026_07_01_000003_add_publication_review_fields_to_courses', 1),
(55, '2026_07_01_000004_add_status_enrolled_at_to_enrollments', 1),
(56, '2026_07_02_000001_add_video_file_columns_to_lessons_table', 1),
(57, '2026_07_03_000001_enhance_existing_quiz_schema', 1),
(58, '2026_07_07_110959_add_transaction_id_and_items_to_orders_table', 1),
(59, '2026_07_09_000001_create_course_reviews_table', 1),
(60, '2026_07_09_000002_enhance_courses_for_workflow', 1),
(61, '2026_07_09_000003_enhance_lesson_progress_table', 1),
(62, '2026_07_09_000004_enhance_enrollments_and_lessons', 1),
(63, '2026_07_09_000005_create_social_accounts_table', 1),
(64, '2026_07_09_000006_enhance_assignments_submissions', 1),
(65, '2026_07_09_000007_migrate_course_status_values', 1),
(66, '2026_07_10_000001_add_provider_email_index_to_social_accounts_table', 1),
(67, '2026_07_10_093909_create_video_moderations_table', 1),
(68, '2026_07_11_000001_create_email_verification_codes_table', 1),
(69, '2026_07_11_000001_enhance_categories_for_course_taxonomy', 1),
(70, '2026_07_16_080713_make_reviewer_id_nullable_in_course_reviews_table', 1),
(71, '2026_07_17_000001_update_recently_viewed_courses_last_viewed_at', 1),
(72, '2026_07_17_000002_add_missing_coupon_columns_to_coupons_table', 1),
(73, '2026_07_17_100000_enhance_reviews_for_course_feedback', 1),
(74, '2026_07_17_100001_add_review_rating_check_constraint', 1),
(75, '2026_07_19_000000_create_study_group_members_table', 1),
(76, '2026_07_19_000001_normalize_coupons_type_enum', 1),
(77, '2026_07_19_000002_add_source_hash_to_ai_summaries_table', 1),
(78, '2026_07_19_000003_create_lesson_ai_summaries_table', 1),
(79, '2026_07_19_000004_add_copyright_agreement_columns_to_courses_table', 1),
(80, '2026_07_19_131254_add_copyright_columns_to_courses_table', 1),
(81, '2026_07_20_000001_add_commission_fields_to_order_items_table', 1),
(82, '2026_07_20_000002_add_bank_details_and_commission_to_users_table', 1),
(83, '2026_07_21_042546_create_active_sessions_table', 1),
(84, '2026_07_21_042601_create_video_access_logs_table', 1),
(85, '2026_07_21_042602_create_video_watch_histories_table', 1),
(86, '2026_07_21_085752_create_study_group_messages_table', 1),
(87, '2026_07_23_000001_add_image_path_to_study_group_messages_table', 1),
(88, '2026_07_24_000000_update_reviews_table_for_replies', 1),
(89, '2026_07_24_000001_enhance_support_tickets_table', 1),
(90, '2026_07_24_000002_create_support_ticket_attachments_table', 1),
(91, '2026_07_25_000001_create_system_settings_table', 1),
(92, '2026_07_27_000001_add_file_fields_to_study_group_messages_table', 1),
(93, '2026_07_27_000001_update_reviews_to_visibility_workflow', 1),
(94, '2026_07_27_000002_create_lesson_notes_table', 1),
(95, '2026_07_27_000003_add_resume_tracking_to_lesson_progress_table', 1),
(96, '2026_07_27_095558_add_attachments_to_discussions_and_replies_tables', 1),
(97, '2026_07_27_110000_add_copyright_agreed_fields_to_courses_table', 1),
(98, '2026_07_27_121901_migrate_existing_reviews_to_approved', 1),
(99, '2026_07_29_000001_create_withdrawals_table', 1),
(100, '2026_07_31_185703_add_grading_history_to_submissions_table', 1),
(101, '2026_08_04_074747_add_instructor_status_to_users_table', 1),
(102, '2026_08_04_074753_create_instructor_profiles_table', 1),
(103, '2026_08_04_100243_create_content_updates_table', 1),
(104, '2026_08_04_100345_add_content_version_to_lessons_and_lesson_progress', 1),
(105, '2026_08_05_000001_add_engagement_columns_to_users_table', 1),
(106, '2026_08_05_000001_make_created_by_nullable_in_push_notifications_table', 1),
(107, '2026_08_05_000002_make_body_nullable_in_push_notifications_table', 1),
(108, '2026_08_06_110225_add_leaderboard_fields_to_tables', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `order_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `coupon_id` bigint UNSIGNED DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `discount_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(12,2) NOT NULL,
  `status` enum('pending','paid','failed','cancelled','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `items` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `commission_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `commission_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `instructor_earning` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `gateway` enum('momo','vnpay','bank_transfer') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'vnpay',
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `status` enum('pending','success','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `gateway_response` json DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'system',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `slug`, `group`, `created_at`, `updated_at`) VALUES
(1, 'Xem người dùng', 'users.view', 'users', '2026-08-07 11:11:52', '2026-08-07 11:11:52'),
(2, 'Thêm người dùng', 'users.create', 'users', '2026-08-07 11:11:51', '2026-08-07 11:11:51'),
(3, 'Sửa người dùng', 'users.update', 'users', '2026-08-07 11:11:51', '2026-08-07 11:11:51'),
(4, 'Xóa người dùng', 'users.delete', 'users', '2026-08-07 11:11:51', '2026-08-07 11:11:51'),
(5, 'Restore người dùng', 'users.restore', 'users', '2026-08-07 11:11:51', '2026-08-07 11:11:51'),
(6, 'Force delete người dùng', 'users.force_delete', 'users', '2026-08-07 11:11:51', '2026-08-07 11:11:51'),
(7, 'Import người dùng', 'users.import', 'users', '2026-08-07 11:11:51', '2026-08-07 11:11:51'),
(8, 'Export người dùng', 'users.export', 'users', '2026-08-07 11:11:51', '2026-08-07 11:11:51'),
(9, 'Xem vai trò', 'roles.view', 'roles', '2026-08-07 11:11:51', '2026-08-07 11:11:51'),
(10, 'Thêm vai trò', 'roles.create', 'roles', '2026-08-07 11:11:51', '2026-08-07 11:11:51'),
(11, 'Sửa vai trò', 'roles.update', 'roles', '2026-08-07 11:11:51', '2026-08-07 11:11:51'),
(12, 'Xóa vai trò', 'roles.delete', 'roles', '2026-08-07 11:11:51', '2026-08-07 11:11:51'),
(13, 'Duyệt khóa học', 'courses.approve', 'courses', '2026-08-07 11:11:51', '2026-08-07 11:11:51'),
(14, 'Xem audit log', 'audit.view', 'audit', '2026-08-07 11:11:51', '2026-08-07 11:11:51'),
(15, 'Quản lý người dùng', 'users.manage', 'users', '2026-08-07 11:11:52', '2026-08-07 11:11:52'),
(16, 'Xem khóa học', 'courses.view', 'courses', '2026-08-07 11:11:52', '2026-08-07 11:11:52'),
(17, 'Quản lý khóa học', 'courses.manage', 'courses', '2026-08-07 11:11:52', '2026-08-07 11:11:52'),
(18, 'Duyệt đơn giảng viên', 'instructor_applications.review', 'instructors', '2026-08-07 11:11:52', '2026-08-07 11:11:52'),
(19, 'Quản lý vai trò', 'roles.manage', 'roles', '2026-08-07 11:11:52', '2026-08-07 11:11:52');

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

CREATE TABLE `permission_role` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permission_role`
--

INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(16, 2),
(1, 5),
(15, 5),
(16, 5),
(17, 5),
(18, 5),
(19, 5);

-- --------------------------------------------------------

--
-- Table structure for table `push_notifications`
--

CREATE TABLE `push_notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` bigint UNSIGNED NOT NULL,
  `lesson_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `pass_score` int UNSIGNED NOT NULL DEFAULT '70',
  `time_limit_minutes` int UNSIGNED DEFAULT NULL,
  `max_attempts` int UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `quiz_id` bigint UNSIGNED NOT NULL,
  `score` int UNSIGNED NOT NULL DEFAULT '0',
  `total_score` int UNSIGNED NOT NULL DEFAULT '0',
  `percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `passed` tinyint(1) NOT NULL DEFAULT '0',
  `answers` json DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempt_answers`
--

CREATE TABLE `quiz_attempt_answers` (
  `id` bigint UNSIGNED NOT NULL,
  `quiz_attempt_id` bigint UNSIGNED NOT NULL,
  `question_id` bigint UNSIGNED NOT NULL,
  `answer_id` bigint UNSIGNED DEFAULT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_options`
--

CREATE TABLE `quiz_options` (
  `id` bigint UNSIGNED NOT NULL,
  `quiz_question_id` bigint UNSIGNED NOT NULL,
  `option_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `id` bigint UNSIGNED NOT NULL,
  `quiz_id` bigint UNSIGNED NOT NULL,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('single','multiple','true_false') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'single',
  `points` int UNSIGNED NOT NULL DEFAULT '1',
  `explanation` text COLLATE utf8mb4_unicode_ci,
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recently_viewed_courses`
--

CREATE TABLE `recently_viewed_courses` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `last_viewed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `rating` tinyint UNSIGNED DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'visible',
  `helpful_count` int UNSIGNED NOT NULL DEFAULT '0',
  `instructor_reply` text COLLATE utf8mb4_unicode_ci,
  `replied_by` bigint UNSIGNED DEFAULT NULL,
  `replied_at` timestamp NULL DEFAULT NULL,
  `moderated_by` bigint UNSIGNED DEFAULT NULL,
  `moderated_at` timestamp NULL DEFAULT NULL,
  `moderation_note` text COLLATE utf8mb4_unicode_ci,
  `verified_purchase` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `is_hidden` tinyint(1) NOT NULL DEFAULT '0'
) ;

-- --------------------------------------------------------

--
-- Table structure for table `review_helpful`
--

CREATE TABLE `review_helpful` (
  `id` bigint UNSIGNED NOT NULL,
  `review_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `is_system`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin', 'Toàn quyền quản trị hệ thống.', 1, '2026-08-07 11:11:51', '2026-08-07 11:11:51'),
(2, 'Instructor', 'instructor', 'Quản lý khóa học và học viên.', 1, '2026-08-07 11:11:51', '2026-08-07 11:11:51'),
(3, 'Student', 'student', 'Học viên sử dụng nền tảng.', 1, '2026-08-07 11:11:51', '2026-08-07 11:11:51'),
(4, 'User', 'user', 'Vai trò cơ bản cho tài khoản mới.', 1, '2026-08-07 11:11:51', '2026-08-07 11:11:51'),
(5, 'Super Admin', 'super_admin', 'Quản trị viên cấp cao', 1, '2026-08-07 11:11:52', '2026-08-07 11:11:52');

-- --------------------------------------------------------

--
-- Table structure for table `role_user`
--

CREATE TABLE `role_user` (
  `role_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_user`
--

INSERT INTO `role_user` (`role_id`, `user_id`) VALUES
(3, 1),
(2, 2),
(1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('HDuX0GQHNeTBHi8OiMQtFJHDpDpQWonLIo6XNEwZ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiIyUXNTcmRVWG5oSVN3MGltVlQ5dXpvNFhXUUtrZm1TNEkybTNub3V6IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1786351968),
('HfRONbK19vMLBQqAb8xKJCFpeqIWKQ2OSNRIURc6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.132.0 Chrome/148.0.7778.280 Electron/42.7.1 Safari/537.36', 'eyJfdG9rZW4iOiJPd1dJOHR2NmR2djRhdG4xNFVPTnVSa0xkNTlqTXkyWmpKTjUzVmRVIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1786380828),
('TmmJMJaMWP34lzbWWYtMAHgGn4ZTaFFdf9SLGVDW', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.132.0 Chrome/148.0.7778.280 Electron/42.7.1 Safari/537.36', 'eyJfdG9rZW4iOiJtNkdpdjNpOTUxdkxRRFhxaEp4WXJkUnBIaWpIT2NvTjU4blNKMWFPIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1786351931),
('TV2StEhj4Zbd2HavAuWAQTQZGbltWx6qAHTRtW6b', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJSTWViNTlFZDhFdlJvT0g3N0NiazI2cThyZjNSdE1QU0R3ajg2bVRuIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2FkbWluXC9kYXNoYm9hcmQiLCJyb3V0ZSI6ImFkbWluLmRhc2hib2FyZCJ9LCJhdXRoX2NhcHRjaGFzIjp7ImRnMFJrTGNtWDFSclVJa05FcGpYYkxHcE12YXBhY2dLRUdCQzIxQngiOnsicHVycG9zZSI6ImxvZ2luIiwiYW5zd2VyIjoiOCIsImV4cGlyZXNfYXQiOjE3ODYzNTQ2MjJ9fSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjN9', 1786354150),
('vsrdJHMOGnR5qAKze4oaV6ByI7uTVNIEpUk5G37T', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJUaFpzWFZ3aVpMU2gzWjZHNjZJZGZ0ZURCVjFxNExNSkhDdjVOdmJKIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2hvbWUiLCJyb3V0ZSI6ImhvbWUifSwiYXV0aF9jYXB0Y2hhcyI6W10sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjozfQ==', 1786356384);

-- --------------------------------------------------------

--
-- Table structure for table `social_accounts`
--

CREATE TABLE `social_accounts` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `provider` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider_user_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `study_groups`
--

CREATE TABLE `study_groups` (
  `id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `creator_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `max_members` int UNSIGNED NOT NULL DEFAULT '50',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `study_group_members`
--

CREATE TABLE `study_group_members` (
  `id` bigint UNSIGNED NOT NULL,
  `study_group_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `role` enum('member','moderator') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'member',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `study_group_messages`
--

CREATE TABLE `study_group_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `study_group_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `message_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `message` text COLLATE utf8mb4_unicode_ci,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `id` bigint UNSIGNED NOT NULL,
  `assignment_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `score` int UNSIGNED DEFAULT NULL,
  `feedback` text COLLATE utf8mb4_unicode_ci,
  `status` enum('submitted','graded','returned') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'submitted',
  `submitted_at` timestamp NULL DEFAULT NULL,
  `graded_at` timestamp NULL DEFAULT NULL,
  `graded_by` bigint UNSIGNED DEFAULT NULL,
  `grading_history` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('open','in_progress','resolved','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `priority` enum('low','medium','high') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `assigned_to` bigint UNSIGNED DEFAULT NULL,
  `last_replied_at` timestamp NULL DEFAULT NULL,
  `last_replied_by` bigint UNSIGNED DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_ticket_attachments`
--

CREATE TABLE `support_ticket_attachments` (
  `id` bigint UNSIGNED NOT NULL,
  `ticket_id` bigint UNSIGNED NOT NULL,
  `message_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_ticket_messages`
--

CREATE TABLE `support_ticket_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `ticket_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `two_factor_codes`
--

CREATE TABLE `two_factor_codes` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `phone_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('student','instructor','admin','super_admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'student',
  `instructor_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'pending, approved, rejected',
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `rejected_reason` text COLLATE utf8mb4_unicode_ci,
  `commission_rate` decimal(5,2) DEFAULT NULL,
  `bank_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive','pending','blocked') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `two_factor_secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `github_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `microsoft_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_learning_at` timestamp NULL DEFAULT NULL,
  `engagement_email_stage` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `last_engagement_sent_at` timestamp NULL DEFAULT NULL,
  `password_changed_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `email_verified_at`, `phone_verified_at`, `password`, `role`, `instructor_status`, `approved_at`, `approved_by`, `rejected_reason`, `commission_rate`, `bank_code`, `bank_name`, `bank_account_number`, `bank_account_name`, `status`, `avatar`, `bio`, `phone`, `google_id`, `facebook_id`, `two_factor_enabled`, `two_factor_secret`, `is_active`, `remember_token`, `created_at`, `updated_at`, `github_id`, `microsoft_id`, `last_login_at`, `last_login_ip`, `last_learning_at`, `engagement_email_stage`, `last_engagement_sent_at`, `password_changed_at`, `deleted_at`) VALUES
(1, 'Văn A', 'van_a', 'vana@gmail.com', NULL, NULL, '$2y$12$vi50UhM6L4Lm7jREx2Tw5ePh2tyvll9r3dHgkrIJNLA9JGbsq0rpi', 'student', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, '0343011588', NULL, NULL, 0, NULL, 1, NULL, '2026-08-07 11:41:30', '2026-08-07 11:41:30', NULL, NULL, NULL, NULL, NULL, 0, NULL, '2026-08-07 11:41:30', NULL),
(2, 'Giảng viên', 'giang_vien', 'giangvien@gmail.com', NULL, NULL, '$2y$12$aSkN2VbSWuWcP/Us7KrYcuEkbrYeVNQQ14Y/cxgv1Z.sgWlFXo1UC', 'instructor', 'approved', '2026-08-07 11:48:25', 3, NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, 'ABC', '0987612345', NULL, NULL, 0, NULL, 1, NULL, '2026-08-07 11:43:47', '2026-08-10 01:55:42', NULL, NULL, '2026-08-10 01:55:42', '127.0.0.1', NULL, 0, NULL, '2026-08-07 11:43:46', NULL),
(3, 'Admin', 'admin', 'admin@gmail.com', NULL, NULL, '$2y$12$F.oedZT9G5yKUrSkf1bf2OFU3wR9SyqXS6Zafp5imTQHwUrQwmoQ6', 'admin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL, NULL, NULL, 0, NULL, 1, 'q5xYphhLu8awg1mQbtr8k4Vko0wchqzJC4j84xrKhDe5WAiQ9WGhpWVUOuCC', '2026-08-07 11:46:28', '2026-08-10 02:42:52', NULL, NULL, '2026-08-10 02:42:52', '127.0.0.1', NULL, 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_badges`
--

CREATE TABLE `user_badges` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `badge_id` bigint UNSIGNED NOT NULL,
  `earned_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_points`
--

CREATE TABLE `user_points` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `points` int NOT NULL DEFAULT '0',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `course_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_points`
--

INSERT INTO `user_points` (`id`, `user_id`, `points`, `type`, `source`, `description`, `created_at`, `updated_at`, `course_id`) VALUES
(1, 1, 5, 'earn', 'daily_login', 'Đăng nhập mỗi ngày', '2026-08-07 11:41:30', '2026-08-07 11:41:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `video_access_logs`
--

CREATE TABLE `video_access_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `lesson_id` bigint UNSIGNED NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `browser` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platform` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `watch_started_at` timestamp NULL DEFAULT NULL,
  `watch_ended_at` timestamp NULL DEFAULT NULL,
  `watch_duration` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `video_moderations`
--

CREATE TABLE `video_moderations` (
  `id` bigint UNSIGNED NOT NULL,
  `lesson_id` bigint UNSIGNED NOT NULL,
  `violence` tinyint(1) NOT NULL DEFAULT '0',
  `adult` tinyint(1) NOT NULL DEFAULT '0',
  `weapon` tinyint(1) NOT NULL DEFAULT '0',
  `tiktok_logo` tinyint(1) NOT NULL DEFAULT '0',
  `youtube_logo` tinyint(1) NOT NULL DEFAULT '0',
  `watermark` tinyint(1) NOT NULL DEFAULT '0',
  `copyright_risk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `details` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `video_notes`
--

CREATE TABLE `video_notes` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `lesson_id` bigint UNSIGNED NOT NULL,
  `timestamp_seconds` int UNSIGNED NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `video_watch_histories`
--

CREATE TABLE `video_watch_histories` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `lesson_id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `current_time` int NOT NULL DEFAULT '0',
  `watched_seconds` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--

CREATE TABLE `withdrawals` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `bank_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_account_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_account_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `transaction_ref` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admin_note` text COLLATE utf8mb4_unicode_ci,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `active_sessions`
--
ALTER TABLE `active_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `active_sessions_session_id_unique` (`session_id`),
  ADD KEY `active_sessions_user_id_index` (`user_id`),
  ADD KEY `active_sessions_is_active_index` (`is_active`),
  ADD KEY `active_sessions_device_id_index` (`device_id`);

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_foreign` (`user_id`),
  ADD KEY `activity_logs_model_type_model_id_index` (`model_type`,`model_id`);

--
-- Indexes for table `ai_chat_messages`
--
ALTER TABLE `ai_chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ai_chat_messages_user_id_foreign` (`user_id`),
  ADD KEY `ai_chat_messages_lesson_id_foreign` (`lesson_id`);

--
-- Indexes for table `ai_summaries`
--
ALTER TABLE `ai_summaries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ai_summaries_lesson_id_language_unique` (`lesson_id`,`language`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assignments_lesson_id_foreign` (`lesson_id`),
  ADD KEY `assignments_course_id_foreign` (`course_id`);

--
-- Indexes for table `badges`
--
ALTER TABLE `badges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `carts_user_id_unique` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cart_items_cart_id_course_id_unique` (`cart_id`,`course_id`),
  ADD KEY `cart_items_course_id_foreign` (`course_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`),
  ADD KEY `categories_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `certificates_user_id_course_id_unique` (`user_id`,`course_id`),
  ADD UNIQUE KEY `certificates_certificate_code_unique` (`certificate_code`),
  ADD KEY `certificates_course_id_foreign` (`course_id`);

--
-- Indexes for table `chapters`
--
ALTER TABLE `chapters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chapters_course_id_foreign` (`course_id`);

--
-- Indexes for table `content_updates`
--
ALTER TABLE `content_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `content_updates_created_by_foreign` (`created_by`),
  ADD KEY `content_updates_reviewed_by_foreign` (`reviewed_by`),
  ADD KEY `content_updates_course_id_status_index` (`course_id`,`status`),
  ADD KEY `content_updates_type_entity_id_index` (`type`,`entity_id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `coupons_code_unique` (`code`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `courses_slug_unique` (`slug`),
  ADD KEY `courses_instructor_id_foreign` (`instructor_id`),
  ADD KEY `courses_copyright_agreed_by_foreign` (`copyright_agreed_by`),
  ADD KEY `courses_category_id_foreign` (`category_id`);

--
-- Indexes for table `course_reviews`
--
ALTER TABLE `course_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_reviews_course_id_submission_number_index` (`course_id`,`submission_number`),
  ADD KEY `course_reviews_status_submitted_at_index` (`status`,`submitted_at`),
  ADD KEY `course_reviews_reviewer_id_foreign` (`reviewer_id`);

--
-- Indexes for table `course_sections`
--
ALTER TABLE `course_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_sections_course_id_foreign` (`course_id`);

--
-- Indexes for table `discussions`
--
ALTER TABLE `discussions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discussions_lesson_id_foreign` (`lesson_id`),
  ADD KEY `discussions_user_id_foreign` (`user_id`);

--
-- Indexes for table `discussion_replies`
--
ALTER TABLE `discussion_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discussion_replies_discussion_id_foreign` (`discussion_id`),
  ADD KEY `discussion_replies_user_id_foreign` (`user_id`);

--
-- Indexes for table `email_verification_codes`
--
ALTER TABLE `email_verification_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email_verification_codes_user_id_expires_at_index` (`user_id`,`expires_at`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `enrollments_user_id_course_id_unique` (`user_id`,`course_id`),
  ADD KEY `enrollments_course_id_foreign` (`course_id`),
  ADD KEY `enrollments_order_id_foreign` (`order_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `homepage_settings`
--
ALTER TABLE `homepage_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `homepage_settings_key_unique` (`key`);

--
-- Indexes for table `instructor_applications`
--
ALTER TABLE `instructor_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `instructor_applications_user_id_unique` (`user_id`),
  ADD KEY `instructor_applications_reviewed_by_foreign` (`reviewed_by`);

--
-- Indexes for table `instructor_profiles`
--
ALTER TABLE `instructor_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `instructor_profiles_user_id_foreign` (`user_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `learning_paths`
--
ALTER TABLE `learning_paths`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `learning_paths_slug_unique` (`slug`);

--
-- Indexes for table `learning_path_courses`
--
ALTER TABLE `learning_path_courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `learning_path_courses_learning_path_id_course_id_unique` (`learning_path_id`,`course_id`),
  ADD KEY `learning_path_courses_course_id_foreign` (`course_id`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lessons_course_id_foreign` (`course_id`),
  ADD KEY `lessons_section_id_foreign` (`section_id`),
  ADD KEY `lessons_chapter_id_foreign` (`chapter_id`);

--
-- Indexes for table `lesson_ai_summaries`
--
ALTER TABLE `lesson_ai_summaries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lesson_ai_summaries_lesson_id_foreign` (`lesson_id`);

--
-- Indexes for table `lesson_attachments`
--
ALTER TABLE `lesson_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lesson_attachments_lesson_id_foreign` (`lesson_id`);

--
-- Indexes for table `lesson_notes`
--
ALTER TABLE `lesson_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lesson_notes_user_id_lesson_id_index` (`user_id`,`lesson_id`),
  ADD KEY `lesson_notes_lesson_id_timestamp_seconds_index` (`lesson_id`,`timestamp_seconds`),
  ADD KEY `lesson_notes_user_id_updated_at_index` (`user_id`,`updated_at`);

--
-- Indexes for table `lesson_progress`
--
ALTER TABLE `lesson_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lesson_progress_user_id_lesson_id_unique` (`user_id`,`lesson_id`),
  ADD KEY `lesson_progress_user_id_index` (`user_id`),
  ADD KEY `lesson_progress_lesson_id_index` (`lesson_id`),
  ADD KEY `lesson_progress_is_completed_index` (`is_completed`),
  ADD KEY `lesson_progress_course_id_foreign` (`course_id`);

--
-- Indexes for table `lesson_subtitles`
--
ALTER TABLE `lesson_subtitles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lesson_subtitles_lesson_id_foreign` (`lesson_id`);

--
-- Indexes for table `live_sessions`
--
ALTER TABLE `live_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `live_sessions_course_id_foreign` (`course_id`),
  ADD KEY `live_sessions_instructor_id_foreign` (`instructor_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_code_unique` (`order_code`),
  ADD KEY `orders_user_id_foreign` (`user_id`),
  ADD KEY `orders_coupon_id_foreign` (`coupon_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_items_order_id_course_id_unique` (`order_id`,`course_id`),
  ADD KEY `order_items_course_id_foreign` (`course_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_order_id_foreign` (`order_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_slug_unique` (`slug`);

--
-- Indexes for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `permission_role_role_id_foreign` (`role_id`);

--
-- Indexes for table `push_notifications`
--
ALTER TABLE `push_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `push_notifications_user_id_foreign` (`user_id`),
  ADD KEY `push_notifications_created_by_foreign` (`created_by`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quizzes_lesson_id_foreign` (`lesson_id`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_attempts_user_id_foreign` (`user_id`),
  ADD KEY `quiz_attempts_quiz_id_foreign` (`quiz_id`);

--
-- Indexes for table `quiz_attempt_answers`
--
ALTER TABLE `quiz_attempt_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_attempt_answers_question_id_foreign` (`question_id`),
  ADD KEY `quiz_attempt_answers_answer_id_foreign` (`answer_id`),
  ADD KEY `quiz_attempt_answers_quiz_attempt_id_question_id_index` (`quiz_attempt_id`,`question_id`);

--
-- Indexes for table `quiz_options`
--
ALTER TABLE `quiz_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_options_quiz_question_id_foreign` (`quiz_question_id`);

--
-- Indexes for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_questions_quiz_id_foreign` (`quiz_id`);

--
-- Indexes for table `recently_viewed_courses`
--
ALTER TABLE `recently_viewed_courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `recently_viewed_courses_user_id_course_id_unique` (`user_id`,`course_id`),
  ADD KEY `recently_viewed_courses_course_id_foreign` (`course_id`),
  ADD KEY `recently_viewed_courses_user_last_viewed_at_index` (`user_id`,`last_viewed_at`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviews_replied_by_foreign` (`replied_by`),
  ADD KEY `reviews_moderated_by_foreign` (`moderated_by`),
  ADD KEY `reviews_course_status_created_index` (`course_id`,`status`,`created_at`),
  ADD KEY `reviews_course_rating_status_index` (`course_id`,`rating`,`status`),
  ADD KEY `reviews_user_id_foreign` (`user_id`),
  ADD KEY `reviews_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `review_helpful`
--
ALTER TABLE `review_helpful`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `review_helpful_review_user_unique` (`review_id`,`user_id`),
  ADD KEY `review_helpful_user_created_index` (`user_id`,`created_at`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_slug_unique` (`slug`);

--
-- Indexes for table `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`role_id`,`user_id`),
  ADD KEY `role_user_user_id_foreign` (`user_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `social_accounts`
--
ALTER TABLE `social_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `social_accounts_provider_provider_user_id_unique` (`provider`,`provider_user_id`),
  ADD KEY `social_accounts_user_id_provider_index` (`user_id`,`provider`),
  ADD KEY `social_accounts_provider_email_index` (`provider_email`);

--
-- Indexes for table `study_groups`
--
ALTER TABLE `study_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `study_groups_course_id_foreign` (`course_id`),
  ADD KEY `study_groups_creator_id_foreign` (`creator_id`);

--
-- Indexes for table `study_group_members`
--
ALTER TABLE `study_group_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `study_group_members_study_group_id_user_id_unique` (`study_group_id`,`user_id`),
  ADD KEY `study_group_members_user_id_foreign` (`user_id`);

--
-- Indexes for table `study_group_messages`
--
ALTER TABLE `study_group_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `study_group_messages_study_group_id_foreign` (`study_group_id`),
  ADD KEY `study_group_messages_user_id_foreign` (`user_id`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `submissions_assignment_id_user_id_unique` (`assignment_id`,`user_id`),
  ADD KEY `submissions_user_id_foreign` (`user_id`),
  ADD KEY `submissions_graded_by_foreign` (`graded_by`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `support_tickets_code_unique` (`code`),
  ADD KEY `support_tickets_user_id_foreign` (`user_id`),
  ADD KEY `support_tickets_assigned_to_foreign` (`assigned_to`),
  ADD KEY `support_tickets_last_replied_by_foreign` (`last_replied_by`);

--
-- Indexes for table `support_ticket_attachments`
--
ALTER TABLE `support_ticket_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `support_ticket_attachments_message_id_foreign` (`message_id`),
  ADD KEY `support_ticket_attachments_user_id_foreign` (`user_id`),
  ADD KEY `support_ticket_attachments_ticket_id_message_id_index` (`ticket_id`,`message_id`);

--
-- Indexes for table `support_ticket_messages`
--
ALTER TABLE `support_ticket_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `support_ticket_messages_ticket_id_foreign` (`ticket_id`),
  ADD KEY `support_ticket_messages_user_id_foreign` (`user_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `two_factor_codes`
--
ALTER TABLE `two_factor_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `two_factor_codes_user_id_code_index` (`user_id`,`code`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_github_id_unique` (`github_id`),
  ADD UNIQUE KEY `users_microsoft_id_unique` (`microsoft_id`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`),
  ADD KEY `users_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `user_badges`
--
ALTER TABLE `user_badges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_badges_user_id_badge_id_unique` (`user_id`,`badge_id`),
  ADD KEY `user_badges_badge_id_foreign` (`badge_id`);

--
-- Indexes for table `user_points`
--
ALTER TABLE `user_points`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_points_user_id_foreign` (`user_id`),
  ADD KEY `user_points_course_id_foreign` (`course_id`);

--
-- Indexes for table `video_access_logs`
--
ALTER TABLE `video_access_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `video_access_logs_user_id_index` (`user_id`),
  ADD KEY `video_access_logs_lesson_id_index` (`lesson_id`),
  ADD KEY `video_access_logs_watch_started_at_index` (`watch_started_at`);

--
-- Indexes for table `video_moderations`
--
ALTER TABLE `video_moderations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `video_moderations_lesson_id_foreign` (`lesson_id`);

--
-- Indexes for table `video_notes`
--
ALTER TABLE `video_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `video_notes_user_id_foreign` (`user_id`),
  ADD KEY `video_notes_lesson_id_foreign` (`lesson_id`);

--
-- Indexes for table `video_watch_histories`
--
ALTER TABLE `video_watch_histories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `video_watch_histories_user_id_lesson_id_unique` (`user_id`,`lesson_id`),
  ADD KEY `video_watch_histories_lesson_id_foreign` (`lesson_id`),
  ADD KEY `video_watch_histories_course_id_foreign` (`course_id`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wishlists_user_id_course_id_unique` (`user_id`,`course_id`),
  ADD KEY `wishlists_course_id_foreign` (`course_id`);

--
-- Indexes for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `withdrawals_user_id_foreign` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `active_sessions`
--
ALTER TABLE `active_sessions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `ai_chat_messages`
--
ALTER TABLE `ai_chat_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_summaries`
--
ALTER TABLE `ai_summaries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `badges`
--
ALTER TABLE `badges`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chapters`
--
ALTER TABLE `chapters`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `content_updates`
--
ALTER TABLE `content_updates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `course_reviews`
--
ALTER TABLE `course_reviews`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_sections`
--
ALTER TABLE `course_sections`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `discussions`
--
ALTER TABLE `discussions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discussion_replies`
--
ALTER TABLE `discussion_replies`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_verification_codes`
--
ALTER TABLE `email_verification_codes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `homepage_settings`
--
ALTER TABLE `homepage_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `instructor_applications`
--
ALTER TABLE `instructor_applications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `instructor_profiles`
--
ALTER TABLE `instructor_profiles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `learning_paths`
--
ALTER TABLE `learning_paths`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `learning_path_courses`
--
ALTER TABLE `learning_path_courses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lesson_ai_summaries`
--
ALTER TABLE `lesson_ai_summaries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lesson_attachments`
--
ALTER TABLE `lesson_attachments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lesson_notes`
--
ALTER TABLE `lesson_notes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lesson_progress`
--
ALTER TABLE `lesson_progress`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lesson_subtitles`
--
ALTER TABLE `lesson_subtitles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `live_sessions`
--
ALTER TABLE `live_sessions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `push_notifications`
--
ALTER TABLE `push_notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_attempt_answers`
--
ALTER TABLE `quiz_attempt_answers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_options`
--
ALTER TABLE `quiz_options`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recently_viewed_courses`
--
ALTER TABLE `recently_viewed_courses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review_helpful`
--
ALTER TABLE `review_helpful`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `social_accounts`
--
ALTER TABLE `social_accounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `study_groups`
--
ALTER TABLE `study_groups`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `study_group_members`
--
ALTER TABLE `study_group_members`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `study_group_messages`
--
ALTER TABLE `study_group_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_ticket_attachments`
--
ALTER TABLE `support_ticket_attachments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_ticket_messages`
--
ALTER TABLE `support_ticket_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `two_factor_codes`
--
ALTER TABLE `two_factor_codes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_badges`
--
ALTER TABLE `user_badges`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_points`
--
ALTER TABLE `user_points`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `video_access_logs`
--
ALTER TABLE `video_access_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `video_moderations`
--
ALTER TABLE `video_moderations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `video_notes`
--
ALTER TABLE `video_notes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `video_watch_histories`
--
ALTER TABLE `video_watch_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `active_sessions`
--
ALTER TABLE `active_sessions`
  ADD CONSTRAINT `active_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ai_chat_messages`
--
ALTER TABLE `ai_chat_messages`
  ADD CONSTRAINT `ai_chat_messages_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ai_chat_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ai_summaries`
--
ALTER TABLE `ai_summaries`
  ADD CONSTRAINT `ai_summaries_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignments_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chapters`
--
ALTER TABLE `chapters`
  ADD CONSTRAINT `chapters_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `content_updates`
--
ALTER TABLE `content_updates`
  ADD CONSTRAINT `content_updates_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `content_updates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `content_updates_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `courses_copyright_agreed_by_foreign` FOREIGN KEY (`copyright_agreed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `courses_instructor_id_foreign` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_reviews`
--
ALTER TABLE `course_reviews`
  ADD CONSTRAINT `course_reviews_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_reviews_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `course_sections`
--
ALTER TABLE `course_sections`
  ADD CONSTRAINT `course_sections_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `discussions`
--
ALTER TABLE `discussions`
  ADD CONSTRAINT `discussions_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discussions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `discussion_replies`
--
ALTER TABLE `discussion_replies`
  ADD CONSTRAINT `discussion_replies_discussion_id_foreign` FOREIGN KEY (`discussion_id`) REFERENCES `discussions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discussion_replies_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `email_verification_codes`
--
ALTER TABLE `email_verification_codes`
  ADD CONSTRAINT `email_verification_codes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `enrollments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `instructor_applications`
--
ALTER TABLE `instructor_applications`
  ADD CONSTRAINT `instructor_applications_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `instructor_applications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `instructor_profiles`
--
ALTER TABLE `instructor_profiles`
  ADD CONSTRAINT `instructor_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `learning_path_courses`
--
ALTER TABLE `learning_path_courses`
  ADD CONSTRAINT `learning_path_courses_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `learning_path_courses_learning_path_id_foreign` FOREIGN KEY (`learning_path_id`) REFERENCES `learning_paths` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_chapter_id_foreign` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lessons_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lessons_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `course_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lesson_ai_summaries`
--
ALTER TABLE `lesson_ai_summaries`
  ADD CONSTRAINT `lesson_ai_summaries_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lesson_attachments`
--
ALTER TABLE `lesson_attachments`
  ADD CONSTRAINT `lesson_attachments_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lesson_notes`
--
ALTER TABLE `lesson_notes`
  ADD CONSTRAINT `lesson_notes_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lesson_notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lesson_progress`
--
ALTER TABLE `lesson_progress`
  ADD CONSTRAINT `lesson_progress_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lesson_progress_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lesson_progress_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lesson_subtitles`
--
ALTER TABLE `lesson_subtitles`
  ADD CONSTRAINT `lesson_subtitles_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `live_sessions`
--
ALTER TABLE `live_sessions`
  ADD CONSTRAINT `live_sessions_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `live_sessions_instructor_id_foreign` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `push_notifications`
--
ALTER TABLE `push_notifications`
  ADD CONSTRAINT `push_notifications_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `push_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `quiz_attempts_quiz_id_foreign` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_attempts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_attempt_answers`
--
ALTER TABLE `quiz_attempt_answers`
  ADD CONSTRAINT `quiz_attempt_answers_answer_id_foreign` FOREIGN KEY (`answer_id`) REFERENCES `quiz_options` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `quiz_attempt_answers_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_attempt_answers_quiz_attempt_id_foreign` FOREIGN KEY (`quiz_attempt_id`) REFERENCES `quiz_attempts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_options`
--
ALTER TABLE `quiz_options`
  ADD CONSTRAINT `quiz_options_quiz_question_id_foreign` FOREIGN KEY (`quiz_question_id`) REFERENCES `quiz_questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD CONSTRAINT `quiz_questions_quiz_id_foreign` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recently_viewed_courses`
--
ALTER TABLE `recently_viewed_courses`
  ADD CONSTRAINT `recently_viewed_courses_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recently_viewed_courses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_moderated_by_foreign` FOREIGN KEY (`moderated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reviews_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_replied_by_foreign` FOREIGN KEY (`replied_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `review_helpful`
--
ALTER TABLE `review_helpful`
  ADD CONSTRAINT `review_helpful_review_id_foreign` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `review_helpful_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_user`
--
ALTER TABLE `role_user`
  ADD CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `social_accounts`
--
ALTER TABLE `social_accounts`
  ADD CONSTRAINT `social_accounts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `study_groups`
--
ALTER TABLE `study_groups`
  ADD CONSTRAINT `study_groups_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `study_groups_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `study_group_members`
--
ALTER TABLE `study_group_members`
  ADD CONSTRAINT `study_group_members_study_group_id_foreign` FOREIGN KEY (`study_group_id`) REFERENCES `study_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `study_group_members_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `study_group_messages`
--
ALTER TABLE `study_group_messages`
  ADD CONSTRAINT `study_group_messages_study_group_id_foreign` FOREIGN KEY (`study_group_id`) REFERENCES `study_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `study_group_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `submissions_assignment_id_foreign` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `submissions_graded_by_foreign` FOREIGN KEY (`graded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `submissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD CONSTRAINT `support_tickets_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `support_tickets_last_replied_by_foreign` FOREIGN KEY (`last_replied_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `support_tickets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `support_ticket_attachments`
--
ALTER TABLE `support_ticket_attachments`
  ADD CONSTRAINT `support_ticket_attachments_message_id_foreign` FOREIGN KEY (`message_id`) REFERENCES `support_ticket_messages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `support_ticket_attachments_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `support_ticket_attachments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `support_ticket_messages`
--
ALTER TABLE `support_ticket_messages`
  ADD CONSTRAINT `support_ticket_messages_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `support_ticket_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `two_factor_codes`
--
ALTER TABLE `two_factor_codes`
  ADD CONSTRAINT `two_factor_codes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_badges`
--
ALTER TABLE `user_badges`
  ADD CONSTRAINT `user_badges_badge_id_foreign` FOREIGN KEY (`badge_id`) REFERENCES `badges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_badges_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_points`
--
ALTER TABLE `user_points`
  ADD CONSTRAINT `user_points_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_points_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `video_access_logs`
--
ALTER TABLE `video_access_logs`
  ADD CONSTRAINT `video_access_logs_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `video_access_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `video_moderations`
--
ALTER TABLE `video_moderations`
  ADD CONSTRAINT `video_moderations_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `video_notes`
--
ALTER TABLE `video_notes`
  ADD CONSTRAINT `video_notes_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `video_notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `video_watch_histories`
--
ALTER TABLE `video_watch_histories`
  ADD CONSTRAINT `video_watch_histories_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `video_watch_histories_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `video_watch_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD CONSTRAINT `withdrawals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
