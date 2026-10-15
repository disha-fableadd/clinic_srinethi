-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 15, 2026 at 07:40 AM
-- Server version: 11.8.9-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u573967329_srinethidb`
--

-- --------------------------------------------------------

--
-- Table structure for table `ai_credentials`
--

CREATE TABLE `ai_credentials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `api_key` varchar(255) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `treatment_id` bigint(20) UNSIGNED NOT NULL,
  `appoint_type` enum('virtual','in-person') DEFAULT NULL,
  `status` enum('upcoming','confirmed','completed','cancelled','follow-up') DEFAULT NULL,
  `date` date DEFAULT NULL,
  `duration` time DEFAULT NULL,
  `clinic_location` varchar(255) DEFAULT NULL,
  `followup_update` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `branch_id`, `user_id`, `patient_id`, `doctor_id`, `treatment_id`, `appoint_type`, `status`, `date`, `duration`, `clinic_location`, `followup_update`, `created_at`, `updated_at`) VALUES
(1, '1', 1, 1, 42, 1, 'virtual', 'upcoming', '2026-10-14', '20:15:00', 'main', NULL, '2026-10-14 19:15:41', '2026-10-14 19:15:41'),
(2, '1', 1, 5, 1, 2, 'in-person', 'completed', '2026-10-15', '14:00:00', 'CHENNAI', NULL, '2026-10-15 10:56:12', '2026-10-15 10:59:11'),
(3, '1', 1, 6, 1, 2, 'in-person', 'confirmed', '2026-10-15', '01:25:00', 'CHENNAI', NULL, '2026-10-15 11:00:51', '2026-10-15 11:00:51'),
(4, '1', 1, 6, 1, 2, 'in-person', 'completed', '2026-10-15', '01:25:00', 'CHENNAI', NULL, '2026-10-15 11:00:51', '2026-10-15 11:04:48'),
(6, '1', 1, 6, 1, 2, 'in-person', 'completed', '2026-10-15', '01:25:00', 'CHENNAI', NULL, '2026-10-15 11:00:51', '2026-10-15 11:03:27');

-- --------------------------------------------------------

--
-- Table structure for table `appointment_history`
--

CREATE TABLE `appointment_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `appointment_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `status` varchar(255) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assessment_template`
--

CREATE TABLE `assessment_template` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `title` longtext DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `image` longtext DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assigned_therapies`
--

CREATE TABLE `assigned_therapies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `therapy_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('virtual','inperson') NOT NULL DEFAULT 'inperson',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('scheduled','ongoing','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`, `email`, `address`, `city`, `state`, `country`, `created_at`, `updated_at`) VALUES
(1, 'Main', 'branch@clinic.com', 'Main Clinic Address', 'Surat', 'Gujarat', 'India', '2026-07-30 06:00:23', '2026-08-31 11:57:36'),
(2, 'vesu', 'vesu@gmail.com', 'vesu', 'surat', 'gujarat', 'India', '2026-07-30 09:41:36', '2026-07-30 09:41:36'),
(3, 'Adajan', 'adajan@gmail.com', 'adajan ,surat', 'Surat', 'Gujarat', 'India', '2026-09-01 12:24:23', '2026-09-01 12:25:11');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` varchar(200) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `branch_id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, '1', 'Antibiotic', 'Used to treat bacterial infections.', '2026-07-30 06:00:23', '2026-07-30 06:00:23'),
(2, '1', 'Painkiller', 'Used to relieve pain.', '2026-07-30 06:00:23', '2026-07-30 06:00:23'),
(3, '1', 'Vitamin', 'Supplements for health and nutrition.', '2026-07-30 06:00:23', '2026-07-30 06:00:23'),
(4, '1', 'Antiseptic', 'Prevents infection on wounds.', '2026-07-30 06:00:23', '2026-07-30 06:00:23'),
(5, NULL, 'Antifungal', 'Used to treat fungal infections.', '2026-07-30 06:00:23', '2026-07-30 06:00:23'),
(6, NULL, 'Antiviral', 'Used to treat viral infections.', '2026-07-30 06:00:23', '2026-07-30 06:00:23'),
(7, NULL, 'Analgesic', 'Medication to relieve pain.', '2026-07-30 06:00:23', '2026-07-30 06:00:23'),
(8, NULL, 'Antipyretic', 'Used to reduce fever.', '2026-07-30 06:00:23', '2026-07-30 06:00:23'),
(9, NULL, 'Antihistamine', 'Used to treat allergies.', '2026-07-30 06:00:23', '2026-07-30 06:00:23'),
(10, NULL, 'Cough Suppressant', 'Used to reduce coughing.', '2026-07-30 06:00:23', '2026-07-30 06:00:23'),
(11, NULL, 'Expectorant', 'Helps clear mucus from airways.', '2026-07-30 06:00:23', '2026-07-30 06:00:23'),
(12, NULL, 'Laxative', 'Used to relieve constipation.', '2026-07-30 06:00:23', '2026-07-30 06:00:23'),
(13, NULL, 'Antacid', 'Used to neutralize stomach acid.', '2026-07-30 06:00:23', '2026-07-30 06:00:23'),
(14, NULL, 'Antidepressant', 'Used to treat depression.', '2026-07-30 06:00:23', '2026-07-30 06:00:23'),
(15, NULL, 'Sedative', 'Used to calm or sedate patients.', '2026-07-30 06:00:23', '2026-07-30 06:00:23');

-- --------------------------------------------------------

--
-- Table structure for table `chatbot_messages`
--

CREATE TABLE `chatbot_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_message` text NOT NULL,
  `bot_response` longtext NOT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_history`
--

CREATE TABLE `chat_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `page_id` int(11) DEFAULT NULL,
  `user_question` text NOT NULL,
  `bot_response` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `code_master`
--

CREATE TABLE `code_master` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `daily_data`
--

CREATE TABLE `daily_data` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `treatment_id` bigint(20) UNSIGNED NOT NULL,
  `comment` text DEFAULT NULL,
  `date` date NOT NULL,
  `remain_amount` decimal(10,2) NOT NULL,
  `collect_by_id` bigint(20) UNSIGNED NOT NULL,
  `status` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `daily_data`
--

INSERT INTO `daily_data` (`id`, `branch_id`, `user_id`, `patient_id`, `treatment_id`, `comment`, `date`, `remain_amount`, `collect_by_id`, `status`, `created_at`, `updated_at`) VALUES
(1, '1', 1, 4, 2, NULL, '2026-10-14', 0.00, 43, 'paid', '2026-10-14 20:01:16', '2026-10-14 20:01:16');

-- --------------------------------------------------------

--
-- Table structure for table `default_email_template`
--

CREATE TABLE `default_email_template` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `default_email_template`
--

INSERT INTO `default_email_template` (`id`, `name`, `content`, `created_at`, `updated_at`) VALUES
(1, 'Welcome Template', '<html><body><h2>Welcome</h2><p>Thank you for choosing our clinic.</p></body></html>', '2026-07-30 06:00:23', '2026-07-30 06:00:23');

-- --------------------------------------------------------

--
-- Table structure for table `diagnosis`
--

CREATE TABLE `diagnosis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `diagnosis`
--

INSERT INTO `diagnosis` (`id`, `name`, `description`, `branch_id`, `created_at`, `updated_at`) VALUES
(1, 'test', NULL, 1, NULL, NULL),
(2, 'Fever', NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `diet_charts`
--

CREATE TABLE `diet_charts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `title` longtext DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `image` longtext DEFAULT NULL,
  `time` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_template`
--

CREATE TABLE `email_template` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `template_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `img1` text DEFAULT NULL,
  `img2` text DEFAULT NULL,
  `img3` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

