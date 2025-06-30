-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 12, 2025 at 03:43 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `meditrack`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `name`, `email`, `password`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@gmail.com', '123', '2025-04-21 13:57:16', '2025-04-21 13:57:16');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int NOT NULL,
  `patient_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `appointment_date` datetime NOT NULL,
  `status` enum('Scheduled','Completed','Cancelled') COLLATE utf8mb4_general_ci DEFAULT 'Scheduled',
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `patient_id`, `doctor_id`, `appointment_date`, `status`, `notes`, `created_at`) VALUES
(20, 3, 2, '2025-05-20 15:00:00', 'Scheduled', 'نعم\r\n', '2025-05-05 11:44:31');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `doctor_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `specialty` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `contact_number` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `hospital_affiliation` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `qualification` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `experience` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('active','inactive') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`doctor_id`, `name`, `specialty`, `contact_number`, `email`, `hospital_affiliation`, `password`, `address`, `qualification`, `experience`, `created_at`, `status`) VALUES
(2, 'د. علاء حامد', 'طب الأطفال', '0107654321', 'dralaahamed@example.com', 'مستشفى ديرب نجم العام', '1015', 'شارع صيدلية بركه', 'استشاري طب الاطفال', 45, '2025-04-28 09:44:01', 'active'),
(3, 'د. كمال فرج', 'باطنه', '0112345678', 'kamalfareg@example.com', 'مستشفي درب نجم', '8888', 'شارع المستوصف', 'اخصائي طب الباطنه', 45, '2025-04-28 09:44:01', 'active'),
(4, 'د. سليم عماد', 'طب الأسنان', '0119876543', 'drselimemad@example.com', 'عيادة الأسنان بالزقازيق', '2910', 'ش النصر', 'اخصائي طب الاسنان', 10, '2025-04-28 09:44:01', 'active'),
(5, 'د. احمد حسني', 'جراحة تجميل', '0101122334', 'drahmedhosny@example.com', 'مستشفى الجامعه', '2222', 'برج الخميسي', 'اخصائي جلديه', 18, '2025-04-28 09:44:01', 'active'),
(6, 'د.محمود نجم', 'نساء وتوليد', '0102233445', 'drmahmoudnegm@example.com', 'مركز الحياه', '3333', 'ش النصر', 'اخصائي نساء وتوليد', 15, '2025-04-28 09:44:01', 'active'),
(7, 'د. عماد عبدالحميد', 'باطنه وجهلز هضمي', '0105566778', 'dremadabdelhamid@example.com', 'مستشفي الجامعه', '8899', 'بجانب الرشيدي', 'استشاري جهلز هضمي', 20, '2025-04-28 09:44:01', 'active'),
(8, 'د.ايمن المصري', 'طب عيون', '0103344556', 'draymenelmasry@gmail.com', 'مستشفي الفتح للعيون', '1020', 'برج الخميسي', 'استشاره طب وحراحة العيون', 35, '2025-04-28 09:44:01', 'active'),
(9, 'د. عادل العوضي', 'طب الأورام', '0106677889', 'dradelelawdy@gmail.com', 'مستشفى الجامعه', '2525', 'ش النصر', 'مدرس واستشاري جراحة الاورام', 25, '2025-04-28 09:44:01', 'inactive'),
(10, 'د. علاء خليل', 'طب الاورام', '0107766991', 'dralaa@gmail.com', 'مستشفى الجامعه', '7744', 'بجوار المستشفي العام', 'استاذ الاورام بكلية الطب', 40, '2025-04-28 09:44:01', 'active'),
(11, 'د.محمد ماهر', 'امراض القلب', '01200240708', 'drmohamedmaher@gmail.com', 'مستشفي ديرب نجم', '1234', 'ش النصر', 'اخصائي قلب', 20, '2025-04-28 09:44:01', 'active'),
(12, 'د.احمد شفيع', 'امراض القلب', '01254789634', 'drahmeds@gmail.com', 'مستشفي الجامعه', '5525', 'طريق الابراهميه', 'استشاري قلب ', 20, '2025-04-28 09:44:01', 'active'),
(13, 'د.محمد علي', 'جراحة العظام', '01258774496', 'drmohamedali@gmail.com', 'مستشفي ديرب نجم', '2511', 'ش النصر', 'اخصائي جراحة العظام', 20, '2025-04-28 09:44:01', 'active'),
(14, 'د.مياده موسي', 'غدد صماء', '01587498876', 'drmayadamousa@gmail.com', 'مستشفي الجامعه', '9966', 'ش النصر', 'اسنشاري غدد صماء وسكر', 30, '2025-04-28 09:44:01', 'active'),
(15, 'د.محمد جابر', 'غدد صماء وسكر', '01558974631', 'drmohamedgaber@gmail.com', 'مستشفي ديرب نجم', '4455', 'ش النصر', 'استاذ مساعد في الغدد الصماء والسكر', 15, '2025-04-28 09:44:01', 'active'),
(16, 'د.عادل حنفي', 'مخ واعصاب', '01225899631', 'dradelhanfy@gmail.com', 'مستشفي ديرب نجم', '2524', 'ش النصر', 'اخصائي مخ واعصاب', 20, '2025-04-28 09:44:01', 'active'),
(17, 'د.محمود مصطفي ', 'مخ واعصاب', '01055478896', 'drmahmoudmostafa@gmail.com', 'ممستشفي ديرب نجم', '1012', 'شارع المعهد الديني', 'اخصائي مخ واعصاب', 17, '2025-04-28 09:44:01', 'active'),
(18, 'د.احمد فاروق', 'قلب واوعيه دمويه', '01225899634', 'drahmedfarouk@gmail.com', 'مستشفي المبره', '5998', 'شارع الميكنه الزراعيه', 'استشاري قلب واوعيه دمويه', 25, '2025-04-28 09:44:01', 'active'),
(19, 'د.احمد ابراهيم', 'انف واذن', '01554789634', 'drahmedibrihem@gmail.com', 'مستشفي التيسير', '1455', 'ش النصر', 'اخصائي انف واذن', 15, '2025-04-28 09:44:01', 'active'),
(20, 'د.السعيد كامل', 'انف واذن', '01258774963', 'drelsaidkamel@gmail.com', 'مستشفي ديرب نجم', '7878', 'شارع احمد عرابي', 'استشاري انف واذن', 45, '2025-04-28 09:44:01', 'active'),
(21, 'د.شريف شرف', 'قلب وقسطره وعلاج حساسية الصدر', '01558744963', 'drsherifsharf@gmail.com', 'مستشفي الجامعه', '7894', 'امام مصر للتامين', 'استاذ الامراض الصدريه', 20, '2025-04-28 09:44:01', 'active'),
(22, 'د.ايهاب صبري', 'جلراحة القلب والصدر', '01002589631', 'drehabsabry@gmail.com', 'مستشفي صلاح سالم', '2589', 'ش النصر', 'استشاري جراحة القلب والصدر', 39, '2025-04-28 09:44:01', 'active'),
(23, 'د.اشرف الشوري', 'الامراض الصدريه', '01556998743', 'drashraf@gmail.com', 'مستشفي ديرب نجم', '9632', 'ش النصر', 'استشاري الامراض الصدريه', 50, '2025-04-28 09:44:01', 'active'),
(24, 'د.رفعت عبدالفتاح', 'حميات', '01115889634', 'drrefatabdelftah@gmail.com', 'مستشفي ديرب نجم', '9631', 'ش النصر', 'استشاري حميات', 40, '2025-04-28 09:44:01', 'active'),
(25, 'د.عمرو شعبان', 'جهاز هضمي', '01254789631', 'dramrshaban@gmail.com', 'مستشفي الجامعه', '7415', 'ش النصر', 'استشاري جهاز هضمي', 25, '2025-04-28 09:44:01', 'active'),
(26, 'د.اسلام عبدالرحمن', 'طب الاسنان', '01556988314', 'dreslamabdelrhman@gmail.com', 'مستشفي ديرب', '2584', 'شارع المرو', 'اخصائي طب الاسنان', 25, '2025-04-28 09:44:01', 'active'),
(27, 'د.محمد سعيد', 'نساوتوليد', '01045879631', 'drmohamedsaed@gmail.com', 'مستشفي ديرب نجم', '9654', 'ش التربه', 'اخصائي نساوتوليد', 15, '2025-04-28 09:44:01', 'active'),
(28, 'د.زينب شعبان', 'نساوتوليد', '01178965412', 'drzeinbsahban@gmail.com', 'مستشفي ديرب نجم', '2475', 'ش النصر ', 'استشاري امراض النسا وعلاج العقم', 20, '2025-04-28 09:44:01', 'active'),
(29, 'د.عز السيد', 'نسا وتوليد', '01225897463', 'drezzelsayed@gmail.com', 'مستشفي ديرب نجم', '1205', 'اول طريق الابراهميه', 'اخصائي نساوتوليد', 20, '2025-04-28 09:44:01', 'active'),
(30, 'د.محمد يوسف', 'الجراحه العامه', '01147752669', 'drmohamedyousef@gmail.com', 'مستشفي الجامعه', '1455', 'ش النصر', 'استشاري الجراحه العامه', 45, '2025-04-28 09:44:01', 'active'),
(31, 'د.محمد نجم', 'جراحة الاورام والمناظير', '01558847796', 'drmohamednegm@gmail.com', 'مستشفي الاندلس', '2254', 'ش النصر', 'مدرس واستشاري الجراحه', 50, '2025-04-28 09:44:01', 'active'),
(32, 'د.محمد الباشا', 'الجراحه', '01144785963', 'drmohamed@gmail.com', 'مستشفي ديرب نجم', '5632', 'ش النصر', 'اخصائي', 15, '2025-04-28 09:44:01', 'active'),
(33, 'د.احمد لطفي', 'امراض الكبد', '01002236958', 'drahmedlotfy@gmail.com', 'مستشفي ديرب نجم', '01255896374', 'خلف قصر الثقافه', 'اخصائي امراض الكبد', 15, '2025-04-28 09:44:01', 'active'),
(34, 'د.تامر حامد', 'الكبد والجهاز الهضمي', '01144785596', 'tamerhamed@gmail.com', 'مستتشفي الجامعه', '8512', 'ديرب نجم', 'استشاري امراض الكبد', 20, '2025-04-28 09:44:01', 'active'),
(39, 'د.علاءعبدالغني', 'اعصاب', '01258896341', 'dralaaabdelghani@gmail.com', 'مستشفي ديرب نجم', '8225', 'شارع مصروالسودان', 'استشاري اعصاب', 20, '2025-04-28 09:44:01', 'active'),
(40, 'د.محمد الحسيني', 'جراحة المخ والاعصاب والعمود الفقري', '01566321899', 'drmohamedelhussiny@gmail.com', 'مستشفي الجامعه', '4456', 'ش النصر', 'اخصائي', 25, '2025-04-28 09:44:01', 'active'),
(41, 'محمد متولي', 'العظام', '01002959192', 'dr.mohamedM@gamil.com', 'مستشفي الجامعه', '412', 'المشايه', 'امراض الروماتزم', 20, '2025-04-28 11:11:01', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_working_hours`
--

CREATE TABLE `doctor_working_hours` (
  `id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `day` int NOT NULL COMMENT '0=Sunday, 1=Monday, ..., 6=Saturday',
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `is_working` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_working_hours`
--

INSERT INTO `doctor_working_hours` (`id`, `doctor_id`, `day`, `start_time`, `end_time`, `is_working`, `created_at`, `updated_at`) VALUES
(8, 2, 0, '09:00:00', '17:00:00', 1, '2025-05-05 11:32:17', '2025-05-05 11:32:17'),
(9, 2, 1, '09:00:00', '17:00:00', 1, '2025-05-05 11:32:17', '2025-05-05 11:32:17'),
(10, 2, 2, '09:00:00', '17:00:00', 1, '2025-05-05 11:32:17', '2025-05-05 11:32:17'),
(11, 2, 3, '09:00:00', '17:00:00', 1, '2025-05-05 11:32:17', '2025-05-05 11:32:17'),
(12, 2, 4, '09:00:00', '17:00:00', 1, '2025-05-05 11:32:17', '2025-05-05 11:32:17'),
(13, 2, 5, '09:00:00', '17:00:00', 1, '2025-05-05 11:32:18', '2025-05-05 11:32:18'),
(14, 2, 6, '09:00:00', '17:00:00', 1, '2025-05-05 11:32:18', '2025-05-05 11:32:18');

-- --------------------------------------------------------

--
-- Table structure for table `labtests`
--

CREATE TABLE `labtests` (
  `test_id` int NOT NULL,
  `patient_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `test_date` date NOT NULL,
  `result` text COLLATE utf8mb4_general_ci,
  `test_type_id` int NOT NULL,
  `notes` text COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `labtests`
--

INSERT INTO `labtests` (`test_id`, `patient_id`, `doctor_id`, `test_date`, `result`, `test_type_id`, `notes`) VALUES
(16, 3, 2, '2025-05-17', '22', 3, '22');

-- --------------------------------------------------------

--
-- Table structure for table `labtesttypes`
--

CREATE TABLE `labtesttypes` (
  `test_type_id` int NOT NULL,
  `test_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `normal_range` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `unit` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `labtesttypes`
--

INSERT INTO `labtesttypes` (`test_type_id`, `test_name`, `description`, `normal_range`, `unit`) VALUES
(1, 'فحص الدم', 'فحص شامل للدم', 'من 12 إلى 16 جم/ديسيلتر', 'جم/ديسيلتر'),
(2, 'فحص البول', 'فحص تحليل البول', 'pH بين 4.5 و 8', 'pH'),
(3, 'فحص سكر الدم', 'فحص مستوى السكر في الدم', 'من 70 إلى 100 مجم/ديسيلتر', 'مجم/ديسيلتر'),
(4, 'فحص الكوليسترول', 'فحص مستوى الكوليسترول في الدم', 'من 150 إلى 200 مجم/ديسيلتر', 'مجم/ديسيلتر'),
(5, 'فحص الكبد', 'فحص وظائف الكبد', 'من 30 إلى 100 وحدة دولية/لتر', 'وحدة دولية/لتر'),
(6, 'فحص الغدة الدرقية', 'فحص مستوى هرمونات الغدة الدرقية', 'من 0.4 إلى 4.0 ميكرو وحدة دولية/مل', 'ميكرو وحدة دولية/مل'),
(7, 'فحص الحمل', 'فحص للكشف عن الحمل', 'سلبي أو إيجابي', 'n/a'),
(8, 'فحص سرطان الثدي', 'فحص للكشف عن سرطان الثدي', 'سلبي أو إيجابي', 'n/a'),
(9, 'فحص ضغط الدم', 'فحص ضغط الدم', '120/80 ملم زئبقي', 'ملم زئبقي'),
(10, 'فحص الأشعة السينية', 'فحص أشعة سينية', 'يجب استشارة الطبيب لتحديد الحاجة', 'n/a');

-- --------------------------------------------------------

--
-- Table structure for table `medicalrecords`
--

CREATE TABLE `medicalrecords` (
  `record_id` int NOT NULL,
  `patient_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `diagnosis` text COLLATE utf8mb4_general_ci,
  `treatment` text COLLATE utf8mb4_general_ci,
  `prescription` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicalrecords`
--

INSERT INTO `medicalrecords` (`record_id`, `patient_id`, `doctor_id`, `diagnosis`, `treatment`, `prescription`, `created_at`, `updated_at`) VALUES
(17, 3, 2, 'فحص باطني', 'باطني', 'باطني', '2025-05-05 11:33:17', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int NOT NULL,
  `user_id` int NOT NULL,
  `user_type` enum('patient','doctor') COLLATE utf8mb4_general_ci NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `related_id` int DEFAULT NULL,
  `type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `user_type`, `message`, `related_id`, `type`, `is_read`, `created_at`) VALUES
(1, 13, 'patient', 'تم إرسال طلب حجز موعدك وبانتظار موافقة الطبيب', 18, 'appointment_request', 1, '2025-05-04 17:57:48'),
(2, 11, 'doctor', 'لديك طلب حجز موعد جديد يحتاج إلى موافقتك', 18, 'appointment_request', 0, '2025-05-04 17:57:48'),
(3, 13, 'patient', 'تم إرسال طلب حجز موعدك وبانتظار موافقة الطبيب', 19, 'appointment_request', 1, '2025-05-04 17:58:40'),
(4, 11, 'doctor', 'لديك طلب حجز موعد جديد يحتاج إلى موافقتك', 19, 'appointment_request', 0, '2025-05-04 17:58:41'),
(5, 1, 'patient', 'تمت الموافقة على موعدك مع الدكتور', 11, 'appointment_Completed', 1, '2025-05-04 18:01:52'),
(6, 13, 'patient', 'تم رفض موعدك مع الدكتور', 18, 'appointment_Cancelled', 1, '2025-05-04 18:01:56'),
(7, 13, 'patient', 'تمت الموافقة على موعدك مع الدكتور', 19, 'appointment_Completed', 1, '2025-05-04 18:01:58'),
(8, 3, 'patient', 'تم إرسال طلب حجز موعدك وبانتظار موافقة الطبيب', 20, 'appointment_request', 1, '2025-05-05 11:44:31'),
(9, 2, 'doctor', 'لديك طلب حجز موعد جديد يحتاج إلى موافقتك', 20, 'appointment_request', 0, '2025-05-05 11:44:31');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `patient_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('Male','Female','Other') COLLATE utf8mb4_general_ci NOT NULL,
  `contact_number` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`patient_id`, `name`, `date_of_birth`, `gender`, `contact_number`, `email`, `address`, `password`, `created_at`, `status`) VALUES
(3, 'احمد العراقي ', '2003-03-03', 'Male', '01002925192', 'ahmedeleraky@gmail.com', 'المنصوره', '412', '2025-04-28 09:47:52', 'active'),
(4, 'مريم محمد', '1985-03-25', 'Female', '0107654321', 'maryammohamed@gmail.com', 'ديرب نجم', 'hashed_password_4', '2025-04-28 09:47:52', 'active'),
(5, 'يوسف عبد الله', '1992-08-15', 'Male', '0117891234', 'youssefabdallah@gmail.com', 'ديرب نجم', 'hashed_password_5', '2025-04-28 09:47:52', 'active'),
(6, 'زينب علي', '1989-11-02', 'Female', '0103125678', 'zenabali@gmail.com', 'الزقازيق', 'hashed_password_6', '2025-04-28 09:47:52', 'active'),
(7, 'عادل حسن', '1975-09-05', 'Male', '0102567890', 'adelhassan@gmail.com', 'المنصورة', 'hashed_password_7', '2025-04-28 09:47:52', 'active'),
(8, 'سارة أحمد', '2000-01-22', 'Female', '0109876543', 'saraahmed@gmail.com', 'ميت غمر', 'hashed_password_8', '2025-04-28 09:47:52', 'active'),
(9, 'خالد علي', '1993-06-18', 'Male', '0111122334', 'khaledali@gmail.com', 'السنبلاوين', 'hashed_password_9', '2025-04-28 09:47:52', 'active'),
(10, 'فاطمة سعيد', '1982-02-14', 'Female', '0105557890', 'fatimasaed@gmail.com', 'الزقازيق', 'hashed_password_10', '2025-04-28 09:47:52', 'active'),
(11, 'محمود محمد', '1995-04-20', 'Male', '0104556677', 'mahmoudmohamed@gmail.com', 'صفط زريق', 'hashed_password_11', '2025-04-28 09:47:52', 'active'),
(12, 'أمينة خالد', '1997-12-10', 'Female', '0102254789', 'aminakhaled@gmail.com', 'دبيج', 'hashed_password_12', '2025-04-28 09:47:52', 'active'),
(13, 'عبدالله حسن', '2025-04-14', 'Male', '01200240708', 'abdullahhassan@gmail.com', 'ديرب نجم', '123', '2025-04-28 09:47:52', 'active'),
(14, 'محمود ابراهيم', '2003-03-03', 'Male', '01002959192', 'mahmoudibrihem@gmail.com', 'الزقازيق', '1020', '2025-04-28 09:47:52', 'active'),
(15, 'اميره السيد', '1995-09-13', 'Female', '01154789963', 'amiraelsayed@gmail.com', 'الزقازيق', '258', '2025-04-28 09:47:52', 'active'),
(16, 'نهي محمد', '2003-05-18', 'Female', '01558977451', 'nohamohamed@gmail.com', 'ديرب نجم', '8777', '2025-04-28 09:47:52', 'active'),
(17, 'عبدالستار محسن', '1967-09-12', 'Male', '01147855269', 'abdelstarmohsen@gmail.com', 'بهنباي', '1236', '2025-04-28 09:47:52', 'active'),
(18, 'السعيد محمود', '1974-02-01', 'Male', '01226695478', 'elsaidmahmoud@gmail.com', 'فرسيس', '258', '2025-04-28 09:47:52', 'active'),
(19, 'ندي محمد', '2002-04-03', 'Female', '01234478952', 'nadamohamed@gmail.com', 'ديرب نجم', '6662', '2025-04-28 09:47:52', 'active'),
(20, 'يوسف السيد', '2000-08-09', 'Male', '01069954783', 'yousefelsayed@gmail.com', 'ديرب نجم', '788', '2025-04-28 09:47:52', 'active'),
(21, 'امجد محمد', '1999-02-23', 'Male', '01147223116', 'amgedmohamed@gmail.com', 'الزقازيق', '1235', '2025-04-28 09:47:52', 'active'),
(22, 'مريم احمد', '2003-05-02', 'Female', '01122358749', 'maryamahmed@gmail.com', 'ديرب نجم', '8888', '2025-04-28 09:47:52', 'active'),
(23, 'عبير ماجد', '1998-03-12', 'Female', '01598874631', 'abeermaged@gmail.com', 'الزقازيق', '9990', '2025-04-28 09:47:52', 'active'),
(24, 'منه ممدوح', '1995-09-22', 'Female', '01227789456', 'mennamamdoh@gmail.com', 'ديرب نجم', '7744', '2025-04-28 09:47:52', 'active'),
(25, 'ناديه محمود', '2004-02-23', 'Female', '01147855247', 'nadiamahmoud@gmail.com', 'الزقازيق', '2874', '2025-04-28 09:47:52', 'active'),
(26, 'نورا السيد', '1997-01-06', 'Female', '01112554788', 'noraelsayed@gmail.com', 'ديرب نجم', '4578', '2025-04-28 09:47:52', 'active'),
(27, 'سليم عماد', '2003-07-02', 'Male', '01140003412', 'sleimmemad@gmail.com', 'الزقازيق', '2910', '2025-04-28 09:47:52', 'active'),
(28, 'زينب عطيه', '1980-09-25', 'Female', '01224788599', 'zeinbattia@gmail.com', 'ديرب نجم', '5855', '2025-04-28 09:47:52', 'active'),
(29, 'عبير مصطفي', '2001-06-05', 'Female', '01005542169', 'abeermostafa@gmail.com', 'المنصوره', '1414', '2025-04-28 09:47:52', 'active'),
(30, 'اسماء محمود', '1985-04-12', 'Female', '01065441239', 'asmaamahoud@gmail.com', 'شربين', '5555', '2025-04-28 09:47:52', 'active'),
(31, 'وفاء ابراهيم', '2002-03-06', 'Female', '01556988774', 'wafaaibrihem@gmail.com', 'ههيا', '9999', '2025-04-28 09:47:52', 'active'),
(32, 'هند عزت', '2003-05-12', 'Female', '011115845479', 'hendezzat@gmail.com', 'الزقازيق', '1013', '2025-04-28 09:47:52', 'active'),
(33, 'يارا احمد', '1992-04-03', 'Female', '01000226554', 'yaraahmed@gmail.com', 'المنوفيه', '4545', '2025-04-28 09:47:52', 'active'),
(34, 'شهد عبدالله', '1996-03-12', 'Female', '01254789641', 'shahdabdallah@gmail.com', 'الغربيه', '9595', '2025-04-28 09:47:52', 'active'),
(35, 'سمير علاء', '1988-06-23', 'Male', '01224475169', 'samiralaa@gmail.com', 'بهنباي', '7471', '2025-04-28 09:47:52', 'active'),
(36, 'اروي السيد', '2001-09-12', 'Female', '01147855269', 'arwaelsayed@gmail.com', 'السنبلاوين', '4145', '2025-04-28 09:47:52', 'active'),
(37, 'مراد علي', '1999-02-04', 'Male', '01578965412', 'moradali@gmail.com', 'المنصوره', '2915', '2025-04-28 09:47:52', 'active'),
(38, 'ايهاب رمزي', '1995-07-13', 'Male', '01222569844', 'ehabramzy@gmail.com', 'القاهره', '1117', '2025-04-28 09:47:52', 'active'),
(39, 'علي ياسر', '1988-07-25', 'Male', '01025669874', 'aliyasser@gmail.com', 'ديرب نجم', '3030', '2025-04-28 09:47:52', 'active'),
(40, 'محمد حسام', '2002-03-25', 'Male', '01547896314', 'mohamedhossam@gmail.com', 'الزقازيق', '8585', '2025-04-28 09:47:52', 'active'),
(41, 'يوسف محمد', '2000-06-05', 'Male', '0101478965', 'yosefmohamed@gmail.com', 'بهنيا', '1475', '2025-04-28 09:47:52', 'active'),
(42, 'رضا رجب', '1982-05-02', 'Male', '01569874156', 'redarageb@gmail.com', 'الدقهليه', '8596', '2025-04-28 09:47:52', 'active'),
(43, 'احمد السيد', '2001-03-01', 'Male', '01269887453', 'ahmedelsayed@gmail.com', 'الزقازيق', '5855', '2025-04-28 09:47:52', 'active'),
(44, 'عطيه مصطفي', '1991-05-24', 'Male', '01225844716', 'attiamostafa@gmail.com', 'ميت غمر', '1477', '2025-04-28 09:47:52', 'active'),
(45, 'السيد عبده', '1979-05-12', 'Male', '01554778963', 'elsayedabdo@gmail.com', 'المنوفيه', '5588', '2025-04-28 09:47:52', 'active'),
(46, 'يمني سعيد', '2001-05-07', 'Female', NULL, 'yomnasaied@gmail.com', 'الزقازيق', '1111', '2025-04-28 09:47:52', 'active'),
(47, 'نوران احمد', '2003-05-12', 'Female', '01011458897', 'noranahmed@gmail.com', 'المنصوره', '7787', '2025-04-28 09:47:52', 'active'),
(48, 'يوسف جابر', '2003-05-12', 'Male', '01225896547', 'yousefgaber@gmail.com', 'دبرب نجم', '5555', '2025-04-28 09:47:52', 'active'),
(49, 'ابراهيم محمد', '2001-05-08', 'Male', '01556697841', 'ibrihemohamed@gmail.com', 'ديرب نجم', '7471', '2025-04-28 09:47:52', 'active'),
(50, 'سامح سامي', '2003-05-12', 'Male', '01014785236', 'samehsami@gmail.com', 'ديرب نجم', '8521', '2025-04-28 09:47:52', 'active'),
(53, 'امينه رمضان', '1994-05-12', 'Female', '01211478859', 'aminaramadan@gmail.com', 'الزقزيق', '7412', '2025-04-28 09:47:52', 'active'),
(54, 'ايمان محمود', '2003-01-25', 'Female', '01014788596', 'emanmahmoud@gmail.com', 'ديرب نجم', '1515', '2025-04-28 09:47:52', 'active'),
(55, 'مصطفي عبده', '1980-04-12', 'Male', '01066958821', 'mostafaabdo@gmail.com', 'الزقازيق', '5825', '2025-04-28 09:47:52', 'active'),
(56, 'سالي ابراهيم', '1982-05-23', 'Female', '011400069874', 'sallyibrihem@gmail.com', 'ديرب نجم', '7722', '2025-04-28 09:47:52', 'active'),
(57, 'اميره محمد', '2003-09-23', 'Female', '01036695521', 'amiramohamed@gmail.com', 'المنوفيه', '2525', '2025-04-28 09:47:52', 'active'),
(58, 'حليم ثروت', '1991-05-02', 'Male', '01556998775', 'halimthrwat@gmail.com', 'المنصوره', '1417', '2025-04-28 09:47:52', 'active'),
(59, 'جميله سامح', '1972-01-12', 'Female', '01589441572', 'gamielasameh@gmail.com', 'ديرب نجم', '7878', '2025-04-28 09:47:52', 'active'),
(60, 'مصطفي نور', '1996-12-04', 'Male', '01012544789', 'mostafanoor@gmail.com', 'الزقازيق', '4455', '2025-04-28 09:47:52', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `patient_doctors`
--

CREATE TABLE `patient_doctors` (
  `id` int NOT NULL,
  `patient_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `registration_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_doctors`
--

INSERT INTO `patient_doctors` (`id`, `patient_id`, `doctor_id`, `registration_date`) VALUES
(6, 13, 9, '2025-04-28 10:33:40'),
(7, 13, 11, '2025-04-28 10:33:40'),
(8, 13, 3, '2025-04-28 10:33:40'),
(9, 3, 41, '2025-04-28 10:33:40'),
(10, 3, 2, '2025-04-28 11:36:14');

-- --------------------------------------------------------

--
-- Table structure for table `patient_xrays`
--

CREATE TABLE `patient_xrays` (
  `patient_xray_id` int NOT NULL,
  `patient_id` int NOT NULL,
  `xray_type_id` int NOT NULL,
  `test_date` date NOT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `doctor_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_xrays`
--

INSERT INTO `patient_xrays` (`patient_xray_id`, `patient_id`, `xray_type_id`, `test_date`, `notes`, `doctor_id`) VALUES
(11, 3, 2, '2025-05-28', 'اشعة', 2),
(12, 3, 2, '2025-05-28', 'اشعة', 2),
(13, 3, 1, '2025-05-06', 'الصدر', 2),
(14, 3, 1, '2025-05-06', 'الصدر', 2),
(16, 3, 2, '2025-05-07', '22', 2);

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `prescription_id` int NOT NULL,
  `patient_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `medicine_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `dosage` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `instructions` text COLLATE utf8mb4_general_ci,
  `prescribed_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`prescription_id`, `patient_id`, `doctor_id`, `medicine_name`, `dosage`, `instructions`, `prescribed_date`) VALUES
(3, 3, 41, 'ميثوتركسيت', 'جرعه اسبوعيه', 'يؤخذ مره واحدة في الاسبوع', '2025-03-05 04:30:00'),
(4, 4, 4, 'مضاد حيوي', 'يستخدم مرتين يومياً', 'ياخذ قبل حشو العصب ', '2025-04-05 07:30:00'),
(5, 5, 17, 'مسكنات لالتهاب الاعصاب', 'تؤخذ 3مرات يوميا', 'يؤخذ لمدة اسبوع ', '2025-04-10 11:30:00'),
(6, 6, 6, 'مثبت حمل', 'يوميا', 'يؤخذ لمدة شهر', '2025-02-09 09:30:00'),
(7, 7, 7, 'مطهر معوي', 'كل 8 ساعات', 'يوخذ لمدة 4 ايام ', '2025-01-14 12:30:00'),
(8, 8, 8, 'قطرات لترطيب العين', 'مرة واحدة يومياً', 'تستخدم قبل النوم', '2025-01-01 06:00:00'),
(9, 9, 9, 'أدوية العلاج الكيميائي', 'مره شهريا', 'تؤخذ مره في الشهر لمدة 6 شهور', '2025-03-20 13:00:00'),
(10, 10, 24, 'أدوية خافضه للحراره\r\nمضادة للصداع', 'مرتين يوميا', 'يؤخذ مرتين يوميا مع كمادات لخفض الحراره', '2025-04-06 01:30:00'),
(12, 38, 23, 'شراب لعلاج حساسيه الصدر', 'مرتين يوميا', 'يؤخذ لمده اسبوع مع شرب سوائل دافيه', '2025-02-19 21:14:24'),
(13, 25, NULL, 'مرهم مضاد حيوي ', '4مرات يوميا', 'يدهن علي الجرح بعد تنظيفه لمده15 يوم', '2025-04-21 02:17:54'),
(14, 3, 2, 'ميثوتركسيت', 'ميثوتركسيت', 'ميثوتركسيت', '2025-05-05 11:33:43');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_id` int NOT NULL,
  `setting_key` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `value` text COLLATE utf8mb4_general_ci,
  `description` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`setting_id`, `setting_key`, `value`, `description`, `created_at`, `updated_at`) VALUES
(1, 'system_name', 'نظام إدارة الأمراض المزمنة', 'اسم النظام', '2025-04-21 13:35:27', '2025-04-21 13:35:27'),
(2, 'max_appointments', '10', 'الحد الأقصى للمواعيد اليومية', '2025-04-21 13:35:27', '2025-04-21 13:35:27'),
(3, 'notification_email', 'admin@example.com', 'البريد الإلكتروني للإشعارات', '2025-04-21 13:35:27', '2025-04-21 13:35:27'),
(4, 'working_hours_start', '09:00', 'وقت بدء ساعات العمل', '2025-04-21 13:35:27', '2025-04-21 13:35:27'),
(5, 'working_hours_end', '17:00', 'وقت نهاية ساعات العمل', '2025-04-21 13:35:27', '2025-04-21 13:35:27'),
(6, 'maintenance_mode', '0', 'وضع الصيانة (0: مغلق، 1: مفتوح)', '2025-04-21 13:35:27', '2025-04-21 13:35:27');

-- --------------------------------------------------------

--
-- Table structure for table `xraytypes`
--

CREATE TABLE `xraytypes` (
  `xray_type_id` int NOT NULL,
  `xray_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `radiation_level` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `required_preparation` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `xraytypes`
--

INSERT INTO `xraytypes` (`xray_type_id`, `xray_name`, `description`, `radiation_level`, `required_preparation`) VALUES
(1, 'أشعة الصدر', 'تستخدم لفحص الرئتين والصدر', 'منخفض', 'لا يتطلب تحضير خاص'),
(2, 'أشعة البطن', 'فحص أشعة للبطن', 'منخفض', 'احيانا صيام 6-8ساعات'),
(3, 'أشعة مقطعيه علي المخ', 'ممكن تجري بصبغه او بدون', 'متوسط ل عالي', 'لا يحتاج إلى تحضير'),
(4, 'رنين مغناطيسي علي الركبه', 'تستخدم لتشخيص الاربطه والغضاريف', 'بدون اشعاع', 'لا يحتاج إلى تحضير'),
(5, 'سونار للحمل', 'امن تماما', 'بدون اشعاع', 'لا يحتاج تحضير'),
(6, 'سونار علي الحوض', 'لفحص المثانه الممتلئه مهمه للرؤيه', 'بدون اشعاع', 'شرب ماء قبلها'),
(7, 'أشعة مقطعيه علي الشرايين', 'فحص الشرايين القلبية او الدماغية', 'عالي جدا', 'صيام+تحاليل كرياتينين'),
(8, 'اشعة بانورما علي الاسنان', 'تستخدم لتقييم الفكين والاسنان', 'منخفض', 'لا يحتاج إلى تحضير '),
(9, 'أشعة مقطعيه علي الجيوب الانفيه', 'لتشخيص التهابات الجيوب', 'متوسط', 'لا يحتاج إلى تحضير'),
(10, 'أشعة ديناميكية للمعده', 'متابعة حركة الطعام', 'عالي', 'صيام');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`doctor_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `doctor_working_hours`
--
ALTER TABLE `doctor_working_hours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `labtests`
--
ALTER TABLE `labtests`
  ADD PRIMARY KEY (`test_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `labtesttypes`
--
ALTER TABLE `labtesttypes`
  ADD PRIMARY KEY (`test_type_id`),
  ADD UNIQUE KEY `test_name` (`test_name`);

--
-- Indexes for table `medicalrecords`
--
ALTER TABLE `medicalrecords`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `idx_user` (`user_id`,`user_type`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`patient_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `patient_doctors`
--
ALTER TABLE `patient_doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_patient_doctor` (`patient_id`,`doctor_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `patient_xrays`
--
ALTER TABLE `patient_xrays`
  ADD PRIMARY KEY (`patient_xray_id`),
  ADD KEY `fk_patient` (`patient_id`),
  ADD KEY `fk_xray_type` (`xray_type_id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`prescription_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `xraytypes`
--
ALTER TABLE `xraytypes`
  ADD PRIMARY KEY (`xray_type_id`),
  ADD UNIQUE KEY `xray_name` (`xray_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `doctor_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `doctor_working_hours`
--
ALTER TABLE `doctor_working_hours`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `labtests`
--
ALTER TABLE `labtests`
  MODIFY `test_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `labtesttypes`
--
ALTER TABLE `labtesttypes`
  MODIFY `test_type_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `medicalrecords`
--
ALTER TABLE `medicalrecords`
  MODIFY `record_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `patient_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `patient_doctors`
--
ALTER TABLE `patient_doctors`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `patient_xrays`
--
ALTER TABLE `patient_xrays`
  MODIFY `patient_xray_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `prescription_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `setting_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `xraytypes`
--
ALTER TABLE `xraytypes`
  MODIFY `xray_type_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`) ON DELETE SET NULL;

--
-- Constraints for table `doctor_working_hours`
--
ALTER TABLE `doctor_working_hours`
  ADD CONSTRAINT `doctor_working_hours_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `labtests`
--
ALTER TABLE `labtests`
  ADD CONSTRAINT `labtests_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `labtests_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`) ON DELETE SET NULL;

--
-- Constraints for table `medicalrecords`
--
ALTER TABLE `medicalrecords`
  ADD CONSTRAINT `medicalrecords_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medicalrecords_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`) ON DELETE SET NULL;

--
-- Constraints for table `patient_doctors`
--
ALTER TABLE `patient_doctors`
  ADD CONSTRAINT `patient_doctors_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_doctors_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`) ON DELETE CASCADE;

--
-- Constraints for table `patient_xrays`
--
ALTER TABLE `patient_xrays`
  ADD CONSTRAINT `fk_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_xray_type` FOREIGN KEY (`xray_type_id`) REFERENCES `xraytypes` (`xray_type_id`) ON DELETE CASCADE;

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescriptions_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
