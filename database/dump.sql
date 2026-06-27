
-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th6 21, 2026 lúc 02:09 PM
-- Phiên bản máy phục vụ: 8.0.30
-- Phiên bản PHP: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `website_datn_wd14`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_id` bigint UNSIGNED DEFAULT NULL,
  `properties` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `model_type`, `model_id`, `properties`, `ip_address`, `created_at`, `updated_at`) VALUES
(1, 3, 'login', 'App\\Models\\User', 3, NULL, '127.0.0.1', '2026-06-21 06:34:35', '2026-06-21 06:34:35'),
(2, 1, 'login', 'App\\Models\\User', 1, NULL, '127.0.0.1', '2026-06-21 06:48:21', '2026-06-21 06:48:21'),
(3, 1, 'logout', 'App\\Models\\User', 1, NULL, '127.0.0.1', '2026-06-21 06:49:15', '2026-06-21 06:49:15'),
(4, 2, 'login', 'App\\Models\\User', 2, NULL, '127.0.0.1', '2026-06-21 06:49:22', '2026-06-21 06:49:22'),
(5, 1, 'login', 'App\\Models\\User', 1, NULL, '127.0.0.1', '2026-06-21 06:59:51', '2026-06-21 06:59:51'),
(6, 3, 'login', 'App\\Models\\User', 3, NULL, '127.0.0.1', '2026-06-21 07:00:14', '2026-06-21 07:00:14'),
(7, 2, 'login', 'App\\Models\\User', 2, NULL, '127.0.0.1', '2026-06-21 07:00:20', '2026-06-21 07:00:20'),
(8, 3, 'login', 'App\\Models\\User', 3, NULL, '127.0.0.1', '2026-06-21 07:00:26', '2026-06-21 07:00:26'),
(9, 2, 'logout', 'App\\Models\\User', 2, NULL, '127.0.0.1', '2026-06-21 07:01:23', '2026-06-21 07:01:23'),
(10, 1, 'login', 'App\\Models\\User', 1, NULL, '127.0.0.1', '2026-06-21 07:01:31', '2026-06-21 07:01:31'),
(11, 1, 'logout', 'App\\Models\\User', 1, NULL, '127.0.0.1', '2026-06-21 07:02:43', '2026-06-21 07:02:43'),
(12, 2, 'login', 'App\\Models\\User', 2, NULL, '127.0.0.1', '2026-06-21 07:02:49', '2026-06-21 07:02:49');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ai_chat_messages`
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
-- Cấu trúc bảng cho bảng `ai_summaries`
--