CREATE TABLE `email_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `service` varchar(255) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `followups`
--

CREATE TABLE `followups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `treatment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `followup_update` text DEFAULT NULL,
  `followup_type` enum('Regular followup','Appointment related','Medicine related','Discharge related','Report related','other') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `followups`
--

INSERT INTO `followups` (`id`, `user_id`, `branch_id`, `patient_id`, `treatment_id`, `doctor_id`, `date`, `followup_update`, `followup_type`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 42, '2026-10-14', 'test', 'Regular followup', '2026-10-14 19:15:57', '2026-10-14 19:15:57');

-- --------------------------------------------------------

--
-- Table structure for table `home_advice`
--

CREATE TABLE `home_advice` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `template_name` varchar(255) DEFAULT NULL,
  `title` longtext DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `image` longtext DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_date` date NOT NULL,
  `status` enum('active','inactive','expired') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` varchar(200) DEFAULT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` enum('service','treatment','medicine','followup') DEFAULT NULL,
  `type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date DEFAULT NULL,
  `instruction` text DEFAULT NULL,
  `types_details` longtext DEFAULT NULL,
  `grand_total` bigint(20) DEFAULT NULL,
  `payment_type` varchar(255) DEFAULT NULL,
  `payment_status` varchar(255) DEFAULT NULL,
  `subtotal` bigint(20) DEFAULT NULL,
  `tax` bigint(20) DEFAULT NULL,
  `discount_type` longtext DEFAULT NULL,
  `discount` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `branch_id`, `patient_id`, `type`, `type_id`, `date`, `instruction`, `types_details`, `grand_total`, `payment_type`, `payment_status`, `subtotal`, `tax`, `discount_type`, `discount`, `created_at`, `updated_at`) VALUES
(1, '1', 1, 'treatment', 5812, '2026-10-14', 'test', '[{\"id\":\"1\",\"name\":\"test\",\"quantity\":\"1\",\"price\":\"200.00\",\"total\":\"200.00\",\"gst_option\":\"Without GST\",\"product_gst\":null}]', 190, 'Cash', 'Paid', 0, 0, 'fixed_amount', 10, '2026-10-14 18:57:10', '2026-10-14 18:57:10'),
(2, '1', 1, 'treatment', 6460, '2026-10-14', NULL, '[{\"id\":\"1\",\"name\":\"test\",\"quantity\":\"1\",\"price\":\"200.00\",\"total\":\"200.00\",\"gst_option\":\"Without GST\",\"product_gst\":null}]', 190, 'Cash', 'Paid', 0, 0, 'fixed_amount', 10, '2026-10-14 19:12:17', '2026-10-14 19:12:17'),
(3, '1', 1, 'treatment', 2421, '2026-10-14', NULL, '[{\"id\":\"1\",\"name\":\"test\",\"quantity\":\"1\",\"price\":\"200.00\",\"total\":\"200.00\",\"gst_option\":\"Without GST\",\"product_gst\":null}]', 198, 'Cash', 'Paid', 0, 0, 'percentage', 1, '2026-10-14 19:15:03', '2026-10-14 19:15:03'),
(4, '1', 2, 'treatment', 1091, '2026-10-15', 'test', '[{\"id\":\"1\",\"name\":\"Treatment 1\",\"quantity\":\"1\",\"price\":\"200.00\",\"total\":\"200.00\",\"gst_option\":\"Without GST\",\"product_gst\":null}]', 190, 'Cash', 'Paid', 0, 0, 'fixed_amount', 10, '2026-10-15 10:59:01', '2026-10-15 10:59:01'),
(5, '1', 3, 'treatment', 9451, '2026-10-15', NULL, '[{\"id\":\"3\",\"name\":\"test-treatment\",\"quantity\":\"1\",\"price\":\"200.00\",\"total\":\"200\",\"gst_option\":\"Without GST\",\"product_gst\":null}]', 190, 'Cash', 'Paid', 0, 0, 'fixed_amount', 10, '2026-10-15 11:09:35', '2026-10-15 11:09:35'),
(6, '1', 2, '', 2828, '2026-10-15', NULL, '[{\"id\":null,\"name\":\"Appointment\",\"quantity\":\"1\",\"price\":\"5000\",\"total\":\"5000.00\",\"gst_option\":\"Without GST\",\"product_gst\":null}]', 4500, 'Online', 'Pending', 0, 0, 'percentage', 10, '2026-10-15 11:21:05', '2026-10-15 11:21:05'),
(7, '1', 2, 'treatment', 3990, '2026-10-15', NULL, '[{\"id\":\"5\",\"name\":\"treatment-gst\",\"quantity\":\"1\",\"price\":\"1000.00\",\"total\":\"1000.00\",\"gst_option\":\"With GST\",\"product_gst\":[{\"tax_name\":\"CGST\",\"tax_rate\":\"12.00\",\"tax_amount\":\"120.00\"}]},{\"id\":\"6\",\"name\":\"disha-gst-test\",\"quantity\":\"1\",\"price\":\"600.00\",\"total\":\"600.00\",\"gst_option\":\"With GST\",\"product_gst\":[{\"tax_name\":\"CGST\",\"tax_rate\":\"12.00\",\"tax_amount\":\"72.00\"}]}]', 1613, 'Cash', 'Paid', 0, 0, 'percentage', 10, '2026-10-15 11:36:02', '2026-10-15 11:36:02'),
(14, '1', 5, 'treatment', 7339, '2026-10-15', NULL, '[{\"id\":\"2\",\"name\":\"Consultations\",\"quantity\":\"1\",\"price\":\"200\",\"total\":\"200.00\",\"gst_option\":\"Without GST\",\"product_gst\":null}]', 100, 'Cash', 'Paid', 0, 0, 'fixed_amount', 100, '2026-10-15 11:47:01', '2026-10-15 11:47:01'),
(15, '1', 5, 'treatment', 7036, '2026-10-15', NULL, '[{\"id\":\"2\",\"name\":\"Consultations\",\"quantity\":\"1\",\"price\":\"200\",\"total\":\"200.00\",\"gst_option\":\"Without GST\",\"product_gst\":null}]', 100, 'Cash', 'Paid', 0, 0, 'fixed_amount', 100, '2026-10-15 11:47:02', '2026-10-15 11:47:02'),
(17, '1', 5, 'treatment', 8367, '2026-10-15', NULL, '[{\"id\":\"2\",\"name\":\"Consultations\",\"quantity\":\"1\",\"price\":\"200\",\"total\":\"200.00\",\"gst_option\":\"Without GST\",\"product_gst\":null}]', 100, 'Cash', 'Paid', 0, 0, 'fixed_amount', 100, '2026-10-15 11:47:02', '2026-10-15 11:47:02'),
(18, '1', 2, 'treatment', 7120, '2026-10-15', NULL, '[{\"id\":\"6\",\"name\":\"disha-gst-test\",\"quantity\":\"1\",\"price\":\"600.00\",\"total\":\"600.00\",\"gst_option\":\"With GST\",\"product_gst\":[{\"tax_name\":\"CGST\",\"tax_rate\":\"12.00\",\"tax_amount\":\"72.00\"}]}]', 672, 'Online', 'Paid', NULL, 0, 'fixed_amount', 0, '2026-10-15 12:24:38', '2026-10-15 12:24:38'),
(19, '1', 2, 'treatment', 4837, '2026-10-15', NULL, '[{\"id\":\"5\",\"name\":\"treatment-gst\",\"quantity\":\"1\",\"price\":\"800.00\",\"total\":\"800\",\"gst_option\":\"With GST\",\"product_gst\":[{\"tax_name\":\"CGST\",\"tax_rate\":\"12.00\",\"tax_amount\":\"96.00\"}]}]', 896, 'Cash', 'Paid', NULL, 0, 'fixed_amount', 0, '2026-10-15 12:27:20', '2026-10-15 12:27:20');

-- --------------------------------------------------------

--
-- Table structure for table `ipd_admissions`
--

CREATE TABLE `ipd_admissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` varchar(200) DEFAULT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `treatment_id` bigint(20) UNSIGNED NOT NULL,
  `admission_date` date NOT NULL,
  `discharge_date` date DEFAULT NULL,
  `room_number` varchar(255) NOT NULL,
  `bed_number` varchar(255) NOT NULL,
  `reason_for_admission` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lab_tests`
--

CREATE TABLE `lab_tests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `doctor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `service_id` bigint(20) UNSIGNED DEFAULT NULL,
  `test_description` text DEFAULT NULL,
  `test_date` date DEFAULT NULL,
  `result` text DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `machines`
--

CREATE TABLE `machines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medical_reports`
--

CREATE TABLE `medical_reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

CREATE TABLE `medicines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `unit` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `quantity` int(11) NOT NULL DEFAULT 0,
  `medicine_unit` varchar(255) NOT NULL,
  `batch_no` varchar(255) NOT NULL,
  `gst_option` varchar(255) NOT NULL,
  `product_gst` text DEFAULT NULL,
  `manufacture_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `image` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medicine_units`
--

