-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 27, 2026 at 04:09 AM
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
-- Database: `maternal_dbase`
--

-- --------------------------------------------------------

--
-- Table structure for table `alerts`
--

CREATE TABLE `alerts` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `prediction_id` int(11) DEFAULT NULL,
  `alert_type` varchar(50) NOT NULL DEFAULT 'HIGH_RISK',
  `message` text NOT NULL,
  `is_resolved` tinyint(1) NOT NULL DEFAULT 0,
  `resolved_by` int(11) DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `alerts`
--

INSERT INTO `alerts` (`id`, `patient_id`, `prediction_id`, `alert_type`, `message`, `is_resolved`, `resolved_by`, `resolved_at`, `created_at`) VALUES
(1, 7, 35, 'HIGH_RISK', 'High risk detected for patient 7 — immediate evaluation required.', 0, NULL, NULL, '2026-04-22 06:57:39'),
(2, 2, 2271, 'HIGH_RISK', 'High risk detected for patient 2 — immediate evaluation required.', 0, NULL, NULL, '2026-04-23 06:42:59'),
(3, 7, 2272, 'HIGH_RISK', 'High risk detected for patient 7 — immediate evaluation required.', 0, NULL, NULL, '2026-04-23 06:43:52'),
(4, 7, 2273, 'HIGH_RISK', 'High risk detected for patient 7 — immediate evaluation required.', 0, NULL, NULL, '2026-04-23 06:44:02'),
(5, 2, 2275, 'HIGH_RISK', 'High risk detected for patient 2 — immediate evaluation required.', 0, NULL, NULL, '2026-04-23 07:27:39'),
(6, 2, 2276, 'HIGH_RISK', 'High risk detected for patient 2 — immediate evaluation required.', 1, NULL, NULL, '2026-04-23 13:45:18'),
(7, 2, 2277, 'HIGH_RISK', 'High risk detected for patient 2 — immediate evaluation required.', 1, NULL, NULL, '2026-04-23 13:51:32');

-- --------------------------------------------------------

--
-- Table structure for table `communities`
--