CREATE TABLE `ai_summaries` (
  `id` bigint UNSIGNED NOT NULL,
  `lesson_id` bigint UNSIGNED NOT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'vi',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `assignments`
--

CREATE TABLE `assignments` (
  `id` bigint UNSIGNED NOT NULL,
  `lesson_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `due_date` timestamp NULL DEFAULT NULL,
  `max_score` int UNSIGNED NOT NULL DEFAULT '100',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `badges`
--

CREATE TABLE `badges` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `points_required` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `badges`
--

INSERT INTO `badges` (`id`, `name`, `slug`, `description`, `icon`, `points_required`, `created_at`, `updated_at`) VALUES
(1, 'Học viên chăm chỉ', 'diligent-student', 'Hoàn thành 5 bài học', NULL, 50, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(2, 'Bậc thầy', 'master', 'Hoàn thành 1 khóa học', NULL, 100, '2026-06-21 06:31:54', '2026-06-21 06:31:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `carts`
--

CREATE TABLE `carts` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart_items`
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
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `created_at`, `updated_at`) VALUES
(1, 'Lập trình Web', 'lap-trinh-web', 'Khóa học về phát triển web', NULL, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(2, 'Thiết kế UI/UX', 'thiet-ke-ui-ux', 'Khóa học thiết kế giao diện', NULL, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(3, 'Data Science', 'data-science', 'Khoa học dữ liệu và AI', NULL, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(4, 'Marketing', 'marketing', 'Digital Marketing', NULL, '2026-06-21 06:31:54', '2026-06-21 06:31:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `certificates`
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
-- Cấu trúc bảng cho bảng `chapters`
--

CREATE TABLE `chapters` (
  `id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chapters`
--

INSERT INTO `chapters` (`id`, `course_id`, `title`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'Chương 1: Giới thiệu', 0, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(2, 2, 'Chương 1: Giới thiệu', 0, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(3, 3, 'Chương 1: Giới thiệu', 0, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(4, 4, 'Chương 1: Giới thiệu', 0, '2026-06-21 06:31:54', '2026-06-21 06:31:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `coupons`
--

CREATE TABLE `coupons` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('percent','fixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percent',
  `value` decimal(12,2) NOT NULL,
  `min_order_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `max_uses` int UNSIGNED DEFAULT NULL,
  `used_count` int UNSIGNED NOT NULL DEFAULT '0',
  `starts_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `type`, `value`, `min_order_amount`, `max_uses`, `used_count`, `starts_at`, `expires_at`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'WELCOME20', 'percent', 20.00, 100000.00, 100, 0, NULL, '2026-09-21 06:31:54', 1, '2026-06-21 06:31:54', '2026-06-21 06:31:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `courses`
--

CREATE TABLE `courses` (
  `id` bigint UNSIGNED NOT NULL,
  `instructor_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `objectives` text COLLATE utf8mb4_unicode_ci,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preview_video` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` enum('beginner','intermediate','advanced') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'beginner',
  `price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `sale_price` decimal(12,2) DEFAULT NULL,
  `status` enum('draft','pending','published','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `rating_avg` decimal(3,2) NOT NULL DEFAULT '0.00',
  `rating_count` int UNSIGNED NOT NULL DEFAULT '0',
  `enrollment_count` int UNSIGNED NOT NULL DEFAULT '0',
  `duration_minutes` int UNSIGNED NOT NULL DEFAULT '0',
  `tags` json DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `courses`
--

INSERT INTO `courses` (`id`, `instructor_id`, `category_id`, `title`, `slug`, `description`, `objectives`, `thumbnail`, `preview_video`, `level`, `price`, `sale_price`, `status`, `rejection_reason`, `rating_avg`, `rating_count`, `enrollment_count`, `duration_minutes`, `tags`, `is_featured`, `published_at`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'Laravel từ Zero đến Hero', 'laravel-tu-zero-den-hero', 'Mô tả chi tiết khóa học Laravel từ Zero đến Hero', 'Nắm vững kiến thức và thực hành dự án thực tế', NULL, NULL, 'beginner', 499000.00, 299000.00, 'published', NULL, 4.50, 10, 0, 0, '[\"php\", \"web\", \"programming\"]', 1, '2026-06-21 06:31:54', '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(2, 2, 2, 'React.js Masterclass', 'reactjs-masterclass', 'Mô tả chi tiết khóa học React.js Masterclass', 'Nắm vững kiến thức và thực hành dự án thực tế', NULL, NULL, 'intermediate', 599000.00, NULL, 'published', NULL, 4.50, 10, 0, 0, '[\"php\", \"web\", \"programming\"]', 1, '2026-06-21 06:31:54', '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(3, 2, 3, 'UI/UX Design Fundamentals', 'uiux-design-fundamentals', 'Mô tả chi tiết khóa học UI/UX Design Fundamentals', 'Nắm vững kiến thức và thực hành dự án thực tế', NULL, NULL, 'beginner', 399000.00, 199000.00, 'published', NULL, 4.50, 10, 0, 0, '[\"php\", \"web\", \"programming\"]', 0, '2026-06-21 06:31:54', '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(4, 2, 4, 'Python cho Data Science', 'python-cho-data-science', 'Mô tả chi tiết khóa học Python cho Data Science', 'Nắm vững kiến thức và thực hành dự án thực tế', NULL, NULL, 'advanced', 699000.00, 499000.00, 'published', NULL, 4.50, 10, 0, 0, '[\"php\", \"web\", \"programming\"]', 0, '2026-06-21 06:31:54', '2026-06-21 06:31:54', '2026-06-21 06:31:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `discussions`
--

CREATE TABLE `discussions` (
  `id` bigint UNSIGNED NOT NULL,
  `lesson_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_resolved` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `discussion_replies`
--

CREATE TABLE `discussion_replies` (
  `id` bigint UNSIGNED NOT NULL,
  `discussion_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_instructor_answer` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `enrollments`
--

CREATE TABLE `enrollments` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `progress_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `faqs`
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

--
-- Đang đổ dữ liệu cho bảng `faqs`
--

INSERT INTO `faqs` (`id`, `question`, `answer`, `category`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Làm sao để đăng ký khóa học?', 'Bạn có thể thêm khóa học vào giỏ hàng và thanh toán qua Momo, VNPay hoặc chuyển khoản.', NULL, 1, 1, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(2, 'Tôi có thể học thử miễn phí không?', 'Có, mỗi khóa học có các bài preview miễn phí.', NULL, 2, 1, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(3, 'Làm sao để nhận chứng chỉ?', 'Hoàn thành 100% khóa học để nhận chứng chỉ PDF.', NULL, 3, 1, '2026-06-21 06:31:54', '2026-06-21 06:31:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `homepage_settings`
--

CREATE TABLE `homepage_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `homepage_settings`
--

INSERT INTO `homepage_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'banner', '{\"image\": \"/images/banner.jpg\", \"title\": \"Học mọi lúc, mọi nơi\", \"subtitle\": \"Nền tảng học trực tuyến hàng đầu\"}', '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(2, 'featured_course_ids', '[1, 2]', '2026-06-21 06:31:54', '2026-06-21 06:31:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `job_batches`
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
-- Cấu trúc bảng cho bảng `learning_paths`
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

--
-- Đang đổ dữ liệu cho bảng `learning_paths`
--

INSERT INTO `learning_paths` (`id`, `title`, `slug`, `description`, `thumbnail`, `level`, `created_at`, `updated_at`) VALUES
(1, 'Lộ trình Fullstack Developer', 'fullstack-developer', 'Học theo trình tự để trở thành Fullstack Developer', NULL, 'intermediate', '2026-06-21 06:31:54', '2026-06-21 06:31:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `learning_path_courses`
--

CREATE TABLE `learning_path_courses` (
  `id` bigint UNSIGNED NOT NULL,
  `learning_path_id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `learning_path_courses`
--

INSERT INTO `learning_path_courses` (`id`, `learning_path_id`, `course_id`, `sort_order`) VALUES
(1, 1, 1, 0),
(2, 1, 2, 1),
(3, 1, 3, 2),
(4, 1, 4, 3);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lessons`
--

CREATE TABLE `lessons` (
  `id` bigint UNSIGNED NOT NULL,
  `chapter_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `type` enum('video','document','quiz','assignment') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'video',
  `video_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration_seconds` int UNSIGNED NOT NULL DEFAULT '0',
  `is_preview` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `lessons`
--

INSERT INTO `lessons` (`id`, `chapter_id`, `title`, `content`, `type`, `video_url`, `duration_seconds`, `is_preview`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'Bài 1: Tổng quan khóa học', 'Nội dung bài giảng giới thiệu về Laravel từ Zero đến Hero', 'video', 'https://example.com/video1.mp4', 600, 1, 0, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(2, 1, 'Bài 2: Kiến thức nền tảng', 'Nội dung bài giảng nền tảng', 'video', 'https://example.com/video2.mp4', 1200, 0, 1, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(3, 2, 'Bài 1: Tổng quan khóa học', 'Nội dung bài giảng giới thiệu về React.js Masterclass', 'video', 'https://example.com/video1.mp4', 600, 1, 0, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(4, 2, 'Bài 2: Kiến thức nền tảng', 'Nội dung bài giảng nền tảng', 'video', 'https://example.com/video2.mp4', 1200, 0, 1, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(5, 3, 'Bài 1: Tổng quan khóa học', 'Nội dung bài giảng giới thiệu về UI/UX Design Fundamentals', 'video', 'https://example.com/video1.mp4', 600, 1, 0, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(6, 3, 'Bài 2: Kiến thức nền tảng', 'Nội dung bài giảng nền tảng', 'video', 'https://example.com/video2.mp4', 1200, 0, 1, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(7, 4, 'Bài 1: Tổng quan khóa học', 'Nội dung bài giảng giới thiệu về Python cho Data Science', 'video', 'https://example.com/video1.mp4', 600, 1, 0, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(8, 4, 'Bài 2: Kiến thức nền tảng', 'Nội dung bài giảng nền tảng', 'video', 'https://example.com/video2.mp4', 1200, 0, 1, '2026-06-21 06:31:54', '2026-06-21 06:31:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lesson_attachments`
--

CREATE TABLE `lesson_attachments` (
  `id` bigint UNSIGNED NOT NULL,
  `lesson_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` bigint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lesson_progress`
--

CREATE TABLE `lesson_progress` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `lesson_id` bigint UNSIGNED NOT NULL,
  `watched_seconds` int UNSIGNED NOT NULL DEFAULT '0',
  `is_completed` tinyint(1) NOT NULL DEFAULT '0',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lesson_subtitles`
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
-- Cấu trúc bảng cho bảng `live_sessions`
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
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_06_01_000001_create_categories_and_courses_tables', 1),
(5, '2024_06_01_000002_create_cart_and_order_tables', 1),
(6, '2024_06_01_000003_create_learning_tables', 1),
(7, '2024_06_01_000004_create_social_and_engagement_tables', 1),
(8, '2024_06_01_000005_create_system_tables', 1),
(9, '2026_06_21_132509_create_personal_access_tokens_table', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint UNSIGNED NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payments`
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
-- Cấu trúc bảng cho bảng `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 3, 'auth-token', 'f338f46e4215e4c8ef7a7c5600aecb0d56459d1a7a77e323d867ddf336305b49', '[\"*\"]', NULL, NULL, '2026-06-21 06:34:35', '2026-06-21 06:34:35');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `push_notifications`
--

CREATE TABLE `push_notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `target` enum('all','students','instructors') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `quizzes`
--

CREATE TABLE `quizzes` (
  `id` bigint UNSIGNED NOT NULL,
  `lesson_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pass_score` int UNSIGNED NOT NULL DEFAULT '70',
  `time_limit_minutes` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `quizzes`
--

INSERT INTO `quizzes` (`id`, `lesson_id`, `title`, `pass_score`, `time_limit_minutes`, `created_at`, `updated_at`) VALUES
(1, 2, 'Quiz chương 1', 70, NULL, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(2, 4, 'Quiz chương 1', 70, NULL, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(3, 6, 'Quiz chương 1', 70, NULL, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(4, 8, 'Quiz chương 1', 70, NULL, '2026-06-21 06:31:54', '2026-06-21 06:31:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `quiz_id` bigint UNSIGNED NOT NULL,
  `score` int UNSIGNED NOT NULL DEFAULT '0',
  `passed` tinyint(1) NOT NULL DEFAULT '0',
  `answers` json DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `quiz_options`
--

CREATE TABLE `quiz_options` (
  `id` bigint UNSIGNED NOT NULL,
  `quiz_question_id` bigint UNSIGNED NOT NULL,
  `option_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `quiz_options`
--

INSERT INTO `quiz_options` (`id`, `quiz_question_id`, `option_text`, `is_correct`, `created_at`, `updated_at`) VALUES
(1, 1, 'Đúng', 1, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(2, 1, 'Sai', 0, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(3, 2, 'Đúng', 1, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(4, 2, 'Sai', 0, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(5, 3, 'Đúng', 1, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(6, 3, 'Sai', 0, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(7, 4, 'Đúng', 1, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(8, 4, 'Sai', 0, '2026-06-21 06:31:54', '2026-06-21 06:31:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `id` bigint UNSIGNED NOT NULL,
  `quiz_id` bigint UNSIGNED NOT NULL,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('single','multiple') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'single',
  `points` int UNSIGNED NOT NULL DEFAULT '1',
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `quiz_questions`
--

INSERT INTO `quiz_questions` (`id`, `quiz_id`, `question`, `type`, `points`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'Đây có phải là câu hỏi mẫu?', 'single', 10, 0, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(2, 2, 'Đây có phải là câu hỏi mẫu?', 'single', 10, 0, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(3, 3, 'Đây có phải là câu hỏi mẫu?', 'single', 10, 0, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(4, 4, 'Đây có phải là câu hỏi mẫu?', 'single', 10, 0, '2026-06-21 06:31:54', '2026-06-21 06:31:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `recently_viewed_courses`
--

CREATE TABLE `recently_viewed_courses` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `viewed_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `rating` tinyint UNSIGNED NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `review_replies`
--

CREATE TABLE `review_replies` (
  `id` bigint UNSIGNED NOT NULL,
  `review_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sessions`
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
-- Đang đổ dữ liệu cho bảng `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('2fSpd7wRk6FdtWyGFlyjtoE2nTh14f7VWc7FnmWw', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.8655', 'eyJfdG9rZW4iOiJYR2Z2amJSUGl2cENPTUh4UGhWOEQwTkFtb3RhYzgzRWM1bHhLNFBpIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hZG1pblwvZGFzaGJvYXJkIiwicm91dGUiOiJhZG1pbi5kYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6WyJlcnJvciJdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjMsImVycm9yIjoiQlx1MWVhMW4ga2hcdTAwZjRuZyBjXHUwMGYzIHF1eVx1MWVjMW4gdHJ1eSBjXHUxZWFkcCB0cmFuZyBuXHUwMGUweS4ifQ==', 1782050427),
('Az8tfNrd5tV1xNgUL94tID7WdfWfAG10Wpw0f8i0', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.8655', 'eyJfdG9rZW4iOiJNRno1akdyVnZucjFPeFhlY3p1WjJDTzVORjJoSDdtOVpiOTBwV0hkIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9pbnN0cnVjdG9yXC9kYXNoYm9hcmQiLCJyb3V0ZSI6Imluc3RydWN0b3IuZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjJ9', 1782050424),
('CnPnbGqq0ejoRzVij4KEMLOsRnuKFz3aRKCKDbpJ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.8655', 'eyJfdG9rZW4iOiI3WFk0U01oZW1ucllReXdXb2xYQUd6cE05b3UyZ1FZNUhaQVZ6SE56IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1782049498),
('ErTtt0vbD4VdxuJZJHaeiMBLk3y0fLkUHWxtpsrP', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'eyJfdG9rZW4iOiJ5RnBrbWY2NzFHR1pWcGZNTXRXTkVJbHhPWFVIYW1lSTVrOHJtamlFIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2luc3RydWN0b3JcL3JldmVudWUiLCJyb3V0ZSI6Imluc3RydWN0b3IucmV2ZW51ZSJ9LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6Mn0=', 1782050579),
('HmJ9kz3DMXebRfXuZJFxaEr3yyc08h0Mo6qrl0rR', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.8655', 'eyJfdG9rZW4iOiJtelhhU0tDaVlrYWdCVXZQZEVUdmlxbkExNk95WlV1NHFYcHpHYzZXIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9zdHVkZW50XC9kYXNoYm9hcmQiLCJyb3V0ZSI6InN0dWRlbnQuZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjN9', 1782050418),
('K2dqaN4stdQZ8PhKEQ376M9fDNjp6e2ObM6h1uZL', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.8655', 'eyJfdG9rZW4iOiI2ZUpwa2VRcHlYMEx4cWhOUnBMbHpDb3dTazRTbFUwRGZLMUV5Nlc5IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9jb3Vyc2VzXC8xIiwicm91dGUiOiJjb3Vyc2VzLnNob3cifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1782049547),
('PrqGcgfCYXdIfybGNH0p3vIboKivLsxeq29WIgae', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.8655', 'eyJfdG9rZW4iOiJuZU1seGs0WFB2QU9uY1lDUm84SkxWR05YRTZVTWtWd3IyZ2pVeUs1IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hZG1pblwvZGFzaGJvYXJkIiwicm91dGUiOiJhZG1pbi5kYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MX0=', 1782050393),
('Qq8cxQEWpYjsinwg4p9Hfzs1Y0ZcdYPykhtfPgBe', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.124.2 Chrome/148.0.7778.97 Electron/42.2.0 Safari/537.36', 'eyJfdG9rZW4iOiJpNjBCTldadm9sTEE4Y1FnYktjQk5CSTQ2RG8zOTBsenNxT1JlWG1JIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1782050590),
('RwBN5pw3wG5NsT4lvOraVdDAl6uajRyLqxNSOILo', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.8655', 'eyJfdG9rZW4iOiJReEFFZFQweFR5OHI1VWZnZWpLMVYxZnhmb1hQa2xPdzBJVmhxcVpiIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1782049548);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `study_groups`
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
-- Cấu trúc bảng cho bảng `study_group_members`
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
-- Cấu trúc bảng cho bảng `submissions`
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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` bigint UNSIGNED NOT NULL,
  `ticket_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` enum('low','medium','high') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `status` enum('open','in_progress','resolved','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `support_ticket_messages`
--

CREATE TABLE `support_ticket_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `support_ticket_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_staff` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `two_factor_codes`
--

CREATE TABLE `two_factor_codes` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `code` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('student','instructor','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'student',
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `avatar`, `bio`, `phone`, `google_id`, `facebook_id`, `two_factor_enabled`, `two_factor_secret`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@example.com', '2026-06-21 06:31:54', '$2y$12$eldoHL8Fn9zOk6wpm7oZrOOYBKNSeDdBJtTCdR0i/nf94zbR8u9Ru', 'admin', NULL, NULL, NULL, NULL, NULL, 0, NULL, 1, NULL, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(2, 'Nguyễn Văn Giảng', 'instructor@example.com', '2026-06-21 06:31:54', '$2y$12$qEnwRo8GNeu1Pm3XL3r02eiTpfbUZrMrIzGzZIg0qGs6v6OGFqZVy', 'instructor', NULL, 'Giảng viên với 10 năm kinh nghiệm lập trình', NULL, NULL, NULL, 0, NULL, 1, NULL, '2026-06-21 06:31:54', '2026-06-21 06:31:54'),
(3, 'Trần Thị Học', 'student@example.com', '2026-06-21 06:31:54', '$2y$12$4vE9Nt/LmPLgO6kjX9IFDeKD7lXpAoBPPEt0AP.haJepOQzQTBKKq', 'student', NULL, NULL, NULL, NULL, NULL, 0, NULL, 1, NULL, '2026-06-21 06:31:54', '2026-06-21 06:31:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_badges`
--

CREATE TABLE `user_badges` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `badge_id` bigint UNSIGNED NOT NULL,
  `earned_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_points`
--

CREATE TABLE `user_points` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `points` int UNSIGNED NOT NULL DEFAULT '0',
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `video_notes`
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
-- Cấu trúc bảng cho bảng `wishlists`
--

CREATE TABLE `wishlists` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `ai_chat_messages`
--
ALTER TABLE `ai_chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ai_chat_messages_user_id_foreign` (`user_id`),
  ADD KEY `ai_chat_messages_lesson_id_foreign` (`lesson_id`);

--
-- Chỉ mục cho bảng `ai_summaries`
--
ALTER TABLE `ai_summaries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ai_summaries_lesson_id_language_unique` (`lesson_id`,`language`);

--
-- Chỉ mục cho bảng `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assignments_lesson_id_foreign` (`lesson_id`);

--
-- Chỉ mục cho bảng `badges`
--
ALTER TABLE `badges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `badges_slug_unique` (`slug`);

--
-- Chỉ mục cho bảng `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Chỉ mục cho bảng `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Chỉ mục cho bảng `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `carts_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cart_items_cart_id_course_id_unique` (`cart_id`,`course_id`),
  ADD KEY `cart_items_course_id_foreign` (`course_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`);

--
-- Chỉ mục cho bảng `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `certificates_user_id_course_id_unique` (`user_id`,`course_id`),
  ADD UNIQUE KEY `certificates_certificate_code_unique` (`certificate_code`),
  ADD KEY `certificates_course_id_foreign` (`course_id`);

--
-- Chỉ mục cho bảng `chapters`
--
ALTER TABLE `chapters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chapters_course_id_foreign` (`course_id`);

--
-- Chỉ mục cho bảng `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `coupons_code_unique` (`code`);

--
-- Chỉ mục cho bảng `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `courses_slug_unique` (`slug`),
  ADD KEY `courses_instructor_id_foreign` (`instructor_id`),
  ADD KEY `courses_category_id_foreign` (`category_id`);

--
-- Chỉ mục cho bảng `discussions`
--
ALTER TABLE `discussions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discussions_lesson_id_foreign` (`lesson_id`),
  ADD KEY `discussions_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `discussion_replies`
--
ALTER TABLE `discussion_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discussion_replies_discussion_id_foreign` (`discussion_id`),
  ADD KEY `discussion_replies_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `enrollments_user_id_course_id_unique` (`user_id`,`course_id`),
  ADD KEY `enrollments_course_id_foreign` (`course_id`),
  ADD KEY `enrollments_order_id_foreign` (`order_id`);

--
-- Chỉ mục cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Chỉ mục cho bảng `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `homepage_settings`
--
ALTER TABLE `homepage_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `homepage_settings_key_unique` (`key`);

--
-- Chỉ mục cho bảng `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Chỉ mục cho bảng `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `learning_paths`
--
ALTER TABLE `learning_paths`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `learning_paths_slug_unique` (`slug`);

--
-- Chỉ mục cho bảng `learning_path_courses`
--
ALTER TABLE `learning_path_courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `learning_path_courses_learning_path_id_course_id_unique` (`learning_path_id`,`course_id`),
  ADD KEY `learning_path_courses_course_id_foreign` (`course_id`);

--
-- Chỉ mục cho bảng `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lessons_chapter_id_foreign` (`chapter_id`);

--
-- Chỉ mục cho bảng `lesson_attachments`
--
ALTER TABLE `lesson_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lesson_attachments_lesson_id_foreign` (`lesson_id`);

--
-- Chỉ mục cho bảng `lesson_progress`
--
ALTER TABLE `lesson_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lesson_progress_user_id_lesson_id_unique` (`user_id`,`lesson_id`),
  ADD KEY `lesson_progress_lesson_id_foreign` (`lesson_id`);

--
-- Chỉ mục cho bảng `lesson_subtitles`
--
ALTER TABLE `lesson_subtitles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lesson_subtitles_lesson_id_foreign` (`lesson_id`);

--
-- Chỉ mục cho bảng `live_sessions`
--
ALTER TABLE `live_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `live_sessions_course_id_foreign` (`course_id`),
  ADD KEY `live_sessions_instructor_id_foreign` (`instructor_id`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_code_unique` (`order_code`),
  ADD KEY `orders_user_id_foreign` (`user_id`),
  ADD KEY `orders_coupon_id_foreign` (`coupon_id`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_course_id_foreign` (`course_id`);

--
-- Chỉ mục cho bảng `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Chỉ mục cho bảng `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_order_id_foreign` (`order_id`);

--
-- Chỉ mục cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Chỉ mục cho bảng `push_notifications`
--
ALTER TABLE `push_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `push_notifications_created_by_foreign` (`created_by`);

--
-- Chỉ mục cho bảng `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quizzes_lesson_id_foreign` (`lesson_id`);

--
-- Chỉ mục cho bảng `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_attempts_user_id_foreign` (`user_id`),
  ADD KEY `quiz_attempts_quiz_id_foreign` (`quiz_id`);

--
-- Chỉ mục cho bảng `quiz_options`
--
ALTER TABLE `quiz_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_options_quiz_question_id_foreign` (`quiz_question_id`);

--
-- Chỉ mục cho bảng `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_questions_quiz_id_foreign` (`quiz_id`);

--
-- Chỉ mục cho bảng `recently_viewed_courses`
--
ALTER TABLE `recently_viewed_courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `recently_viewed_courses_user_id_course_id_unique` (`user_id`,`course_id`),
  ADD KEY `recently_viewed_courses_course_id_foreign` (`course_id`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reviews_user_id_course_id_unique` (`user_id`,`course_id`),
  ADD KEY `reviews_course_id_foreign` (`course_id`);

--
-- Chỉ mục cho bảng `review_replies`
--
ALTER TABLE `review_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `review_replies_review_id_foreign` (`review_id`),
  ADD KEY `review_replies_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Chỉ mục cho bảng `study_groups`
--
ALTER TABLE `study_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `study_groups_course_id_foreign` (`course_id`),
  ADD KEY `study_groups_creator_id_foreign` (`creator_id`);

--
-- Chỉ mục cho bảng `study_group_members`
--
ALTER TABLE `study_group_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `study_group_members_study_group_id_user_id_unique` (`study_group_id`,`user_id`),
  ADD KEY `study_group_members_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `submissions_assignment_id_user_id_unique` (`assignment_id`,`user_id`),
  ADD KEY `submissions_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `support_tickets_ticket_code_unique` (`ticket_code`),
  ADD KEY `support_tickets_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `support_ticket_messages`
--
ALTER TABLE `support_ticket_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `support_ticket_messages_support_ticket_id_foreign` (`support_ticket_id`),
  ADD KEY `support_ticket_messages_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `two_factor_codes`
--
ALTER TABLE `two_factor_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `two_factor_codes_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_google_id_unique` (`google_id`),
  ADD UNIQUE KEY `users_facebook_id_unique` (`facebook_id`);

--
-- Chỉ mục cho bảng `user_badges`
--
ALTER TABLE `user_badges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_badges_user_id_badge_id_unique` (`user_id`,`badge_id`),
  ADD KEY `user_badges_badge_id_foreign` (`badge_id`);

--
-- Chỉ mục cho bảng `user_points`
--
ALTER TABLE `user_points`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_points_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `video_notes`
--
ALTER TABLE `video_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `video_notes_user_id_foreign` (`user_id`),
  ADD KEY `video_notes_lesson_id_foreign` (`lesson_id`);

--
-- Chỉ mục cho bảng `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wishlists_user_id_course_id_unique` (`user_id`,`course_id`),
  ADD KEY `wishlists_course_id_foreign` (`course_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `ai_chat_messages`
--
ALTER TABLE `ai_chat_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `ai_summaries`
--
ALTER TABLE `ai_summaries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `badges`
--
ALTER TABLE `badges`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `chapters`
--
ALTER TABLE `chapters`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `courses`
--
ALTER TABLE `courses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `discussions`
--
ALTER TABLE `discussions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `discussion_replies`
--
ALTER TABLE `discussion_replies`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `homepage_settings`
--
ALTER TABLE `homepage_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `learning_paths`
--
ALTER TABLE `learning_paths`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `learning_path_courses`
--
ALTER TABLE `learning_path_courses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `lesson_attachments`
--
ALTER TABLE `lesson_attachments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `lesson_progress`
--
ALTER TABLE `lesson_progress`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `lesson_subtitles`
--
ALTER TABLE `lesson_subtitles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `live_sessions`
--
ALTER TABLE `live_sessions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `push_notifications`
--
ALTER TABLE `push_notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `quiz_options`
--
ALTER TABLE `quiz_options`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `recently_viewed_courses`
--
ALTER TABLE `recently_viewed_courses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `review_replies`
--
ALTER TABLE `review_replies`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `study_groups`
--
ALTER TABLE `study_groups`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `study_group_members`
--
ALTER TABLE `study_group_members`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `support_ticket_messages`
--
ALTER TABLE `support_ticket_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `two_factor_codes`
--
ALTER TABLE `two_factor_codes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `user_badges`
--
ALTER TABLE `user_badges`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `user_points`
--
ALTER TABLE `user_points`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `video_notes`
--
ALTER TABLE `video_notes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Ràng buộc đối với các bảng kết xuất
--

--
-- Ràng buộc cho bảng `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ràng buộc cho bảng `ai_chat_messages`
--
ALTER TABLE `ai_chat_messages`
  ADD CONSTRAINT `ai_chat_messages_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ai_chat_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `ai_summaries`
--
ALTER TABLE `ai_summaries`
  ADD CONSTRAINT `ai_summaries_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `chapters`
--
ALTER TABLE `chapters`
  ADD CONSTRAINT `chapters_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `courses_instructor_id_foreign` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `discussions`
--
ALTER TABLE `discussions`
  ADD CONSTRAINT `discussions_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discussions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `discussion_replies`
--
ALTER TABLE `discussion_replies`
  ADD CONSTRAINT `discussion_replies_discussion_id_foreign` FOREIGN KEY (`discussion_id`) REFERENCES `discussions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discussion_replies_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `enrollments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `learning_path_courses`
--
ALTER TABLE `learning_path_courses`
  ADD CONSTRAINT `learning_path_courses_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `learning_path_courses_learning_path_id_foreign` FOREIGN KEY (`learning_path_id`) REFERENCES `learning_paths` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_chapter_id_foreign` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `lesson_attachments`
--
ALTER TABLE `lesson_attachments`
  ADD CONSTRAINT `lesson_attachments_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `lesson_progress`
--
ALTER TABLE `lesson_progress`
  ADD CONSTRAINT `lesson_progress_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lesson_progress_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `lesson_subtitles`
--
ALTER TABLE `lesson_subtitles`
  ADD CONSTRAINT `lesson_subtitles_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `live_sessions`
--
ALTER TABLE `live_sessions`
  ADD CONSTRAINT `live_sessions_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `live_sessions_instructor_id_foreign` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `push_notifications`
--
ALTER TABLE `push_notifications`
  ADD CONSTRAINT `push_notifications_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `quiz_attempts_quiz_id_foreign` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_attempts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `quiz_options`
--
ALTER TABLE `quiz_options`
  ADD CONSTRAINT `quiz_options_quiz_question_id_foreign` FOREIGN KEY (`quiz_question_id`) REFERENCES `quiz_questions` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD CONSTRAINT `quiz_questions_quiz_id_foreign` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `recently_viewed_courses`
--
ALTER TABLE `recently_viewed_courses`
  ADD CONSTRAINT `recently_viewed_courses_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recently_viewed_courses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `review_replies`
--
ALTER TABLE `review_replies`
  ADD CONSTRAINT `review_replies_review_id_foreign` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `review_replies_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `study_groups`
--
ALTER TABLE `study_groups`
  ADD CONSTRAINT `study_groups_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `study_groups_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `study_group_members`
--
ALTER TABLE `study_group_members`
  ADD CONSTRAINT `study_group_members_study_group_id_foreign` FOREIGN KEY (`study_group_id`) REFERENCES `study_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `study_group_members_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `submissions_assignment_id_foreign` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `submissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD CONSTRAINT `support_tickets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `support_ticket_messages`
--
ALTER TABLE `support_ticket_messages`
  ADD CONSTRAINT `support_ticket_messages_support_ticket_id_foreign` FOREIGN KEY (`support_ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `support_ticket_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `two_factor_codes`
--
ALTER TABLE `two_factor_codes`
  ADD CONSTRAINT `two_factor_codes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `user_badges`
--
ALTER TABLE `user_badges`
  ADD CONSTRAINT `user_badges_badge_id_foreign` FOREIGN KEY (`badge_id`) REFERENCES `badges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_badges_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `user_points`
--
ALTER TABLE `user_points`
  ADD CONSTRAINT `user_points_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `video_notes`
--
ALTER TABLE `video_notes`
  ADD CONSTRAINT `video_notes_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `video_notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