CREATE TABLE `medicine_units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `unit` varchar(255) NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(2, '2025_01_31_060756_modules', 1),
(3, '2025_01_31_061100_create_user_table', 1),
(4, '2025_01_31_061345_user_details', 1),
(5, '2025_01_31_061754_categories', 1),
(6, '2025_01_31_062154_medicine', 1),
(7, '2025_01_31_062421_patients', 1),
(8, '2025_01_31_062649_appointments', 1),
(9, '2025_01_31_062913_patient_discharge_dets', 1),
(10, '2025_01_31_063103_services', 1),
(11, '2025_01_31_063957_treatment', 1),
(12, '2025_01_31_064215_inventory', 1),
(13, '2025_01_31_064537_supplier', 1),
(14, '2025_01_31_064733_medical_report', 1),
(15, '2025_01_31_121013_create_role_table', 1),
(16, '2025_02_17_050910_add_created_by_to_user_table', 1),
(17, '2025_02_17_071646_create_send_sms_table', 1),
(18, '2025_02_17_071802_create_sms_templates_table', 1),
(19, '2025_02_17_102436_create_settings_table', 1),
(20, '2025_02_18_082449_create_email_templates_table', 1),
(21, '2025_02_18_101153_create_send_emails_table', 1),
(22, '2025_02_20_051828_add_user_id_to_appointments_table', 1),
(23, '2025_02_20_052028_add_user_id_to_patients_table', 1),
(24, '2025_02_21_042328_add_user_id_to_inventory_table', 1),
(25, '2025_02_21_042547_add_user_id_to_medical_reports_table', 1),
(26, '2025_02_21_042749_add_user_id_to_medicines_table', 1),
(27, '2025_02_21_042938_add_user_id_to_patient_discharge_dets_table', 1),
(28, '2025_02_21_043217_add_user_id_to_services_table', 1),
(29, '2025_02_21_043339_add_user_id_to_suppliers_table', 1),
(30, '2025_02_21_043507_add_user_id_to_treatments_table', 1),
(31, '2025_02_21_113830_create_plans_table', 1),
(32, '2025_02_25_051251_create_followups_table', 1),
(33, '2025_02_26_092524_create_email_template_table', 1),
(34, '2025_03_25_123536_add_note_to_patients_table', 1),
(35, '2025_04_01_121522_add_clinic_fields_to_user_table', 1),
(36, '2025_04_01_162516_add_followup_type_to_followups_table', 1),
(37, '2025_04_02_103250_create_appointment_history_table', 1),
(38, '2025_04_03_145557_create_patient_medicine_table', 1),
(39, '2025_04_03_162901_add_appointment_id_to_patient_medicine_table', 1),
(40, '2025_04_09_105250_create_notifications_table', 1),
(41, '2025_04_10_105743_create_invoices_table', 1),
(42, '2025_04_11_165114_add_treatment_id_to_patient_medicine_table', 1),
(43, '2025_04_12_115636_add_tax_discount_subtotal_to_invoices_table', 1),
(44, '2025_04_12_125833_add_discount_type_to_invoices_table', 1),
(45, '2025_04_14_162238_add_payment_type_to_invoices_table', 1),
(46, '2025_04_16_124815_add_symptom_fields_to_patients_table', 1),
(47, '2025_04_16_133554_create_symptoms_table', 1),
(48, '2025_04_18_165616_create_jobs_table', 1),
(49, '2025_04_22_150836_add_payment_status_to_invoices_table', 1),
(50, '2025_07_18_112600_create_pathology_tests_table', 1),
(51, '2025_07_18_120641_create_pathology_reports_table', 1),
(52, '2025_07_18_120821_create_radiology_tests_table', 1),
(53, '2025_07_18_121011_create_radiology_reports_table', 1),
(54, '2025_07_18_121202_create_opd_visits_table', 1),
(55, '2025_07_18_121304_create_ot_procedures_table', 1),
(56, '2025_07_18_125347_create_radiology_tests_table', 1),
(57, '2025_07_18_152000_create_ipd_admissions_table', 1),
(58, '2025_07_18_165519_create_radiology_reports_table', 1),
(59, '2025_07_21_105622_update_services_table', 1),
(60, '2025_07_22_110227_add_converted_image_to_radiology_reports_table', 1),
(61, '2025_07_24_143756_add_password_to_patients_table', 1),
(62, '2025_07_24_164003_add_login_id_to_patients_table', 1),
(63, '2025_08_05_100814_create_therapy_table', 1),
(64, '2025_08_05_101426_create_assigned_therapies_table', 1),
(65, '2025_08_07_120437_create_daily_data_table', 1),
(66, '2025_08_08_133003_create_daily_data_table', 1),
(67, '2025_08_08_151625_create_payment_history_table', 1),
(68, '2025_09_01_110030_create_branches_table', 1),
(69, '2025_09_16_113411_create_patient_diagnosis_table', 1),
(70, '2025_12_10_130437_create_ai_credentials_table', 1),
(71, '2025_12_10_162801_create_chatbot_messages_table', 1),
(72, '2025_12_11_121740_create_chat_history_table', 1),
(73, '2026_03_06_172811_create_medicine_units_table', 1),
(74, '2026_03_12_100000_add_invoice_types_appointment_therapy_discharge', 1),
(75, '2026_05_21_000001_create_missing_feature_tables', 1),
(76, '2026_05_21_183900_expand_encrypted_columns_on_user_table', 1),
(77, '2026_07_29_171242_create_project_types_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Dashboard', '2026-07-30 06:00:23', '2026-07-30 06:00:23'),
(2, 'Followup', '2026-07-30 06:00:23', '2026-07-30 06:00:23'),
(3, 'User', '2026-07-30 06:00:23', '2026-07-30 06:00:23'),
(5, 'Patients', '2026-07-30 06:00:23', '2026-07-30 06:00:23'),
(6, 'Appointments', '2026-07-30 06:00:23', '2026-07-30 06:00:23'),
(7, 'Treatments', '2026-07-30 06:00:23', '2026-07-30 06:00:23'),
(18, 'Invoice', '2026-07-30 06:00:23', '2026-07-30 06:00:23'),
(35, 'Tax Rate', '2026-07-30 06:00:23', '2026-07-30 06:00:23'),
(36, 'Referal Doctor', '2026-07-30 06:00:23', '2026-07-30 06:00:23');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED DEFAULT NULL,
  `receiver_id` bigint(20) UNSIGNED DEFAULT NULL,
  `info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`info`)),
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `sender_id`, `receiver_id`, `info`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 1, 36, '{\"message\":\"Welcome Riya, your profile has been created successfully.\\nTreatment: N\\/A\\nEmail: riya@gmail.com\\nPassword: Riya\",\"module\":\"patient\",\"module_id\":1,\"sender_id\":1,\"receiver_id\":36,\"type\":\"notification\",\"patient_id\":null}', 0, '2026-10-14 18:29:24', '2026-10-14 18:29:24'),
(7, 1, 42, '{\"message\":\"Welcome onboard, disha! We\'re excited to have you with us.\",\"module\":\"user\",\"module_id\":42,\"sender_id\":1,\"receiver_id\":42,\"type\":\"notification\",\"patient_id\":null}', 0, '2026-10-14 18:55:46', '2026-10-14 18:55:46'),
(8, 1, 1, '{\"message\":\"Email and SMS notifications services are off\",\"module\":\"user\",\"module_id\":1,\"sender_id\":1,\"receiver_id\":1,\"type\":\"notification\",\"patient_id\":null}', 1, '2026-10-14 18:55:46', '2026-10-14 19:07:21'),
(9, 1, 42, '{\"message\":\"Dr disha You have a New treatment \'test\' has been assigned to you by Srinethi Admin.\",\"module\":\"treatment\",\"module_id\":1,\"sender_id\":1,\"receiver_id\":42,\"type\":\"notification\",\"patient_id\":null}', 0, '2026-10-14 18:56:41', '2026-10-14 18:56:41'),
(10, 1, 43, '{\"message\":\"Welcome onboard, riddhi! We\'re excited to have you with us.\",\"module\":\"user\",\"module_id\":43,\"sender_id\":1,\"receiver_id\":43,\"type\":\"notification\",\"patient_id\":null}', 0, '2026-10-14 19:03:54', '2026-10-14 19:03:54'),
(11, 1, 1, '{\"message\":\"Email and SMS notifications services are off\",\"module\":\"user\",\"module_id\":1,\"sender_id\":1,\"receiver_id\":1,\"type\":\"notification\",\"patient_id\":null}', 1, '2026-10-14 19:03:54', '2026-10-14 19:07:20'),
(12, 1, 1, '{\"message\":\"Email and SMS notifications services are off\",\"module\":\"user\",\"module_id\":1,\"sender_id\":1,\"receiver_id\":1,\"type\":\"notification\",\"patient_id\":null}', 1, '2026-10-14 19:05:19', '2026-10-14 19:07:19'),
(13, 1, 1, '{\"message\":\"Email and SMS notifications services are off\",\"module\":\"user\",\"module_id\":1,\"sender_id\":1,\"receiver_id\":1,\"type\":\"notification\",\"patient_id\":null}', 1, '2026-10-14 19:05:26', '2026-10-14 19:07:18'),
(14, 1, 42, '{\"message\":\"Dr disha You have a New appointment with Patient Riya on 14\\/10\\/2026 08:15 PM.\",\"module\":\"appointment\",\"module_id\":1,\"sender_id\":1,\"receiver_id\":\"42\",\"type\":\"notification\",\"patient_id\":\"1\"}', 0, '2026-10-14 19:15:41', '2026-10-14 19:15:41'),
(15, 1, 42, '{\"message\":\"Dr disha You have a New Follow-up scheduled! For patient Riya on 14\\/10\\/2026.\",\"module\":\"followup\",\"module_id\":1,\"sender_id\":1,\"receiver_id\":\"42\",\"type\":\"notification\",\"patient_id\":\"1\"}', 0, '2026-10-14 19:15:57', '2026-10-14 19:15:57'),
(16, 1, 36, '{\"message\":\"Dear Riya, your follow-up with Dr. disha has been scheduled on 14\\/10\\/2026.\",\"module\":\"followup\",\"module_id\":1,\"sender_id\":1,\"receiver_id\":36,\"type\":\"notification\",\"patient_id\":\"1\"}', 0, '2026-10-14 19:15:57', '2026-10-14 19:15:57'),
(17, 1, 1, '{\"message\":\"Srinethi Admin created a follow-up for patient Riya with Dr. disha on 14\\/10\\/2026.\",\"module\":\"followup\",\"module_id\":1,\"sender_id\":1,\"receiver_id\":1,\"type\":\"notification\",\"patient_id\":\"1\"}', 0, '2026-10-14 19:15:57', '2026-10-14 19:15:57'),
(18, 1, 44, '{\"message\":\"Welcome test-disha, your profile has been created successfully.\\nTreatment: N\\/A\\nEmail: \\nPassword: test-disha\",\"module\":\"patient\",\"module_id\":2,\"sender_id\":1,\"receiver_id\":44,\"type\":\"notification\",\"patient_id\":null}', 0, '2026-10-14 19:16:18', '2026-10-14 19:16:18'),
(19, 1, 45, '{\"message\":\"Welcome test-patient, your profile has been created successfully.\\nTreatment: N\\/A\\nEmail: \\nPassword: test-patient\",\"module\":\"patient\",\"module_id\":3,\"sender_id\":1,\"receiver_id\":45,\"type\":\"notification\",\"patient_id\":null}', 0, '2026-10-14 19:18:26', '2026-10-14 19:18:26'),
(20, 1, 1, '{\"message\":\"Dr Srinethi Admin You have a New treatment \'Consultations\' has been assigned to you by Srinethi Admin.\",\"module\":\"treatment\",\"module_id\":2,\"sender_id\":1,\"receiver_id\":1,\"type\":\"notification\",\"patient_id\":null}', 0, '2026-10-14 19:18:35', '2026-10-14 19:18:35'),
(21, 1, 42, '{\"message\":\"Dr disha You have a New treatment \'test-treatment\' has been assigned to you by Srinethi Admin.\",\"module\":\"treatment\",\"module_id\":3,\"sender_id\":1,\"receiver_id\":42,\"type\":\"notification\",\"patient_id\":null}', 0, '2026-10-14 19:20:04', '2026-10-14 19:20:04'),
(22, 1, 46, '{\"message\":\"Welcome Mangai, your profile has been created successfully.\\nTreatment: N\\/A\\nEmail: \\nPassword: Mangai\",\"module\":\"patient\",\"module_id\":4,\"sender_id\":1,\"receiver_id\":46,\"type\":\"notification\",\"patient_id\":null}', 0, '2026-10-14 19:59:35', '2026-10-14 19:59:35'),
(23, 1, 43, '{\"message\":\"Payment of \\u20b9200 received from patient \'Mangai\' by Srinethi Admin.\",\"module\":\"payment\",\"module_id\":1,\"sender_id\":1,\"receiver_id\":43,\"type\":\"notification\",\"patient_id\":4}', 0, '2026-10-14 20:01:16', '2026-10-14 20:01:16'),
(24, 1, 47, '{\"message\":\"Welcome DRESH, your profile has been created successfully.\\nTreatment: N\\/A\\nEmail: \\nPassword: DRESH\",\"module\":\"patient\",\"module_id\":5,\"sender_id\":1,\"receiver_id\":47,\"type\":\"notification\",\"patient_id\":null}', 0, '2026-10-15 10:53:15', '2026-10-15 10:53:15'),
(25, 1, 1, '{\"message\":\"Dr Srinethi Admin You have a New appointment with Patient DRESH on 15\\/10\\/2026 02:00 PM.\",\"module\":\"appointment\",\"module_id\":2,\"sender_id\":1,\"receiver_id\":\"1\",\"type\":\"notification\",\"patient_id\":\"5\"}', 0, '2026-10-15 10:56:12', '2026-10-15 10:56:12'),
(26, 1, 48, '{\"message\":\"Welcome LINGESH, your profile has been created successfully.\\nTreatment: N\\/A\\nEmail: \\nPassword: LINGESH\",\"module\":\"patient\",\"module_id\":6,\"sender_id\":1,\"receiver_id\":48,\"type\":\"notification\",\"patient_id\":null}', 0, '2026-10-15 10:59:51', '2026-10-15 10:59:51'),
(27, 1, 1, '{\"message\":\"Dr Srinethi Admin You have a New appointment with Patient LINGESH on 15\\/10\\/2026 01:25 AM.\",\"module\":\"appointment\",\"module_id\":3,\"sender_id\":1,\"receiver_id\":\"1\",\"type\":\"notification\",\"patient_id\":\"6\"}', 1, '2026-10-15 11:00:51', '2026-10-15 11:05:40'),
(28, 1, 1, '{\"message\":\"Dr Srinethi Admin You have a New appointment with Patient LINGESH on 15\\/10\\/2026 01:25 AM.\",\"module\":\"appointment\",\"module_id\":4,\"sender_id\":1,\"receiver_id\":\"1\",\"type\":\"notification\",\"patient_id\":\"6\"}', 0, '2026-10-15 11:00:51', '2026-10-15 11:00:51'),
(29, 1, 1, '{\"message\":\"Dr Srinethi Admin You have a New appointment with Patient LINGESH on 15\\/10\\/2026 01:25 AM.\",\"module\":\"appointment\",\"module_id\":5,\"sender_id\":1,\"receiver_id\":\"1\",\"type\":\"notification\",\"patient_id\":\"6\"}', 0, '2026-10-15 11:00:51', '2026-10-15 11:00:51'),
(30, 1, 1, '{\"message\":\"Dr Srinethi Admin You have a New appointment with Patient LINGESH on 15\\/10\\/2026 01:25 AM.\",\"module\":\"appointment\",\"module_id\":6,\"sender_id\":1,\"receiver_id\":\"1\",\"type\":\"notification\",\"patient_id\":\"6\"}', 0, '2026-10-15 11:00:51', '2026-10-15 11:00:51'),
(31, 1, 1, '{\"message\":\"Dr Srinethi Admin You have a New appointment with Patient LINGESH on 15\\/10\\/2026 01:25 AM.\",\"module\":\"appointment\",\"module_id\":7,\"sender_id\":1,\"receiver_id\":\"1\",\"type\":\"notification\",\"patient_id\":\"6\"}', 0, '2026-10-15 11:00:51', '2026-10-15 11:00:51'),
(32, 1, 1, '{\"message\":\"Dr Srinethi Admin You have a New appointment with Patient LINGESH on 15\\/10\\/2026 01:25 AM.\",\"module\":\"appointment\",\"module_id\":8,\"sender_id\":1,\"receiver_id\":\"1\",\"type\":\"notification\",\"patient_id\":\"6\"}', 0, '2026-10-15 11:00:51', '2026-10-15 11:00:51'),
(33, 1, 1, '{\"message\":\"Dr Srinethi Admin You have a New appointment with Patient LINGESH on 15\\/10\\/2026 01:25 AM.\",\"module\":\"appointment\",\"module_id\":9,\"sender_id\":1,\"receiver_id\":\"1\",\"type\":\"notification\",\"patient_id\":\"6\"}', 0, '2026-10-15 11:00:51', '2026-10-15 11:00:51'),
(34, 1, 1, '{\"message\":\"Dr Srinethi Admin You have a New appointment with Patient LINGESH on 15\\/10\\/2026 01:25 AM.\",\"module\":\"appointment\",\"module_id\":10,\"sender_id\":1,\"receiver_id\":\"1\",\"type\":\"notification\",\"patient_id\":\"6\"}', 0, '2026-10-15 11:00:51', '2026-10-15 11:00:51'),
(35, 1, 1, '{\"message\":\"Dr Srinethi Admin You have a New appointment with Patient LINGESH on 15\\/10\\/2026 01:25 AM.\",\"module\":\"appointment\",\"module_id\":11,\"sender_id\":1,\"receiver_id\":\"1\",\"type\":\"notification\",\"patient_id\":\"6\"}', 0, '2026-10-15 11:00:51', '2026-10-15 11:00:51'),
(36, 1, 1, '{\"message\":\"Dr Srinethi Admin You have a New appointment with Patient LINGESH on 15\\/10\\/2026 01:25 AM.\",\"module\":\"appointment\",\"module_id\":12,\"sender_id\":1,\"receiver_id\":\"1\",\"type\":\"notification\",\"patient_id\":\"6\"}', 0, '2026-10-15 11:00:51', '2026-10-15 11:00:51'),
(37, 1, 1, '{\"message\":\"Dr Srinethi Admin You have a New appointment with Patient LINGESH on 15\\/10\\/2026 01:25 AM.\",\"module\":\"appointment\",\"module_id\":13,\"sender_id\":1,\"receiver_id\":\"1\",\"type\":\"notification\",\"patient_id\":\"6\"}', 0, '2026-10-15 11:00:51', '2026-10-15 11:00:51'),
(38, 1, 1, '{\"message\":\"Dr Srinethi Admin You have a New appointment with Patient LINGESH on 15\\/10\\/2026 01:25 AM.\",\"module\":\"appointment\",\"module_id\":14,\"sender_id\":1,\"receiver_id\":\"1\",\"type\":\"notification\",\"patient_id\":\"6\"}', 0, '2026-10-15 11:00:51', '2026-10-15 11:00:51'),
(39, 1, 42, '{\"message\":\"Dr disha You have a New treatment \'Test1\' has been assigned to you by Srinethi Admin.\",\"module\":\"treatment\",\"module_id\":4,\"sender_id\":1,\"receiver_id\":42,\"type\":\"notification\",\"patient_id\":null}', 0, '2026-10-15 11:10:07', '2026-10-15 11:10:07'),
(40, 1, 42, '{\"message\":\"Dr disha You have a New treatment \'treatment-gst\' has been assigned to you by Srinethi Admin.\",\"module\":\"treatment\",\"module_id\":5,\"sender_id\":1,\"receiver_id\":42,\"type\":\"notification\",\"patient_id\":null}', 0, '2026-10-15 11:35:06', '2026-10-15 11:35:06'),
(41, 1, 1, '{\"message\":\"Dr Srinethi Admin You have a New treatment \'disha-gst-test\' has been assigned to you by Srinethi Admin.\",\"module\":\"treatment\",\"module_id\":6,\"sender_id\":1,\"receiver_id\":1,\"type\":\"notification\",\"patient_id\":null}', 0, '2026-10-15 11:35:24', '2026-10-15 11:35:24');