CREATE TABLE `communities` (
  `id` int(11) NOT NULL,
  `region` varchar(100) NOT NULL,
  `municipality` varchar(100) NOT NULL,
  `barangay` varchar(150) NOT NULL,
  `community` varchar(200) NOT NULL COMMENT 'Full label: Barangay X, Municipality',
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `socioeconomic_index` tinyint(4) DEFAULT 0 COMMENT '0=moderate, 1=low, 2=very low',
  `low_resource_area` tinyint(1) NOT NULL DEFAULT 0,
  `population_approx` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `communities`
--

INSERT INTO `communities` (`id`, `region`, `municipality`, `barangay`, `community`, `latitude`, `longitude`, `socioeconomic_index`, `low_resource_area`, `population_approx`) VALUES
(1, 'NCR', 'Quezon City', 'Barangay Batasan Hills', 'Barangay Batasan Hills, Quezon City', 14.6869000, 121.0980000, 1, 1, 85000),
(2, 'NCR', 'Quezon City', 'Barangay Commonwealth', 'Barangay Commonwealth, Quezon City', 14.7049000, 121.1050000, 1, 1, 92000),
(3, 'NCR', 'Quezon City', 'Barangay Payatas', 'Barangay Payatas, Quezon City', 14.7302000, 121.1102000, 2, 1, 110000),
(4, 'NCR', 'Quezon City', 'Barangay Bagong Silangan', 'Barangay Bagong Silangan, Quezon City', 14.6946000, 121.1241000, 1, 1, 78000),
(5, 'NCR', 'Quezon City', 'Barangay Holy Spirit', 'Barangay Holy Spirit, Quezon City', 14.7115000, 121.0936000, 1, 1, 65000),
(6, 'NCR', 'Quezon City', 'Barangay Matandang Balara', 'Barangay Matandang Balara, Quezon City', 14.6827000, 121.0721000, 0, 0, 42000),
(7, 'NCR', 'Caloocan City', 'Barangay 176', 'Barangay 176, Caloocan City', 14.7573000, 120.9834000, 1, 1, 55000),
(8, 'NCR', 'Caloocan City', 'Barangay Camarin', 'Barangay Camarin, Caloocan City', 14.7821000, 120.9956000, 1, 1, 68000),
(9, 'NCR', 'Caloocan City', 'Barangay Bagumbong', 'Barangay Bagumbong, Caloocan City', 14.7692000, 120.9891000, 2, 1, 73000),
(10, 'NCR', 'Caloocan City', 'Barangay EDSA', 'Barangay EDSA, Caloocan City', 14.6493000, 120.9863000, 0, 0, 31000),
(11, 'NCR', 'Marikina City', 'Barangay Nangka', 'Barangay Nangka, Marikina City', 14.6412000, 121.1112000, 0, 0, 38000),
(12, 'NCR', 'Marikina City', 'Barangay Concepcion Uno', 'Barangay Concepcion Uno, Marikina City', 14.6595000, 121.1051000, 0, 0, 29000),
(13, 'NCR', 'Marikina City', 'Barangay Industrial Valley', 'Barangay Industrial Valley, Marikina City', 14.6711000, 121.0968000, 1, 1, 44000),
(14, 'NCR', 'Pasig City', 'Barangay Pinagbuhatan', 'Barangay Pinagbuhatan, Pasig City', 14.5658000, 121.0843000, 1, 1, 52000),
(15, 'NCR', 'Pasig City', 'Barangay Manggahan', 'Barangay Manggahan, Pasig City', 14.5812000, 121.0912000, 0, 0, 41000),
(16, 'NCR', 'Pasig City', 'Barangay Kapasigan', 'Barangay Kapasigan, Pasig City', 14.5721000, 121.0759000, 0, 0, 35000),
(17, 'NCR', 'Mandaluyong', 'Barangay Hagdan Bato', 'Barangay Hagdan Bato, Mandaluyong', 14.5817000, 121.0394000, 0, 0, 28000),
(18, 'NCR', 'Mandaluyong', 'Barangay Addition Hills', 'Barangay Addition Hills, Mandaluyong', 14.5897000, 121.0481000, 0, 0, 22000),
(19, 'NCR', 'Taguig City', 'Barangay Hagonoy', 'Barangay Hagonoy, Taguig City', 14.5024000, 121.0721000, 2, 1, 61000),
(20, 'NCR', 'Taguig City', 'Barangay Ususan', 'Barangay Ususan, Taguig City', 14.5102000, 121.0619000, 2, 1, 57000),
(21, 'NCR', 'Taguig City', 'Barangay Western Bicutan', 'Barangay Western Bicutan, Taguig City', 14.5212000, 121.0528000, 1, 1, 48000),
(22, 'NCR', 'Las Piñas City', 'Barangay Almanza Uno', 'Barangay Almanza Uno, Las Piñas City', 14.4489000, 120.9912000, 0, 0, 34000),
(23, 'NCR', 'Las Piñas City', 'Barangay Pamplona Tres', 'Barangay Pamplona Tres, Las Piñas City', 14.4612000, 121.0021000, 1, 1, 41000),
(24, 'NCR', 'Muntinlupa City', 'Barangay Poblacion', 'Barangay Poblacion, Muntinlupa City', 14.4081000, 121.0453000, 0, 0, 27000),
(25, 'NCR', 'Muntinlupa City', 'Barangay Tunasan', 'Barangay Tunasan, Muntinlupa City', 14.3921000, 121.0502000, 1, 1, 39000),
(26, 'NCR', 'Parañaque City', 'Barangay San Isidro', 'Barangay San Isidro, Parañaque City', 14.4712000, 121.0187000, 1, 1, 45000),
(27, 'NCR', 'Parañaque City', 'Barangay Tambo', 'Barangay Tambo, Parañaque City', 14.4819000, 121.0021000, 1, 1, 38000),
(28, 'NCR', 'Valenzuela City', 'Barangay Malinta', 'Barangay Malinta, Valenzuela City', 14.7102000, 120.9834000, 1, 1, 50000),
(29, 'NCR', 'Valenzuela City', 'Barangay Karuhatan', 'Barangay Karuhatan, Valenzuela City', 14.7201000, 120.9756000, 1, 1, 43000),
(30, 'NCR', 'Malabon City', 'Barangay Tonsuya', 'Barangay Tonsuya, Malabon City', 14.6681000, 120.9572000, 2, 1, 67000),
(31, 'NCR', 'Malabon City', 'Barangay Catmon', 'Barangay Catmon, Malabon City', 14.6712000, 120.9631000, 2, 1, 59000),
(32, 'Region III', 'Bulacan', 'Barangay Sta. Maria', 'Barangay Sta. Maria, Bulacan', 14.8121000, 121.0021000, 1, 1, 32000),
(33, 'Region III', 'Pampanga', 'Barangay San Fernando', 'Barangay San Fernando, Pampanga', 15.0289000, 120.6894000, 1, 1, 28000),
(34, 'Region IV-A', 'Cavite', 'Barangay Rosario', 'Barangay Rosario, Cavite', 14.4158000, 120.8521000, 1, 1, 36000),
(35, 'Region IV-A', 'Laguna', 'Barangay Sta. Cruz Proper', 'Barangay Sta. Cruz Proper, Laguna', 14.2812000, 121.4189000, 0, 0, 24000),
(36, 'Region IV-A', 'Rizal', 'Barangay Cogeo', 'Barangay Cogeo, Rizal', 14.6512000, 121.1789000, 2, 1, 55000);

-- --------------------------------------------------------

--
-- Table structure for table `health_facilities`
--

CREATE TABLE `health_facilities` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `facility_type` enum('barangay_health_center','rural_health_unit','district_hospital','provincial_hospital','lying_in_clinic','private_clinic','other') NOT NULL DEFAULT 'barangay_health_center',
  `municipality` varchar(100) DEFAULT NULL,
  `barangay` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `community` varchar(150) DEFAULT NULL COMMENT 'Composite: barangay + municipality for display/search',
  `address` text DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `operating_hours` varchar(200) DEFAULT NULL,
  `has_ob_service` tinyint(1) NOT NULL DEFAULT 0,
  `has_prenatal` tinyint(1) NOT NULL DEFAULT 0,
  `has_delivery` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `health_facilities`
--

INSERT INTO `health_facilities` (`id`, `name`, `facility_type`, `municipality`, `barangay`, `region`, `community`, `address`, `latitude`, `longitude`, `contact_number`, `operating_hours`, `has_ob_service`, `has_prenatal`, `has_delivery`, `is_active`, `created_at`) VALUES
(1, 'Batasan Hills Health Center', 'barangay_health_center', 'Quezon City', 'Barangay Batasan Hills', 'NCR', 'Barangay Batasan Hills, Quezon City', NULL, 14.6875000, 121.0977000, '(02) 8931-1234', 'Mon–Fri 8AM–5PM', 0, 1, 0, 1, '2026-04-23 01:03:08'),
(2, 'Commonwealth Health Center', 'barangay_health_center', 'Quezon City', 'Barangay Commonwealth', 'NCR', 'Barangay Commonwealth, Quezon City', NULL, 14.7044000, 121.1048000, '(02) 8931-2345', 'Mon–Sat 8AM–5PM', 0, 1, 0, 1, '2026-04-23 01:03:08'),
(3, 'Payatas Health Center', 'barangay_health_center', 'Quezon City', 'Barangay Payatas', 'NCR', 'Barangay Payatas, Quezon City', NULL, 14.7298000, 121.1098000, '(02) 8931-3456', 'Mon–Fri 8AM–5PM', 0, 1, 0, 1, '2026-04-23 01:03:08'),
(4, 'Quirino Memorial Medical Center', 'district_hospital', 'Quezon City', 'Barangay Batasan Hills', 'NCR', 'Barangay Batasan Hills, Quezon City', NULL, 14.6501000, 121.0438000, '(02) 8426-2701', '24 Hours', 1, 1, 1, 1, '2026-04-23 01:03:08'),
(5, 'Novaliches District Hospital', 'district_hospital', 'Quezon City', 'Barangay Commonwealth', 'NCR', 'Barangay Commonwealth, Quezon City', NULL, 14.7315000, 121.0412000, '(02) 8930-1234', '24 Hours', 1, 1, 1, 1, '2026-04-23 01:03:08'),
(6, 'Camarin Health Center', 'barangay_health_center', 'Caloocan City', 'Barangay Camarin', 'NCR', 'Barangay Camarin, Caloocan City', NULL, 14.7818000, 120.9952000, '(02) 8288-1234', 'Mon–Fri 8AM–5PM', 0, 1, 0, 1, '2026-04-23 01:03:08'),
(7, 'Caloocan City Medical Center', 'district_hospital', 'Caloocan City', 'Barangay EDSA', 'NCR', 'Barangay EDSA, Caloocan City', NULL, 14.6598000, 120.9778000, '(02) 8288-5678', '24 Hours', 1, 1, 1, 1, '2026-04-23 01:03:08'),
(8, 'Marikina Health Center – Nangka', 'barangay_health_center', 'Marikina City', 'Barangay Nangka', 'NCR', 'Barangay Nangka, Marikina City', NULL, 14.6409000, 121.1110000, '(02) 8647-1234', 'Mon–Sat 8AM–5PM', 0, 1, 0, 1, '2026-04-23 01:03:08'),
(9, 'Marikina Valley Medical Center', 'district_hospital', 'Marikina City', 'Barangay Concepcion Uno', 'NCR', 'Barangay Concepcion Uno, Marikina City', NULL, 14.6592000, 121.1002000, '(02) 8647-7890', '24 Hours', 1, 1, 1, 1, '2026-04-23 01:03:08'),
(10, 'Pinagbuhatan Health Center', 'barangay_health_center', 'Pasig City', 'Barangay Pinagbuhatan', 'NCR', 'Barangay Pinagbuhatan, Pasig City', NULL, 14.5655000, 121.0840000, '(02) 8641-1234', 'Mon–Fri 8AM–5PM', 0, 1, 0, 1, '2026-04-23 01:03:08'),
(11, 'Pasig City General Hospital', 'district_hospital', 'Pasig City', 'Barangay Kapasigan', 'NCR', 'Barangay Kapasigan, Pasig City', NULL, 14.5718000, 121.0756000, '(02) 8641-5678', '24 Hours', 1, 1, 1, 1, '2026-04-23 01:03:08'),
(12, 'Hagonoy Rural Health Unit', 'rural_health_unit', 'Taguig City', 'Barangay Hagonoy', 'NCR', 'Barangay Hagonoy, Taguig City', NULL, 14.5021000, 121.0718000, '(02) 8839-1234', 'Mon–Fri 8AM–5PM', 0, 1, 0, 1, '2026-04-23 01:03:08'),
(13, 'Taguig-Pateros District Hospital', 'district_hospital', 'Taguig City', 'Barangay Western Bicutan', 'NCR', 'Barangay Western Bicutan, Taguig City', NULL, 14.5208000, 121.0524000, '(02) 8839-5678', '24 Hours', 1, 1, 1, 1, '2026-04-23 01:03:08'),
(14, 'Almanza Uno Health Center', 'barangay_health_center', 'Las Piñas City', 'Barangay Almanza Uno', 'NCR', 'Barangay Almanza Uno, Las Piñas City', NULL, 14.4486000, 120.9910000, '(02) 8807-1234', 'Mon–Sat 8AM–5PM', 0, 1, 0, 1, '2026-04-23 01:03:08'),
(15, 'Las Piñas City Medical Center', 'district_hospital', 'Las Piñas City', 'Barangay Pamplona Tres', 'NCR', 'Barangay Pamplona Tres, Las Piñas City', NULL, 14.4608000, 121.0018000, '(02) 8807-5678', '24 Hours', 1, 1, 1, 1, '2026-04-23 01:03:08'),
(16, 'Alabang Health Center', 'barangay_health_center', 'Muntinlupa City', 'Barangay Poblacion', 'NCR', 'Barangay Poblacion, Muntinlupa City', NULL, 14.4078000, 121.0450000, '(02) 8850-1234', 'Mon–Fri 8AM–5PM', 0, 1, 0, 1, '2026-04-23 01:03:08'),
(17, 'Muntinlupa City General Hospital', 'district_hospital', 'Muntinlupa City', 'Barangay Tunasan', 'NCR', 'Barangay Tunasan, Muntinlupa City', NULL, 14.3918000, 121.0498000, '(02) 8850-5678', '24 Hours', 1, 1, 1, 1, '2026-04-23 01:03:08'),
(18, 'San Isidro Health Center', 'barangay_health_center', 'Parañaque City', 'Barangay San Isidro', 'NCR', 'Barangay San Isidro, Parañaque City', NULL, 14.4709000, 121.0184000, '(02) 8822-1234', 'Mon–Fri 8AM–5PM', 0, 1, 0, 1, '2026-04-23 01:03:08'),
(19, 'Parañaque City General Hospital', 'district_hospital', 'Parañaque City', 'Barangay Tambo', 'NCR', 'Barangay Tambo, Parañaque City', NULL, 14.4815000, 121.0018000, '(02) 8822-5678', '24 Hours', 1, 1, 1, 1, '2026-04-23 01:03:08'),
(20, 'Malinta Health Center', 'barangay_health_center', 'Valenzuela City', 'Barangay Malinta', 'NCR', 'Barangay Malinta, Valenzuela City', NULL, 14.7098000, 120.9831000, '(02) 8293-1234', 'Mon–Sat 8AM–5PM', 0, 1, 0, 1, '2026-04-23 01:03:08'),
(21, 'Valenzuela City Medical Center', 'district_hospital', 'Valenzuela City', 'Barangay Karuhatan', 'NCR', 'Barangay Karuhatan, Valenzuela City', NULL, 14.7198000, 120.9752000, '(02) 8293-5678', '24 Hours', 1, 1, 1, 1, '2026-04-23 01:03:08'),
(22, 'Tonsuya Health Center', 'barangay_health_center', 'Malabon City', 'Barangay Tonsuya', 'NCR', 'Barangay Tonsuya, Malabon City', NULL, 14.6678000, 120.9569000, '(02) 8281-1234', 'Mon–Fri 8AM–5PM', 0, 1, 0, 1, '2026-04-23 01:03:08'),
(23, 'Malabon City Hospital', 'district_hospital', 'Malabon City', 'Barangay Catmon', 'NCR', 'Barangay Catmon, Malabon City', NULL, 14.6708000, 120.9628000, '(02) 8281-5678', '24 Hours', 1, 1, 1, 1, '2026-04-23 01:03:08');

-- --------------------------------------------------------

--
-- Table structure for table `health_records`
--

CREATE TABLE `health_records` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `age` float DEFAULT NULL,
  `systolic_bp` float DEFAULT NULL,
  `diastolic_bp` float DEFAULT NULL,
  `blood_sugar` float DEFAULT NULL,
  `body_temp` float DEFAULT NULL,
  `heart_rate` float DEFAULT NULL,
  `recorded_by` int(11) DEFAULT NULL,
  `recorded_at` datetime NOT NULL DEFAULT current_timestamp(),
  `community` varchar(200) DEFAULT NULL COMMENT 'Composite: Barangay, Municipality',
  `municipality` varchar(100) DEFAULT NULL,
  `barangay` varchar(150) DEFAULT NULL,
  `distance_to_facility_km` float DEFAULT NULL,
  `socioeconomic_index` int(11) DEFAULT NULL,
  `low_resource_area` tinyint(1) DEFAULT NULL,
  `prenatal_visits` int(11) DEFAULT NULL,
  `gravida` int(11) DEFAULT NULL,
  `para` int(11) DEFAULT NULL,
  `referral_delay_hours` int(11) DEFAULT NULL,
  `has_prior_complication` tinyint(1) DEFAULT NULL,
  `prior_complications` varchar(255) DEFAULT NULL,
  `has_comorbidity` tinyint(1) DEFAULT NULL,
  `comorbidities` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `health_records`
--

INSERT INTO `health_records` (`id`, `patient_id`, `age`, `systolic_bp`, `diastolic_bp`, `blood_sugar`, `body_temp`, `heart_rate`, `recorded_by`, `recorded_at`, `community`, `municipality`, `barangay`, `distance_to_facility_km`, `socioeconomic_index`, `low_resource_area`, `prenatal_visits`, `gravida`, `para`, `referral_delay_hours`, `has_prior_complication`, `prior_complications`, `has_comorbidity`, `comorbidities`) VALUES
(1, 1, 26, 92, 65, 6.5, 98, 63, NULL, '2026-04-22 14:25:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 7, 23, 140, 80, 7.1, 98, 70, NULL, '2026-04-22 14:57:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 1, 32, 118, 60, 5.9, 97.7, 75, NULL, '2026-04-23 08:21:21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 1, 32, 118, 60, 5.9, 97.7, 75, NULL, '2026-04-23 13:47:39', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 1, 32, 118, 60, 5.9, 97.7, 75, NULL, '2026-04-23 14:04:20', NULL, NULL, NULL, NULL, NULL, NULL, 5, 2, 1, 0, 1, 'preterm_birth', 1, 'hypertension'),
(6, 31, 28, 120, 80, 6.5, 98, 64, NULL, '2026-04-23 14:36:48', 'Barangay Batasan Hills, Quezon City', 'Quezon City', 'Barangay Batasan Hills', 0.07, 1, 1, 4, 2, 2, 0, 0, 'none', 1, 'hypertension,anemia'),
(7, 2, 29, 138, 92, 6.8, 97.9, 70, NULL, '2026-04-23 14:43:08', 'Barangay Bagong Silangan, Quezon City', 'Quezon City', 'Barangay Bagong Silangan', 2.34, 1, 1, 4, 4, 4, 30, 1, 'eclampsia,hemorrhage', 0, 'none');

-- --------------------------------------------------------

--
-- Table structure for table `model_versions`
--

CREATE TABLE `model_versions` (
  `id` int(11) NOT NULL,
  `version_name` varchar(100) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `accuracy` float DEFAULT NULL,
  `precision_score` float DEFAULT NULL,
  `recall_score` float DEFAULT NULL,
  `f1_score` float DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `auc_roc` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_versions`
--

INSERT INTO `model_versions` (`id`, `version_name`, `file_path`, `accuracy`, `precision_score`, `recall_score`, `f1_score`, `is_active`, `created_at`, `created_by`, `auc_roc`) VALUES
(3, 'model_v1.pkl', 'models/model_v1.pkl', 0.8522, 0.8512, 0.8522, 0.8512, 0, '2026-04-22 06:27:06', NULL, NULL),
(4, 'model_v2.pkl', 'models/model_v2.pkl', 0.8, 0.8004, 0.8, 0.799, 0, '2026-04-22 09:05:45', NULL, NULL),
(5, 'model_v3.pkl', 'models/model_v3.pkl', 0.8, 0.8004, 0.8, 0.799, 1, '2026-04-23 06:37:40', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `patient_code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `contact_number` varchar(30) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `community` varchar(200) DEFAULT NULL COMMENT 'Composite: Barangay, Municipality',
  `municipality` varchar(100) DEFAULT NULL,
  `barangay` varchar(150) DEFAULT NULL,
  `distance_to_facility_km` float DEFAULT NULL,
  `socioeconomic_index` tinyint(4) DEFAULT NULL,
  `latest_risk_level` enum('low risk','mid risk','high risk') DEFAULT NULL,
  `latest_prediction_at` datetime DEFAULT NULL,
  `latest_probability_score` float DEFAULT NULL,
  `low_resource_area` tinyint(1) DEFAULT NULL,
  `last_prediction_at` datetime DEFAULT NULL,
  `prenatal_visits` int(11) DEFAULT NULL,
  `gravida` int(11) DEFAULT NULL,
  `para` int(11) DEFAULT NULL,
  `referral_delay_hours` int(11) DEFAULT NULL,
  `has_prior_complication` tinyint(1) DEFAULT NULL,
  `prior_complications` varchar(255) DEFAULT NULL,
  `has_comorbidity` tinyint(1) DEFAULT NULL,
  `comorbidities` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `patient_code`, `name`, `date_of_birth`, `age`, `contact_number`, `address`, `created_at`, `created_by`, `community`, `municipality`, `barangay`, `distance_to_facility_km`, `socioeconomic_index`, `latest_risk_level`, `latest_prediction_at`, `latest_probability_score`, `low_resource_area`, `last_prediction_at`, `prenatal_visits`, `gravida`, `para`, `referral_delay_hours`, `has_prior_complication`, `prior_complications`, `has_comorbidity`, `comorbidities`) VALUES
(1, 'PAT-0001', 'Lorna Dela Cruz', '1998-03-14', 26, '09171234568', 'Blk 3 Lot 5 Sampaguita St., Quezon City', '2026-04-22 11:13:50', 1, 'Barangay Ususan, Taguig City', 'Taguig City', 'Barangay Ususan', NULL, NULL, 'mid risk', '2026-04-23 06:04:14', 0.7377, NULL, '2026-04-23 06:04:14', 5, 2, 1, 0, 1, 'preterm_birth', 1, 'hypertension'),
(2, 'PAT-0002', 'Remedios Villanueva', '1995-07-22', 29, '09281234567', '123 Malaya Ave., Caloocan City', '2026-04-22 11:13:50', 1, 'Barangay Commonwealth, Quezon City', 'Quezon City', 'Barangay Commonwealth', 0.06, 1, 'high risk', NULL, 0.9175, 1, '2026-04-23 13:51:32', 4, 4, 4, 30, 1, 'eclampsia,hemorrhage', 0, 'none'),
(3, 'PAT-0003', 'Grace Fernandez', '2000-11-05', 24, '09391234567', '45 Rosal St., Marikina City', '2026-04-22 11:13:50', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'PAT-0004', 'Jocelyn Ramos', '1990-01-30', 34, '09451234567', '78 Camia Rd., Pasig City', '2026-04-22 11:13:50', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'PAT-0005', 'Maricel Bautista', '1993-06-18', 31, '09561234567', '22 Ilang-Ilang St., Mandaluyong City', '2026-04-22 11:13:50', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'PAT-0006', 'Teresita Gonzales', '1988-09-09', 36, '09671234567', '9 Sampaguita Ave., Taguig City', '2026-04-22 11:13:50', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'PAT-0007', 'Cristina Mendoza', '2001-04-25', 23, '09781234567', '56 Dahlia St., Las Piñas City', '2026-04-22 11:13:50', 1, 'Barangay Payatas, Quezon City', 'Quezon City', 'Barangay Payatas', 0.06, 2, 'high risk', '2026-04-22 06:57:39', 0.9023, 1, '2026-04-23 06:44:02', 5, NULL, 1, 28, 1, 'eclampsia,hemorrhage', 0, 'none'),
(8, 'PAT-0008', 'Elisa Aguilar', '1985-12-12', 39, '09891234567', '101 Orchid Lane, Muntinlupa City', '2026-04-22 11:13:50', 1, NULL, NULL, NULL, NULL, NULL, 'low risk', '2026-04-22 06:24:06', 0.6855, NULL, '2026-04-22 06:24:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'PAT-0009', 'Rowena Castro', '1997-08-03', 27, '09901234567', '33 Rose St., Parañaque City', '2026-04-22 11:13:50', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 'PAT-0010', 'Natividad Soriano', '1992-02-20', 32, '09121234567', '14 Jasmine Blvd., Valenzuela City', '2026-04-22 11:13:50', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 'PAT-0011', 'Gertrudes Gonzales', '2000-01-01', 24, NULL, NULL, '2026-04-23 14:25:30', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 'PAT-0012', 'Luz Tamayo', '1999-03-15', 25, NULL, NULL, '2026-04-23 14:25:30', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 'PAT-0013', 'Perla Magno', '1997-07-22', 27, NULL, NULL, '2026-04-23 14:25:30', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 'PAT-0014', 'Erlinda Morales', '1995-11-08', 29, NULL, NULL, '2026-04-23 14:25:30', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 'PAT-0015', 'Ursula Lopez', '1993-05-30', 31, NULL, NULL, '2026-04-23 14:25:30', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 'PAT-0016', 'Lolita Tamayo', '1990-09-12', 35, NULL, NULL, '2026-04-23 14:25:30', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 'PAT-0017', 'Raquel Recto', '1988-02-28', 36, NULL, NULL, '2026-04-23 14:25:30', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 'PAT-0018', 'Natividad Abad', '2001-08-19', 23, NULL, NULL, '2026-04-23 14:25:30', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 'PAT-0019', 'Filipina Mendoza', '1996-04-04', 28, NULL, NULL, '2026-04-23 14:25:30', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 'PAT-0020', 'Luz Villanueva', '1992-12-25', 32, NULL, NULL, '2026-04-23 14:25:30', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(31, 'ANON-1.00', 'Gertrudes Gonzales', NULL, 28, NULL, NULL, '2026-04-23 14:31:05', NULL, 'Barangay Batasan Hills, Quezon City', 'Quezon City', 'Barangay Batasan Hills', 0.07, 1, 'mid risk', NULL, 0.8284, 1, '2026-04-23 06:38:03', 4, 2, 2, 0, 0, 'none', 1, 'hypertension,anemia'),
(32, 'ANON-2.00', 'Luz Tamayo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.6272, NULL, '2026-02-18 07:48:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 'ANON-3.00', 'Perla Magno', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.6945, NULL, '2026-02-18 12:06:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(34, 'ANON-4.00', 'Erlinda Morales', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.7169, NULL, '2026-02-12 14:34:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(35, 'ANON-5.00', 'Ursula Lopez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.8267, NULL, '2026-02-16 12:36:12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(36, 'ANON-6.00', 'Lolita Tamayo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.7229, NULL, '2026-02-12 10:55:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(37, 'ANON-7.00', 'Raquel Recto', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.8836, NULL, '2026-02-14 10:42:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(38, 'ANON-8.00', 'Natividad Abad', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.575, NULL, '2026-02-16 10:10:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(39, 'ANON-9.00', 'Filipina Mendoza', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.8176, NULL, '2026-02-17 12:53:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(40, 'ANON-10.0', 'Luz Villanueva', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.5612, NULL, '2026-02-14 08:13:58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(41, 'ANON-11.0', 'Caridad Pascual', NULL, 28, NULL, NULL, '2026-04-23 14:31:05', NULL, 'Barangay Batasan Hills, Quezon City', 'Quezon City', 'Barangay Batasan Hills', 0.07, 1, 'mid risk', NULL, 0.8069, 1, '2026-04-23 07:24:24', 3, 1, 4, 22, 1, 'eclampsia', 1, 'hypertension,diabetes'),
(42, 'ANON-12.0', 'Pamela Cabanero', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.7909, NULL, '2026-02-23 13:57:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(43, 'ANON-13.0', 'Luz Tinio', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.6746, NULL, '2026-02-23 14:05:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(44, 'ANON-14.0', 'Margarita Quimpo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.6373, NULL, '2026-02-25 17:27:38', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(45, 'ANON-15.0', 'Isabelita Obsena', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.7298, NULL, '2026-02-23 11:35:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(46, 'ANON-16.0', 'Ursula Baguio', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.8677, NULL, '2026-02-23 11:49:41', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(47, 'ANON-17.0', 'Erlinda Fajardo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.6998, NULL, '2026-02-19 11:32:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(48, 'ANON-18.0', 'Aileen Ilagan', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.9103, NULL, '2026-02-21 17:32:38', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(49, 'ANON-19.0', 'Genoveva Santos', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.7271, NULL, '2026-02-25 15:58:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(50, 'ANON-20.0', 'Arlene Soriano', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.721, NULL, '2026-02-21 11:15:03', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(51, 'ANON-21.0', 'Ofelia Camacho', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.8842, NULL, '2026-03-04 15:49:08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(52, 'ANON-22.0', 'Resurreccion Ilagan', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.7869, NULL, '2026-03-04 16:27:13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(53, 'ANON-23.0', 'Anita Santiago', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.8708, NULL, '2026-03-01 17:41:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(54, 'ANON-24.0', 'Arlene Madriaga', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.6411, NULL, '2026-02-28 07:37:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(55, 'ANON-25.0', 'Jocelyn Villanueva', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.6241, NULL, '2026-02-27 08:57:02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(56, 'ANON-26.0', 'Zenaida Fernandez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.7262, NULL, '2026-02-27 15:08:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(57, 'ANON-27.0', 'Trinidad Valdez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.8013, NULL, '2026-03-04 13:12:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(58, 'ANON-28.0', 'Celestina Espinosa', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.744, NULL, '2026-03-03 07:43:41', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(59, 'ANON-29.0', 'Genoveva Torres', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.7788, NULL, '2026-03-04 08:15:12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(60, 'ANON-30.0', 'Tessie Galang', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.7434, NULL, '2026-03-06 08:28:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(61, 'ANON-31.0', 'Tina Castillo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.8261, NULL, '2026-03-09 07:05:59', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(62, 'ANON-32.0', 'Vivian Villafuerte', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.6327, NULL, '2026-03-08 10:55:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(63, 'ANON-33.0', 'Rowena Saguisag', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.7027, NULL, '2026-03-08 11:59:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(64, 'ANON-34.0', 'Dolores Feria', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.6498, NULL, '2026-03-10 15:42:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(65, 'ANON-35.0', 'Emmeline Lagman', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.6165, NULL, '2026-03-05 16:47:34', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(66, 'ANON-36.0', 'Yolanda Dela Cruz', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.7066, NULL, '2026-03-08 15:58:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(67, 'ANON-37.0', 'Paz Ramirez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.5699, NULL, '2026-03-11 09:04:38', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(68, 'ANON-38.0', 'Lorna Jacinto', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.893, NULL, '2026-03-09 10:37:38', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(69, 'ANON-39.0', 'Leticia Zulueta', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.7922, NULL, '2026-03-14 10:42:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(70, 'ANON-40.0', 'Dolores Bautista', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.6445, NULL, '2026-03-15 12:59:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(71, 'ANON-41.0', 'Melinda Pascual', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.8488, NULL, '2026-03-12 08:34:13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(72, 'ANON-42.0', 'Narcisa Fernandez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.6722, NULL, '2026-03-13 12:18:10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(73, 'ANON-43.0', 'Zenaida Ilagan', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.7029, NULL, '2026-03-18 17:33:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(74, 'ANON-44.0', 'Jenny Gatmaitan', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.8761, NULL, '2026-03-13 11:07:56', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(75, 'ANON-45.0', 'Josie Lagman', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.6926, NULL, '2026-03-17 12:13:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(76, 'ANON-46.0', 'Fe Palma', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.721, NULL, '2026-03-18 07:05:40', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(77, 'ANON-47.0', 'Trinidad Mercado', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.6012, NULL, '2026-03-24 11:10:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(78, 'ANON-48.0', 'Kristina Rivera', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.8399, NULL, '2026-03-19 08:56:44', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(79, 'ANON-49.0', 'Ofelia Manio', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.8246, NULL, '2026-03-23 15:09:27', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(80, 'ANON-50.0', 'Herminia Aquino', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.7048, NULL, '2026-03-25 07:57:22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(81, 'ANON-51.0', 'Celestina Gatmaitan', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.6848, NULL, '2026-03-25 15:56:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(82, 'ANON-52.0', 'Estrella Quezon', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.811, NULL, '2026-03-20 09:51:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(83, 'ANON-53.0', 'Tessie Magno', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.7402, NULL, '2026-03-21 13:51:42', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(84, 'ANON-54.0', 'Fe Hilario', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.9094, NULL, '2026-03-25 08:24:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(85, 'ANON-55.0', 'Zenaida Rivera', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.76, NULL, '2026-03-22 12:19:52', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(86, 'ANON-56.0', 'Vivian Gatmaitan', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.6297, NULL, '2026-03-20 13:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(87, 'ANON-57.0', 'Nena Tinio', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.8553, NULL, '2026-04-01 15:21:01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(88, 'ANON-58.0', 'Erlinda Manio', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.6607, NULL, '2026-03-28 07:06:38', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(89, 'ANON-59.0', 'Nilda Zulueta', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.7067, NULL, '2026-03-30 08:24:57', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(90, 'ANON-60.0', 'Carmela Ramos', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.7981, NULL, '2026-04-01 15:43:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(91, 'ANON-61.0', 'Leticia Mendoza', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.7131, NULL, '2026-03-31 12:39:20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(92, 'ANON-62.0', 'Genoveva Manalo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.865, NULL, '2026-03-31 13:20:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(93, 'ANON-63.0', 'Charito Rivera', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.6171, NULL, '2026-03-29 17:47:57', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(94, 'ANON-64.0', 'Cristina Santiago', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.7381, NULL, '2026-03-28 11:13:27', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(95, 'ANON-65.0', 'Teodora Umali', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.6628, NULL, '2026-03-31 10:32:30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(96, 'ANON-66.0', 'Patricia Quimpo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.8076, NULL, '2026-03-28 15:42:40', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(97, 'ANON-67.0', 'Mercedita Santiago', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.9546, NULL, '2026-03-28 10:51:12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(98, 'ANON-68.0', 'Isabelita Lacson', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.9649, NULL, '2026-04-01 08:29:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(99, 'ANON-69.0', 'Bebelyn Hontiveros', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.8799, NULL, '2026-03-29 10:09:41', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(100, 'ANON-70.0', 'Jenny Aguilar', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.9253, NULL, '2026-04-08 13:14:11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(101, 'ANON-71.0', 'Conception Morales', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.8727, NULL, '2026-04-06 10:58:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(102, 'ANON-72.0', 'Milagros Ureta', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.6453, NULL, '2026-04-06 15:38:20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(103, 'ANON-73.0', 'Wanda Quezon', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.9353, NULL, '2026-04-07 15:27:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(104, 'ANON-74.0', 'Conception Ureta', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.7927, NULL, '2026-04-08 14:28:16', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(105, 'ANON-75.0', 'Zelda Camacho', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.844, NULL, '2026-04-08 15:31:40', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(106, 'ANON-76.0', 'Raquel Wenceslao', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.7496, NULL, '2026-04-03 11:21:20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(107, 'ANON-77.0', 'Charito Lagman', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.6758, NULL, '2026-04-05 09:45:13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(108, 'ANON-78.0', 'Queenie Lacson', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.7386, NULL, '2026-04-05 07:13:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(109, 'ANON-79.0', 'Natividad Verzosa', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.9076, NULL, '2026-04-07 07:54:56', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(110, 'ANON-80.0', 'Grace Gonzales', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.6831, NULL, '2026-04-04 11:48:24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(111, 'ANON-81.0', 'Anita Feria', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.9551, NULL, '2026-04-07 15:51:38', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(112, 'ANON-82.0', 'Fe Manio', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.72, NULL, '2026-04-14 17:51:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(113, 'ANON-83.0', 'Filipina Bustamante', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.8718, NULL, '2026-04-13 07:58:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(114, 'ANON-84.0', 'Cristina Chua', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.5794, NULL, '2026-04-15 14:11:03', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(115, 'ANON-85.0', 'Zenaida Bustamante', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.7546, NULL, '2026-04-12 11:48:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(116, 'ANON-86.0', 'Katrina Perez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.7599, NULL, '2026-04-09 12:14:41', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(117, 'ANON-87.0', 'Adoracion Saguisag', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.6137, NULL, '2026-04-10 10:53:01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(118, 'ANON-88.0', 'Narcisa Reyes', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.7157, NULL, '2026-04-10 14:44:16', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(119, 'ANON-89.0', 'Minda Ricofort', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.7625, NULL, '2026-04-09 09:19:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(120, 'ANON-90.0', 'Remedios Manio', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.7515, NULL, '2026-04-12 13:45:12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(121, 'ANON-91.0', 'Quirina Quezon', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.8133, NULL, '2026-04-15 11:54:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(122, 'ANON-92.0', 'Wilhelmina Rivera', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.8943, NULL, '2026-04-11 15:27:42', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(123, 'ANON-93.0', 'Bernadette Hontiveros', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.716, NULL, '2026-04-15 14:06:27', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(124, 'ANON-94.0', 'Resurreccion Gonzales', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.9152, NULL, '2026-04-12 09:46:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(125, 'ANON-95.0', 'Dolores Dalisay', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.8748, NULL, '2026-04-19 14:27:52', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(126, 'ANON-96.0', 'Fe Galang', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.6439, NULL, '2026-04-22 08:17:56', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(127, 'ANON-97.0', 'Maria Ricofort', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.8127, NULL, '2026-04-21 13:21:01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(128, 'ANON-98.0', 'Patricia Ricofort', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.6638, NULL, '2026-04-18 11:21:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(129, 'ANON-99.0', 'Conception Macapagal', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.8744, NULL, '2026-04-16 15:12:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(130, 'ANON-100.', 'Arlene Lim', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.7382, NULL, '2026-04-17 14:41:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(131, 'ANON-101.', 'Pamela Lopez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.8275, NULL, '2026-04-17 13:44:15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(132, 'ANON-102.', 'Olympia Feria', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'low risk', NULL, 0.7977, NULL, '2026-04-20 12:27:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(133, 'ANON-103.', 'Felicidad Gatmaitan', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.6731, NULL, '2026-04-18 11:14:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(134, 'ANON-104.', 'Margarita Castro', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.6604, NULL, '2026-04-22 09:12:13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(135, 'ANON-105.', 'Wilma Yap', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.6468, NULL, '2026-04-22 15:38:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(136, 'ANON-106.', 'Fe Recto', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.9164, NULL, '2026-04-18 09:19:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(137, 'ANON-107.', 'Carmela Perez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'mid risk', NULL, 0.5943, NULL, '2026-04-16 15:18:44', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(138, 'ANON-108.', 'Ursulina Santos', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Batasan Hills', NULL, NULL, 'high risk', NULL, 0.8541, NULL, '2026-04-16 07:36:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(139, 'ANON-109.', 'Belen Buenaventura', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.6175, NULL, '2026-02-15 08:52:04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(140, 'ANON-110.', 'Nancy Cruz', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.8514, NULL, '2026-02-13 16:19:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(141, 'ANON-111.', 'Remedios Lagman', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.8946, NULL, '2026-02-18 16:14:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(142, 'ANON-112.', 'Patricia Dela Torre', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.705, NULL, '2026-02-15 11:36:39', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(143, 'ANON-113.', 'Florencia Ramos', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.6337, NULL, '2026-02-17 10:16:42', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(144, 'ANON-114.', 'Florencia Umali', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.7877, NULL, '2026-02-15 14:44:38', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(145, 'ANON-115.', 'Yasmin Bautista', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.7422, NULL, '2026-02-18 14:04:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(146, 'ANON-116.', 'Rowena Salcedo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.869, NULL, '2026-02-18 10:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(147, 'ANON-117.', 'Nancy Ramirez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.8679, NULL, '2026-02-12 07:10:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(148, 'ANON-118.', 'Arlene Quezon', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.7935, NULL, '2026-02-12 14:44:19', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(149, 'ANON-119.', 'Bernadette Diaz', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.7252, NULL, '2026-02-12 16:02:56', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(150, 'ANON-120.', 'Leonora Jacinto', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.6851, NULL, '2026-02-17 16:37:01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(151, 'ANON-121.', 'Lily Saguisag', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.6443, NULL, '2026-02-25 09:30:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(152, 'ANON-122.', 'Estrella Espinosa', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.8706, NULL, '2026-02-23 13:40:52', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(153, 'ANON-123.', 'Sheila Cabanero', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.5819, NULL, '2026-02-21 12:42:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(154, 'ANON-124.', 'Filipina Santiago', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.7555, NULL, '2026-02-21 17:25:52', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(155, 'ANON-125.', 'Vivian Recto', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.5628, NULL, '2026-02-21 12:07:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(156, 'ANON-126.', 'Wilhelmina Salcedo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.775, NULL, '2026-02-24 15:29:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(157, 'ANON-127.', 'Nena De Leon', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.7763, NULL, '2026-02-22 17:28:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(158, 'ANON-128.', 'Rebecca Dela Torre', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.6908, NULL, '2026-02-21 14:56:44', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(159, 'ANON-129.', 'Charito Katipunan', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.5601, NULL, '2026-02-25 10:45:10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(160, 'ANON-130.', 'Isabelita Palma', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.6046, NULL, '2026-02-20 08:29:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(161, 'ANON-131.', 'Hazel Ongsiako', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.6039, NULL, '2026-02-21 15:45:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(162, 'ANON-132.', 'Ina Castillo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.764, NULL, '2026-02-22 15:09:24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(163, 'ANON-133.', 'Milagros Hernandez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.8038, NULL, '2026-02-20 08:17:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(164, 'ANON-134.', 'Cristina Madriaga', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.6952, NULL, '2026-02-23 11:52:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(165, 'ANON-135.', 'Rosa Abad', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.7015, NULL, '2026-02-24 14:55:09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(166, 'ANON-136.', 'Vera Pascual', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.7647, NULL, '2026-02-25 15:24:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(167, 'ANON-137.', 'Aileen Hernandez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.7264, NULL, '2026-03-04 13:02:20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(168, 'ANON-138.', 'Maria Aquino', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.834, NULL, '2026-03-03 17:09:31', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(169, 'ANON-139.', 'Paz Feria', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.9589, NULL, '2026-02-26 14:06:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(170, 'ANON-140.', 'Maricel Pascual', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.6961, NULL, '2026-02-27 08:30:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(171, 'ANON-141.', 'Cristina Mendoza', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.8717, NULL, '2026-03-04 12:54:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(172, 'ANON-142.', 'Narcisa Pagcaliwagan', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.7513, NULL, '2026-03-04 14:55:34', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(173, 'ANON-143.', 'Elisa Navarette', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.852, NULL, '2026-02-27 08:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(174, 'ANON-144.', 'Florencia Ilagan', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.5852, NULL, '2026-02-28 07:02:20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(175, 'ANON-145.', 'Kristina Kalaw', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.6812, NULL, '2026-03-02 13:36:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(176, 'ANON-146.', 'Caridad Recto', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.5776, NULL, '2026-03-02 17:15:31', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(177, 'ANON-147.', 'Leonora Ricofort', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.7976, NULL, '2026-02-28 17:00:57', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(178, 'ANON-148.', 'Wenifreda Viray', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.8668, NULL, '2026-03-01 12:37:19', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(179, 'ANON-149.', 'Yolanda Ibarra', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.6375, NULL, '2026-02-27 13:54:30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(180, 'ANON-150.', 'Milagros Ibarra', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.722, NULL, '2026-03-03 11:01:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(181, 'ANON-151.', 'Genoveva Ablaza', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.7481, NULL, '2026-03-03 07:58:38', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(182, 'ANON-152.', 'Lorena Fernandez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.9389, NULL, '2026-03-06 16:51:22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(183, 'ANON-153.', 'Teresita Jacinto', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.6646, NULL, '2026-03-11 17:43:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(184, 'ANON-154.', 'Gloria Guerrero', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.633, NULL, '2026-03-05 11:50:28', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(185, 'ANON-155.', 'Filipina Ablaza', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.7241, NULL, '2026-03-07 12:47:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(186, 'ANON-156.', 'Katrina Salcedo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.6449, NULL, '2026-03-07 15:32:58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(187, 'ANON-157.', 'Grace Larena', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.6559, NULL, '2026-03-08 11:47:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(188, 'ANON-158.', 'Isabelita Hontiveros', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.6392, NULL, '2026-03-06 10:55:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(189, 'ANON-159.', 'Teresita Ramos', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.893, NULL, '2026-03-11 15:23:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(190, 'ANON-160.', 'Charito Salcedo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.5549, NULL, '2026-03-08 12:43:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(191, 'ANON-161.', 'Resurreccion Lopez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.7546, NULL, '2026-03-07 08:43:14', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(192, 'ANON-162.', 'Milagros Dela Cruz', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.7668, NULL, '2026-03-07 16:14:41', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(193, 'ANON-163.', 'Lorena Quezon', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.8801, NULL, '2026-03-07 17:26:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(194, 'ANON-164.', 'Ina Morales', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.9217, NULL, '2026-03-08 08:06:15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(195, 'ANON-165.', 'Filipina Tan', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.6934, NULL, '2026-03-10 15:26:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(196, 'ANON-166.', 'Perla Hilario', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.6337, NULL, '2026-03-15 11:02:44', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(197, 'ANON-167.', 'Milagros Aquino', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.9232, NULL, '2026-03-12 17:23:34', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(198, 'ANON-168.', 'Hazel Saguisag', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.6694, NULL, '2026-03-12 14:05:42', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(199, 'ANON-169.', 'Teresita Flores', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.9302, NULL, '2026-03-14 10:08:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(200, 'ANON-170.', 'Isabelita Recto', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.8949, NULL, '2026-03-13 10:21:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(201, 'ANON-171.', 'Karla Reyes', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.601, NULL, '2026-03-13 09:34:16', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(202, 'ANON-172.', 'Lilia Tolentino', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.9273, NULL, '2026-03-14 10:37:20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(203, 'ANON-173.', 'Estrella Ablaza', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.6431, NULL, '2026-03-12 08:30:28', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(204, 'ANON-174.', 'Nancy Jimenez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.5882, NULL, '2026-03-16 07:46:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(205, 'ANON-175.', 'Caridad Quizon', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.7465, NULL, '2026-03-12 07:30:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(206, 'ANON-176.', 'Salvacion Ramos', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.7667, NULL, '2026-03-12 08:20:38', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(207, 'ANON-177.', 'Estrella Villafuerte', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.8123, NULL, '2026-03-17 12:24:38', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(208, 'ANON-178.', 'Maria Chua', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.7619, NULL, '2026-03-17 08:54:41', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `patients` (`id`, `patient_code`, `name`, `date_of_birth`, `age`, `contact_number`, `address`, `created_at`, `created_by`, `community`, `municipality`, `barangay`, `distance_to_facility_km`, `socioeconomic_index`, `latest_risk_level`, `latest_prediction_at`, `latest_probability_score`, `low_resource_area`, `last_prediction_at`, `prenatal_visits`, `gravida`, `para`, `referral_delay_hours`, `has_prior_complication`, `prior_complications`, `has_comorbidity`, `comorbidities`) VALUES
(209, 'ANON-179.', 'Conception Galang', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.8023, NULL, '2026-03-15 10:26:21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(210, 'ANON-180.', 'Luisa Camacho', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.8481, NULL, '2026-03-21 17:16:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(211, 'ANON-181.', 'Ofelia Salcedo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.8698, NULL, '2026-03-19 08:05:27', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(212, 'ANON-182.', 'Dalisay Tinio', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.8512, NULL, '2026-03-23 07:37:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(213, 'ANON-183.', 'Jovita Quimpo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.7845, NULL, '2026-03-25 17:48:27', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(214, 'ANON-184.', 'Nancy Castro', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.8807, NULL, '2026-03-23 11:22:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(215, 'ANON-185.', 'Lorena Salazar', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.6245, NULL, '2026-03-20 08:22:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(216, 'ANON-186.', 'Caridad Ramos', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.5902, NULL, '2026-03-20 13:54:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(217, 'ANON-187.', 'Zelda Tamayo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.9119, NULL, '2026-03-24 15:01:38', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(218, 'ANON-188.', 'Ursula Ramos', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.9154, NULL, '2026-03-20 11:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(219, 'ANON-189.', 'Violeta Manalo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.7155, NULL, '2026-03-25 09:36:42', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(220, 'ANON-190.', 'Maricel Flores', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.6482, NULL, '2026-03-19 08:47:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(221, 'ANON-191.', 'Zelda Jimenez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.7427, NULL, '2026-03-21 11:46:20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(222, 'ANON-192.', 'Pamela Perez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.7487, NULL, '2026-03-19 09:10:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(223, 'ANON-193.', 'Fe Ramos', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.7859, NULL, '2026-03-24 13:31:38', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(224, 'ANON-194.', 'Queenie Bautista', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.6929, NULL, '2026-03-19 12:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(225, 'ANON-195.', 'Lilia Tan', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.7835, NULL, '2026-03-29 16:03:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(226, 'ANON-196.', 'Cristina Bustamante', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.8609, NULL, '2026-03-28 12:07:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(227, 'ANON-197.', 'Cristina Larena', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.5952, NULL, '2026-03-27 15:35:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(228, 'ANON-198.', 'Narcisa Tolentino', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.689, NULL, '2026-03-29 07:29:58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(229, 'ANON-199.', 'Charito Castillo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.746, NULL, '2026-03-31 13:18:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(230, 'ANON-200.', 'Xiomara Umali', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.6584, NULL, '2026-03-29 12:05:27', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(231, 'ANON-201.', 'Milagros Wenceslao', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.8384, NULL, '2026-03-29 11:47:21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(232, 'ANON-202.', 'Belen Torres', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.626, NULL, '2026-03-30 15:12:57', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(233, 'ANON-203.', 'Emmeline Bautista', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.8854, NULL, '2026-03-27 10:06:09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(234, 'ANON-204.', 'Edna Jacinto', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.652, NULL, '2026-03-26 09:49:40', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(235, 'ANON-205.', 'Zelda Umali', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.816, NULL, '2026-04-06 17:40:39', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(236, 'ANON-206.', 'Rhea Manalo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.9301, NULL, '2026-04-05 08:30:28', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(237, 'ANON-207.', 'Wenifreda Espinosa', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.8287, NULL, '2026-04-04 15:04:19', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(238, 'ANON-208.', 'Josefina Katipunan', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.5632, NULL, '2026-04-04 08:41:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(239, 'ANON-209.', 'Rebecca Manalo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.6789, NULL, '2026-04-05 14:37:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(240, 'ANON-210.', 'Florencia Espinosa', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.8865, NULL, '2026-04-08 16:41:12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(241, 'ANON-211.', 'Bebelyn Diaz', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.7617, NULL, '2026-04-02 14:06:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(242, 'ANON-212.', 'Kristina Hontiveros', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.7599, NULL, '2026-04-06 17:11:02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(243, 'ANON-213.', 'Felicidad Galang', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.7488, NULL, '2026-04-06 16:10:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(244, 'ANON-214.', 'Mercedita Verzosa', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.6962, NULL, '2026-04-04 17:38:03', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(245, 'ANON-215.', 'Wenifreda Aquino', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.7766, NULL, '2026-04-02 15:43:24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(246, 'ANON-216.', 'Ana Galang', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.8461, NULL, '2026-04-06 09:21:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(247, 'ANON-217.', 'Emmeline Umali', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.929, NULL, '2026-04-12 09:38:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(248, 'ANON-218.', 'Teresita Aquino', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.7705, NULL, '2026-04-15 09:42:44', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(249, 'ANON-219.', 'Tina Santiago', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.8693, NULL, '2026-04-14 17:27:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(250, 'ANON-220.', 'Pamela Espinosa', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.6613, NULL, '2026-04-15 14:12:14', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(251, 'ANON-221.', 'Leonora Valdez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.7447, NULL, '2026-04-13 15:53:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(252, 'ANON-222.', 'Patricia Ramirez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.7578, NULL, '2026-04-10 16:24:09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(253, 'ANON-223.', 'Patricia Magno', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.8622, NULL, '2026-04-10 14:02:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(254, 'ANON-224.', 'Wilma Navarro', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.9297, NULL, '2026-04-15 14:14:34', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(255, 'ANON-225.', 'Fe Evangelista', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.8667, NULL, '2026-04-10 12:43:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(256, 'ANON-226.', 'Josie Recto', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.7402, NULL, '2026-04-13 13:10:52', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(257, 'ANON-227.', 'Paz Domingo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.8968, NULL, '2026-04-12 12:35:59', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(258, 'ANON-228.', 'Diana Imperio', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.8894, NULL, '2026-04-15 09:17:28', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(259, 'ANON-229.', 'Sheila Reyes', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'high risk', NULL, 0.79, NULL, '2026-04-10 14:56:22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(260, 'ANON-230.', 'Kristina Saguisag', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.7348, NULL, '2026-04-19 08:23:14', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(261, 'ANON-231.', 'Felicidad Jacinto', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.9165, NULL, '2026-04-21 12:50:09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(262, 'ANON-232.', 'Jocelyn Morales', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.6975, NULL, '2026-04-21 09:48:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(263, 'ANON-233.', 'Conception Gonzales', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.7654, NULL, '2026-04-16 11:13:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(264, 'ANON-234.', 'Quirina Lopez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.9197, NULL, '2026-04-19 08:49:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(265, 'ANON-235.', 'Violeta Saguisag', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.6414, NULL, '2026-04-21 16:29:04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(266, 'ANON-236.', 'Anita Rivera', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.9077, NULL, '2026-04-16 17:32:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(267, 'ANON-237.', 'Luisa Cabanero', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.6488, NULL, '2026-04-20 12:15:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(268, 'ANON-238.', 'Josefina Quimpo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.8259, NULL, '2026-04-18 08:33:34', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(269, 'ANON-239.', 'Resurreccion Mercado', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.8959, NULL, '2026-04-19 14:02:40', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(270, 'ANON-240.', 'Minda Gatmaitan', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.7269, NULL, '2026-04-18 08:22:15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(271, 'ANON-241.', 'Melinda Espinosa', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.7699, NULL, '2026-04-21 12:08:02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(272, 'ANON-242.', 'Pamela Kalaw', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.7151, NULL, '2026-04-22 17:29:44', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(273, 'ANON-243.', 'Adoracion Flores', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'mid risk', NULL, 0.6138, NULL, '2026-04-21 14:02:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(274, 'ANON-244.', 'Natividad Gatmaitan', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Commonwealth', NULL, NULL, 'low risk', NULL, 0.869, NULL, '2026-04-18 11:32:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(275, 'ANON-245.', 'Filipina Ilagan', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.8137, NULL, '2026-02-14 07:55:41', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(276, 'ANON-246.', 'Lorna Santos', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.6788, NULL, '2026-02-17 14:57:24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(277, 'ANON-247.', 'Norma Zulueta', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.7922, NULL, '2026-02-18 15:17:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(278, 'ANON-248.', 'Leonora Gatmaitan', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.7464, NULL, '2026-02-13 15:18:20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(279, 'ANON-249.', 'Conception Soriano', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.7005, NULL, '2026-02-17 13:10:44', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(280, 'ANON-250.', 'Ursula Evangelista', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.8044, NULL, '2026-02-16 13:17:40', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(281, 'ANON-251.', 'Vera Platon', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.674, NULL, '2026-02-14 15:51:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(282, 'ANON-252.', 'Filipina Tinio', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.6957, NULL, '2026-02-18 14:02:08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(283, 'ANON-253.', 'Ana Gonzales', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.7246, NULL, '2026-02-16 07:38:09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(284, 'ANON-254.', 'Elisa Natividad', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.769, NULL, '2026-02-16 09:33:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(285, 'ANON-255.', 'Salvacion Castro', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.6374, NULL, '2026-02-17 09:31:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286, 'ANON-256.', 'Ursula Mendoza', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.6416, NULL, '2026-02-15 10:39:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(287, 'ANON-257.', 'Josie Palma', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.7141, NULL, '2026-02-12 14:11:57', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(288, 'ANON-258.', 'Zelda Saguisag', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.9094, NULL, '2026-02-14 17:22:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(289, 'ANON-259.', 'Corazon Dalisay', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.9031, NULL, '2026-02-17 17:11:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(290, 'ANON-260.', 'Leonora Torres', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.8948, NULL, '2026-02-13 14:01:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(291, 'ANON-261.', 'Esmeralda Lagman', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.8313, NULL, '2026-02-16 08:04:19', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(292, 'ANON-262.', 'Salvacion Torres', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.7175, NULL, '2026-02-15 16:04:08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(293, 'ANON-263.', 'Salvacion Guerrero', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.794, NULL, '2026-02-14 09:56:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(294, 'ANON-264.', 'Josefina Manio', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.7082, NULL, '2026-02-15 15:58:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(295, 'ANON-265.', 'Milagros Katipunan', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.652, NULL, '2026-02-14 10:22:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(296, 'ANON-266.', 'Jocelyn Guerrero', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.7302, NULL, '2026-02-16 11:08:40', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(297, 'ANON-267.', 'Carmela Tinio', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.7259, NULL, '2026-02-16 09:10:42', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(298, 'ANON-268.', 'Quirina Ablaza', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.758, NULL, '2026-02-12 07:05:02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(299, 'ANON-269.', 'Emmeline Ureta', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.7347, NULL, '2026-02-16 13:39:40', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(300, 'ANON-270.', 'Anita Tolentino', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.7855, NULL, '2026-02-14 14:15:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(301, 'ANON-271.', 'Narcisa Soriano', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.7951, NULL, '2026-02-13 14:26:30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(302, 'ANON-272.', 'Fedelina Lim', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.6003, NULL, '2026-02-21 12:25:08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(303, 'ANON-273.', 'Patricia Ablaza', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.8148, NULL, '2026-02-20 14:07:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(304, 'ANON-274.', 'Luz Lim', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.5993, NULL, '2026-02-22 16:26:15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(305, 'ANON-275.', 'Ursula Pagcaliwagan', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.9387, NULL, '2026-02-23 12:12:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(306, 'ANON-276.', 'Dalisay Ablaza', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.9386, NULL, '2026-02-21 14:01:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(307, 'ANON-277.', 'Fedelina Katipunan', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.8117, NULL, '2026-02-20 16:22:03', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(308, 'ANON-278.', 'Narcisa Tamayo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.7683, NULL, '2026-02-24 17:30:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(309, 'ANON-279.', 'Genoveva Magno', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.8465, NULL, '2026-02-21 12:48:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(310, 'ANON-280.', 'Imelda Soriano', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.6902, NULL, '2026-02-20 12:35:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(311, 'ANON-281.', 'Dalisay Obsena', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.7714, NULL, '2026-02-21 16:43:39', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(312, 'ANON-282.', 'Jocelyn Villafuerte', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.9315, NULL, '2026-02-25 12:35:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(313, 'ANON-283.', 'Olympia Mendoza', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.7137, NULL, '2026-02-23 07:02:02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(314, 'ANON-284.', 'Melinda Ibarra', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.7132, NULL, '2026-02-22 09:38:57', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(315, 'ANON-285.', 'Erlinda De Leon', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.6648, NULL, '2026-02-20 13:39:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(316, 'ANON-286.', 'Hilda Galang', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.84, NULL, '2026-02-23 15:31:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(317, 'ANON-287.', 'Soledad Dela Torre', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.7162, NULL, '2026-02-21 17:07:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(318, 'ANON-288.', 'Irene Pascual', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.8289, NULL, '2026-02-24 17:01:38', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(319, 'ANON-289.', 'Erlinda Garcia', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.8885, NULL, '2026-02-25 16:36:14', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(320, 'ANON-290.', 'Jovita Dela Cruz', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.8367, NULL, '2026-02-24 16:49:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(321, 'ANON-291.', 'Charito Pagcaliwagan', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.8376, NULL, '2026-02-23 08:12:01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(322, 'ANON-292.', 'Perla Ramirez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.6214, NULL, '2026-03-02 14:55:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(323, 'ANON-293.', 'Bernadette Ilagan', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.6942, NULL, '2026-02-28 17:30:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(324, 'ANON-294.', 'Soledad Hontiveros', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.7109, NULL, '2026-03-02 12:43:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(325, 'ANON-295.', 'Queenie Quizon', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.9072, NULL, '2026-03-01 10:15:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(326, 'ANON-296.', 'Lolita Quezon', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.8905, NULL, '2026-02-28 10:44:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(327, 'ANON-297.', 'Norma Garcia', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.7462, NULL, '2026-03-03 11:18:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(328, 'ANON-298.', 'Remedios Espinosa', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.8635, NULL, '2026-02-28 09:18:02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(329, 'ANON-299.', 'Nenita Wenceslao', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.6712, NULL, '2026-02-28 14:13:12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(330, 'ANON-300.', 'Conception Mendoza', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.8298, NULL, '2026-02-26 16:47:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(331, 'ANON-301.', 'Jocelyn Bustamante', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.9397, NULL, '2026-02-27 07:06:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(332, 'ANON-302.', 'Gloria Valdez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.7884, NULL, '2026-03-02 09:26:41', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(333, 'ANON-303.', 'Tessie Manalo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.8028, NULL, '2026-02-28 12:18:41', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(334, 'ANON-304.', 'Gloria Cabanero', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.8218, NULL, '2026-03-03 07:58:58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(335, 'ANON-305.', 'Queenie Gonzales', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.6598, NULL, '2026-03-04 13:50:31', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(336, 'ANON-306.', 'Amelia Viray', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.8966, NULL, '2026-02-26 07:19:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(337, 'ANON-307.', 'Lorena Macaraeg', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.7572, NULL, '2026-03-03 15:33:31', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(338, 'ANON-308.', 'Lorna Platon', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.8113, NULL, '2026-03-04 08:21:10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(339, 'ANON-309.', 'Gloria Santos', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.88, NULL, '2026-02-28 11:36:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(340, 'ANON-310.', 'Rhea Quezon', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.924, NULL, '2026-03-04 13:32:20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(341, 'ANON-311.', 'Xiomara Tamayo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.6628, NULL, '2026-02-28 10:00:16', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(342, 'ANON-312.', 'Ursula Katipunan', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.6434, NULL, '2026-03-09 16:00:16', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(343, 'ANON-313.', 'Margarita Lagman', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.8717, NULL, '2026-03-05 14:19:10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(344, 'ANON-314.', 'Gloria Ramirez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.7259, NULL, '2026-03-10 11:44:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(345, 'ANON-315.', 'Yasmin Katipunan', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.951, NULL, '2026-03-06 10:08:30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(346, 'ANON-316.', 'Ursulina Pagcaliwagan', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.8542, NULL, '2026-03-08 15:58:30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(347, 'ANON-317.', 'Emmeline Manalo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.9066, NULL, '2026-03-11 10:43:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(348, 'ANON-318.', 'Jovita Castillo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.8256, NULL, '2026-03-09 12:04:58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(349, 'ANON-319.', 'Salud Domingo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.5716, NULL, '2026-03-09 10:36:34', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(350, 'ANON-320.', 'Pamela Dela Torre', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.7116, NULL, '2026-03-05 17:13:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(351, 'ANON-321.', 'Kristina Ablaza', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.5818, NULL, '2026-03-11 07:29:08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(352, 'ANON-322.', 'Nilda Quizon', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.7099, NULL, '2026-03-08 17:51:19', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(353, 'ANON-323.', 'Ofelia Salazar', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.7767, NULL, '2026-03-10 10:37:04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(354, 'ANON-324.', 'Vera Diaz', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.7171, NULL, '2026-03-05 08:59:30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(355, 'ANON-325.', 'Lorena Baguio', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.739, NULL, '2026-03-11 17:46:41', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(356, 'ANON-326.', 'Kristina Villafuerte', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.7698, NULL, '2026-03-08 13:24:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(357, 'ANON-327.', 'Lolita Ureta', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.8618, NULL, '2026-03-10 12:07:11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(358, 'ANON-328.', 'Salvacion Lim', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.7758, NULL, '2026-03-06 07:48:01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(359, 'ANON-329.', 'Diana Umali', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.7982, NULL, '2026-03-08 15:24:52', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(360, 'ANON-330.', 'Felicidad Quizon', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.7566, NULL, '2026-03-06 08:02:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(361, 'ANON-331.', 'Vera Santos', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.9345, NULL, '2026-03-12 08:38:02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(362, 'ANON-332.', 'Felicidad Navarro', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.8553, NULL, '2026-03-12 13:28:14', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(363, 'ANON-333.', 'Josefina Dalisay', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.8216, NULL, '2026-03-14 10:52:58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(364, 'ANON-334.', 'Dalisay Wenceslao', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.8413, NULL, '2026-03-14 10:19:56', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(365, 'ANON-335.', 'Celestina Imperio', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.6752, NULL, '2026-03-12 15:37:56', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(366, 'ANON-336.', 'Nilda Hilario', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.8672, NULL, '2026-03-12 12:41:42', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(367, 'ANON-337.', 'Hazel Evangelista', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.7937, NULL, '2026-03-14 13:11:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(368, 'ANON-338.', 'Lolita Santiago', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.6289, NULL, '2026-03-13 14:58:03', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(369, 'ANON-339.', 'Queenie Ibarra', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.7451, NULL, '2026-03-13 11:13:21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(370, 'ANON-340.', 'Elvira Tamayo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.9211, NULL, '2026-03-17 10:03:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(371, 'ANON-341.', 'Estrella Jacinto', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.5638, NULL, '2026-03-18 16:46:42', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(372, 'ANON-342.', 'Rowena Navarro', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.8543, NULL, '2026-03-12 10:59:12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(373, 'ANON-343.', 'Queenie Jacinto', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.9699, NULL, '2026-03-13 16:45:13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(374, 'ANON-344.', 'Gertrudes Estepa', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.6626, NULL, '2026-03-15 15:41:22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(375, 'ANON-345.', 'Norma Villanueva', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.859, NULL, '2026-03-18 14:48:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(376, 'ANON-346.', 'Hilda Verzosa', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.6595, NULL, '2026-03-18 10:47:09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(377, 'ANON-347.', 'Florencia Larena', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.7986, NULL, '2026-03-14 09:12:21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(378, 'ANON-348.', 'Esmeralda Hernandez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.6835, NULL, '2026-03-17 12:16:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(379, 'ANON-349.', 'Genoveva De Leon', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.8553, NULL, '2026-03-13 12:10:08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(380, 'ANON-350.', 'Corazon Garcia', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.9434, NULL, '2026-03-17 07:33:02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(381, 'ANON-351.', 'Lorna Magno', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.9118, NULL, '2026-03-12 13:08:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(382, 'ANON-352.', 'Lily Dacanay', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.6983, NULL, '2026-03-15 12:03:39', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(383, 'ANON-353.', 'Lilia Zulueta', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.9421, NULL, '2026-03-19 07:34:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(384, 'ANON-354.', 'Elisa Galang', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.5559, NULL, '2026-03-23 11:01:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(385, 'ANON-355.', 'Edna Feria', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.8657, NULL, '2026-03-20 08:58:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(386, 'ANON-356.', 'Rebecca Obsena', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.6343, NULL, '2026-03-21 12:17:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(387, 'ANON-357.', 'Amelia Lacson', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.6806, NULL, '2026-03-23 10:44:14', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(388, 'ANON-358.', 'Charito Magno', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.8398, NULL, '2026-03-25 17:48:02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(389, 'ANON-359.', 'Corazon Imperio', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.7289, NULL, '2026-03-19 17:00:44', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(390, 'ANON-360.', 'Nena Santos', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.77, NULL, '2026-03-25 12:36:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(391, 'ANON-361.', 'Raquel Verzosa', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.9363, NULL, '2026-03-20 10:57:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(392, 'ANON-362.', 'Narcisa Zulueta', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.8314, NULL, '2026-03-19 17:59:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(393, 'ANON-363.', 'Carmela Camacho', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.8303, NULL, '2026-03-25 12:01:13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(394, 'ANON-364.', 'Erlinda Lim', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.8133, NULL, '2026-03-22 08:19:10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(395, 'ANON-365.', 'Fedelina Obsena', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.7483, NULL, '2026-03-24 15:21:24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(396, 'ANON-366.', 'Rhea Hilario', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.6948, NULL, '2026-03-24 08:32:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(397, 'ANON-367.', 'Grace Tinio', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.6672, NULL, '2026-03-25 08:21:38', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(398, 'ANON-368.', 'Milagros Castillo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.9556, NULL, '2026-03-25 12:01:40', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(399, 'ANON-369.', 'Jenny Morales', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.8974, NULL, '2026-03-21 15:24:27', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(400, 'ANON-370.', 'Fedelina Hilario', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.7992, NULL, '2026-03-25 16:18:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(401, 'ANON-371.', 'Kristina Perez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.625, NULL, '2026-03-22 17:09:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(402, 'ANON-372.', 'Wanda Bustamante', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.6762, NULL, '2026-03-21 14:23:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(403, 'ANON-373.', 'Raquel Evangelista', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.5964, NULL, '2026-03-22 15:35:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(404, 'ANON-374.', 'Diana Ricofort', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.5592, NULL, '2026-03-19 16:38:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(405, 'ANON-375.', 'Raquel Macapagal', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.6857, NULL, '2026-03-30 10:36:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(406, 'ANON-376.', 'Filipina Saguisag', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.6472, NULL, '2026-03-29 09:04:10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(407, 'ANON-377.', 'Ina Soriano', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.7195, NULL, '2026-03-28 10:46:40', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(408, 'ANON-378.', 'Violeta Ibarra', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.7103, NULL, '2026-03-26 13:21:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `patients` (`id`, `patient_code`, `name`, `date_of_birth`, `age`, `contact_number`, `address`, `created_at`, `created_by`, `community`, `municipality`, `barangay`, `distance_to_facility_km`, `socioeconomic_index`, `latest_risk_level`, `latest_prediction_at`, `latest_probability_score`, `low_resource_area`, `last_prediction_at`, `prenatal_visits`, `gravida`, `para`, `referral_delay_hours`, `has_prior_complication`, `prior_complications`, `has_comorbidity`, `comorbidities`) VALUES
(409, 'ANON-379.', 'Ina Espinosa', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.7555, NULL, '2026-04-01 17:38:08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(410, 'ANON-380.', 'Wilhelmina Magno', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.7934, NULL, '2026-03-27 13:20:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(411, 'ANON-381.', 'Karla Gatmaitan', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.9597, NULL, '2026-03-28 13:17:52', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(412, 'ANON-382.', 'Perla Zulueta', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.9483, NULL, '2026-03-30 09:43:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(413, 'ANON-383.', 'Carmela Ongsiako', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.7116, NULL, '2026-03-29 11:53:44', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(414, 'ANON-384.', 'Nancy Feria', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.8703, NULL, '2026-03-27 17:19:15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(415, 'ANON-385.', 'Hazel Aquino', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.6981, NULL, '2026-03-29 16:30:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(416, 'ANON-386.', 'Rebecca Navarro', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.8721, NULL, '2026-03-30 07:50:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(417, 'ANON-387.', 'Florencia Rivera', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.84, NULL, '2026-03-26 10:42:42', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(418, 'ANON-388.', 'Norma Manalo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.5655, NULL, '2026-03-31 17:25:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(419, 'ANON-389.', 'Rowena Morales', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.5871, NULL, '2026-03-27 15:47:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(420, 'ANON-390.', 'Anita Pascual', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.6274, NULL, '2026-03-29 17:37:44', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(421, 'ANON-391.', 'Amelia Natividad', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.5586, NULL, '2026-03-29 08:07:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(422, 'ANON-392.', 'Anita Platon', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.7498, NULL, '2026-03-27 15:30:22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(423, 'ANON-393.', 'Caridad Ramirez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.7939, NULL, '2026-04-04 07:51:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(424, 'ANON-394.', 'Edna Ablaza', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.8887, NULL, '2026-04-07 09:59:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(425, 'ANON-395.', 'Norma Recto', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.6593, NULL, '2026-04-06 13:29:14', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(426, 'ANON-396.', 'Katrina Baguio', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.8515, NULL, '2026-04-07 13:25:01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(427, 'ANON-397.', 'Maricel Mercado', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.9326, NULL, '2026-04-05 13:00:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(428, 'ANON-398.', 'Josie Baguio', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.6946, NULL, '2026-04-08 08:36:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(429, 'ANON-399.', 'Amelia Zulueta', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.8218, NULL, '2026-04-03 14:07:16', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(430, 'ANON-400.', 'Zelda Obsena', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.8066, NULL, '2026-04-04 16:24:39', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(431, 'ANON-401.', 'Rosa Yap', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.59, NULL, '2026-04-05 16:11:52', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(432, 'ANON-402.', 'Yolanda Villafuerte', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.9062, NULL, '2026-04-06 08:43:08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(433, 'ANON-403.', 'Mylene Santos', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.6335, NULL, '2026-04-04 09:32:24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(434, 'ANON-404.', 'Xiomara Guerrero', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.5983, NULL, '2026-04-03 14:40:34', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(435, 'ANON-405.', 'Felicidad Verzosa', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.8565, NULL, '2026-04-06 09:31:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(436, 'ANON-406.', 'Amelia Baguio', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.707, NULL, '2026-04-02 12:00:31', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(437, 'ANON-407.', 'Tina Ureta', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.8772, NULL, '2026-04-06 17:31:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(438, 'ANON-408.', 'Ursula Recto', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.783, NULL, '2026-04-05 09:05:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(439, 'ANON-409.', 'Fedelina Cabanero', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.8588, NULL, '2026-04-04 10:34:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(440, 'ANON-410.', 'Raquel Hernandez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.7555, NULL, '2026-04-08 14:35:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(441, 'ANON-411.', 'Nancy Hernandez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.6388, NULL, '2026-04-08 12:34:52', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(442, 'ANON-412.', 'Imelda Dalisay', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.8932, NULL, '2026-04-03 13:06:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(443, 'ANON-413.', 'Nancy Garcia', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.9698, NULL, '2026-04-11 08:28:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(444, 'ANON-414.', 'Amelia Madriaga', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.7258, NULL, '2026-04-13 12:55:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(445, 'ANON-415.', 'Kristina Larena', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.8472, NULL, '2026-04-15 10:51:03', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(446, 'ANON-416.', 'Fedelina Quizon', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.7589, NULL, '2026-04-14 11:18:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(447, 'ANON-417.', 'Salud Ocampo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.8949, NULL, '2026-04-09 09:48:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(448, 'ANON-418.', 'Karla Hilario', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.7387, NULL, '2026-04-14 09:15:09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(449, 'ANON-419.', 'Ursulina Diaz', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.8937, NULL, '2026-04-12 09:36:40', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(450, 'ANON-420.', 'Emmeline Quezon', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.7082, NULL, '2026-04-15 08:25:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(451, 'ANON-421.', 'Luz Enriquez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.9307, NULL, '2026-04-12 12:37:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(452, 'ANON-422.', 'Aileen Umali', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.6798, NULL, '2026-04-14 17:44:22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(453, 'ANON-423.', 'Jovita Garcia', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.7773, NULL, '2026-04-15 10:31:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(454, 'ANON-424.', 'Rowena Hontiveros', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.7416, NULL, '2026-04-15 14:50:21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(455, 'ANON-425.', 'Filipina Morales', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.6647, NULL, '2026-04-15 14:01:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(456, 'ANON-426.', 'Narcisa Hilario', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.7961, NULL, '2026-04-12 10:44:58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(457, 'ANON-427.', 'Evelyn Flores', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.6541, NULL, '2026-04-11 08:41:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(458, 'ANON-428.', 'Conception Villafuerte', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.8663, NULL, '2026-04-12 17:19:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(459, 'ANON-429.', 'Nilda Santiago', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.8598, NULL, '2026-04-09 10:30:04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(460, 'ANON-430.', 'Jenny Buenaventura', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.8312, NULL, '2026-04-14 12:52:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(461, 'ANON-431.', 'Resurreccion Yap', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.6923, NULL, '2026-04-12 08:46:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(462, 'ANON-432.', 'Patricia Villafuerte', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.8995, NULL, '2026-04-15 09:41:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(463, 'ANON-433.', 'Perla Verzosa', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.6029, NULL, '2026-04-12 12:46:13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(464, 'ANON-434.', 'Yasmin Baguio', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.7817, NULL, '2026-04-15 10:05:57', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(465, 'ANON-435.', 'Adoracion Bautista', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.8005, NULL, '2026-04-12 12:50:08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(466, 'ANON-436.', 'Lily Cruz', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.6844, NULL, '2026-04-14 16:18:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(467, 'ANON-437.', 'Violeta Recto', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.9211, NULL, '2026-04-10 15:38:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(468, 'ANON-438.', 'Grace Valdez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.7762, NULL, '2026-04-22 10:31:24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(469, 'ANON-439.', 'Erlinda Imperio', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.7699, NULL, '2026-04-20 07:23:59', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(470, 'ANON-440.', 'Cristina Abaya', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.6854, NULL, '2026-04-19 12:34:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(471, 'ANON-441.', 'Isabelita Torres', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.7601, NULL, '2026-04-16 07:37:04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(472, 'ANON-442.', 'Wilhelmina Garcia', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.6734, NULL, '2026-04-22 13:32:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(473, 'ANON-443.', 'Yasmin Magno', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.8103, NULL, '2026-04-21 13:05:40', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(474, 'ANON-444.', 'Margarita Cruz', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.7656, NULL, '2026-04-18 08:32:58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(475, 'ANON-445.', 'Emmeline Castro', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.8747, NULL, '2026-04-19 16:40:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(476, 'ANON-446.', 'Florencia Diaz', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.8679, NULL, '2026-04-21 13:46:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(477, 'ANON-447.', 'Lily Katipunan', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.6831, NULL, '2026-04-16 14:39:38', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(478, 'ANON-448.', 'Remedios Umali', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.8265, NULL, '2026-04-17 08:00:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(479, 'ANON-449.', 'Yasmin Santiago', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.6039, NULL, '2026-04-18 15:33:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(480, 'ANON-450.', 'Katrina Obsena', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'high risk', NULL, 0.733, NULL, '2026-04-21 11:46:56', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(481, 'ANON-451.', 'Adoracion Zulueta', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.906, NULL, '2026-04-19 11:33:04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(482, 'ANON-452.', 'Wilma Ocampo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'mid risk', NULL, 0.8913, NULL, '2026-04-19 14:32:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(483, 'ANON-453.', 'Celestina Cabanero', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Payatas', NULL, NULL, 'low risk', NULL, 0.8922, NULL, '2026-04-17 11:49:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(484, 'ANON-454.', 'Violeta Quimpo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Bagong Silangan', NULL, NULL, 'high risk', NULL, 0.887, NULL, '2026-02-18 07:15:02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(485, 'ANON-455.', 'Remedios Abaya', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Bagong Silangan', NULL, NULL, 'mid risk', NULL, 0.6025, NULL, '2026-02-18 07:35:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(486, 'ANON-456.', 'Hilda Soriano', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Bagong Silangan', NULL, NULL, 'high risk', NULL, 0.7842, NULL, '2026-02-15 12:17:52', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(487, 'ANON-457.', 'Esmeralda Lopez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Bagong Silangan', NULL, NULL, 'low risk', NULL, 0.7707, NULL, '2026-02-13 10:51:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(488, 'ANON-458.', 'Zelda Garcia', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Bagong Silangan', NULL, NULL, 'high risk', NULL, 0.6721, NULL, '2026-02-14 11:20:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(489, 'ANON-459.', 'Vivian Dela Torre', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Bagong Silangan', NULL, NULL, 'high risk', NULL, 0.7142, NULL, '2026-02-14 16:15:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(490, 'ANON-460.', 'Yolanda Larena', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Bagong Silangan', NULL, NULL, 'low risk', NULL, 0.9392, NULL, '2026-02-17 15:48:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(491, 'ANON-461.', 'Teodora Villafuerte', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Bagong Silangan', NULL, NULL, 'high risk', NULL, 0.7812, NULL, '2026-02-14 17:53:44', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(492, 'ANON-462.', 'Bebelyn Quimpo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Bagong Silangan', NULL, NULL, 'high risk', NULL, 0.6505, NULL, '2026-02-18 11:06:13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(493, 'ANON-463.', 'Margarita Aquino', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Bagong Silangan', NULL, NULL, 'mid risk', NULL, 0.7222, NULL, '2026-02-13 11:05:02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(494, 'ANON-464.', 'Aileen Natividad', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Bagong Silangan', NULL, NULL, 'high risk', NULL, 0.8163, NULL, '2026-02-14 14:47:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(495, 'ANON-465.', 'Josie Feria', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Bagong Silangan', NULL, NULL, 'mid risk', NULL, 0.773, NULL, '2026-02-16 16:03:27', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(496, 'ANON-466.', 'Milagros Lopez', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Bagong Silangan', NULL, NULL, 'low risk', NULL, 0.6992, NULL, '2026-02-25 13:44:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(497, 'ANON-467.', 'Ofelia Zulueta', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Bagong Silangan', NULL, NULL, 'mid risk', NULL, 0.6613, NULL, '2026-02-19 10:08:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(498, 'ANON-468.', 'Felicidad Manio', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Bagong Silangan', NULL, NULL, 'mid risk', NULL, 0.5881, NULL, '2026-02-22 14:20:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(499, 'ANON-469.', 'Belen Castillo', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Bagong Silangan', NULL, NULL, 'low risk', NULL, 0.7218, NULL, '2026-02-22 11:29:16', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(500, 'ANON-470.', 'Elvira Umali', NULL, NULL, NULL, NULL, '2026-04-23 14:31:05', NULL, NULL, 'Quezon City', 'Barangay Bagong Silangan', NULL, NULL, 'high risk', NULL, 0.6789, NULL, '2026-02-21 16:02:13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4126, 'PAT-0021', 'TEST', '2001-04-21', 25, '09565535401', 'BAYAN GLORI', '2026-04-24 23:13:22', NULL, 'Barangay Bagong Silangan, Quezon City', 'Quezon City', 'Barangay Bagong Silangan', 2.34, 1, NULL, NULL, NULL, 1, NULL, 5, 5, 5, 28, 1, 'eclampsia,hemorrhage', 1, 'diabetes,anemia');

-- --------------------------------------------------------

--
-- Table structure for table `predictions`
--

CREATE TABLE `predictions` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `municipality` varchar(100) DEFAULT NULL,
  `barangay` varchar(150) DEFAULT NULL,
  `risk_level` enum('low risk','mid risk','high risk') NOT NULL,
  `probability_score` float NOT NULL,
  `all_probabilities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`all_probabilities`)),
  `model_version_id` int(11) DEFAULT NULL,
  `recorded_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `community` varchar(200) DEFAULT NULL COMMENT 'Composite: Barangay, Municipality',
  `mortality_risk_label` enum('low','moderate','high','very high') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `predictions`
--

INSERT INTO `predictions` (`id`, `patient_id`, `municipality`, `barangay`, `risk_level`, `probability_score`, `all_probabilities`, `model_version_id`, `recorded_by`, `created_at`, `community`, `mortality_risk_label`) VALUES
(10, 1, NULL, NULL, 'low risk', 0.9194, '{\"low risk\": 0.9194, \"mid risk\": 0.0719, \"high risk\": 0.0088}', NULL, NULL, '2026-04-22 05:01:26', NULL, NULL),
(11, 1, NULL, NULL, 'mid risk', 0.8889, '{\"low risk\": 0.0748, \"mid risk\": 0.8889, \"high risk\": 0.0362}', NULL, NULL, '2026-04-22 05:46:19', NULL, NULL),
(24, 1, NULL, NULL, 'low risk', 0.9002, '{\"low risk\": 0.9002, \"mid risk\": 0.0916, \"high risk\": 0.0082}', NULL, NULL, '2026-04-22 06:15:05', NULL, NULL),
(25, 1, NULL, NULL, 'low risk', 0.9191, '{\"low risk\": 0.9191, \"mid risk\": 0.0675, \"high risk\": 0.0134}', NULL, NULL, '2026-04-22 06:17:04', NULL, NULL),
(26, 1, NULL, NULL, 'low risk', 0.9191, '{\"low risk\": 0.9191, \"mid risk\": 0.0675, \"high risk\": 0.0134}', NULL, NULL, '2026-04-22 06:17:21', NULL, NULL),
(27, 1, NULL, NULL, 'low risk', 0.9191, '{\"low risk\": 0.9191, \"mid risk\": 0.0675, \"high risk\": 0.0134}', NULL, NULL, '2026-04-22 06:17:31', NULL, NULL),
(28, 8, NULL, NULL, 'low risk', 0.6855, '{\"low risk\": 0.6855, \"mid risk\": 0.2981, \"high risk\": 0.0164}', NULL, NULL, '2026-04-22 06:24:06', NULL, NULL),
(29, 1, NULL, NULL, 'low risk', 0.8653, '{\"low risk\": 0.8653, \"mid risk\": 0.1272, \"high risk\": 0.0075}', NULL, NULL, '2026-04-22 06:25:37', NULL, NULL),
(30, 1, NULL, NULL, 'low risk', 0.8653, '{\"low risk\": 0.8653, \"mid risk\": 0.1272, \"high risk\": 0.0075}', NULL, NULL, '2026-04-22 06:25:43', NULL, NULL),
(31, 1, NULL, NULL, 'low risk', 0.8653, '{\"low risk\": 0.8653, \"mid risk\": 0.1272, \"high risk\": 0.0075}', NULL, NULL, '2026-04-22 06:26:08', NULL, NULL),
(32, 1, NULL, NULL, 'low risk', 0.8653, '{\"low risk\": 0.8653, \"mid risk\": 0.1272, \"high risk\": 0.0075}', NULL, NULL, '2026-04-22 06:52:08', NULL, NULL),
(33, 1, NULL, NULL, 'low risk', 0.8675, '{\"low risk\": 0.8675, \"mid risk\": 0.1242, \"high risk\": 0.0083}', 3, NULL, '2026-04-22 06:55:21', NULL, NULL),
(34, 1, NULL, NULL, 'mid risk', 0.8933, '{\"low risk\": 0.0718, \"mid risk\": 0.8933, \"high risk\": 0.0349}', 3, NULL, '2026-04-22 06:55:47', NULL, NULL),
(35, 7, NULL, NULL, 'high risk', 0.951, '{\"low risk\": 0.0113, \"mid risk\": 0.0377, \"high risk\": 0.951}', 3, NULL, '2026-04-22 06:57:39', NULL, NULL),
(36, 1, NULL, NULL, 'low risk', 0.8675, '{\"low risk\": 0.8675, \"mid risk\": 0.1242, \"high risk\": 0.0083}', 3, NULL, '2026-04-22 08:00:48', NULL, NULL),
(37, 1, NULL, NULL, 'low risk', 0.8067, '{\"low risk\": 0.8067, \"mid risk\": 0.1807, \"high risk\": 0.0126}', 4, NULL, '2026-04-23 00:20:05', NULL, NULL),
(38, 1, NULL, NULL, 'mid risk', 0.7377, '{\"low risk\": 0.2394, \"mid risk\": 0.7377, \"high risk\": 0.0229}', 4, NULL, '2026-04-23 00:20:39', NULL, NULL),
(39, 1, NULL, NULL, 'mid risk', 0.7377, '{\"low risk\": 0.2394, \"mid risk\": 0.7377, \"high risk\": 0.0229}', 4, NULL, '2026-04-23 01:06:31', NULL, NULL),
(40, 31, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.72, '{\"low risk\": 0.1414, \"mid risk\": 0.1386, \"high risk\": 0.72}', 4, NULL, '2026-02-12 15:12:45', NULL, NULL),
(41, 32, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.6272, '{\"low risk\": 0.2343, \"mid risk\": 0.6272, \"high risk\": 0.1385}', 4, NULL, '2026-02-18 07:48:51', NULL, NULL),
(42, 33, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.6945, '{\"low risk\": 0.6945, \"mid risk\": 0.1029, \"high risk\": 0.2026}', 4, NULL, '2026-02-18 12:06:05', NULL, NULL),
(43, 34, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.7169, '{\"low risk\": 0.7169, \"mid risk\": 0.109, \"high risk\": 0.1741}', 4, NULL, '2026-02-12 14:34:07', NULL, NULL),
(44, 35, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.8267, '{\"low risk\": 0.1371, \"mid risk\": 0.0362, \"high risk\": 0.8267}', 4, NULL, '2026-02-16 12:36:12', NULL, NULL),
(45, 36, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.7229, '{\"low risk\": 0.0844, \"mid risk\": 0.1927, \"high risk\": 0.7229}', 4, NULL, '2026-02-12 10:55:06', NULL, NULL),
(46, 37, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.8836, '{\"low risk\": 0.8836, \"mid risk\": 0.0592, \"high risk\": 0.0572}', 4, NULL, '2026-02-14 10:42:17', NULL, NULL),
(47, 38, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.575, '{\"low risk\": 0.2818, \"mid risk\": 0.575, \"high risk\": 0.1432}', 4, NULL, '2026-02-16 10:10:29', NULL, NULL),
(48, 39, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.8176, '{\"low risk\": 0.8176, \"mid risk\": 0.1182, \"high risk\": 0.0642}', 4, NULL, '2026-02-17 12:53:49', NULL, NULL),
(49, 40, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.5612, '{\"low risk\": 0.1695, \"mid risk\": 0.5612, \"high risk\": 0.2693}', 4, NULL, '2026-02-14 08:13:58', NULL, NULL),
(50, 41, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.7507, '{\"low risk\": 0.1603, \"mid risk\": 0.089, \"high risk\": 0.7507}', 4, NULL, '2026-02-15 17:29:09', NULL, NULL),
(51, 42, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.7909, '{\"low risk\": 0.7909, \"mid risk\": 0.0892, \"high risk\": 0.1199}', 4, NULL, '2026-02-23 13:57:37', NULL, NULL),
(52, 43, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.6746, '{\"low risk\": 0.6746, \"mid risk\": 0.3147, \"high risk\": 0.0107}', 4, NULL, '2026-02-23 14:05:48', NULL, NULL),
(53, 44, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.6373, '{\"low risk\": 0.6373, \"mid risk\": 0.2399, \"high risk\": 0.1228}', 4, NULL, '2026-02-25 17:27:38', NULL, NULL),
(54, 45, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.7298, '{\"low risk\": 0.7298, \"mid risk\": 0.2594, \"high risk\": 0.0108}', 4, NULL, '2026-02-23 11:35:55', NULL, NULL),
(55, 46, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.8677, '{\"low risk\": 0.0229, \"mid risk\": 0.1094, \"high risk\": 0.8677}', 4, NULL, '2026-02-23 11:49:41', NULL, NULL),
(56, 47, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.6998, '{\"low risk\": 0.6998, \"mid risk\": 0.088, \"high risk\": 0.2122}', 4, NULL, '2026-02-19 11:32:48', NULL, NULL),
(57, 48, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.9103, '{\"low risk\": 0.9103, \"mid risk\": 0.0759, \"high risk\": 0.0138}', 4, NULL, '2026-02-21 17:32:38', NULL, NULL),
(58, 49, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.7271, '{\"low risk\": 0.7271, \"mid risk\": 0.0844, \"high risk\": 0.1885}', 4, NULL, '2026-02-25 15:58:00', NULL, NULL),
(59, 50, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.721, '{\"low risk\": 0.0745, \"mid risk\": 0.721, \"high risk\": 0.2045}', 4, NULL, '2026-02-21 11:15:03', NULL, NULL),
(60, 51, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.8842, '{\"low risk\": 0.0882, \"mid risk\": 0.0276, \"high risk\": 0.8842}', 4, NULL, '2026-03-04 15:49:08', NULL, NULL),
(61, 52, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.7869, '{\"low risk\": 0.7869, \"mid risk\": 0.0906, \"high risk\": 0.1225}', 4, NULL, '2026-03-04 16:27:13', NULL, NULL),
(62, 53, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.8708, '{\"low risk\": 0.0879, \"mid risk\": 0.0413, \"high risk\": 0.8708}', 4, NULL, '2026-03-01 17:41:23', NULL, NULL),
(63, 54, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.6411, '{\"low risk\": 0.6411, \"mid risk\": 0.1172, \"high risk\": 0.2417}', 4, NULL, '2026-02-28 07:37:35', NULL, NULL),
(64, 55, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.6241, '{\"low risk\": 0.6241, \"mid risk\": 0.2494, \"high risk\": 0.1265}', 4, NULL, '2026-02-27 08:57:02', NULL, NULL),
(65, 56, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.7262, '{\"low risk\": 0.1798, \"mid risk\": 0.094, \"high risk\": 0.7262}', 4, NULL, '2026-02-27 15:08:46', NULL, NULL),
(66, 57, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.8013, '{\"low risk\": 0.1502, \"mid risk\": 0.0485, \"high risk\": 0.8013}', 4, NULL, '2026-03-04 13:12:06', NULL, NULL),
(67, 58, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.744, '{\"low risk\": 0.744, \"mid risk\": 0.1415, \"high risk\": 0.1145}', 4, NULL, '2026-03-03 07:43:41', NULL, NULL),
(68, 59, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.7788, '{\"low risk\": 0.0783, \"mid risk\": 0.1429, \"high risk\": 0.7788}', 4, NULL, '2026-03-04 08:15:12', NULL, NULL),
(69, 60, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.7434, '{\"low risk\": 0.7434, \"mid risk\": 0.1048, \"high risk\": 0.1518}', 4, NULL, '2026-03-06 08:28:51', NULL, NULL),
(70, 61, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.8261, '{\"low risk\": 0.0178, \"mid risk\": 0.1561, \"high risk\": 0.8261}', 4, NULL, '2026-03-09 07:05:59', NULL, NULL),
(71, 62, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.6327, '{\"low risk\": 0.1749, \"mid risk\": 0.6327, \"high risk\": 0.1924}', 4, NULL, '2026-03-08 10:55:25', NULL, NULL),
(72, 63, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.7027, '{\"low risk\": 0.0106, \"mid risk\": 0.2867, \"high risk\": 0.7027}', 4, NULL, '2026-03-08 11:59:50', NULL, NULL),
(73, 64, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.6498, '{\"low risk\": 0.2522, \"mid risk\": 0.6498, \"high risk\": 0.098}', 4, NULL, '2026-03-10 15:42:45', NULL, NULL),
(74, 65, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.6165, '{\"low risk\": 0.1204, \"mid risk\": 0.6165, \"high risk\": 0.2631}', 4, NULL, '2026-03-05 16:47:34', NULL, NULL),
(75, 66, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.7066, '{\"low risk\": 0.7066, \"mid risk\": 0.0617, \"high risk\": 0.2317}', 4, NULL, '2026-03-08 15:58:54', NULL, NULL),
(76, 67, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.5699, '{\"low risk\": 0.2379, \"mid risk\": 0.5699, \"high risk\": 0.1922}', 4, NULL, '2026-03-11 09:04:38', NULL, NULL),
(77, 68, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.893, '{\"low risk\": 0.893, \"mid risk\": 0.069, \"high risk\": 0.038}', 4, NULL, '2026-03-09 10:37:38', NULL, NULL),
(78, 69, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.7922, '{\"low risk\": 0.7922, \"mid risk\": 0.0968, \"high risk\": 0.111}', 4, NULL, '2026-03-14 10:42:45', NULL, NULL),
(79, 70, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.6445, '{\"low risk\": 0.6445, \"mid risk\": 0.2407, \"high risk\": 0.1148}', 4, NULL, '2026-03-15 12:59:48', NULL, NULL),
(80, 71, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.8488, '{\"low risk\": 0.0839, \"mid risk\": 0.0673, \"high risk\": 0.8488}', 4, NULL, '2026-03-12 08:34:13', NULL, NULL),
(81, 72, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.6722, '{\"low risk\": 0.0684, \"mid risk\": 0.6722, \"high risk\": 0.2594}', 4, NULL, '2026-03-13 12:18:10', NULL, NULL),
(82, 73, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.7029, '{\"low risk\": 0.7029, \"mid risk\": 0.2833, \"high risk\": 0.0138}', 4, NULL, '2026-03-18 17:33:00', NULL, NULL),
(83, 74, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.8761, '{\"low risk\": 0.0566, \"mid risk\": 0.8761, \"high risk\": 0.0673}', 4, NULL, '2026-03-13 11:07:56', NULL, NULL),
(84, 75, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.6926, '{\"low risk\": 0.6926, \"mid risk\": 0.1996, \"high risk\": 0.1078}', 4, NULL, '2026-03-17 12:13:43', NULL, NULL),
(85, 76, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.721, '{\"low risk\": 0.2483, \"mid risk\": 0.721, \"high risk\": 0.0307}', 4, NULL, '2026-03-18 07:05:40', NULL, NULL),
(86, 77, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.6012, '{\"low risk\": 0.6012, \"mid risk\": 0.3113, \"high risk\": 0.0875}', 4, NULL, '2026-03-24 11:10:47', NULL, NULL),
(87, 78, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.8399, '{\"low risk\": 0.8399, \"mid risk\": 0.1061, \"high risk\": 0.054}', 4, NULL, '2026-03-19 08:56:44', NULL, NULL),
(88, 79, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.8246, '{\"low risk\": 0.1397, \"mid risk\": 0.0357, \"high risk\": 0.8246}', 4, NULL, '2026-03-23 15:09:27', NULL, NULL),
(89, 80, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.7048, '{\"low risk\": 0.7048, \"mid risk\": 0.2614, \"high risk\": 0.0338}', 4, NULL, '2026-03-25 07:57:22', NULL, NULL),
(90, 81, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.6848, '{\"low risk\": 0.6848, \"mid risk\": 0.0762, \"high risk\": 0.239}', 4, NULL, '2026-03-25 15:56:55', NULL, NULL),
(91, 82, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.811, '{\"low risk\": 0.811, \"mid risk\": 0.0699, \"high risk\": 0.1191}', 4, NULL, '2026-03-20 09:51:51', NULL, NULL),
(92, 83, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.7402, '{\"low risk\": 0.7402, \"mid risk\": 0.0858, \"high risk\": 0.174}', 4, NULL, '2026-03-21 13:51:42', NULL, NULL),
(93, 84, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.9094, '{\"low risk\": 0.0288, \"mid risk\": 0.0618, \"high risk\": 0.9094}', 4, NULL, '2026-03-25 08:24:55', NULL, NULL),
(94, 85, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.76, '{\"low risk\": 0.76, \"mid risk\": 0.0859, \"high risk\": 0.1541}', 4, NULL, '2026-03-22 12:19:52', NULL, NULL),
(95, 86, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.6297, '{\"low risk\": 0.0573, \"mid risk\": 0.6297, \"high risk\": 0.313}', 4, NULL, '2026-03-20 13:21:17', NULL, NULL),
(96, 87, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.8553, '{\"low risk\": 0.0598, \"mid risk\": 0.0849, \"high risk\": 0.8553}', 4, NULL, '2026-04-01 15:21:01', NULL, NULL),
(97, 88, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.6607, '{\"low risk\": 0.6607, \"mid risk\": 0.3188, \"high risk\": 0.0205}', 4, NULL, '2026-03-28 07:06:38', NULL, NULL),
(98, 89, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.7067, '{\"low risk\": 0.7067, \"mid risk\": 0.1914, \"high risk\": 0.1019}', 4, NULL, '2026-03-30 08:24:57', NULL, NULL),
(99, 90, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.7981, '{\"low risk\": 0.0502, \"mid risk\": 0.7981, \"high risk\": 0.1517}', 4, NULL, '2026-04-01 15:43:46', NULL, NULL),
(100, 91, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.7131, '{\"low risk\": 0.1251, \"mid risk\": 0.1618, \"high risk\": 0.7131}', 4, NULL, '2026-03-31 12:39:20', NULL, NULL),
(101, 92, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.865, '{\"low risk\": 0.088, \"mid risk\": 0.865, \"high risk\": 0.047}', 4, NULL, '2026-03-31 13:20:25', NULL, NULL),
(102, 93, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.6171, '{\"low risk\": 0.2647, \"mid risk\": 0.6171, \"high risk\": 0.1182}', 4, NULL, '2026-03-29 17:47:57', NULL, NULL),
(103, 94, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.7381, '{\"low risk\": 0.7381, \"mid risk\": 0.2183, \"high risk\": 0.0436}', 4, NULL, '2026-03-28 11:13:27', NULL, NULL),
(104, 95, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.6628, '{\"low risk\": 0.1725, \"mid risk\": 0.6628, \"high risk\": 0.1647}', 4, NULL, '2026-03-31 10:32:30', NULL, NULL),
(105, 96, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.8076, '{\"low risk\": 0.1372, \"mid risk\": 0.8076, \"high risk\": 0.0552}', 4, NULL, '2026-03-28 15:42:40', NULL, NULL),
(106, 97, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.9546, '{\"low risk\": 0.016, \"mid risk\": 0.0294, \"high risk\": 0.9546}', 4, NULL, '2026-03-28 10:51:12', NULL, NULL),
(107, 98, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.9649, '{\"low risk\": 0.0192, \"mid risk\": 0.0159, \"high risk\": 0.9649}', 4, NULL, '2026-04-01 08:29:26', NULL, NULL),
(108, 99, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.8799, '{\"low risk\": 0.0484, \"mid risk\": 0.0717, \"high risk\": 0.8799}', 4, NULL, '2026-03-29 10:09:41', NULL, NULL),
(109, 100, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.9253, '{\"low risk\": 0.0584, \"mid risk\": 0.0163, \"high risk\": 0.9253}', 4, NULL, '2026-04-08 13:14:11', NULL, NULL),
(110, 101, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.8727, '{\"low risk\": 0.0598, \"mid risk\": 0.0675, \"high risk\": 0.8727}', 4, NULL, '2026-04-06 10:58:54', NULL, NULL),
(111, 102, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.6453, '{\"low risk\": 0.6453, \"mid risk\": 0.1869, \"high risk\": 0.1678}', 4, NULL, '2026-04-06 15:38:20', NULL, NULL),
(112, 103, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.9353, '{\"low risk\": 0.0374, \"mid risk\": 0.0273, \"high risk\": 0.9353}', 4, NULL, '2026-04-07 15:27:53', NULL, NULL),
(113, 104, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.7927, '{\"low risk\": 0.0398, \"mid risk\": 0.1675, \"high risk\": 0.7927}', 4, NULL, '2026-04-08 14:28:16', NULL, NULL),
(114, 105, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.844, '{\"low risk\": 0.0766, \"mid risk\": 0.844, \"high risk\": 0.0794}', 4, NULL, '2026-04-08 15:31:40', NULL, NULL),
(115, 106, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.7496, '{\"low risk\": 0.7496, \"mid risk\": 0.1859, \"high risk\": 0.0645}', 4, NULL, '2026-04-03 11:21:20', NULL, NULL),
(116, 107, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.6758, '{\"low risk\": 0.0559, \"mid risk\": 0.2683, \"high risk\": 0.6758}', 4, NULL, '2026-04-05 09:45:13', NULL, NULL),
(117, 108, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.7386, '{\"low risk\": 0.7386, \"mid risk\": 0.1593, \"high risk\": 0.1021}', 4, NULL, '2026-04-05 07:13:53', NULL, NULL),
(118, 109, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.9076, '{\"low risk\": 0.9076, \"mid risk\": 0.0689, \"high risk\": 0.0235}', 4, NULL, '2026-04-07 07:54:56', NULL, NULL),
(119, 110, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.6831, '{\"low risk\": 0.0515, \"mid risk\": 0.6831, \"high risk\": 0.2654}', 4, NULL, '2026-04-04 11:48:24', NULL, NULL),
(120, 111, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.9551, '{\"low risk\": 0.0204, \"mid risk\": 0.0245, \"high risk\": 0.9551}', 4, NULL, '2026-04-07 15:51:38', NULL, NULL),
(121, 112, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.72, '{\"low risk\": 0.1355, \"mid risk\": 0.72, \"high risk\": 0.1445}', 4, NULL, '2026-04-14 17:51:25', NULL, NULL),
(122, 113, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.8718, '{\"low risk\": 0.1169, \"mid risk\": 0.8718, \"high risk\": 0.0113}', 4, NULL, '2026-04-13 07:58:25', NULL, NULL),
(123, 114, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.5794, '{\"low risk\": 0.2046, \"mid risk\": 0.5794, \"high risk\": 0.216}', 4, NULL, '2026-04-15 14:11:03', NULL, NULL),
(124, 115, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.7546, '{\"low risk\": 0.7546, \"mid risk\": 0.1126, \"high risk\": 0.1328}', 4, NULL, '2026-04-12 11:48:53', NULL, NULL),
(125, 116, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.7599, '{\"low risk\": 0.7599, \"mid risk\": 0.1849, \"high risk\": 0.0552}', 4, NULL, '2026-04-09 12:14:41', NULL, NULL),
(126, 117, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.6137, '{\"low risk\": 0.6137, \"mid risk\": 0.0601, \"high risk\": 0.3262}', 4, NULL, '2026-04-10 10:53:01', NULL, NULL),
(127, 118, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.7157, '{\"low risk\": 0.0757, \"mid risk\": 0.7157, \"high risk\": 0.2086}', 4, NULL, '2026-04-10 14:44:16', NULL, NULL),
(128, 119, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.7625, '{\"low risk\": 0.1828, \"mid risk\": 0.7625, \"high risk\": 0.0547}', 4, NULL, '2026-04-09 09:19:06', NULL, NULL),
(129, 120, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.7515, '{\"low risk\": 0.2212, \"mid risk\": 0.7515, \"high risk\": 0.0273}', 4, NULL, '2026-04-12 13:45:12', NULL, NULL),
(130, 121, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.8133, '{\"low risk\": 0.8133, \"mid risk\": 0.0629, \"high risk\": 0.1238}', 4, NULL, '2026-04-15 11:54:43', NULL, NULL),
(131, 122, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.8943, '{\"low risk\": 0.0858, \"mid risk\": 0.8943, \"high risk\": 0.0199}', 4, NULL, '2026-04-11 15:27:42', NULL, NULL),
(132, 123, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.716, '{\"low risk\": 0.716, \"mid risk\": 0.2403, \"high risk\": 0.0437}', 4, NULL, '2026-04-15 14:06:27', NULL, NULL),
(133, 124, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.9152, '{\"low risk\": 0.0558, \"mid risk\": 0.029, \"high risk\": 0.9152}', 4, NULL, '2026-04-12 09:46:33', NULL, NULL),
(134, 125, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.8748, '{\"low risk\": 0.8748, \"mid risk\": 0.0851, \"high risk\": 0.0401}', 4, NULL, '2026-04-19 14:27:52', NULL, NULL),
(135, 126, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.6439, '{\"low risk\": 0.3022, \"mid risk\": 0.6439, \"high risk\": 0.0539}', 4, NULL, '2026-04-22 08:17:56', NULL, NULL),
(136, 127, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.8127, '{\"low risk\": 0.1225, \"mid risk\": 0.8127, \"high risk\": 0.0648}', 4, NULL, '2026-04-21 13:21:01', NULL, NULL),
(137, 128, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.6638, '{\"low risk\": 0.1847, \"mid risk\": 0.6638, \"high risk\": 0.1515}', 4, NULL, '2026-04-18 11:21:17', NULL, NULL),
(138, 129, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.8744, '{\"low risk\": 0.0392, \"mid risk\": 0.0864, \"high risk\": 0.8744}', 4, NULL, '2026-04-16 15:12:05', NULL, NULL),
(139, 130, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.7382, '{\"low risk\": 0.7382, \"mid risk\": 0.162, \"high risk\": 0.0998}', 4, NULL, '2026-04-17 14:41:45', NULL, NULL),
(140, 131, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.8275, '{\"low risk\": 0.0605, \"mid risk\": 0.8275, \"high risk\": 0.112}', 4, NULL, '2026-04-17 13:44:15', NULL, NULL),
(141, 132, 'Quezon City', 'Barangay Batasan Hills', 'low risk', 0.7977, '{\"low risk\": 0.7977, \"mid risk\": 0.1173, \"high risk\": 0.085}', 4, NULL, '2026-04-20 12:27:47', NULL, NULL),
(142, 133, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.6731, '{\"low risk\": 0.1711, \"mid risk\": 0.6731, \"high risk\": 0.1558}', 4, NULL, '2026-04-18 11:14:07', NULL, NULL),
(143, 134, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.6604, '{\"low risk\": 0.2577, \"mid risk\": 0.6604, \"high risk\": 0.0819}', 4, NULL, '2026-04-22 09:12:13', NULL, NULL),
(144, 135, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.6468, '{\"low risk\": 0.2229, \"mid risk\": 0.6468, \"high risk\": 0.1303}', 4, NULL, '2026-04-22 15:38:18', NULL, NULL),
(145, 136, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.9164, '{\"low risk\": 0.0288, \"mid risk\": 0.0548, \"high risk\": 0.9164}', 4, NULL, '2026-04-18 09:19:00', NULL, NULL),
(146, 137, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.5943, '{\"low risk\": 0.0657, \"mid risk\": 0.5943, \"high risk\": 0.34}', 4, NULL, '2026-04-16 15:18:44', NULL, NULL),
(147, 138, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.8541, '{\"low risk\": 0.1048, \"mid risk\": 0.0411, \"high risk\": 0.8541}', 4, NULL, '2026-04-16 07:36:18', NULL, NULL),
(148, 139, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.6175, '{\"low risk\": 0.6175, \"mid risk\": 0.3535, \"high risk\": 0.029}', 4, NULL, '2026-02-15 08:52:04', NULL, NULL),
(149, 140, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.8514, '{\"low risk\": 0.0169, \"mid risk\": 0.1317, \"high risk\": 0.8514}', 4, NULL, '2026-02-13 16:19:05', NULL, NULL),
(150, 141, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.8946, '{\"low risk\": 0.0618, \"mid risk\": 0.0436, \"high risk\": 0.8946}', 4, NULL, '2026-02-18 16:14:49', NULL, NULL),
(151, 142, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.705, '{\"low risk\": 0.2521, \"mid risk\": 0.705, \"high risk\": 0.0429}', 4, NULL, '2026-02-15 11:36:39', NULL, NULL),
(152, 143, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.6337, '{\"low risk\": 0.6337, \"mid risk\": 0.2836, \"high risk\": 0.0827}', 4, NULL, '2026-02-17 10:16:42', NULL, NULL),
(153, 144, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.7877, '{\"low risk\": 0.7877, \"mid risk\": 0.0738, \"high risk\": 0.1385}', 4, NULL, '2026-02-15 14:44:38', NULL, NULL),
(154, 145, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.7422, '{\"low risk\": 0.0772, \"mid risk\": 0.1806, \"high risk\": 0.7422}', 4, NULL, '2026-02-18 14:04:43', NULL, NULL),
(155, 146, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.869, '{\"low risk\": 0.869, \"mid risk\": 0.0919, \"high risk\": 0.0391}', 4, NULL, '2026-02-18 10:27:07', NULL, NULL),
(156, 147, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.8679, '{\"low risk\": 0.1096, \"mid risk\": 0.8679, \"high risk\": 0.0225}', 4, NULL, '2026-02-12 07:10:50', NULL, NULL),
(157, 148, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.7935, '{\"low risk\": 0.7935, \"mid risk\": 0.0923, \"high risk\": 0.1142}', 4, NULL, '2026-02-12 14:44:19', NULL, NULL),
(158, 149, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.7252, '{\"low risk\": 0.1561, \"mid risk\": 0.7252, \"high risk\": 0.1187}', 4, NULL, '2026-02-12 16:02:56', NULL, NULL),
(159, 150, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.6851, '{\"low risk\": 0.6851, \"mid risk\": 0.0733, \"high risk\": 0.2416}', 4, NULL, '2026-02-17 16:37:01', NULL, NULL),
(160, 151, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.6443, '{\"low risk\": 0.0619, \"mid risk\": 0.6443, \"high risk\": 0.2938}', 4, NULL, '2026-02-25 09:30:33', NULL, NULL),
(161, 152, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.8706, '{\"low risk\": 0.0626, \"mid risk\": 0.8706, \"high risk\": 0.0668}', 4, NULL, '2026-02-23 13:40:52', NULL, NULL),
(162, 153, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.5819, '{\"low risk\": 0.1746, \"mid risk\": 0.5819, \"high risk\": 0.2435}', 4, NULL, '2026-02-21 12:42:06', NULL, NULL),
(163, 154, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.7555, '{\"low risk\": 0.1657, \"mid risk\": 0.0788, \"high risk\": 0.7555}', 4, NULL, '2026-02-21 17:25:52', NULL, NULL),
(164, 155, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.5628, '{\"low risk\": 0.0832, \"mid risk\": 0.5628, \"high risk\": 0.354}', 4, NULL, '2026-02-21 12:07:49', NULL, NULL),
(165, 156, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.775, '{\"low risk\": 0.775, \"mid risk\": 0.213, \"high risk\": 0.012}', 4, NULL, '2026-02-24 15:29:26', NULL, NULL),
(166, 157, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.7763, '{\"low risk\": 0.7763, \"mid risk\": 0.1519, \"high risk\": 0.0718}', 4, NULL, '2026-02-22 17:28:48', NULL, NULL),
(167, 158, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.6908, '{\"low risk\": 0.6908, \"mid risk\": 0.0826, \"high risk\": 0.2266}', 4, NULL, '2026-02-21 14:56:44', NULL, NULL),
(168, 159, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.5601, '{\"low risk\": 0.2893, \"mid risk\": 0.5601, \"high risk\": 0.1506}', 4, NULL, '2026-02-25 10:45:10', NULL, NULL),
(169, 160, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.6046, '{\"low risk\": 0.6046, \"mid risk\": 0.1868, \"high risk\": 0.2086}', 4, NULL, '2026-02-20 08:29:07', NULL, NULL),
(170, 161, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.6039, '{\"low risk\": 0.3635, \"mid risk\": 0.6039, \"high risk\": 0.0326}', 4, NULL, '2026-02-21 15:45:17', NULL, NULL),
(171, 162, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.764, '{\"low risk\": 0.764, \"mid risk\": 0.1331, \"high risk\": 0.1029}', 4, NULL, '2026-02-22 15:09:24', NULL, NULL),
(172, 163, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.8038, '{\"low risk\": 0.8038, \"mid risk\": 0.1516, \"high risk\": 0.0446}', 4, NULL, '2026-02-20 08:17:49', NULL, NULL),
(173, 164, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.6952, '{\"low risk\": 0.2787, \"mid risk\": 0.6952, \"high risk\": 0.0261}', 4, NULL, '2026-02-23 11:52:00', NULL, NULL),
(174, 165, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.7015, '{\"low risk\": 0.7015, \"mid risk\": 0.19, \"high risk\": 0.1085}', 4, NULL, '2026-02-24 14:55:09', NULL, NULL),
(175, 166, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.7647, '{\"low risk\": 0.7647, \"mid risk\": 0.1083, \"high risk\": 0.127}', 4, NULL, '2026-02-25 15:24:29', NULL, NULL),
(176, 167, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.7264, '{\"low risk\": 0.1071, \"mid risk\": 0.1665, \"high risk\": 0.7264}', 4, NULL, '2026-03-04 13:02:20', NULL, NULL),
(177, 168, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.834, '{\"low risk\": 0.0909, \"mid risk\": 0.834, \"high risk\": 0.0751}', 4, NULL, '2026-03-03 17:09:31', NULL, NULL),
(178, 169, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.9589, '{\"low risk\": 0.017, \"mid risk\": 0.0241, \"high risk\": 0.9589}', 4, NULL, '2026-02-26 14:06:33', NULL, NULL),
(179, 170, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.6961, '{\"low risk\": 0.2573, \"mid risk\": 0.0466, \"high risk\": 0.6961}', 4, NULL, '2026-02-27 08:30:50', NULL, NULL),
(180, 171, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.8717, '{\"low risk\": 0.0804, \"mid risk\": 0.0479, \"high risk\": 0.8717}', 4, NULL, '2026-03-04 12:54:43', NULL, NULL),
(181, 172, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.7513, '{\"low risk\": 0.1744, \"mid risk\": 0.0743, \"high risk\": 0.7513}', 4, NULL, '2026-03-04 14:55:34', NULL, NULL),
(182, 173, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.852, '{\"low risk\": 0.1279, \"mid risk\": 0.0201, \"high risk\": 0.852}', 4, NULL, '2026-02-27 08:27:06', NULL, NULL),
(183, 174, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.5852, '{\"low risk\": 0.109, \"mid risk\": 0.5852, \"high risk\": 0.3058}', 4, NULL, '2026-02-28 07:02:20', NULL, NULL),
(184, 175, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.6812, '{\"low risk\": 0.0877, \"mid risk\": 0.6812, \"high risk\": 0.2311}', 4, NULL, '2026-03-02 13:36:43', NULL, NULL),
(185, 176, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.5776, '{\"low risk\": 0.3656, \"mid risk\": 0.5776, \"high risk\": 0.0568}', 4, NULL, '2026-03-02 17:15:31', NULL, NULL),
(186, 177, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.7976, '{\"low risk\": 0.0563, \"mid risk\": 0.1461, \"high risk\": 0.7976}', 4, NULL, '2026-02-28 17:00:57', NULL, NULL),
(187, 178, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.8668, '{\"low risk\": 0.0279, \"mid risk\": 0.1053, \"high risk\": 0.8668}', 4, NULL, '2026-03-01 12:37:19', NULL, NULL),
(188, 179, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.6375, '{\"low risk\": 0.3057, \"mid risk\": 0.6375, \"high risk\": 0.0568}', 4, NULL, '2026-02-27 13:54:30', NULL, NULL),
(189, 180, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.722, '{\"low risk\": 0.722, \"mid risk\": 0.1145, \"high risk\": 0.1635}', 4, NULL, '2026-03-03 11:01:53', NULL, NULL),
(190, 181, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.7481, '{\"low risk\": 0.1816, \"mid risk\": 0.7481, \"high risk\": 0.0703}', 4, NULL, '2026-03-03 07:58:38', NULL, NULL),
(191, 182, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.9389, '{\"low risk\": 0.0419, \"mid risk\": 0.0192, \"high risk\": 0.9389}', 4, NULL, '2026-03-06 16:51:22', NULL, NULL),
(192, 183, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.6646, '{\"low risk\": 0.6646, \"mid risk\": 0.119, \"high risk\": 0.2164}', 4, NULL, '2026-03-11 17:43:53', NULL, NULL),
(193, 184, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.633, '{\"low risk\": 0.633, \"mid risk\": 0.2426, \"high risk\": 0.1244}', 4, NULL, '2026-03-05 11:50:28', NULL, NULL),
(194, 185, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.7241, '{\"low risk\": 0.7241, \"mid risk\": 0.0784, \"high risk\": 0.1975}', 4, NULL, '2026-03-07 12:47:26', NULL, NULL),
(195, 186, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.6449, '{\"low risk\": 0.6449, \"mid risk\": 0.2092, \"high risk\": 0.1459}', 4, NULL, '2026-03-07 15:32:58', NULL, NULL),
(196, 187, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.6559, '{\"low risk\": 0.6559, \"mid risk\": 0.3097, \"high risk\": 0.0344}', 4, NULL, '2026-03-08 11:47:55', NULL, NULL),
(197, 188, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.6392, '{\"low risk\": 0.6392, \"mid risk\": 0.3396, \"high risk\": 0.0212}', 4, NULL, '2026-03-06 10:55:43', NULL, NULL),
(198, 189, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.893, '{\"low risk\": 0.0955, \"mid risk\": 0.893, \"high risk\": 0.0115}', 4, NULL, '2026-03-11 15:23:05', NULL, NULL),
(199, 190, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.5549, '{\"low risk\": 0.2566, \"mid risk\": 0.5549, \"high risk\": 0.1885}', 4, NULL, '2026-03-08 12:43:47', NULL, NULL),
(200, 191, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.7546, '{\"low risk\": 0.2025, \"mid risk\": 0.7546, \"high risk\": 0.0429}', 4, NULL, '2026-03-07 08:43:14', NULL, NULL),
(201, 192, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.7668, '{\"low risk\": 0.2134, \"mid risk\": 0.7668, \"high risk\": 0.0198}', 4, NULL, '2026-03-07 16:14:41', NULL, NULL),
(202, 193, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.8801, '{\"low risk\": 0.8801, \"mid risk\": 0.1045, \"high risk\": 0.0154}', 4, NULL, '2026-03-07 17:26:07', NULL, NULL),
(203, 194, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.9217, '{\"low risk\": 0.9217, \"mid risk\": 0.0556, \"high risk\": 0.0227}', 4, NULL, '2026-03-08 08:06:15', NULL, NULL),
(204, 195, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.6934, '{\"low risk\": 0.14, \"mid risk\": 0.1666, \"high risk\": 0.6934}', 4, NULL, '2026-03-10 15:26:37', NULL, NULL),
(205, 196, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.6337, '{\"low risk\": 0.6337, \"mid risk\": 0.1999, \"high risk\": 0.1664}', 4, NULL, '2026-03-15 11:02:44', NULL, NULL),
(206, 197, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.9232, '{\"low risk\": 0.9232, \"mid risk\": 0.0644, \"high risk\": 0.0124}', 4, NULL, '2026-03-12 17:23:34', NULL, NULL),
(207, 198, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.6694, '{\"low risk\": 0.0957, \"mid risk\": 0.2349, \"high risk\": 0.6694}', 4, NULL, '2026-03-12 14:05:42', NULL, NULL),
(208, 199, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.9302, '{\"low risk\": 0.9302, \"mid risk\": 0.0505, \"high risk\": 0.0193}', 4, NULL, '2026-03-14 10:08:50', NULL, NULL),
(209, 200, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.8949, '{\"low risk\": 0.0276, \"mid risk\": 0.0775, \"high risk\": 0.8949}', 4, NULL, '2026-03-13 10:21:49', NULL, NULL),
(210, 201, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.601, '{\"low risk\": 0.601, \"mid risk\": 0.341, \"high risk\": 0.058}', 4, NULL, '2026-03-13 09:34:16', NULL, NULL),
(211, 202, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.9273, '{\"low risk\": 0.0169, \"mid risk\": 0.0558, \"high risk\": 0.9273}', 4, NULL, '2026-03-14 10:37:20', NULL, NULL),
(212, 203, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.6431, '{\"low risk\": 0.6431, \"mid risk\": 0.175, \"high risk\": 0.1819}', 4, NULL, '2026-03-12 08:30:28', NULL, NULL),
(213, 204, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.5882, '{\"low risk\": 0.2273, \"mid risk\": 0.5882, \"high risk\": 0.1845}', 4, NULL, '2026-03-16 07:46:50', NULL, NULL),
(214, 205, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.7465, '{\"low risk\": 0.1602, \"mid risk\": 0.0933, \"high risk\": 0.7465}', 4, NULL, '2026-03-12 07:30:54', NULL, NULL),
(215, 206, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.7667, '{\"low risk\": 0.7667, \"mid risk\": 0.2077, \"high risk\": 0.0256}', 4, NULL, '2026-03-12 08:20:38', NULL, NULL),
(216, 207, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.8123, '{\"low risk\": 0.8123, \"mid risk\": 0.1248, \"high risk\": 0.0629}', 4, NULL, '2026-03-17 12:24:38', NULL, NULL),
(217, 208, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.7619, '{\"low risk\": 0.0677, \"mid risk\": 0.7619, \"high risk\": 0.1704}', 4, NULL, '2026-03-17 08:54:41', NULL, NULL),
(218, 209, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.8023, '{\"low risk\": 0.0796, \"mid risk\": 0.8023, \"high risk\": 0.1181}', 4, NULL, '2026-03-15 10:26:21', NULL, NULL),
(219, 210, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.8481, '{\"low risk\": 0.8481, \"mid risk\": 0.0787, \"high risk\": 0.0732}', 4, NULL, '2026-03-21 17:16:23', NULL, NULL),
(220, 211, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.8698, '{\"low risk\": 0.0623, \"mid risk\": 0.0679, \"high risk\": 0.8698}', 4, NULL, '2026-03-19 08:05:27', NULL, NULL),
(221, 212, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.8512, '{\"low risk\": 0.8512, \"mid risk\": 0.1221, \"high risk\": 0.0267}', 4, NULL, '2026-03-23 07:37:35', NULL, NULL),
(222, 213, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.7845, '{\"low risk\": 0.1139, \"mid risk\": 0.7845, \"high risk\": 0.1016}', 4, NULL, '2026-03-25 17:48:27', NULL, NULL),
(223, 214, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.8807, '{\"low risk\": 0.1062, \"mid risk\": 0.0131, \"high risk\": 0.8807}', 4, NULL, '2026-03-23 11:22:06', NULL, NULL),
(224, 215, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.6245, '{\"low risk\": 0.2572, \"mid risk\": 0.6245, \"high risk\": 0.1183}', 4, NULL, '2026-03-20 08:22:54', NULL, NULL),
(225, 216, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.5902, '{\"low risk\": 0.1474, \"mid risk\": 0.5902, \"high risk\": 0.2624}', 4, NULL, '2026-03-20 13:54:35', NULL, NULL),
(226, 217, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.9119, '{\"low risk\": 0.0518, \"mid risk\": 0.0363, \"high risk\": 0.9119}', 4, NULL, '2026-03-24 15:01:38', NULL, NULL),
(227, 218, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.9154, '{\"low risk\": 0.0273, \"mid risk\": 0.0573, \"high risk\": 0.9154}', 4, NULL, '2026-03-20 11:44:48', NULL, NULL),
(228, 219, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.7155, '{\"low risk\": 0.7155, \"mid risk\": 0.0514, \"high risk\": 0.2331}', 4, NULL, '2026-03-25 09:36:42', NULL, NULL),
(229, 220, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.6482, '{\"low risk\": 0.6482, \"mid risk\": 0.2348, \"high risk\": 0.117}', 4, NULL, '2026-03-19 08:47:33', NULL, NULL),
(230, 221, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.7427, '{\"low risk\": 0.7427, \"mid risk\": 0.1173, \"high risk\": 0.14}', 4, NULL, '2026-03-21 11:46:20', NULL, NULL),
(231, 222, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.7487, '{\"low risk\": 0.0663, \"mid risk\": 0.7487, \"high risk\": 0.185}', 4, NULL, '2026-03-19 09:10:48', NULL, NULL),
(232, 223, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.7859, '{\"low risk\": 0.0919, \"mid risk\": 0.7859, \"high risk\": 0.1222}', 4, NULL, '2026-03-24 13:31:38', NULL, NULL),
(233, 224, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.6929, '{\"low risk\": 0.6929, \"mid risk\": 0.2365, \"high risk\": 0.0706}', 4, NULL, '2026-03-19 12:27:07', NULL, NULL),
(234, 225, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.7835, '{\"low risk\": 0.0571, \"mid risk\": 0.7835, \"high risk\": 0.1594}', 4, NULL, '2026-03-29 16:03:00', NULL, NULL),
(235, 226, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.8609, '{\"low risk\": 0.8609, \"mid risk\": 0.1104, \"high risk\": 0.0287}', 4, NULL, '2026-03-28 12:07:00', NULL, NULL),
(236, 227, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.5952, '{\"low risk\": 0.2336, \"mid risk\": 0.5952, \"high risk\": 0.1712}', 4, NULL, '2026-03-27 15:35:53', NULL, NULL),
(237, 228, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.689, '{\"low risk\": 0.2363, \"mid risk\": 0.689, \"high risk\": 0.0747}', 4, NULL, '2026-03-29 07:29:58', NULL, NULL),
(238, 229, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.746, '{\"low risk\": 0.746, \"mid risk\": 0.1285, \"high risk\": 0.1255}', 4, NULL, '2026-03-31 13:18:07', NULL, NULL),
(239, 230, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.6584, '{\"low risk\": 0.6584, \"mid risk\": 0.3168, \"high risk\": 0.0248}', 4, NULL, '2026-03-29 12:05:27', NULL, NULL),
(240, 231, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.8384, '{\"low risk\": 0.0842, \"mid risk\": 0.0774, \"high risk\": 0.8384}', 4, NULL, '2026-03-29 11:47:21', NULL, NULL),
(241, 232, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.626, '{\"low risk\": 0.626, \"mid risk\": 0.2488, \"high risk\": 0.1252}', 4, NULL, '2026-03-30 15:12:57', NULL, NULL),
(242, 233, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.8854, '{\"low risk\": 0.0852, \"mid risk\": 0.8854, \"high risk\": 0.0294}', 4, NULL, '2026-03-27 10:06:09', NULL, NULL),
(243, 234, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.652, '{\"low risk\": 0.652, \"mid risk\": 0.2686, \"high risk\": 0.0794}', 4, NULL, '2026-03-26 09:49:40', NULL, NULL),
(244, 235, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.816, '{\"low risk\": 0.1057, \"mid risk\": 0.816, \"high risk\": 0.0783}', 4, NULL, '2026-04-06 17:40:39', NULL, NULL),
(245, 236, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.9301, '{\"low risk\": 0.9301, \"mid risk\": 0.0531, \"high risk\": 0.0168}', 4, NULL, '2026-04-05 08:30:28', NULL, NULL),
(246, 237, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.8287, '{\"low risk\": 0.1158, \"mid risk\": 0.8287, \"high risk\": 0.0555}', 4, NULL, '2026-04-04 15:04:19', NULL, NULL),
(247, 238, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.5632, '{\"low risk\": 0.1889, \"mid risk\": 0.5632, \"high risk\": 0.2479}', 4, NULL, '2026-04-04 08:41:55', NULL, NULL),
(248, 239, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.6789, '{\"low risk\": 0.1889, \"mid risk\": 0.1322, \"high risk\": 0.6789}', 4, NULL, '2026-04-05 14:37:35', NULL, NULL),
(249, 240, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.8865, '{\"low risk\": 0.0138, \"mid risk\": 0.0997, \"high risk\": 0.8865}', 4, NULL, '2026-04-08 16:41:12', NULL, NULL),
(250, 241, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.7617, '{\"low risk\": 0.7617, \"mid risk\": 0.0769, \"high risk\": 0.1614}', 4, NULL, '2026-04-02 14:06:51', NULL, NULL),
(251, 242, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.7599, '{\"low risk\": 0.1672, \"mid risk\": 0.0729, \"high risk\": 0.7599}', 4, NULL, '2026-04-06 17:11:02', NULL, NULL),
(252, 243, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.7488, '{\"low risk\": 0.7488, \"mid risk\": 0.134, \"high risk\": 0.1172}', 4, NULL, '2026-04-06 16:10:23', NULL, NULL),
(253, 244, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.6962, '{\"low risk\": 0.6962, \"mid risk\": 0.1497, \"high risk\": 0.1541}', 4, NULL, '2026-04-04 17:38:03', NULL, NULL),
(254, 245, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.7766, '{\"low risk\": 0.0608, \"mid risk\": 0.7766, \"high risk\": 0.1626}', 4, NULL, '2026-04-02 15:43:24', NULL, NULL),
(255, 246, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.8461, '{\"low risk\": 0.8461, \"mid risk\": 0.1117, \"high risk\": 0.0422}', 4, NULL, '2026-04-06 09:21:05', NULL, NULL),
(256, 247, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.929, '{\"low risk\": 0.929, \"mid risk\": 0.0577, \"high risk\": 0.0133}', 4, NULL, '2026-04-12 09:38:45', NULL, NULL),
(257, 248, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.7705, '{\"low risk\": 0.1759, \"mid risk\": 0.0536, \"high risk\": 0.7705}', 4, NULL, '2026-04-15 09:42:44', NULL, NULL),
(258, 249, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.8693, '{\"low risk\": 0.0683, \"mid risk\": 0.0624, \"high risk\": 0.8693}', 4, NULL, '2026-04-14 17:27:32', NULL, NULL),
(259, 250, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.6613, '{\"low risk\": 0.6613, \"mid risk\": 0.1097, \"high risk\": 0.229}', 4, NULL, '2026-04-15 14:12:14', NULL, NULL),
(260, 251, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.7447, '{\"low risk\": 0.1954, \"mid risk\": 0.0599, \"high risk\": 0.7447}', 4, NULL, '2026-04-13 15:53:47', NULL, NULL),
(261, 252, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.7578, '{\"low risk\": 0.1803, \"mid risk\": 0.0619, \"high risk\": 0.7578}', 4, NULL, '2026-04-10 16:24:09', NULL, NULL),
(262, 253, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.8622, '{\"low risk\": 0.8622, \"mid risk\": 0.1129, \"high risk\": 0.0249}', 4, NULL, '2026-04-10 14:02:26', NULL, NULL),
(263, 254, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.9297, '{\"low risk\": 0.9297, \"mid risk\": 0.0563, \"high risk\": 0.014}', 4, NULL, '2026-04-15 14:14:34', NULL, NULL),
(264, 255, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.8667, '{\"low risk\": 0.8667, \"mid risk\": 0.1163, \"high risk\": 0.017}', 4, NULL, '2026-04-10 12:43:36', NULL, NULL),
(265, 256, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.7402, '{\"low risk\": 0.1016, \"mid risk\": 0.1582, \"high risk\": 0.7402}', 4, NULL, '2026-04-13 13:10:52', NULL, NULL),
(266, 257, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.8968, '{\"low risk\": 0.8968, \"mid risk\": 0.0523, \"high risk\": 0.0509}', 4, NULL, '2026-04-12 12:35:59', NULL, NULL),
(267, 258, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.8894, '{\"low risk\": 0.8894, \"mid risk\": 0.0644, \"high risk\": 0.0462}', 4, NULL, '2026-04-15 09:17:28', NULL, NULL),
(268, 259, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.79, '{\"low risk\": 0.1898, \"mid risk\": 0.0202, \"high risk\": 0.79}', 4, NULL, '2026-04-10 14:56:22', NULL, NULL),
(269, 260, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.7348, '{\"low risk\": 0.7348, \"mid risk\": 0.1267, \"high risk\": 0.1385}', 4, NULL, '2026-04-19 08:23:14', NULL, NULL),
(270, 261, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.9165, '{\"low risk\": 0.9165, \"mid risk\": 0.0697, \"high risk\": 0.0138}', 4, NULL, '2026-04-21 12:50:09', NULL, NULL),
(271, 262, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.6975, '{\"low risk\": 0.6975, \"mid risk\": 0.2512, \"high risk\": 0.0513}', 4, NULL, '2026-04-21 09:48:45', NULL, NULL),
(272, 263, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.7654, '{\"low risk\": 0.2082, \"mid risk\": 0.7654, \"high risk\": 0.0264}', 4, NULL, '2026-04-16 11:13:53', NULL, NULL),
(273, 264, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.9197, '{\"low risk\": 0.9197, \"mid risk\": 0.0624, \"high risk\": 0.0179}', 4, NULL, '2026-04-19 08:49:18', NULL, NULL),
(274, 265, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.6414, '{\"low risk\": 0.6414, \"mid risk\": 0.1212, \"high risk\": 0.2374}', 4, NULL, '2026-04-21 16:29:04', NULL, NULL),
(275, 266, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.9077, '{\"low risk\": 0.9077, \"mid risk\": 0.0693, \"high risk\": 0.023}', 4, NULL, '2026-04-16 17:32:36', NULL, NULL),
(276, 267, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.6488, '{\"low risk\": 0.6488, \"mid risk\": 0.175, \"high risk\": 0.1762}', 4, NULL, '2026-04-20 12:15:36', NULL, NULL),
(277, 268, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.8259, '{\"low risk\": 0.8259, \"mid risk\": 0.0598, \"high risk\": 0.1143}', 4, NULL, '2026-04-18 08:33:34', NULL, NULL),
(278, 269, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.8959, '{\"low risk\": 0.0744, \"mid risk\": 0.8959, \"high risk\": 0.0297}', 4, NULL, '2026-04-19 14:02:40', NULL, NULL),
(279, 270, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.7269, '{\"low risk\": 0.7269, \"mid risk\": 0.2093, \"high risk\": 0.0638}', 4, NULL, '2026-04-18 08:22:15', NULL, NULL),
(280, 271, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.7699, '{\"low risk\": 0.1812, \"mid risk\": 0.7699, \"high risk\": 0.0489}', 4, NULL, '2026-04-21 12:08:02', NULL, NULL),
(281, 272, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.7151, '{\"low risk\": 0.7151, \"mid risk\": 0.1946, \"high risk\": 0.0903}', 4, NULL, '2026-04-22 17:29:44', NULL, NULL),
(282, 273, 'Quezon City', 'Barangay Commonwealth', 'mid risk', 0.6138, '{\"low risk\": 0.094, \"mid risk\": 0.6138, \"high risk\": 0.2922}', 4, NULL, '2026-04-21 14:02:18', NULL, NULL),
(283, 274, 'Quezon City', 'Barangay Commonwealth', 'low risk', 0.869, '{\"low risk\": 0.869, \"mid risk\": 0.1129, \"high risk\": 0.0181}', 4, NULL, '2026-04-18 11:32:25', NULL, NULL),
(284, 275, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.8137, '{\"low risk\": 0.0741, \"mid risk\": 0.8137, \"high risk\": 0.1122}', 4, NULL, '2026-02-14 07:55:41', NULL, NULL),
(285, 276, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.6788, '{\"low risk\": 0.2823, \"mid risk\": 0.6788, \"high risk\": 0.0389}', 4, NULL, '2026-02-17 14:57:24', NULL, NULL),
(286, 277, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.7922, '{\"low risk\": 0.1043, \"mid risk\": 0.7922, \"high risk\": 0.1035}', 4, NULL, '2026-02-18 15:17:51', NULL, NULL),
(287, 278, 'Quezon City', 'Barangay Payatas', 'low risk', 0.7464, '{\"low risk\": 0.7464, \"mid risk\": 0.2427, \"high risk\": 0.0109}', 4, NULL, '2026-02-13 15:18:20', NULL, NULL),
(288, 279, 'Quezon City', 'Barangay Payatas', 'low risk', 0.7005, '{\"low risk\": 0.7005, \"mid risk\": 0.1568, \"high risk\": 0.1427}', 4, NULL, '2026-02-17 13:10:44', NULL, NULL),
(289, 280, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.8044, '{\"low risk\": 0.1736, \"mid risk\": 0.8044, \"high risk\": 0.022}', 4, NULL, '2026-02-16 13:17:40', NULL, NULL),
(290, 281, 'Quezon City', 'Barangay Payatas', 'high risk', 0.674, '{\"low risk\": 0.2051, \"mid risk\": 0.1209, \"high risk\": 0.674}', 4, NULL, '2026-02-14 15:51:47', NULL, NULL),
(291, 282, 'Quezon City', 'Barangay Payatas', 'high risk', 0.6957, '{\"low risk\": 0.1827, \"mid risk\": 0.1216, \"high risk\": 0.6957}', 4, NULL, '2026-02-18 14:02:08', NULL, NULL),
(292, 283, 'Quezon City', 'Barangay Payatas', 'low risk', 0.7246, '{\"low risk\": 0.7246, \"mid risk\": 0.1325, \"high risk\": 0.1429}', 4, NULL, '2026-02-16 07:38:09', NULL, NULL),
(293, 284, 'Quezon City', 'Barangay Payatas', 'high risk', 0.769, '{\"low risk\": 0.171, \"mid risk\": 0.06, \"high risk\": 0.769}', 4, NULL, '2026-02-16 09:33:23', NULL, NULL);
INSERT INTO `predictions` (`id`, `patient_id`, `municipality`, `barangay`, `risk_level`, `probability_score`, `all_probabilities`, `model_version_id`, `recorded_by`, `created_at`, `community`, `mortality_risk_label`) VALUES
(294, 285, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.6374, '{\"low risk\": 0.0843, \"mid risk\": 0.6374, \"high risk\": 0.2783}', 4, NULL, '2026-02-17 09:31:33', NULL, NULL),
(295, 286, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.6416, '{\"low risk\": 0.1277, \"mid risk\": 0.6416, \"high risk\": 0.2307}', 4, NULL, '2026-02-15 10:39:18', NULL, NULL),
(296, 287, 'Quezon City', 'Barangay Payatas', 'high risk', 0.7141, '{\"low risk\": 0.0461, \"mid risk\": 0.2398, \"high risk\": 0.7141}', 4, NULL, '2026-02-12 14:11:57', NULL, NULL),
(297, 288, 'Quezon City', 'Barangay Payatas', 'high risk', 0.9094, '{\"low risk\": 0.0783, \"mid risk\": 0.0123, \"high risk\": 0.9094}', 4, NULL, '2026-02-14 17:22:45', NULL, NULL),
(298, 289, 'Quezon City', 'Barangay Payatas', 'low risk', 0.9031, '{\"low risk\": 0.9031, \"mid risk\": 0.0814, \"high risk\": 0.0155}', 4, NULL, '2026-02-17 17:11:50', NULL, NULL),
(299, 290, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.8948, '{\"low risk\": 0.0858, \"mid risk\": 0.8948, \"high risk\": 0.0194}', 4, NULL, '2026-02-13 14:01:23', NULL, NULL),
(300, 291, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.8313, '{\"low risk\": 0.11, \"mid risk\": 0.8313, \"high risk\": 0.0587}', 4, NULL, '2026-02-16 08:04:19', NULL, NULL),
(301, 292, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.7175, '{\"low risk\": 0.1414, \"mid risk\": 0.7175, \"high risk\": 0.1411}', 4, NULL, '2026-02-15 16:04:08', NULL, NULL),
(302, 293, 'Quezon City', 'Barangay Payatas', 'high risk', 0.794, '{\"low risk\": 0.1365, \"mid risk\": 0.0695, \"high risk\": 0.794}', 4, NULL, '2026-02-14 09:56:53', NULL, NULL),
(303, 294, 'Quezon City', 'Barangay Payatas', 'high risk', 0.7082, '{\"low risk\": 0.2726, \"mid risk\": 0.0192, \"high risk\": 0.7082}', 4, NULL, '2026-02-15 15:58:55', NULL, NULL),
(304, 295, 'Quezon City', 'Barangay Payatas', 'low risk', 0.652, '{\"low risk\": 0.652, \"mid risk\": 0.0974, \"high risk\": 0.2506}', 4, NULL, '2026-02-14 10:22:33', NULL, NULL),
(305, 296, 'Quezon City', 'Barangay Payatas', 'high risk', 0.7302, '{\"low risk\": 0.1686, \"mid risk\": 0.1012, \"high risk\": 0.7302}', 4, NULL, '2026-02-16 11:08:40', NULL, NULL),
(306, 297, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.7259, '{\"low risk\": 0.0861, \"mid risk\": 0.7259, \"high risk\": 0.188}', 4, NULL, '2026-02-16 09:10:42', NULL, NULL),
(307, 298, 'Quezon City', 'Barangay Payatas', 'high risk', 0.758, '{\"low risk\": 0.2151, \"mid risk\": 0.0269, \"high risk\": 0.758}', 4, NULL, '2026-02-12 07:05:02', NULL, NULL),
(308, 299, 'Quezon City', 'Barangay Payatas', 'high risk', 0.7347, '{\"low risk\": 0.0617, \"mid risk\": 0.2036, \"high risk\": 0.7347}', 4, NULL, '2026-02-16 13:39:40', NULL, NULL),
(309, 300, 'Quezon City', 'Barangay Payatas', 'low risk', 0.7855, '{\"low risk\": 0.7855, \"mid risk\": 0.1492, \"high risk\": 0.0653}', 4, NULL, '2026-02-14 14:15:51', NULL, NULL),
(310, 301, 'Quezon City', 'Barangay Payatas', 'high risk', 0.7951, '{\"low risk\": 0.1373, \"mid risk\": 0.0676, \"high risk\": 0.7951}', 4, NULL, '2026-02-13 14:26:30', NULL, NULL),
(311, 302, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.6003, '{\"low risk\": 0.3426, \"mid risk\": 0.6003, \"high risk\": 0.0571}', 4, NULL, '2026-02-21 12:25:08', NULL, NULL),
(312, 303, 'Quezon City', 'Barangay Payatas', 'high risk', 0.8148, '{\"low risk\": 0.0275, \"mid risk\": 0.1577, \"high risk\": 0.8148}', 4, NULL, '2026-02-20 14:07:17', NULL, NULL),
(313, 304, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.5993, '{\"low risk\": 0.0672, \"mid risk\": 0.5993, \"high risk\": 0.3335}', 4, NULL, '2026-02-22 16:26:15', NULL, NULL),
(314, 305, 'Quezon City', 'Barangay Payatas', 'high risk', 0.9387, '{\"low risk\": 0.0436, \"mid risk\": 0.0177, \"high risk\": 0.9387}', 4, NULL, '2026-02-23 12:12:48', NULL, NULL),
(315, 306, 'Quezon City', 'Barangay Payatas', 'low risk', 0.9386, '{\"low risk\": 0.9386, \"mid risk\": 0.0507, \"high risk\": 0.0107}', 4, NULL, '2026-02-21 14:01:05', NULL, NULL),
(316, 307, 'Quezon City', 'Barangay Payatas', 'high risk', 0.8117, '{\"low risk\": 0.1727, \"mid risk\": 0.0156, \"high risk\": 0.8117}', 4, NULL, '2026-02-20 16:22:03', NULL, NULL),
(317, 308, 'Quezon City', 'Barangay Payatas', 'low risk', 0.7683, '{\"low risk\": 0.7683, \"mid risk\": 0.2015, \"high risk\": 0.0302}', 4, NULL, '2026-02-24 17:30:18', NULL, NULL),
(318, 309, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.8465, '{\"low risk\": 0.0903, \"mid risk\": 0.8465, \"high risk\": 0.0632}', 4, NULL, '2026-02-21 12:48:25', NULL, NULL),
(319, 310, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.6902, '{\"low risk\": 0.1925, \"mid risk\": 0.6902, \"high risk\": 0.1173}', 4, NULL, '2026-02-20 12:35:18', NULL, NULL),
(320, 311, 'Quezon City', 'Barangay Payatas', 'low risk', 0.7714, '{\"low risk\": 0.7714, \"mid risk\": 0.1789, \"high risk\": 0.0497}', 4, NULL, '2026-02-21 16:43:39', NULL, NULL),
(321, 312, 'Quezon City', 'Barangay Payatas', 'low risk', 0.9315, '{\"low risk\": 0.9315, \"mid risk\": 0.0533, \"high risk\": 0.0152}', 4, NULL, '2026-02-25 12:35:23', NULL, NULL),
(322, 313, 'Quezon City', 'Barangay Payatas', 'high risk', 0.7137, '{\"low risk\": 0.1456, \"mid risk\": 0.1407, \"high risk\": 0.7137}', 4, NULL, '2026-02-23 07:02:02', NULL, NULL),
(323, 314, 'Quezon City', 'Barangay Payatas', 'low risk', 0.7132, '{\"low risk\": 0.7132, \"mid risk\": 0.1574, \"high risk\": 0.1294}', 4, NULL, '2026-02-22 09:38:57', NULL, NULL),
(324, 315, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.6648, '{\"low risk\": 0.2187, \"mid risk\": 0.6648, \"high risk\": 0.1165}', 4, NULL, '2026-02-20 13:39:47', NULL, NULL),
(325, 316, 'Quezon City', 'Barangay Payatas', 'high risk', 0.84, '{\"low risk\": 0.081, \"mid risk\": 0.079, \"high risk\": 0.84}', 4, NULL, '2026-02-23 15:31:45', NULL, NULL),
(326, 317, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.7162, '{\"low risk\": 0.0537, \"mid risk\": 0.7162, \"high risk\": 0.2301}', 4, NULL, '2026-02-21 17:07:26', NULL, NULL),
(327, 318, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.8289, '{\"low risk\": 0.1306, \"mid risk\": 0.8289, \"high risk\": 0.0405}', 4, NULL, '2026-02-24 17:01:38', NULL, NULL),
(328, 319, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.8885, '{\"low risk\": 0.0903, \"mid risk\": 0.8885, \"high risk\": 0.0212}', 4, NULL, '2026-02-25 16:36:14', NULL, NULL),
(329, 320, 'Quezon City', 'Barangay Payatas', 'high risk', 0.8367, '{\"low risk\": 0.0344, \"mid risk\": 0.1289, \"high risk\": 0.8367}', 4, NULL, '2026-02-24 16:49:53', NULL, NULL),
(330, 321, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.8376, '{\"low risk\": 0.0748, \"mid risk\": 0.8376, \"high risk\": 0.0876}', 4, NULL, '2026-02-23 08:12:01', NULL, NULL),
(331, 322, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.6214, '{\"low risk\": 0.2099, \"mid risk\": 0.6214, \"high risk\": 0.1687}', 4, NULL, '2026-03-02 14:55:54', NULL, NULL),
(332, 323, 'Quezon City', 'Barangay Payatas', 'high risk', 0.6942, '{\"low risk\": 0.0693, \"mid risk\": 0.2365, \"high risk\": 0.6942}', 4, NULL, '2026-02-28 17:30:33', NULL, NULL),
(333, 324, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.7109, '{\"low risk\": 0.1721, \"mid risk\": 0.7109, \"high risk\": 0.117}', 4, NULL, '2026-03-02 12:43:49', NULL, NULL),
(334, 325, 'Quezon City', 'Barangay Payatas', 'high risk', 0.9072, '{\"low risk\": 0.0292, \"mid risk\": 0.0636, \"high risk\": 0.9072}', 4, NULL, '2026-03-01 10:15:17', NULL, NULL),
(335, 326, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.8905, '{\"low risk\": 0.0647, \"mid risk\": 0.8905, \"high risk\": 0.0448}', 4, NULL, '2026-02-28 10:44:45', NULL, NULL),
(336, 327, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.7462, '{\"low risk\": 0.2311, \"mid risk\": 0.7462, \"high risk\": 0.0227}', 4, NULL, '2026-03-03 11:18:07', NULL, NULL),
(337, 328, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.8635, '{\"low risk\": 0.0802, \"mid risk\": 0.8635, \"high risk\": 0.0563}', 4, NULL, '2026-02-28 09:18:02', NULL, NULL),
(338, 329, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.6712, '{\"low risk\": 0.1689, \"mid risk\": 0.6712, \"high risk\": 0.1599}', 4, NULL, '2026-02-28 14:13:12', NULL, NULL),
(339, 330, 'Quezon City', 'Barangay Payatas', 'high risk', 0.8298, '{\"low risk\": 0.0508, \"mid risk\": 0.1194, \"high risk\": 0.8298}', 4, NULL, '2026-02-26 16:47:37', NULL, NULL),
(340, 331, 'Quezon City', 'Barangay Payatas', 'high risk', 0.9397, '{\"low risk\": 0.0191, \"mid risk\": 0.0412, \"high risk\": 0.9397}', 4, NULL, '2026-02-27 07:06:26', NULL, NULL),
(341, 332, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.7884, '{\"low risk\": 0.0708, \"mid risk\": 0.7884, \"high risk\": 0.1408}', 4, NULL, '2026-03-02 09:26:41', NULL, NULL),
(342, 333, 'Quezon City', 'Barangay Payatas', 'high risk', 0.8028, '{\"low risk\": 0.0453, \"mid risk\": 0.1519, \"high risk\": 0.8028}', 4, NULL, '2026-02-28 12:18:41', NULL, NULL),
(343, 334, 'Quezon City', 'Barangay Payatas', 'low risk', 0.8218, '{\"low risk\": 0.8218, \"mid risk\": 0.0774, \"high risk\": 0.1008}', 4, NULL, '2026-03-03 07:58:58', NULL, NULL),
(344, 335, 'Quezon City', 'Barangay Payatas', 'low risk', 0.6598, '{\"low risk\": 0.6598, \"mid risk\": 0.3077, \"high risk\": 0.0325}', 4, NULL, '2026-03-04 13:50:31', NULL, NULL),
(345, 336, 'Quezon City', 'Barangay Payatas', 'low risk', 0.8966, '{\"low risk\": 0.8966, \"mid risk\": 0.0626, \"high risk\": 0.0408}', 4, NULL, '2026-02-26 07:19:36', NULL, NULL),
(346, 337, 'Quezon City', 'Barangay Payatas', 'high risk', 0.7572, '{\"low risk\": 0.1112, \"mid risk\": 0.1316, \"high risk\": 0.7572}', 4, NULL, '2026-03-03 15:33:31', NULL, NULL),
(347, 338, 'Quezon City', 'Barangay Payatas', 'high risk', 0.8113, '{\"low risk\": 0.056, \"mid risk\": 0.1327, \"high risk\": 0.8113}', 4, NULL, '2026-03-04 08:21:10', NULL, NULL),
(348, 339, 'Quezon City', 'Barangay Payatas', 'high risk', 0.88, '{\"low risk\": 0.0114, \"mid risk\": 0.1086, \"high risk\": 0.88}', 4, NULL, '2026-02-28 11:36:43', NULL, NULL),
(349, 340, 'Quezon City', 'Barangay Payatas', 'high risk', 0.924, '{\"low risk\": 0.0602, \"mid risk\": 0.0158, \"high risk\": 0.924}', 4, NULL, '2026-03-04 13:32:20', NULL, NULL),
(350, 341, 'Quezon City', 'Barangay Payatas', 'low risk', 0.6628, '{\"low risk\": 0.6628, \"mid risk\": 0.089, \"high risk\": 0.2482}', 4, NULL, '2026-02-28 10:00:16', NULL, NULL),
(351, 342, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.6434, '{\"low risk\": 0.1478, \"mid risk\": 0.6434, \"high risk\": 0.2088}', 4, NULL, '2026-03-09 16:00:16', NULL, NULL),
(352, 343, 'Quezon City', 'Barangay Payatas', 'high risk', 0.8717, '{\"low risk\": 0.0167, \"mid risk\": 0.1116, \"high risk\": 0.8717}', 4, NULL, '2026-03-05 14:19:10', NULL, NULL),
(353, 344, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.7259, '{\"low risk\": 0.2416, \"mid risk\": 0.7259, \"high risk\": 0.0325}', 4, NULL, '2026-03-10 11:44:07', NULL, NULL),
(354, 345, 'Quezon City', 'Barangay Payatas', 'high risk', 0.951, '{\"low risk\": 0.0207, \"mid risk\": 0.0283, \"high risk\": 0.951}', 4, NULL, '2026-03-06 10:08:30', NULL, NULL),
(355, 346, 'Quezon City', 'Barangay Payatas', 'low risk', 0.8542, '{\"low risk\": 0.8542, \"mid risk\": 0.102, \"high risk\": 0.0438}', 4, NULL, '2026-03-08 15:58:30', NULL, NULL),
(356, 347, 'Quezon City', 'Barangay Payatas', 'high risk', 0.9066, '{\"low risk\": 0.0706, \"mid risk\": 0.0228, \"high risk\": 0.9066}', 4, NULL, '2026-03-11 10:43:48', NULL, NULL),
(357, 348, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.8256, '{\"low risk\": 0.1101, \"mid risk\": 0.8256, \"high risk\": 0.0643}', 4, NULL, '2026-03-09 12:04:58', NULL, NULL),
(358, 349, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.5716, '{\"low risk\": 0.2518, \"mid risk\": 0.5716, \"high risk\": 0.1766}', 4, NULL, '2026-03-09 10:36:34', NULL, NULL),
(359, 350, 'Quezon City', 'Barangay Payatas', 'low risk', 0.7116, '{\"low risk\": 0.7116, \"mid risk\": 0.1688, \"high risk\": 0.1196}', 4, NULL, '2026-03-05 17:13:45', NULL, NULL),
(360, 351, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.5818, '{\"low risk\": 0.2328, \"mid risk\": 0.5818, \"high risk\": 0.1854}', 4, NULL, '2026-03-11 07:29:08', NULL, NULL),
(361, 352, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.7099, '{\"low risk\": 0.0633, \"mid risk\": 0.7099, \"high risk\": 0.2268}', 4, NULL, '2026-03-08 17:51:19', NULL, NULL),
(362, 353, 'Quezon City', 'Barangay Payatas', 'high risk', 0.7767, '{\"low risk\": 0.176, \"mid risk\": 0.0473, \"high risk\": 0.7767}', 4, NULL, '2026-03-10 10:37:04', NULL, NULL),
(363, 354, 'Quezon City', 'Barangay Payatas', 'low risk', 0.7171, '{\"low risk\": 0.7171, \"mid risk\": 0.0642, \"high risk\": 0.2187}', 4, NULL, '2026-03-05 08:59:30', NULL, NULL),
(364, 355, 'Quezon City', 'Barangay Payatas', 'low risk', 0.739, '{\"low risk\": 0.739, \"mid risk\": 0.2046, \"high risk\": 0.0564}', 4, NULL, '2026-03-11 17:46:41', NULL, NULL),
(365, 356, 'Quezon City', 'Barangay Payatas', 'high risk', 0.7698, '{\"low risk\": 0.1974, \"mid risk\": 0.0328, \"high risk\": 0.7698}', 4, NULL, '2026-03-08 13:24:05', NULL, NULL),
(366, 357, 'Quezon City', 'Barangay Payatas', 'high risk', 0.8618, '{\"low risk\": 0.0737, \"mid risk\": 0.0645, \"high risk\": 0.8618}', 4, NULL, '2026-03-10 12:07:11', NULL, NULL),
(367, 358, 'Quezon City', 'Barangay Payatas', 'high risk', 0.7758, '{\"low risk\": 0.036, \"mid risk\": 0.1882, \"high risk\": 0.7758}', 4, NULL, '2026-03-06 07:48:01', NULL, NULL),
(368, 359, 'Quezon City', 'Barangay Payatas', 'high risk', 0.7982, '{\"low risk\": 0.1407, \"mid risk\": 0.0611, \"high risk\": 0.7982}', 4, NULL, '2026-03-08 15:24:52', NULL, NULL),
(369, 360, 'Quezon City', 'Barangay Payatas', 'low risk', 0.7566, '{\"low risk\": 0.7566, \"mid risk\": 0.0784, \"high risk\": 0.165}', 4, NULL, '2026-03-06 08:02:51', NULL, NULL),
(370, 361, 'Quezon City', 'Barangay Payatas', 'high risk', 0.9345, '{\"low risk\": 0.021, \"mid risk\": 0.0445, \"high risk\": 0.9345}', 4, NULL, '2026-03-12 08:38:02', NULL, NULL),
(371, 362, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.8553, '{\"low risk\": 0.0707, \"mid risk\": 0.8553, \"high risk\": 0.074}', 4, NULL, '2026-03-12 13:28:14', NULL, NULL),
(372, 363, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.8216, '{\"low risk\": 0.0666, \"mid risk\": 0.8216, \"high risk\": 0.1118}', 4, NULL, '2026-03-14 10:52:58', NULL, NULL),
(373, 364, 'Quezon City', 'Barangay Payatas', 'high risk', 0.8413, '{\"low risk\": 0.1033, \"mid risk\": 0.0554, \"high risk\": 0.8413}', 4, NULL, '2026-03-14 10:19:56', NULL, NULL),
(374, 365, 'Quezon City', 'Barangay Payatas', 'low risk', 0.6752, '{\"low risk\": 0.6752, \"mid risk\": 0.1296, \"high risk\": 0.1952}', 4, NULL, '2026-03-12 15:37:56', NULL, NULL),
(375, 366, 'Quezon City', 'Barangay Payatas', 'high risk', 0.8672, '{\"low risk\": 0.0727, \"mid risk\": 0.0601, \"high risk\": 0.8672}', 4, NULL, '2026-03-12 12:41:42', NULL, NULL),
(376, 367, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.7937, '{\"low risk\": 0.1097, \"mid risk\": 0.7937, \"high risk\": 0.0966}', 4, NULL, '2026-03-14 13:11:48', NULL, NULL),
(377, 368, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.6289, '{\"low risk\": 0.3168, \"mid risk\": 0.6289, \"high risk\": 0.0543}', 4, NULL, '2026-03-13 14:58:03', NULL, NULL),
(378, 369, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.7451, '{\"low risk\": 0.0761, \"mid risk\": 0.7451, \"high risk\": 0.1788}', 4, NULL, '2026-03-13 11:13:21', NULL, NULL),
(379, 370, 'Quezon City', 'Barangay Payatas', 'high risk', 0.9211, '{\"low risk\": 0.0154, \"mid risk\": 0.0635, \"high risk\": 0.9211}', 4, NULL, '2026-03-17 10:03:17', NULL, NULL),
(380, 371, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.5638, '{\"low risk\": 0.0774, \"mid risk\": 0.5638, \"high risk\": 0.3588}', 4, NULL, '2026-03-18 16:46:42', NULL, NULL),
(381, 372, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.8543, '{\"low risk\": 0.0785, \"mid risk\": 0.8543, \"high risk\": 0.0672}', 4, NULL, '2026-03-12 10:59:12', NULL, NULL),
(382, 373, 'Quezon City', 'Barangay Payatas', 'high risk', 0.9699, '{\"low risk\": 0.0148, \"mid risk\": 0.0153, \"high risk\": 0.9699}', 4, NULL, '2026-03-13 16:45:13', NULL, NULL),
(383, 374, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.6626, '{\"low risk\": 0.1285, \"mid risk\": 0.6626, \"high risk\": 0.2089}', 4, NULL, '2026-03-15 15:41:22', NULL, NULL),
(384, 375, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.859, '{\"low risk\": 0.0877, \"mid risk\": 0.859, \"high risk\": 0.0533}', 4, NULL, '2026-03-18 14:48:53', NULL, NULL),
(385, 376, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.6595, '{\"low risk\": 0.0628, \"mid risk\": 0.6595, \"high risk\": 0.2777}', 4, NULL, '2026-03-18 10:47:09', NULL, NULL),
(386, 377, 'Quezon City', 'Barangay Payatas', 'low risk', 0.7986, '{\"low risk\": 0.7986, \"mid risk\": 0.1518, \"high risk\": 0.0496}', 4, NULL, '2026-03-14 09:12:21', NULL, NULL),
(387, 378, 'Quezon City', 'Barangay Payatas', 'low risk', 0.6835, '{\"low risk\": 0.6835, \"mid risk\": 0.1912, \"high risk\": 0.1253}', 4, NULL, '2026-03-17 12:16:48', NULL, NULL),
(388, 379, 'Quezon City', 'Barangay Payatas', 'high risk', 0.8553, '{\"low risk\": 0.0712, \"mid risk\": 0.0735, \"high risk\": 0.8553}', 4, NULL, '2026-03-13 12:10:08', NULL, NULL),
(389, 380, 'Quezon City', 'Barangay Payatas', 'high risk', 0.9434, '{\"low risk\": 0.0298, \"mid risk\": 0.0268, \"high risk\": 0.9434}', 4, NULL, '2026-03-17 07:33:02', NULL, NULL),
(390, 381, 'Quezon City', 'Barangay Payatas', 'high risk', 0.9118, '{\"low risk\": 0.0133, \"mid risk\": 0.0749, \"high risk\": 0.9118}', 4, NULL, '2026-03-12 13:08:53', NULL, NULL),
(391, 382, 'Quezon City', 'Barangay Payatas', 'high risk', 0.6983, '{\"low risk\": 0.0716, \"mid risk\": 0.2301, \"high risk\": 0.6983}', 4, NULL, '2026-03-15 12:03:39', NULL, NULL),
(392, 383, 'Quezon City', 'Barangay Payatas', 'high risk', 0.9421, '{\"low risk\": 0.035, \"mid risk\": 0.0229, \"high risk\": 0.9421}', 4, NULL, '2026-03-19 07:34:35', NULL, NULL),
(393, 384, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.5559, '{\"low risk\": 0.3274, \"mid risk\": 0.5559, \"high risk\": 0.1167}', 4, NULL, '2026-03-23 11:01:32', NULL, NULL),
(394, 385, 'Quezon City', 'Barangay Payatas', 'high risk', 0.8657, '{\"low risk\": 0.1021, \"mid risk\": 0.0322, \"high risk\": 0.8657}', 4, NULL, '2026-03-20 08:58:06', NULL, NULL),
(395, 386, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.6343, '{\"low risk\": 0.2395, \"mid risk\": 0.6343, \"high risk\": 0.1262}', 4, NULL, '2026-03-21 12:17:50', NULL, NULL),
(396, 387, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.6806, '{\"low risk\": 0.1553, \"mid risk\": 0.6806, \"high risk\": 0.1641}', 4, NULL, '2026-03-23 10:44:14', NULL, NULL),
(397, 388, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.8398, '{\"low risk\": 0.0581, \"mid risk\": 0.8398, \"high risk\": 0.1021}', 4, NULL, '2026-03-25 17:48:02', NULL, NULL),
(398, 389, 'Quezon City', 'Barangay Payatas', 'low risk', 0.7289, '{\"low risk\": 0.7289, \"mid risk\": 0.1667, \"high risk\": 0.1044}', 4, NULL, '2026-03-19 17:00:44', NULL, NULL),
(399, 390, 'Quezon City', 'Barangay Payatas', 'low risk', 0.77, '{\"low risk\": 0.77, \"mid risk\": 0.1238, \"high risk\": 0.1062}', 4, NULL, '2026-03-25 12:36:54', NULL, NULL),
(400, 391, 'Quezon City', 'Barangay Payatas', 'high risk', 0.9363, '{\"low risk\": 0.0502, \"mid risk\": 0.0135, \"high risk\": 0.9363}', 4, NULL, '2026-03-20 10:57:55', NULL, NULL),
(401, 392, 'Quezon City', 'Barangay Payatas', 'high risk', 0.8314, '{\"low risk\": 0.0503, \"mid risk\": 0.1183, \"high risk\": 0.8314}', 4, NULL, '2026-03-19 17:59:17', NULL, NULL),
(402, 393, 'Quezon City', 'Barangay Payatas', 'high risk', 0.8303, '{\"low risk\": 0.0149, \"mid risk\": 0.1548, \"high risk\": 0.8303}', 4, NULL, '2026-03-25 12:01:13', NULL, NULL),
(403, 394, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.8133, '{\"low risk\": 0.1403, \"mid risk\": 0.8133, \"high risk\": 0.0464}', 4, NULL, '2026-03-22 08:19:10', NULL, NULL),
(404, 395, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.7483, '{\"low risk\": 0.2128, \"mid risk\": 0.7483, \"high risk\": 0.0389}', 4, NULL, '2026-03-24 15:21:24', NULL, NULL),
(405, 396, 'Quezon City', 'Barangay Payatas', 'high risk', 0.6948, '{\"low risk\": 0.2919, \"mid risk\": 0.0133, \"high risk\": 0.6948}', 4, NULL, '2026-03-24 08:32:47', NULL, NULL),
(406, 397, 'Quezon City', 'Barangay Payatas', 'high risk', 0.6672, '{\"low risk\": 0.1467, \"mid risk\": 0.1861, \"high risk\": 0.6672}', 4, NULL, '2026-03-25 08:21:38', NULL, NULL),
(407, 398, 'Quezon City', 'Barangay Payatas', 'high risk', 0.9556, '{\"low risk\": 0.0197, \"mid risk\": 0.0247, \"high risk\": 0.9556}', 4, NULL, '2026-03-25 12:01:40', NULL, NULL),
(408, 399, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.8974, '{\"low risk\": 0.0709, \"mid risk\": 0.8974, \"high risk\": 0.0317}', 4, NULL, '2026-03-21 15:24:27', NULL, NULL),
(409, 400, 'Quezon City', 'Barangay Payatas', 'low risk', 0.7992, '{\"low risk\": 0.7992, \"mid risk\": 0.1035, \"high risk\": 0.0973}', 4, NULL, '2026-03-25 16:18:51', NULL, NULL),
(410, 401, 'Quezon City', 'Barangay Payatas', 'low risk', 0.625, '{\"low risk\": 0.625, \"mid risk\": 0.1344, \"high risk\": 0.2406}', 4, NULL, '2026-03-22 17:09:47', NULL, NULL),
(411, 402, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.6762, '{\"low risk\": 0.0741, \"mid risk\": 0.6762, \"high risk\": 0.2497}', 4, NULL, '2026-03-21 14:23:48', NULL, NULL),
(412, 403, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.5964, '{\"low risk\": 0.1144, \"mid risk\": 0.5964, \"high risk\": 0.2892}', 4, NULL, '2026-03-22 15:35:32', NULL, NULL),
(413, 404, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.5592, '{\"low risk\": 0.1848, \"mid risk\": 0.5592, \"high risk\": 0.256}', 4, NULL, '2026-03-19 16:38:50', NULL, NULL),
(414, 405, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.6857, '{\"low risk\": 0.0715, \"mid risk\": 0.6857, \"high risk\": 0.2428}', 4, NULL, '2026-03-30 10:36:33', NULL, NULL),
(415, 406, 'Quezon City', 'Barangay Payatas', 'low risk', 0.6472, '{\"low risk\": 0.6472, \"mid risk\": 0.1382, \"high risk\": 0.2146}', 4, NULL, '2026-03-29 09:04:10', NULL, NULL),
(416, 407, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.7195, '{\"low risk\": 0.067, \"mid risk\": 0.7195, \"high risk\": 0.2135}', 4, NULL, '2026-03-28 10:46:40', NULL, NULL),
(417, 408, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.7103, '{\"low risk\": 0.0811, \"mid risk\": 0.7103, \"high risk\": 0.2086}', 4, NULL, '2026-03-26 13:21:53', NULL, NULL),
(418, 409, 'Quezon City', 'Barangay Payatas', 'high risk', 0.7555, '{\"low risk\": 0.0851, \"mid risk\": 0.1594, \"high risk\": 0.7555}', 4, NULL, '2026-04-01 17:38:08', NULL, NULL),
(419, 410, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.7934, '{\"low risk\": 0.1202, \"mid risk\": 0.7934, \"high risk\": 0.0864}', 4, NULL, '2026-03-27 13:20:18', NULL, NULL),
(420, 411, 'Quezon City', 'Barangay Payatas', 'high risk', 0.9597, '{\"low risk\": 0.0259, \"mid risk\": 0.0144, \"high risk\": 0.9597}', 4, NULL, '2026-03-28 13:17:52', NULL, NULL),
(421, 412, 'Quezon City', 'Barangay Payatas', 'high risk', 0.9483, '{\"low risk\": 0.0163, \"mid risk\": 0.0354, \"high risk\": 0.9483}', 4, NULL, '2026-03-30 09:43:49', NULL, NULL),
(422, 413, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.7116, '{\"low risk\": 0.2118, \"mid risk\": 0.7116, \"high risk\": 0.0766}', 4, NULL, '2026-03-29 11:53:44', NULL, NULL),
(423, 414, 'Quezon City', 'Barangay Payatas', 'low risk', 0.8703, '{\"low risk\": 0.8703, \"mid risk\": 0.0969, \"high risk\": 0.0328}', 4, NULL, '2026-03-27 17:19:15', NULL, NULL),
(424, 415, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.6981, '{\"low risk\": 0.1412, \"mid risk\": 0.6981, \"high risk\": 0.1607}', 4, NULL, '2026-03-29 16:30:37', NULL, NULL),
(425, 416, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.8721, '{\"low risk\": 0.097, \"mid risk\": 0.8721, \"high risk\": 0.0309}', 4, NULL, '2026-03-30 07:50:23', NULL, NULL),
(426, 417, 'Quezon City', 'Barangay Payatas', 'high risk', 0.84, '{\"low risk\": 0.138, \"mid risk\": 0.022, \"high risk\": 0.84}', 4, NULL, '2026-03-26 10:42:42', NULL, NULL),
(427, 418, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.5655, '{\"low risk\": 0.4184, \"mid risk\": 0.5655, \"high risk\": 0.0161}', 4, NULL, '2026-03-31 17:25:48', NULL, NULL),
(428, 419, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.5871, '{\"low risk\": 0.0848, \"mid risk\": 0.5871, \"high risk\": 0.3281}', 4, NULL, '2026-03-27 15:47:33', NULL, NULL),
(429, 420, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.6274, '{\"low risk\": 0.1669, \"mid risk\": 0.6274, \"high risk\": 0.2057}', 4, NULL, '2026-03-29 17:37:44', NULL, NULL),
(430, 421, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.5586, '{\"low risk\": 0.0899, \"mid risk\": 0.5586, \"high risk\": 0.3515}', 4, NULL, '2026-03-29 08:07:53', NULL, NULL),
(431, 422, 'Quezon City', 'Barangay Payatas', 'high risk', 0.7498, '{\"low risk\": 0.1148, \"mid risk\": 0.1354, \"high risk\": 0.7498}', 4, NULL, '2026-03-27 15:30:22', NULL, NULL),
(432, 423, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.7939, '{\"low risk\": 0.0966, \"mid risk\": 0.7939, \"high risk\": 0.1095}', 4, NULL, '2026-04-04 07:51:45', NULL, NULL),
(433, 424, 'Quezon City', 'Barangay Payatas', 'low risk', 0.8887, '{\"low risk\": 0.8887, \"mid risk\": 0.0832, \"high risk\": 0.0281}', 4, NULL, '2026-04-07 09:59:47', NULL, NULL),
(434, 425, 'Quezon City', 'Barangay Payatas', 'low risk', 0.6593, '{\"low risk\": 0.6593, \"mid risk\": 0.095, \"high risk\": 0.2457}', 4, NULL, '2026-04-06 13:29:14', NULL, NULL),
(435, 426, 'Quezon City', 'Barangay Payatas', 'high risk', 0.8515, '{\"low risk\": 0.034, \"mid risk\": 0.1145, \"high risk\": 0.8515}', 4, NULL, '2026-04-07 13:25:01', NULL, NULL),
(436, 427, 'Quezon City', 'Barangay Payatas', 'high risk', 0.9326, '{\"low risk\": 0.0313, \"mid risk\": 0.0361, \"high risk\": 0.9326}', 4, NULL, '2026-04-05 13:00:45', NULL, NULL),
(437, 428, 'Quezon City', 'Barangay Payatas', 'low risk', 0.6946, '{\"low risk\": 0.6946, \"mid risk\": 0.2227, \"high risk\": 0.0827}', 4, NULL, '2026-04-08 08:36:06', NULL, NULL),
(438, 429, 'Quezon City', 'Barangay Payatas', 'high risk', 0.8218, '{\"low risk\": 0.0679, \"mid risk\": 0.1103, \"high risk\": 0.8218}', 4, NULL, '2026-04-03 14:07:16', NULL, NULL),
(439, 430, 'Quezon City', 'Barangay Payatas', 'high risk', 0.8066, '{\"low risk\": 0.1775, \"mid risk\": 0.0159, \"high risk\": 0.8066}', 4, NULL, '2026-04-04 16:24:39', NULL, NULL),
(440, 431, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.59, '{\"low risk\": 0.1732, \"mid risk\": 0.59, \"high risk\": 0.2368}', 4, NULL, '2026-04-05 16:11:52', NULL, NULL),
(441, 432, 'Quezon City', 'Barangay Payatas', 'high risk', 0.9062, '{\"low risk\": 0.0778, \"mid risk\": 0.016, \"high risk\": 0.9062}', 4, NULL, '2026-04-06 08:43:08', NULL, NULL),
(442, 433, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.6335, '{\"low risk\": 0.0858, \"mid risk\": 0.6335, \"high risk\": 0.2807}', 4, NULL, '2026-04-04 09:32:24', NULL, NULL),
(443, 434, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.5983, '{\"low risk\": 0.1811, \"mid risk\": 0.5983, \"high risk\": 0.2206}', 4, NULL, '2026-04-03 14:40:34', NULL, NULL),
(444, 435, 'Quezon City', 'Barangay Payatas', 'high risk', 0.8565, '{\"low risk\": 0.1335, \"mid risk\": 0.01, \"high risk\": 0.8565}', 4, NULL, '2026-04-06 09:31:18', NULL, NULL),
(445, 436, 'Quezon City', 'Barangay Payatas', 'low risk', 0.707, '{\"low risk\": 0.707, \"mid risk\": 0.1551, \"high risk\": 0.1379}', 4, NULL, '2026-04-02 12:00:31', NULL, NULL),
(446, 437, 'Quezon City', 'Barangay Payatas', 'low risk', 0.8772, '{\"low risk\": 0.8772, \"mid risk\": 0.1105, \"high risk\": 0.0123}', 4, NULL, '2026-04-06 17:31:26', NULL, NULL),
(447, 438, 'Quezon City', 'Barangay Payatas', 'high risk', 0.783, '{\"low risk\": 0.1755, \"mid risk\": 0.0415, \"high risk\": 0.783}', 4, NULL, '2026-04-05 09:05:36', NULL, NULL),
(448, 439, 'Quezon City', 'Barangay Payatas', 'low risk', 0.8588, '{\"low risk\": 0.8588, \"mid risk\": 0.0737, \"high risk\": 0.0675}', 4, NULL, '2026-04-04 10:34:18', NULL, NULL),
(449, 440, 'Quezon City', 'Barangay Payatas', 'low risk', 0.7555, '{\"low risk\": 0.7555, \"mid risk\": 0.187, \"high risk\": 0.0575}', 4, NULL, '2026-04-08 14:35:32', NULL, NULL),
(450, 441, 'Quezon City', 'Barangay Payatas', 'low risk', 0.6388, '{\"low risk\": 0.6388, \"mid risk\": 0.2835, \"high risk\": 0.0777}', 4, NULL, '2026-04-08 12:34:52', NULL, NULL),
(451, 442, 'Quezon City', 'Barangay Payatas', 'high risk', 0.8932, '{\"low risk\": 0.0483, \"mid risk\": 0.0585, \"high risk\": 0.8932}', 4, NULL, '2026-04-03 13:06:47', NULL, NULL),
(452, 443, 'Quezon City', 'Barangay Payatas', 'high risk', 0.9698, '{\"low risk\": 0.0146, \"mid risk\": 0.0156, \"high risk\": 0.9698}', 4, NULL, '2026-04-11 08:28:55', NULL, NULL),
(453, 444, 'Quezon City', 'Barangay Payatas', 'high risk', 0.7258, '{\"low risk\": 0.2161, \"mid risk\": 0.0581, \"high risk\": 0.7258}', 4, NULL, '2026-04-13 12:55:45', NULL, NULL),
(454, 445, 'Quezon City', 'Barangay Payatas', 'high risk', 0.8472, '{\"low risk\": 0.1142, \"mid risk\": 0.0386, \"high risk\": 0.8472}', 4, NULL, '2026-04-15 10:51:03', NULL, NULL),
(455, 446, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.7589, '{\"low risk\": 0.0805, \"mid risk\": 0.7589, \"high risk\": 0.1606}', 4, NULL, '2026-04-14 11:18:36', NULL, NULL),
(456, 447, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.8949, '{\"low risk\": 0.0806, \"mid risk\": 0.8949, \"high risk\": 0.0245}', 4, NULL, '2026-04-09 09:48:50', NULL, NULL),
(457, 448, 'Quezon City', 'Barangay Payatas', 'high risk', 0.7387, '{\"low risk\": 0.1683, \"mid risk\": 0.093, \"high risk\": 0.7387}', 4, NULL, '2026-04-14 09:15:09', NULL, NULL),
(458, 449, 'Quezon City', 'Barangay Payatas', 'high risk', 0.8937, '{\"low risk\": 0.0684, \"mid risk\": 0.0379, \"high risk\": 0.8937}', 4, NULL, '2026-04-12 09:36:40', NULL, NULL),
(459, 450, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.7082, '{\"low risk\": 0.1969, \"mid risk\": 0.7082, \"high risk\": 0.0949}', 4, NULL, '2026-04-15 08:25:32', NULL, NULL),
(460, 451, 'Quezon City', 'Barangay Payatas', 'high risk', 0.9307, '{\"low risk\": 0.0324, \"mid risk\": 0.0369, \"high risk\": 0.9307}', 4, NULL, '2026-04-12 12:37:00', NULL, NULL),
(461, 452, 'Quezon City', 'Barangay Payatas', 'high risk', 0.6798, '{\"low risk\": 0.3018, \"mid risk\": 0.0184, \"high risk\": 0.6798}', 4, NULL, '2026-04-14 17:44:22', NULL, NULL),
(462, 453, 'Quezon City', 'Barangay Payatas', 'high risk', 0.7773, '{\"low risk\": 0.2062, \"mid risk\": 0.0165, \"high risk\": 0.7773}', 4, NULL, '2026-04-15 10:31:17', NULL, NULL),
(463, 454, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.7416, '{\"low risk\": 0.0759, \"mid risk\": 0.7416, \"high risk\": 0.1825}', 4, NULL, '2026-04-15 14:50:21', NULL, NULL),
(464, 455, 'Quezon City', 'Barangay Payatas', 'high risk', 0.6647, '{\"low risk\": 0.2074, \"mid risk\": 0.1279, \"high risk\": 0.6647}', 4, NULL, '2026-04-15 14:01:07', NULL, NULL),
(465, 456, 'Quezon City', 'Barangay Payatas', 'high risk', 0.7961, '{\"low risk\": 0.1924, \"mid risk\": 0.0115, \"high risk\": 0.7961}', 4, NULL, '2026-04-12 10:44:58', NULL, NULL),
(466, 457, 'Quezon City', 'Barangay Payatas', 'low risk', 0.6541, '{\"low risk\": 0.6541, \"mid risk\": 0.3167, \"high risk\": 0.0292}', 4, NULL, '2026-04-11 08:41:23', NULL, NULL),
(467, 458, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.8663, '{\"low risk\": 0.0622, \"mid risk\": 0.8663, \"high risk\": 0.0715}', 4, NULL, '2026-04-12 17:19:46', NULL, NULL),
(468, 459, 'Quezon City', 'Barangay Payatas', 'high risk', 0.8598, '{\"low risk\": 0.0946, \"mid risk\": 0.0456, \"high risk\": 0.8598}', 4, NULL, '2026-04-09 10:30:04', NULL, NULL),
(469, 460, 'Quezon City', 'Barangay Payatas', 'low risk', 0.8312, '{\"low risk\": 0.8312, \"mid risk\": 0.051, \"high risk\": 0.1178}', 4, NULL, '2026-04-14 12:52:07', NULL, NULL),
(470, 461, 'Quezon City', 'Barangay Payatas', 'high risk', 0.6923, '{\"low risk\": 0.0304, \"mid risk\": 0.2773, \"high risk\": 0.6923}', 4, NULL, '2026-04-12 08:46:51', NULL, NULL),
(471, 462, 'Quezon City', 'Barangay Payatas', 'low risk', 0.8995, '{\"low risk\": 0.8995, \"mid risk\": 0.0556, \"high risk\": 0.0449}', 4, NULL, '2026-04-15 09:41:43', NULL, NULL),
(472, 463, 'Quezon City', 'Barangay Payatas', 'low risk', 0.6029, '{\"low risk\": 0.6029, \"mid risk\": 0.2691, \"high risk\": 0.128}', 4, NULL, '2026-04-12 12:46:13', NULL, NULL),
(473, 464, 'Quezon City', 'Barangay Payatas', 'high risk', 0.7817, '{\"low risk\": 0.1461, \"mid risk\": 0.0722, \"high risk\": 0.7817}', 4, NULL, '2026-04-15 10:05:57', NULL, NULL),
(474, 465, 'Quezon City', 'Barangay Payatas', 'low risk', 0.8005, '{\"low risk\": 0.8005, \"mid risk\": 0.1036, \"high risk\": 0.0959}', 4, NULL, '2026-04-12 12:50:08', NULL, NULL),
(475, 466, 'Quezon City', 'Barangay Payatas', 'low risk', 0.6844, '{\"low risk\": 0.6844, \"mid risk\": 0.2036, \"high risk\": 0.112}', 4, NULL, '2026-04-14 16:18:49', NULL, NULL),
(476, 467, 'Quezon City', 'Barangay Payatas', 'high risk', 0.9211, '{\"low risk\": 0.0616, \"mid risk\": 0.0173, \"high risk\": 0.9211}', 4, NULL, '2026-04-10 15:38:32', NULL, NULL),
(477, 468, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.7762, '{\"low risk\": 0.1784, \"mid risk\": 0.7762, \"high risk\": 0.0454}', 4, NULL, '2026-04-22 10:31:24', NULL, NULL),
(478, 469, 'Quezon City', 'Barangay Payatas', 'low risk', 0.7699, '{\"low risk\": 0.7699, \"mid risk\": 0.151, \"high risk\": 0.0791}', 4, NULL, '2026-04-20 07:23:59', NULL, NULL),
(479, 470, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.6854, '{\"low risk\": 0.2636, \"mid risk\": 0.6854, \"high risk\": 0.051}', 4, NULL, '2026-04-19 12:34:43', NULL, NULL),
(480, 471, 'Quezon City', 'Barangay Payatas', 'low risk', 0.7601, '{\"low risk\": 0.7601, \"mid risk\": 0.2276, \"high risk\": 0.0123}', 4, NULL, '2026-04-16 07:37:04', NULL, NULL),
(481, 472, 'Quezon City', 'Barangay Payatas', 'low risk', 0.6734, '{\"low risk\": 0.6734, \"mid risk\": 0.296, \"high risk\": 0.0306}', 4, NULL, '2026-04-22 13:32:18', NULL, NULL),
(482, 473, 'Quezon City', 'Barangay Payatas', 'high risk', 0.8103, '{\"low risk\": 0.0805, \"mid risk\": 0.1092, \"high risk\": 0.8103}', 4, NULL, '2026-04-21 13:05:40', NULL, NULL),
(483, 474, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.7656, '{\"low risk\": 0.0984, \"mid risk\": 0.7656, \"high risk\": 0.136}', 4, NULL, '2026-04-18 08:32:58', NULL, NULL),
(484, 475, 'Quezon City', 'Barangay Payatas', 'low risk', 0.8747, '{\"low risk\": 0.8747, \"mid risk\": 0.085, \"high risk\": 0.0403}', 4, NULL, '2026-04-19 16:40:48', NULL, NULL),
(485, 476, 'Quezon City', 'Barangay Payatas', 'high risk', 0.8679, '{\"low risk\": 0.0174, \"mid risk\": 0.1147, \"high risk\": 0.8679}', 4, NULL, '2026-04-21 13:46:54', NULL, NULL),
(486, 477, 'Quezon City', 'Barangay Payatas', 'low risk', 0.6831, '{\"low risk\": 0.6831, \"mid risk\": 0.2875, \"high risk\": 0.0294}', 4, NULL, '2026-04-16 14:39:38', NULL, NULL),
(487, 478, 'Quezon City', 'Barangay Payatas', 'low risk', 0.8265, '{\"low risk\": 0.8265, \"mid risk\": 0.1247, \"high risk\": 0.0488}', 4, NULL, '2026-04-17 08:00:45', NULL, NULL),
(488, 479, 'Quezon City', 'Barangay Payatas', 'low risk', 0.6039, '{\"low risk\": 0.6039, \"mid risk\": 0.2168, \"high risk\": 0.1793}', 4, NULL, '2026-04-18 15:33:50', NULL, NULL),
(489, 480, 'Quezon City', 'Barangay Payatas', 'high risk', 0.733, '{\"low risk\": 0.1019, \"mid risk\": 0.1651, \"high risk\": 0.733}', 4, NULL, '2026-04-21 11:46:56', NULL, NULL),
(490, 481, 'Quezon City', 'Barangay Payatas', 'low risk', 0.906, '{\"low risk\": 0.906, \"mid risk\": 0.0614, \"high risk\": 0.0326}', 4, NULL, '2026-04-19 11:33:04', NULL, NULL),
(491, 482, 'Quezon City', 'Barangay Payatas', 'mid risk', 0.8913, '{\"low risk\": 0.0806, \"mid risk\": 0.8913, \"high risk\": 0.0281}', 4, NULL, '2026-04-19 14:32:23', NULL, NULL),
(492, 483, 'Quezon City', 'Barangay Payatas', 'low risk', 0.8922, '{\"low risk\": 0.8922, \"mid risk\": 0.058, \"high risk\": 0.0498}', 4, NULL, '2026-04-17 11:49:06', NULL, NULL),
(493, 484, 'Quezon City', 'Barangay Bagong Silangan', 'high risk', 0.887, '{\"low risk\": 0.0102, \"mid risk\": 0.1028, \"high risk\": 0.887}', 4, NULL, '2026-02-18 07:15:02', NULL, NULL),
(494, 485, 'Quezon City', 'Barangay Bagong Silangan', 'mid risk', 0.6025, '{\"low risk\": 0.3643, \"mid risk\": 0.6025, \"high risk\": 0.0332}', 4, NULL, '2026-02-18 07:35:51', NULL, NULL),
(495, 486, 'Quezon City', 'Barangay Bagong Silangan', 'high risk', 0.7842, '{\"low risk\": 0.073, \"mid risk\": 0.1428, \"high risk\": 0.7842}', 4, NULL, '2026-02-15 12:17:52', NULL, NULL),
(496, 487, 'Quezon City', 'Barangay Bagong Silangan', 'low risk', 0.7707, '{\"low risk\": 0.7707, \"mid risk\": 0.2027, \"high risk\": 0.0266}', 4, NULL, '2026-02-13 10:51:33', NULL, NULL),
(497, 488, 'Quezon City', 'Barangay Bagong Silangan', 'high risk', 0.6721, '{\"low risk\": 0.1532, \"mid risk\": 0.1747, \"high risk\": 0.6721}', 4, NULL, '2026-02-14 11:20:05', NULL, NULL),
(498, 489, 'Quezon City', 'Barangay Bagong Silangan', 'high risk', 0.7142, '{\"low risk\": 0.1599, \"mid risk\": 0.1259, \"high risk\": 0.7142}', 4, NULL, '2026-02-14 16:15:29', NULL, NULL),
(499, 490, 'Quezon City', 'Barangay Bagong Silangan', 'low risk', 0.9392, '{\"low risk\": 0.9392, \"mid risk\": 0.0506, \"high risk\": 0.0102}', 4, NULL, '2026-02-17 15:48:50', NULL, NULL),
(500, 491, 'Quezon City', 'Barangay Bagong Silangan', 'high risk', 0.7812, '{\"low risk\": 0.0563, \"mid risk\": 0.1625, \"high risk\": 0.7812}', 4, NULL, '2026-02-14 17:53:44', NULL, NULL),
(2269, 31, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.8284, '{\"low risk\": 0.1591, \"mid risk\": 0.8284, \"high risk\": 0.0125}', 4, NULL, '2026-04-23 06:36:27', 'Barangay Batasan Hills, Quezon City', NULL),
(2270, 31, NULL, NULL, 'mid risk', 0.8284, '{\"low risk\": 0.1591, \"mid risk\": 0.8284, \"high risk\": 0.0125}', 5, NULL, '2026-04-23 06:38:03', NULL, NULL),
(2271, 2, 'Quezon City', 'Barangay Bagong Silangan', 'high risk', 0.9175, '{\"low risk\": 0.0235, \"mid risk\": 0.059, \"high risk\": 0.9175}', 5, NULL, '2026-04-23 06:42:59', 'Barangay Bagong Silangan, Quezon City', NULL),
(2272, 7, 'Quezon City', 'Barangay Payatas', 'high risk', 0.9023, '{\"low risk\": 0.0237, \"mid risk\": 0.074, \"high risk\": 0.9023}', 5, NULL, '2026-04-23 06:43:52', 'Barangay Payatas, Quezon City', NULL),
(2273, 7, 'Quezon City', 'Barangay Payatas', 'high risk', 0.9023, '{\"low risk\": 0.0237, \"mid risk\": 0.074, \"high risk\": 0.9023}', 5, NULL, '2026-04-23 06:44:02', 'Barangay Payatas, Quezon City', NULL),
(2274, 41, 'Quezon City', 'Barangay Batasan Hills', 'mid risk', 0.8069, '{\"low risk\": 0.1796, \"mid risk\": 0.8069, \"high risk\": 0.0135}', 5, NULL, '2026-04-23 07:24:24', 'Barangay Batasan Hills, Quezon City', NULL),
(2275, 2, 'Quezon City', 'Barangay Batasan Hills', 'high risk', 0.9175, '{\"low risk\": 0.0235, \"mid risk\": 0.059, \"high risk\": 0.9175}', 5, NULL, '2026-04-23 07:27:39', 'Barangay Batasan Hills, Quezon City', NULL),
(2276, 2, 'Quezon City', 'Barangay Commonwealth', 'high risk', 0.9175, '{\"low risk\": 0.0235, \"mid risk\": 0.059, \"high risk\": 0.9175}', 5, NULL, '2026-04-23 13:45:18', 'Barangay Commonwealth, Quezon City', NULL),
(2277, 2, NULL, NULL, 'high risk', 0.9175, '{\"low risk\": 0.0235, \"mid risk\": 0.059, \"high risk\": 0.9175}', 5, NULL, '2026-04-23 13:51:32', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `role` enum('nurse','doctor','admin') NOT NULL DEFAULT 'nurse',
  `password_hash` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `full_name`, `role`, `password_hash`, `is_active`, `created_at`) VALUES
(1, 'admin', 'Admin User', 'admin', '$2y$10$EUj9zVTNOGnPFsc4K4Qif.jahARG8dSHJ4FxRtZ.I0O7hLyiWY9oa', 1, '2026-04-22 09:51:07'),
(2, 'nurse1', 'Maria Santos 1', 'nurse', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2026-04-22 11:13:50'),
(3, 'doctor1', 'Dr. Jose Reyes', 'doctor', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2026-04-22 11:13:50'),
(4, 'nurse2', 'Ana Cruz', 'nurse', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2026-04-22 11:13:50'),
(11, 'test', 'Test 2', 'nurse', '$2y$10$4GaJIKhy2QBXrasrBLv3I.pP1wntVso4qes8cCm3X.2v3RiVYo/9y', 1, '2026-04-27 10:07:39');

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_community_risk`
-- (See below for the actual view)
--
CREATE TABLE `vw_community_risk` (
`municipality` varchar(100)
,`barangay` varchar(150)
,`community` varchar(200)
,`total_patients` bigint(21)
,`low_count` bigint(21)
,`mid_count` bigint(21)
,`high_count` bigint(21)
,`high_risk_pct` decimal(25,1)
,`last_prediction_at` datetime
);

-- --------------------------------------------------------

--
-- Structure for view `vw_community_risk`
--
DROP TABLE IF EXISTS `vw_community_risk`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_community_risk`  AS SELECT `p`.`municipality` AS `municipality`, `p`.`barangay` AS `barangay`, `p`.`community` AS `community`, count(distinct `p`.`patient_id`) AS `total_patients`, count(distinct case when `p`.`risk_level` = 'low risk' then `p`.`patient_id` end) AS `low_count`, count(distinct case when `p`.`risk_level` = 'mid risk' then `p`.`patient_id` end) AS `mid_count`, count(distinct case when `p`.`risk_level` = 'high risk' then `p`.`patient_id` end) AS `high_count`, round(100.0 * count(distinct case when `p`.`risk_level` = 'high risk' then `p`.`patient_id` end) / nullif(count(distinct `p`.`patient_id`),0),1) AS `high_risk_pct`, max(`p`.`created_at`) AS `last_prediction_at` FROM `predictions` AS `p` WHERE `p`.`patient_id` is not null AND `p`.`municipality` is not null AND `p`.`municipality` <> '' AND `p`.`barangay` is not null AND `p`.`barangay` <> '' AND `p`.`id` = (select max(`p2`.`id`) from `predictions` `p2` where `p2`.`patient_id` = `p`.`patient_id` AND `p2`.`municipality` = `p`.`municipality` AND `p2`.`barangay` = `p`.`barangay`) GROUP BY `p`.`municipality`, `p`.`barangay`, `p`.`community` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alerts`
--
ALTER TABLE `alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_alerts_patient` (`patient_id`),
  ADD KEY `idx_alerts_resolved` (`is_resolved`),
  ADD KEY `fk_alerts_prediction` (`prediction_id`),
  ADD KEY `fk_alerts_resolved_by` (`resolved_by`);

--
-- Indexes for table `communities`
--
ALTER TABLE `communities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_community` (`municipality`,`barangay`),
  ADD KEY `idx_comm_region` (`region`),
  ADD KEY `idx_comm_municipality` (`municipality`),
  ADD KEY `idx_comm_ses` (`socioeconomic_index`);
ALTER TABLE `communities` ADD FULLTEXT KEY `ft_comm_search` (`municipality`,`barangay`,`community`);

--
-- Indexes for table `health_facilities`
--
ALTER TABLE `health_facilities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_fac_municipality` (`municipality`),
  ADD KEY `idx_fac_barangay` (`barangay`),
  ADD KEY `idx_fac_type` (`facility_type`),
  ADD KEY `idx_fac_coords` (`latitude`,`longitude`);
ALTER TABLE `health_facilities` ADD FULLTEXT KEY `ft_fac_search` (`name`,`municipality`,`barangay`,`community`);

--
-- Indexes for table `health_records`
--
ALTER TABLE `health_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_hr_patient` (`patient_id`),
  ADD KEY `fk_hr_recorded_by` (`recorded_by`),
  ADD KEY `idx_health_records_patient_id` (`patient_id`),
  ADD KEY `idx_health_records_recorded_at` (`recorded_at`);

--
-- Indexes for table `model_versions`
--
ALTER TABLE `model_versions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_model_version_name` (`version_name`),
  ADD KEY `fk_mv_created_by` (`created_by`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_patients_code` (`patient_code`),
  ADD KEY `fk_patients_created_by` (`created_by`);

--
-- Indexes for table `predictions`
--
ALTER TABLE `predictions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_predictions_patient` (`patient_id`),
  ADD KEY `idx_predictions_risk` (`risk_level`),
  ADD KEY `fk_pred_model` (`model_version_id`),
  ADD KEY `fk_pred_recorded_by` (`recorded_by`),
  ADD KEY `idx_pred_muni_brgy` (`municipality`,`barangay`),
  ADD KEY `idx_predictions_patient_id` (`patient_id`),
  ADD KEY `idx_predictions_location` (`municipality`,`barangay`),
  ADD KEY `idx_predictions_created_at` (`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alerts`
--
ALTER TABLE `alerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `communities`
--
ALTER TABLE `communities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `health_facilities`
--
ALTER TABLE `health_facilities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `health_records`
--
ALTER TABLE `health_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `model_versions`
--
ALTER TABLE `model_versions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4127;

--
-- AUTO_INCREMENT for table `predictions`
--
ALTER TABLE `predictions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2278;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alerts`
--
ALTER TABLE `alerts`
  ADD CONSTRAINT `fk_alerts_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_alerts_prediction` FOREIGN KEY (`prediction_id`) REFERENCES `predictions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_alerts_resolved_by` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `health_records`
--
ALTER TABLE `health_records`
  ADD CONSTRAINT `fk_hr_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_hr_recorded_by` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `model_versions`
--
ALTER TABLE `model_versions`
  ADD CONSTRAINT `fk_mv_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `fk_patients_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `predictions`
--
ALTER TABLE `predictions`
  ADD CONSTRAINT `fk_pred_model` FOREIGN KEY (`model_version_id`) REFERENCES `model_versions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_pred_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_pred_recorded_by` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