-- --------------------------------------------------------

--
-- Table structure for table `opd_visits`
--

CREATE TABLE `opd_visits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` varchar(200) DEFAULT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `visit_date` date NOT NULL,
  `chief_complaint` text DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `prescription` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`prescription`)),
  `consultation_fees` decimal(8,2) NOT NULL DEFAULT 0.00,
  `status` enum('active','completed') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ot_procedures`
--

CREATE TABLE `ot_procedures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` varchar(200) DEFAULT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `procedure_name` varchar(255) NOT NULL,
  `procedure_date` date NOT NULL,
  `operation_notes` text DEFAULT NULL,
  `status` enum('scheduled','completed') NOT NULL DEFAULT 'scheduled',
  `updated_at` date DEFAULT current_timestamp(),
  `created_at` date DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pathology_reports`
--

CREATE TABLE `pathology_reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` varchar(255) DEFAULT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `test_id` bigint(20) UNSIGNED NOT NULL,
  `sample_collected_date` date NOT NULL,
  `report_date` date NOT NULL,
  `result` text NOT NULL,
  `report_file` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pathology_tests`
--

CREATE TABLE `pathology_tests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` varchar(255) DEFAULT NULL,
  `test_name` varchar(255) NOT NULL,
  `test_code` varchar(255) NOT NULL,
  `sample_type` varchar(255) NOT NULL,
  `normal_range` varchar(255) NOT NULL,
  `cost` decimal(8,2) NOT NULL,
  `gst_option` varchar(255) NOT NULL,
  `product_gst` text DEFAULT NULL,
  `report_format` varchar(255) NOT NULL DEFAULT 'PDF',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` varchar(255) DEFAULT NULL,
  `patient_unique_id` text DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `login_patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `treatment_id` varchar(200) DEFAULT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `email` text DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `age` varchar(255) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `patient_type` enum('All','Cosmetic','OPD','IPD','Home') DEFAULT NULL,
  `referral_source` varchar(255) DEFAULT NULL,
  `source_details` text DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `diagnosis_id` varchar(255) DEFAULT NULL,
  `last_appointment` text DEFAULT NULL,
  `profile` text DEFAULT NULL,
  `note_type` varchar(255) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `note_interval` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `symptoms` varchar(1000) DEFAULT NULL,
  `improvement_history` varchar(500) DEFAULT NULL,
  `symptom_images` varchar(500) DEFAULT NULL,
  `symptom_remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `branch_id`, `patient_unique_id`, `user_id`, `login_patient_id`, `treatment_id`, `fullname`, `email`, `password`, `phone`, `address`, `age`, `birthdate`, `gender`, `patient_type`, `referral_source`, `source_details`, `state`, `city`, `diagnosis_id`, `last_appointment`, `profile`, `note_type`, `note`, `note_interval`, `created_at`, `updated_at`, `symptoms`, `improvement_history`, `symptom_images`, `symptom_remarks`) VALUES
(1, '1', 'TP001', 1, 36, NULL, 'Riya', 'riya@gmail.com', '$2y$12$AgxEG15cM.MQ6EiUQUtdrOTEDgHSF07Nxss2qT0zOed/4dRz0Xjde', '7744231123', 'Surat', '24', '2002-02-01', NULL, 'All', NULL, NULL, 'Gujarat', 'Surat', '[\"1\"]', NULL, NULL, NULL, NULL, NULL, '2026-10-14 18:29:24', '2026-10-14 18:29:24', '[\"1\"]', NULL, NULL, NULL),
(2, '1', 'TP002', 1, 44, NULL, 'test-disha', NULL, '$2y$12$Z5i2TWKZGCeyxxFSQNSnouPh2.QJodmhYjQROLz7npZYY5fV7z8SO', '3020562104', NULL, '30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', NULL, NULL, 'speech', NULL, '4010', '2026-10-14 19:16:18', '2026-10-14 19:16:18', NULL, NULL, NULL, NULL),
(3, '1', 'TP003', 1, 45, NULL, 'test-patient', NULL, '$2y$12$XW1oA1/Ou6M4o8q3XlhNveEO/BLT411041Kz8Tp.oe1vvxLTmW2Vy', '9824781385', NULL, '21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', NULL, NULL, 'speech', NULL, '4010', '2026-10-14 19:18:26', '2026-10-14 19:18:26', NULL, NULL, NULL, NULL),
(4, '1', 'TP004', 1, 46, NULL, 'Mangai', NULL, '$2y$12$6a3pyq/uRrKJMAUvkJ1bOOmewNrDFmbnLEVUQt9bIXkEB.agHhEi2', '9840633625', NULL, '58', NULL, NULL, 'OPD', NULL, NULL, NULL, NULL, '[\"2\"]', NULL, NULL, NULL, NULL, NULL, '2026-10-14 19:59:35', '2026-10-14 19:59:35', NULL, NULL, NULL, 'BP-180/70 , SPO2- 98'),
(5, '1', 'TP005', 1, 47, NULL, 'DRESH', NULL, '$2y$12$tUdL97lcma9eFXBRD.Mb4u.FhFhf2wUmh.TLWBgLQSsf2WB52pM1i', '9874562125', NULL, '74', NULL, NULL, 'OPD', NULL, NULL, NULL, NULL, '[\"2\"]', NULL, NULL, NULL, NULL, NULL, '2026-10-15 10:53:15', '2026-10-15 10:53:15', NULL, NULL, NULL, NULL),
(6, '1', 'TP006', 1, 48, NULL, 'LINGESH', NULL, '$2y$12$xytcSJu7scx2BYWsCUAGTO7hgaCRmxC4K1IvbiqnV7YXGsTJeubIe', '7305791601', NULL, '28', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', NULL, NULL, 'speech', NULL, '4010', '2026-10-15 10:59:51', '2026-10-15 10:59:51', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `patient_assign_assessment`
--

CREATE TABLE `patient_assign_assessment` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `template_id` longtext DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_assign_dietchart`
--

CREATE TABLE `patient_assign_dietchart` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` varchar(255) DEFAULT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `diet_template_id` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`diet_template_id`)),
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`description`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_assign_homeadvice`
--

CREATE TABLE `patient_assign_homeadvice` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `template_id` longtext DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_diagnosis`
--

CREATE TABLE `patient_diagnosis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_discharge_dets`
--

CREATE TABLE `patient_discharge_dets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` varchar(200) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `treatment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ipd_id` varchar(200) DEFAULT NULL,
  `gst_option` varchar(200) DEFAULT NULL,
  `product_gst` varchar(200) DEFAULT NULL,
  `room_number` varchar(255) DEFAULT NULL,
  `bed_number` varchar(255) DEFAULT NULL,
  `admit_date` date DEFAULT NULL,
  `discharge_date` date NOT NULL,
  `total_bill` decimal(10,2) NOT NULL DEFAULT 0.00,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('paid','unpaid') NOT NULL DEFAULT 'unpaid',
  `discharge_note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_medicine`
--

CREATE TABLE `patient_medicine` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` varchar(255) DEFAULT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `medicine_id` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`medicine_id`)),
  `treatment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `appointment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` text DEFAULT NULL,
  `value` text DEFAULT NULL,
  `value_type` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_history`
--

CREATE TABLE `payment_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `daily_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_mode` enum('cash','online','cash+online') NOT NULL,
  `paid_type` enum('fully','partial') NOT NULL,
  `cash_amount` int(11) DEFAULT NULL,
  `online_amount` int(11) DEFAULT NULL,
  `paid_amount` decimal(10,2) NOT NULL,
  `remain_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_history`
--

INSERT INTO `payment_history` (`id`, `branch_id`, `user_id`, `daily_id`, `amount`, `payment_mode`, `paid_type`, `cash_amount`, `online_amount`, `paid_amount`, `remain_amount`, `created_at`, `updated_at`) VALUES
(1, NULL, 1, 1, 200.00, 'cash', 'fully', 200, 0, 200.00, 0.00, '2026-10-14 20:01:16', '2026-10-14 20:01:16');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(22, 'App\\Models\\User', 7, 'authToken', 'b195b49201bf8fc974020eaa74a0b0cd699c98b8138c92824ae7f3886e21a1db', '[\"*\"]', NULL, NULL, '2026-07-31 15:35:01', '2026-07-31 15:35:01'),
(24, 'App\\Models\\User', 14, 'authToken', '6a8e1819367aad56fb3f4379504a85250a10c159e9dd5d0e61a1f9edf549255d', '[\"*\"]', NULL, NULL, '2026-07-31 15:45:33', '2026-07-31 15:45:33'),
(164, 'App\\Models\\User', 33, 'authToken', '342931a50981937bb46c59e010921bffea77be3758412436951bc1a39f13e220', '[\"*\"]', '2026-10-08 12:44:49', NULL, '2026-10-08 12:44:48', '2026-10-08 12:44:49'),
(165, 'App\\Models\\User', 33, 'authToken', '823b113ac5d89e202e67fdc7de4417f99cae1c6da041271295217c97b8408089', '[\"*\"]', '2026-10-08 12:54:38', NULL, '2026-10-08 12:53:03', '2026-10-08 12:54:38'),
(167, 'App\\Models\\User', 33, 'authToken', '12ba8898ce423f72fac0fb1b09898ac9fb924862c4660dad24dcb9453f4502ad', '[\"*\"]', '2026-10-08 12:55:30', NULL, '2026-10-08 12:54:54', '2026-10-08 12:55:30'),
(168, 'App\\Models\\User', 33, 'authToken', 'd632bb180e7d42febe8831b4a15c7d1d147e70440f3a6d1034de1074851c13c1', '[\"*\"]', '2026-10-08 13:05:25', NULL, '2026-10-08 13:05:21', '2026-10-08 13:05:25'),
(170, 'App\\Models\\User', 33, 'authToken', '3ac7df073f4ba15e39f6607c4df4b9723e0a977dc93d619d763d5af96fa01eeb', '[\"*\"]', '2026-10-08 17:47:04', NULL, '2026-10-08 17:47:03', '2026-10-08 17:47:04'),
(171, 'App\\Models\\User', 33, 'authToken', '486cc7e46346da02270625b9506a4dd4fde922c88307dfc6ad44ed20f54b0bf5', '[\"*\"]', '2026-10-08 18:57:07', NULL, '2026-10-08 18:53:51', '2026-10-08 18:57:07'),
(173, 'App\\Models\\User', 33, 'authToken', 'd908eea385abc339f4ce5ac9291df6ee76aaa1fdc1190155fba717219a575d8a', '[\"*\"]', '2026-10-12 11:42:12', NULL, '2026-10-12 11:42:09', '2026-10-12 11:42:12'),
(185, 'App\\Models\\User', 35, 'authToken', 'd399f158093aea30ac4b05fdf4fdeb1da57667311c70e60a936045bc96c39472', '[\"*\"]', '2026-10-14 14:51:26', NULL, '2026-10-14 14:51:23', '2026-10-14 14:51:26'),
(186, 'App\\Models\\User', 35, 'authToken', '1fdf913c5f83d45513dccaa9698b6e1a8d751966433c2b288e22eff4a86517bd', '[\"*\"]', '2026-10-14 14:56:47', NULL, '2026-10-14 14:54:33', '2026-10-14 14:56:47'),
(187, 'App\\Models\\User', 35, 'authToken', '8c283f271bb5cf2b7d2f49e40b55372c85f3635d133660c7268ce0d726476d8b', '[\"*\"]', '2026-10-14 15:07:10', NULL, '2026-10-14 15:03:59', '2026-10-14 15:07:10'),
(188, 'App\\Models\\User', 35, 'authToken', '46ef1ba55b8c7e70b45827051d767d05245fc54af93370e7c6578d1eaa3b4826', '[\"*\"]', '2026-10-14 15:08:13', NULL, '2026-10-14 15:07:23', '2026-10-14 15:08:13'),
(189, 'App\\Models\\User', 35, 'authToken', '78ed9c8d644967b8b18db601aebc11419bb149bbb7d4891c3c54f8b0927beb19', '[\"*\"]', '2026-10-14 15:12:27', NULL, '2026-10-14 15:10:07', '2026-10-14 15:12:27'),
(190, 'App\\Models\\User', 35, 'authToken', '4ec31076c408b05634abbb3601da5f0bcc4d177d9b17c79db9f7183a844d37a4', '[\"*\"]', NULL, NULL, '2026-10-14 15:11:22', '2026-10-14 15:11:22'),
(191, 'App\\Models\\User', 35, 'authToken', 'bdaf97f721f0a6aad4f3bda34d1535387bf7465b3be432e59391b623412e5aff', '[\"*\"]', '2026-10-14 15:17:19', NULL, '2026-10-14 15:17:15', '2026-10-14 15:17:19'),
(192, 'App\\Models\\User', 35, 'authToken', '24ec83c75aa7036131dfacb16f9fbb3de2160667f7a6ca13539eb583bdb06837', '[\"*\"]', '2026-10-14 15:22:01', NULL, '2026-10-14 15:17:24', '2026-10-14 15:22:01'),
(194, 'App\\Models\\User', 35, 'authToken', 'bc3c737e8f493085a34c29a119407fbc558f3b7dc9c52f220445726782ee8b08', '[\"*\"]', '2026-10-14 15:31:48', NULL, '2026-10-14 15:30:39', '2026-10-14 15:31:48'),
(195, 'App\\Models\\User', 35, 'authToken', '8287c4a65c42420950925665edfb15e4142786fc6076c65278498bfd962768ec', '[\"*\"]', '2026-10-14 15:37:30', NULL, '2026-10-14 15:37:24', '2026-10-14 15:37:30'),
(202, 'App\\Models\\User', 1, 'authToken', 'e023ff61c7e5c3572d34b54d5ed5a731e7fdbc49d7474818bb8510159f99e03d', '[\"*\"]', NULL, NULL, '2026-10-14 19:22:04', '2026-10-14 19:22:04'),
(203, 'App\\Models\\User', 1, 'authToken', '2018565d2b60f77a6a260fb56525756cad1d45637f739cf11b69fa69e235e2dd', '[\"*\"]', '2026-10-14 22:43:26', NULL, '2026-10-14 19:48:40', '2026-10-14 22:43:26'),
(204, 'App\\Models\\User', 1, 'authToken', 'da65f3a3a4479072c0e8cfef30dd3b1a4e74aab08fd10c28e523427f405fb3e0', '[\"*\"]', NULL, NULL, '2026-10-15 10:01:28', '2026-10-15 10:01:28'),
(205, 'App\\Models\\User', 1, 'authToken', '6982f203bd825dd1b46f36c4c358efebc7f4201293c4e441efd31cbd0a878b37', '[\"*\"]', NULL, NULL, '2026-10-15 10:15:11', '2026-10-15 10:15:11'),
(206, 'App\\Models\\User', 1, 'authToken', '9152f6d3f07e85b741f4b28e18a05f155838cc8564e5d9204b56056acf592826', '[\"*\"]', NULL, NULL, '2026-10-15 10:51:49', '2026-10-15 10:51:49'),
(207, 'App\\Models\\User', 1, 'authToken', 'a3e8bd8cb816d125b7ee0213625596af113e91d9a6bed6d34b2eec5cc500e1d2', '[\"*\"]', NULL, NULL, '2026-10-15 11:36:11', '2026-10-15 11:36:11');

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `duration` varchar(255) NOT NULL DEFAULT 'month',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `user_limit` int(11) DEFAULT NULL,
  `branch_limit` int(11) DEFAULT NULL,
  `storage_limit` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sub_branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `features` longtext DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`id`, `name`, `price`, `duration`, `start_date`, `end_date`, `subtitle`, `user_limit`, `branch_limit`, `storage_limit`, `is_active`, `sub_branch_id`, `features`, `total_amount`, `created_at`, `updated_at`) VALUES
(1, 'Professional', 1500.00, '1 Year', '2025-07-01', '2027-07-01', 'd', 25, 3, 50, 1, NULL, '[\"Up to 25 users(Staff)\",\"All Modules \\/ Features Included\",\"Whatsapp \\/ SMS integrations\",\"Multi Branch (Max 3)\",\"50GB Storage\"]', 18000.00, '2026-07-30 09:59:57', '2026-07-30 10:15:07');

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `doctor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `medicine_id` bigint(20) UNSIGNED DEFAULT NULL,
  `dosage` varchar(255) DEFAULT NULL,
  `frequency` varchar(255) DEFAULT NULL,
  `duration` varchar(255) DEFAULT NULL,
  `instruction` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_types`
--

CREATE TABLE `project_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_types`
--

INSERT INTO `project_types` (`id`, `name`, `code`, `icon`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Hospital HMS', 'hospital', NULL, 1, '2026-07-30 05:40:38', '2026-07-30 05:40:38'),
(2, 'Dental HMS', 'dental', NULL, 1, '2026-07-30 05:40:38', '2026-07-30 05:40:38'),
(3, 'Physio HMS', 'physio', NULL, 1, '2026-07-30 05:40:38', '2026-07-30 05:40:38');

-- --------------------------------------------------------

--
-- Table structure for table `radiology_reports`
--

CREATE TABLE `radiology_reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` varchar(255) DEFAULT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `test_id` bigint(20) UNSIGNED NOT NULL,
  `report_date` date NOT NULL,
  `report_file` varchar(255) NOT NULL,
  `converted_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `radiology_tests`
--

CREATE TABLE `radiology_tests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` varchar(255) DEFAULT NULL,
  `test_name` varchar(255) NOT NULL,
  `test_code` varchar(255) NOT NULL,
  `body_part` varchar(255) NOT NULL,
  `cost` decimal(8,2) NOT NULL,
  `gst_option` varchar(255) NOT NULL,
  `product_gst` text DEFAULT NULL,
  `report_format` varchar(255) NOT NULL DEFAULT 'Digital Image',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referal_doctors`
--

CREATE TABLE `referal_doctors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `doctor_name` varchar(255) DEFAULT NULL,
  `specialist` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'SuperAdmin', 'Super admin access to all functionality.', '2026-07-30 06:00:23', '2026-07-30 06:00:23'),
(2, 'Admin', 'Admin access to clinic operations.', '2026-07-30 06:00:23', '2026-07-30 06:00:23'),
(3, 'Doctor', 'Doctor access with assigned permissions.', '2026-07-30 06:00:23', '2026-07-30 06:00:23'),
(4, 'Receptionist', 'Receptionist access for front desk operations.', '2026-07-30 06:00:23', '2026-07-30 06:00:23'),
(5, 'Patient', 'Patient portal access.', '2026-07-30 06:00:23', '2026-07-30 06:00:23');

-- --------------------------------------------------------

--
-- Table structure for table `send_emails`
--

CREATE TABLE `send_emails` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `template_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `send_sms`
--

CREATE TABLE `send_sms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `template_id` bigint(20) UNSIGNED NOT NULL,
  `service` varchar(255) NOT NULL,
  `sender_id` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `service_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` varchar(255) DEFAULT NULL,
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `branch_id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(15, NULL, 'clinic_name', 'Kaveri Street, Rajaji Nagar Villivakkam', NULL, NULL),
(16, NULL, 'clinic_logo', 'uploads/clinic_logos/1791983441_image (2).png', NULL, NULL),
(17, NULL, 'clinic_email', 'support@fableadtech.com', NULL, NULL),
(18, NULL, 'clinic_phone', '9940633625', NULL, NULL),
(19, NULL, 'clinic_address', 'No.44(17), Kaveri Street, Rajaji Nagar Villivakkam Chennai -600049', NULL, NULL),
(20, NULL, 'clinic_city', 'Chennai', NULL, NULL),
(21, NULL, 'clinic_state', 'Tamil Nadu', NULL, NULL),
(22, NULL, 'tax_name', 'GST', NULL, NULL),
(23, NULL, 'tax_type', 'percentage', NULL, NULL),
(24, NULL, 'value', '18', NULL, NULL),
(37, NULL, 'project_type_id', '1', '2026-07-30 06:07:27', '2026-10-07 12:21:21'),
(38, NULL, 'sms_status', 'off', '2026-10-14 17:52:20', '2026-10-14 17:52:31'),
(39, NULL, 'whatsapp_status', 'off', '2026-10-14 17:52:25', '2026-10-14 17:52:35'),
(40, NULL, 'smtp_status', 'off', '2026-10-14 17:52:40', '2026-10-14 17:52:44'),
(41, NULL, 'invoice_declaration', 'Terms & Conditions\n\n1. All invoices must be paid according to the hospital\'s payment policy.\n2. Patients should verify invoice details before making payment.\n3. Consultation, diagnostic tests, medicines, and other services will be charged as applicable.\n4. Any billing discrepancy must be reported to the hospital billing department.', '2026-10-14 19:11:50', '2026-10-14 19:14:38'),
(42, NULL, 'invoice_declaration_status', 'on', '2026-10-14 19:11:50', '2026-10-14 19:11:50');

-- --------------------------------------------------------

--
-- Table structure for table `sms_templates`
--

CREATE TABLE `sms_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `soap`
--

CREATE TABLE `soap` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date DEFAULT NULL,
  `subjective` longtext DEFAULT NULL,
  `objective` longtext DEFAULT NULL,
  `assessment` longtext DEFAULT NULL,
  `plan` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_person` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `symptoms`
--

CREATE TABLE `symptoms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `symptoms`
--

INSERT INTO `symptoms` (`id`, `branch_id`, `name`, `details`, `created_at`, `updated_at`) VALUES
(1, '1', 'test', NULL, '2026-10-14 18:29:11', '2026-10-14 18:29:11');

-- --------------------------------------------------------

--
-- Table structure for table `taxes`
--

CREATE TABLE `taxes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tax_name` varchar(255) DEFAULT NULL,
  `tax_rate` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `taxes`
--

INSERT INTO `taxes` (`id`, `tax_name`, `tax_rate`, `status`, `branch_id`, `isDeleted`, `created_at`, `updated_at`) VALUES
(1, 'CGST', 12.00, 'active', 1, 0, '2026-07-31 14:48:33', '2026-07-31 14:48:33');

-- --------------------------------------------------------

--
-- Table structure for table `therapy`
--

CREATE TABLE `therapy` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `duration_minutes` int(11) NOT NULL,
  `cost` decimal(8,2) NOT NULL,
  `gst_option` varchar(255) NOT NULL,
  `product_gst` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `treatments`
--

CREATE TABLE `treatments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gst_option` varchar(255) NOT NULL,
  `product_gst` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `treatments`
--

INSERT INTO `treatments` (`id`, `branch_id`, `user_id`, `doctor_id`, `name`, `price`, `gst_option`, `product_gst`, `description`, `created_at`, `updated_at`) VALUES
(1, '1', 1, 42, 'Treatment 1', 200.00, 'Without GST', NULL, 'Treatment 1 Description', '2026-10-14 18:56:41', '2026-10-14 19:15:43'),
(2, '1', 1, 1, 'Consultations', 500.00, 'Without GST', NULL, 'Consultations Description', '2026-10-14 19:18:35', '2026-10-14 19:18:35'),
(3, '1', 1, 42, 'test-treatment', 200.00, 'Without GST', NULL, NULL, '2026-10-14 19:20:04', '2026-10-14 19:20:04'),
(4, '1', 1, 42, 'Test1', 200.00, 'Without GST', NULL, NULL, '2026-10-15 11:10:07', '2026-10-15 11:10:07'),
(5, '1', 1, 42, 'treatment-gst', 1000.00, 'With GST', '[{\"tax_name\":\"CGST\",\"tax_rate\":\"12.00\",\"tax_amount\":120}]', 'testing', '2026-10-15 11:35:06', '2026-10-15 11:35:06'),
(6, '1', 1, 1, 'disha-gst-test', 600.00, 'With GST', '[{\"tax_name\":\"CGST\",\"tax_rate\":\"12.00\",\"tax_amount\":72}]', 'testing', '2026-10-15 11:35:24', '2026-10-15 11:35:24');

-- --------------------------------------------------------

--
-- Table structure for table `treatment_booking`
--

CREATE TABLE `treatment_booking` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `treatment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `machine_id` longtext DEFAULT NULL,
  `plan` text DEFAULT NULL,
  `remain_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `treatment_payment_history`
--

CREATE TABLE `treatment_payment_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `treatment_booking_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_mode` varchar(255) DEFAULT NULL,
  `paid_type` varchar(255) DEFAULT NULL,
  `cash` decimal(12,2) NOT NULL DEFAULT 0.00,
  `online` decimal(12,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `remain_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `plan_id` varchar(200) DEFAULT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `fullname` text DEFAULT NULL,
  `email` text DEFAULT NULL,
  `phone` text DEFAULT NULL,
  `profile` text DEFAULT NULL,
  `password` text DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `clinic_name` varchar(255) DEFAULT NULL,
  `clinic_logo` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `fcm_token` text DEFAULT NULL,
  `forget_pass_key` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `plan_id`, `role_id`, `username`, `fullname`, `email`, `phone`, `profile`, `password`, `branch_id`, `clinic_name`, `clinic_logo`, `reset_token`, `status`, `fcm_token`, `forget_pass_key`, `is_deleted`, `created_by`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, '1', 2, NULL, 'Srinethi Admin', 'srinethi-admin@gmail.com', '9940633625', 'uploads/users/1791979372_Image (1).jpg', '$2y$12$uy4Ku8pomxqSkBq84uYdLuo41g.o3nQbs5N5X7GgkZWk2zQYdDLD2', 1, 'Clinic Manage System', NULL, 'BL4v1g2Rw2xFiKSFUnuIeGW2eTwCrZHD2WIW0QxlwoTMLnL1br2XSc2nqQTI', 'active', NULL, NULL, 0, NULL, NULL, '2026-07-30 05:35:06', '2026-10-14 19:13:07'),
(36, NULL, 5, NULL, 'Riya', 'riya@gmail.com', '7744231123', NULL, '$2y$12$2uN5USl4IcFkKh8fBqfJtu81GS4GSSpU3lJQPXhiuWgnXkRu95VCC', NULL, NULL, NULL, NULL, 'active', NULL, NULL, 0, 1, NULL, '2026-10-14 18:29:23', '2026-10-14 18:29:23'),
(42, NULL, 3, NULL, 'disha', 'disha@gmail.com', '8523601452', 'uploads/users/1791984345_Image (2).jpg', '$2y$12$5saOHUXsNlmhuAgMZ7GGu.045FuWgoDRH6d2sutu3QgEGrsOHW94K', 1, NULL, NULL, NULL, 'active', NULL, NULL, 0, 1, NULL, '2026-10-14 18:55:45', '2026-10-14 18:55:45'),
(43, NULL, 3, NULL, 'riddhi', 'riddhi@gmail.com', '2030105236', 'uploads/users/1791984834_Image (1).jpg', '$2y$12$aFO/llXqNJmnNES1NJV4FuOKkY1lI.mTaLCBxZOffb9UikqE3M.bW', 1, NULL, NULL, NULL, 'active', NULL, NULL, 0, 1, NULL, '2026-10-14 19:03:54', '2026-10-14 19:03:54'),
(44, NULL, 5, NULL, 'test-disha', NULL, '3020562104', NULL, '$2y$12$EDHub/6htbAjSWQTOSKCku6q74D6RD6OmAD5sbsXp7fmpbNQ8Efs.', NULL, NULL, NULL, NULL, 'active', NULL, NULL, 0, 1, NULL, '2026-10-14 19:16:18', '2026-10-14 19:16:18'),
(45, NULL, 5, NULL, 'test-patient', NULL, '9824781385', NULL, '$2y$12$iUwzFgmqmAZ2PPkB7sA7SepWbtVGM/F4Fy4aOmeJ9ZnsCbCXyGJYO', NULL, NULL, NULL, NULL, 'active', NULL, NULL, 0, 1, NULL, '2026-10-14 19:18:26', '2026-10-14 19:18:26'),
(46, NULL, 5, NULL, 'Mangai', NULL, '9840633625', NULL, '$2y$12$d9EhkSfRUAOZI6COIOjHweFMBJvfgzKIvZwlqjsA7AyL7NMRu5uCy', NULL, NULL, NULL, NULL, 'active', NULL, NULL, 0, 1, NULL, '2026-10-14 19:59:35', '2026-10-14 19:59:35'),
(47, NULL, 5, NULL, 'DRESH', NULL, '9874562125', NULL, '$2y$12$TvmeaftyZ5t1hossl8m/F.4oFsrCzWn3jMZ.jZQdeiN5bWL/WYygW', NULL, NULL, NULL, NULL, 'active', NULL, NULL, 0, 1, NULL, '2026-10-15 10:53:14', '2026-10-15 10:53:14'),
(48, NULL, 5, NULL, 'LINGESH', NULL, '7305791601', NULL, '$2y$12$da7qpF95S8YKrgsmfwYsf.Kakfri0AmbbsOjzsD6NwBLGblaK2piC', NULL, NULL, NULL, NULL, 'active', NULL, NULL, 0, 1, NULL, '2026-10-15 10:59:50', '2026-10-15 10:59:50');

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `education` text DEFAULT NULL,
  `experience` text DEFAULT NULL,
  `shift` varchar(255) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`id`, `user_id`, `address`, `state`, `city`, `gender`, `birth_date`, `education`, `experience`, `shift`, `salary`, `created_at`, `updated_at`) VALUES
(1, 1, 'No.44(17), Kaveri Street, Rajaji Nagar Villivakkam Chennai -600049', 'Gujarat', 'Surat', 'Male', '2000-01-01', NULL, NULL, 'FullDay', 0.00, '2026-07-30 05:35:06', '2026-10-14 17:32:52'),
(15, 42, 'main st', 'Gujarat', 'Surat', 'Female', '2013-02-13', NULL, NULL, 'FullDay', 0.00, '2026-10-14 18:55:46', '2026-10-14 19:05:26'),
(16, 43, 'main st', 'Gujarat', 'Surat', 'Female', '2022-01-13', NULL, NULL, 'FullDay', 0.00, '2026-10-14 19:03:54', '2026-10-14 19:05:19');

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `module_id` bigint(20) UNSIGNED DEFAULT NULL,
  `create` tinyint(1) NOT NULL DEFAULT 0,
  `view` tinyint(1) NOT NULL DEFAULT 0,
  `update` tinyint(1) NOT NULL DEFAULT 0,
  `delete` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_permissions`
--

INSERT INTO `user_permissions` (`id`, `user_id`, `module_id`, `create`, `view`, `update`, `delete`, `created_at`, `updated_at`) VALUES
(1, 36, 6, 1, 1, 1, 1, '2026-10-14 18:29:24', '2026-10-14 18:29:24'),
(2, 36, 2, 1, 1, 1, 1, '2026-10-14 18:29:24', '2026-10-14 18:29:24'),
(3, 42, 1, 1, 1, 1, 1, '2026-10-14 18:55:46', '2026-10-14 18:55:46'),
(4, 42, 2, 1, 1, 1, 1, '2026-10-14 18:55:46', '2026-10-14 18:55:46'),
(5, 43, 1, 1, 1, 1, 1, '2026-10-14 19:03:54', '2026-10-14 19:03:54'),
(6, 43, 2, 1, 1, 1, 1, '2026-10-14 19:03:54', '2026-10-14 19:03:54'),
(7, 43, 5, 1, 1, 1, 1, '2026-10-14 19:03:54', '2026-10-14 19:03:54'),
(8, 43, 3, 0, 0, 0, 0, '2026-10-14 19:05:19', '2026-10-14 19:05:19'),
(9, 43, 6, 0, 0, 0, 0, '2026-10-14 19:05:19', '2026-10-14 19:05:19'),
(10, 43, 7, 0, 0, 0, 0, '2026-10-14 19:05:19', '2026-10-14 19:05:19'),
(11, 43, 18, 0, 0, 0, 0, '2026-10-14 19:05:19', '2026-10-14 19:05:19'),
(12, 43, 35, 0, 0, 0, 0, '2026-10-14 19:05:19', '2026-10-14 19:05:19'),
(13, 43, 36, 0, 0, 0, 0, '2026-10-14 19:05:19', '2026-10-14 19:05:19'),
(14, 42, 3, 0, 0, 0, 0, '2026-10-14 19:05:26', '2026-10-14 19:05:26'),
(15, 42, 5, 0, 0, 0, 0, '2026-10-14 19:05:26', '2026-10-14 19:05:26'),
(16, 42, 6, 0, 0, 0, 0, '2026-10-14 19:05:26', '2026-10-14 19:05:26'),
(17, 42, 7, 0, 0, 0, 0, '2026-10-14 19:05:26', '2026-10-14 19:05:26'),
(18, 42, 18, 0, 0, 0, 0, '2026-10-14 19:05:26', '2026-10-14 19:05:26'),
(19, 42, 35, 0, 0, 0, 0, '2026-10-14 19:05:26', '2026-10-14 19:05:26'),
(20, 42, 36, 0, 0, 0, 0, '2026-10-14 19:05:26', '2026-10-14 19:05:26'),
(21, 44, 6, 1, 1, 1, 1, '2026-10-14 19:16:18', '2026-10-14 19:16:18'),
(22, 44, 2, 1, 1, 1, 1, '2026-10-14 19:16:18', '2026-10-14 19:16:18'),
(23, 45, 6, 1, 1, 1, 1, '2026-10-14 19:18:26', '2026-10-14 19:18:26'),
(24, 45, 2, 1, 1, 1, 1, '2026-10-14 19:18:26', '2026-10-14 19:18:26'),
(25, 46, 6, 1, 1, 1, 1, '2026-10-14 19:59:35', '2026-10-14 19:59:35'),
(26, 46, 2, 1, 1, 1, 1, '2026-10-14 19:59:35', '2026-10-14 19:59:35'),
(27, 47, 6, 1, 1, 1, 1, '2026-10-15 10:53:15', '2026-10-15 10:53:15'),
(28, 47, 2, 1, 1, 1, 1, '2026-10-15 10:53:15', '2026-10-15 10:53:15'),
(29, 48, 6, 1, 1, 1, 1, '2026-10-15 10:59:51', '2026-10-15 10:59:51'),
(30, 48, 2, 1, 1, 1, 1, '2026-10-15 10:59:51', '2026-10-15 10:59:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ai_credentials`
--
ALTER TABLE `ai_credentials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointment_history`
--
ALTER TABLE `appointment_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `assessment_template`
--
ALTER TABLE `assessment_template`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `assigned_therapies`
--
ALTER TABLE `assigned_therapies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branches_email_unique` (`email`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chatbot_messages`
--
ALTER TABLE `chatbot_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat_history`
--
ALTER TABLE `chat_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `code_master`
--
ALTER TABLE `code_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `daily_data`
--
ALTER TABLE `daily_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `default_email_template`
--
ALTER TABLE `default_email_template`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `diagnosis`
--
ALTER TABLE `diagnosis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `diet_charts`
--
ALTER TABLE `diet_charts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_template`
--
ALTER TABLE `email_template`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `followups`
--
ALTER TABLE `followups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `home_advice`
--
ALTER TABLE `home_advice`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ipd_admissions`
--
ALTER TABLE `ipd_admissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `lab_tests`
--
ALTER TABLE `lab_tests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `machines`
--
ALTER TABLE `machines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medical_reports`
--
ALTER TABLE `medical_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medicine_units`
--
ALTER TABLE `medicine_units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_sender_id_foreign` (`sender_id`),
  ADD KEY `notifications_receiver_id_foreign` (`receiver_id`);

--
-- Indexes for table `opd_visits`
--
ALTER TABLE `opd_visits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ot_procedures`
--
ALTER TABLE `ot_procedures`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pathology_reports`
--
ALTER TABLE `pathology_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pathology_tests`
--
ALTER TABLE `pathology_tests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pathology_tests_test_code_unique` (`test_code`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patient_assign_assessment`
--
ALTER TABLE `patient_assign_assessment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patient_assign_dietchart`
--
ALTER TABLE `patient_assign_dietchart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patient_assign_homeadvice`
--
ALTER TABLE `patient_assign_homeadvice`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patient_diagnosis`
--
ALTER TABLE `patient_diagnosis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patient_discharge_dets`
--
ALTER TABLE `patient_discharge_dets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patient_medicine`
--
ALTER TABLE `patient_medicine`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_history`
--
ALTER TABLE `payment_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_types`
--
ALTER TABLE `project_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_types_code_unique` (`code`);

--
-- Indexes for table `radiology_reports`
--
ALTER TABLE `radiology_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `radiology_tests`
--
ALTER TABLE `radiology_tests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `radiology_tests_test_code_unique` (`test_code`);

--
-- Indexes for table `referal_doctors`
--
ALTER TABLE `referal_doctors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name_unique` (`name`);

--
-- Indexes for table `send_emails`
--
ALTER TABLE `send_emails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `send_sms`
--
ALTER TABLE `send_sms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `sms_templates`
--
ALTER TABLE `sms_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `soap`
--
ALTER TABLE `soap`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `symptoms`
--
ALTER TABLE `symptoms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `taxes`
--
ALTER TABLE `taxes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `therapy`
--
ALTER TABLE `therapy`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `treatments`
--
ALTER TABLE `treatments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `treatment_booking`
--
ALTER TABLE `treatment_booking`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `treatment_payment_history`
--
ALTER TABLE `treatment_payment_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ai_credentials`
--
ALTER TABLE `ai_credentials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `appointment_history`
--
ALTER TABLE `appointment_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assessment_template`
--
ALTER TABLE `assessment_template`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assigned_therapies`
--
ALTER TABLE `assigned_therapies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `chatbot_messages`
--
ALTER TABLE `chatbot_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_history`
--
ALTER TABLE `chat_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `code_master`
--
ALTER TABLE `code_master`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `daily_data`
--
ALTER TABLE `daily_data`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `default_email_template`
--
ALTER TABLE `default_email_template`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `diagnosis`
--
ALTER TABLE `diagnosis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `diet_charts`
--
ALTER TABLE `diet_charts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_template`
--
ALTER TABLE `email_template`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `followups`
--
ALTER TABLE `followups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `home_advice`
--
ALTER TABLE `home_advice`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `ipd_admissions`
--
ALTER TABLE `ipd_admissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lab_tests`
--
ALTER TABLE `lab_tests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `machines`
--
ALTER TABLE `machines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medical_reports`
--
ALTER TABLE `medical_reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medicine_units`
--
ALTER TABLE `medicine_units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `opd_visits`
--
ALTER TABLE `opd_visits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ot_procedures`
--
ALTER TABLE `ot_procedures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pathology_reports`
--
ALTER TABLE `pathology_reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pathology_tests`
--
ALTER TABLE `pathology_tests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `patient_assign_assessment`
--
ALTER TABLE `patient_assign_assessment`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_assign_dietchart`
--
ALTER TABLE `patient_assign_dietchart`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_assign_homeadvice`
--
ALTER TABLE `patient_assign_homeadvice`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_diagnosis`
--
ALTER TABLE `patient_diagnosis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_discharge_dets`
--
ALTER TABLE `patient_discharge_dets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_medicine`
--
ALTER TABLE `patient_medicine`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_history`
--
ALTER TABLE `payment_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=208;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_types`
--
ALTER TABLE `project_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `radiology_reports`
--
ALTER TABLE `radiology_reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `radiology_tests`
--
ALTER TABLE `radiology_tests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `referal_doctors`
--
ALTER TABLE `referal_doctors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `send_emails`
--
ALTER TABLE `send_emails`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `send_sms`
--
ALTER TABLE `send_sms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `sms_templates`
--
ALTER TABLE `sms_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `soap`
--
ALTER TABLE `soap`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `symptoms`
--
ALTER TABLE `symptoms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `taxes`
--
ALTER TABLE `taxes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `therapy`
--
ALTER TABLE `therapy`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `treatments`
--
ALTER TABLE `treatments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `treatment_booking`
--
ALTER TABLE `treatment_booking`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `treatment_payment_history`
--
ALTER TABLE `treatment_payment_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
