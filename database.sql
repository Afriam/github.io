-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 10, 2025 at 06:14 PM
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
-- Database: `mpdoc`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `updationDate` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `updationDate`) VALUES
(1, 'admin', 'Test@12345', '04-03-2024 11:42:05 AM');

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `id` int(11) NOT NULL,
  `doctorSpecialization` varchar(255) DEFAULT NULL,
  `doctorId` int(11) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `consultancyFees` int(11) DEFAULT NULL,
  `appointmentDate` varchar(255) DEFAULT NULL,
  `appointmentTime` varchar(255) DEFAULT NULL,
  `postingDate` timestamp NULL DEFAULT current_timestamp(),
  `userStatus` int(11) DEFAULT NULL,
  `doctorStatus` int(11) DEFAULT NULL,
  `updationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`id`, `doctorSpecialization`, `doctorId`, `userId`, `consultancyFees`, `appointmentDate`, `appointmentTime`, `postingDate`, `userStatus`, `doctorStatus`, `updationDate`) VALUES
(18, 'Anesthesia', 4, 17, 1500, '2025-05-15', '00:11', '2025-05-10 16:09:05', 1, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `barangays`
--

CREATE TABLE `barangays` (
  `barangay_id` int(11) NOT NULL,
  `barangay_name` varchar(100) NOT NULL,
  `city_mun_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barangays`
--

INSERT INTO `barangays` (`barangay_id`, `barangay_name`, `city_mun_id`) VALUES
(1, 'Barangay 1', 1),
(2, 'Barangay 2', 1),
(3, 'Diliman', 3);

-- --------------------------------------------------------

--
-- Table structure for table `cities_municipalities`
--

CREATE TABLE `cities_municipalities` (
  `city_mun_id` int(11) NOT NULL,
  `city_mun_name` varchar(100) NOT NULL,
  `province_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cities_municipalities`
--

INSERT INTO `cities_municipalities` (`city_mun_id`, `city_mun_name`, `province_id`) VALUES
(1, 'Laoag City', 1),
(2, 'Vigan City', 2),
(3, 'Quezon City', 3);

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `doctorName` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `address` longtext DEFAULT NULL,
  `docFees` varchar(255) DEFAULT NULL,
  `contactno` bigint(11) DEFAULT NULL,
  `docEmail` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `creationDate` timestamp NULL DEFAULT current_timestamp(),
  `updationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `specialization`, `doctorName`, `username`, `address`, `docFees`, `contactno`, `docEmail`, `password`, `creationDate`, `updationDate`) VALUES
(4, 'Anesthesia', 'ARAH AWINGAN', 'arah', 'P6, DEMS TABUK CITY', '1500', 9876432213, 'awingan@gmail.com', '$2y$10$4u3GoFVthHK3XpVJPwFYmOfABk24KemAZ3yl.CrIZLFzsyZdhVLJ6', '2025-05-10 15:03:36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `doctorspecialization`
--

CREATE TABLE `doctorspecialization` (
  `id` int(11) NOT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `creationDate` timestamp NULL DEFAULT current_timestamp(),
  `updationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `doctorspecialization`
--

INSERT INTO `doctorspecialization` (`id`, `specialization`, `creationDate`, `updationDate`) VALUES
(1, 'Orthopedics', '2024-04-09 02:09:46', '2024-05-13 17:26:47'),
(2, 'Internal Medicine', '2024-04-09 02:09:46', '2024-05-13 17:26:56'),
(3, 'Obstetrics and Gynecology', '2024-04-09 02:09:46', '2024-05-13 17:26:56'),
(4, 'Dermatology', '2024-04-09 02:09:46', '2024-05-13 17:26:56'),
(5, 'Pediatrics', '2024-04-09 02:09:46', '2024-05-13 17:26:56'),
(6, 'Radiology', '2024-04-09 02:09:46', '2024-05-13 17:26:56'),
(7, 'General Surgery', '2024-04-09 02:09:46', '2024-05-13 17:26:56'),
(8, 'Ophthalmology', '2024-04-09 02:09:46', '2024-05-13 17:26:56'),
(9, 'Anesthesia', '2024-04-09 02:09:46', '2024-05-13 17:26:56'),
(10, 'Pathology', '2024-04-09 02:09:46', '2024-05-13 17:26:56'),
(11, 'ENT', '2024-04-09 02:09:46', '2024-05-13 17:26:56'),
(12, 'Dental Care', '2024-04-09 02:09:46', '2024-05-13 17:26:56'),
(13, 'Dermatologists', '2024-04-09 02:09:46', '2024-05-13 17:26:56'),
(14, 'Endocrinologists', '2024-04-09 02:09:46', '2024-05-13 17:26:56'),
(15, 'Neurologists', '2024-04-09 02:09:46', '2024-05-13 17:26:56'),
(1, 'Orthopedics', '2024-04-09 10:09:46', '2024-05-14 01:26:47'),
(2, 'Internal Medicine', '2024-04-09 10:09:46', '2024-05-14 01:26:56'),
(3, 'Obstetrics and Gynecology', '2024-04-09 10:09:46', '2024-05-14 01:26:56'),
(4, 'Dermatology', '2024-04-09 10:09:46', '2024-05-14 01:26:56'),
(5, 'Pediatrics', '2024-04-09 10:09:46', '2024-05-14 01:26:56'),
(6, 'Radiology', '2024-04-09 10:09:46', '2024-05-14 01:26:56'),
(7, 'General Surgery', '2024-04-09 10:09:46', '2024-05-14 01:26:56'),
(8, 'Ophthalmology', '2024-04-09 10:09:46', '2024-05-14 01:26:56'),
(9, 'Anesthesia', '2024-04-09 10:09:46', '2024-05-14 01:26:56'),
(10, 'Pathology', '2024-04-09 10:09:46', '2024-05-14 01:26:56'),
(11, 'ENT', '2024-04-09 10:09:46', '2024-05-14 01:26:56'),
(12, 'Dental Care', '2024-04-09 10:09:46', '2024-05-14 01:26:56'),
(13, 'Dermatologists', '2024-04-09 10:09:46', '2024-05-14 01:26:56'),
(14, 'Endocrinologists', '2024-04-09 10:09:46', '2024-05-14 01:26:56'),
(15, 'Neurologists', '2024-04-09 10:09:46', '2024-05-14 01:26:56');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `patient_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `family_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) NOT NULL,
  `ext_name` varchar(20) DEFAULT NULL,
  `date_of_birth` date NOT NULL,
  `age` int(11) NOT NULL,
  `place_of_birth` varchar(255) NOT NULL,
  `email_address` varchar(100) NOT NULL,
  `has_disability` tinyint(1) DEFAULT 0,
  `disability_type` varchar(50) DEFAULT NULL,
  `is_indigenous` tinyint(1) DEFAULT 0,
  `indigenous_group` varchar(100) DEFAULT NULL,
  `perm_region` varchar(100) NOT NULL,
  `perm_province` varchar(100) NOT NULL,
  `perm_city_municipality` varchar(100) NOT NULL,
  `perm_barangay` varchar(100) NOT NULL,
  `perm_street` varchar(255) NOT NULL,
  `perm_zip_code` varchar(10) NOT NULL,
  `home_region` varchar(100) NOT NULL,
  `home_province` varchar(100) NOT NULL,
  `home_city_municipality` varchar(100) NOT NULL,
  `home_barangay` varchar(100) NOT NULL,
  `home_street` varchar(255) NOT NULL,
  `home_zip_code` varchar(10) NOT NULL,
  `sex` varchar(10) NOT NULL,
  `civil_status` varchar(20) NOT NULL,
  `nationality` varchar(100) NOT NULL,
  `spouse_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`patient_id`, `username`, `password`, `family_name`, `first_name`, `middle_name`, `ext_name`, `date_of_birth`, `age`, `place_of_birth`, `email_address`, `has_disability`, `disability_type`, `is_indigenous`, `indigenous_group`, `perm_region`, `perm_province`, `perm_city_municipality`, `perm_barangay`, `perm_street`, `perm_zip_code`, `home_region`, `home_province`, `home_city_municipality`, `home_barangay`, `home_street`, `home_zip_code`, `sex`, `civil_status`, `nationality`, `spouse_name`, `created_at`, `updated_at`) VALUES
(17, 'afriam', '$2y$10$ZHllVqnVnIDQThpBqmRh1.Rri38fR7q0Kk1GF5GbGrNCCbjlaiOS.', 'MANA-ING', 'AFRIAM', 'DONA-AL', NULL, '2002-01-12', 23, 'ANGACAN SUR MANGALI TANUDAN KALINGA', 'afriammanaing144@gmail.com', 0, NULL, 0, NULL, 'NCR', 'Metro Manila', 'Quezon City', 'Diliman', 'P7, BACCRAS', '3805', 'NCR', 'Metro Manila', 'Quezon City', 'Diliman', 'P7, BACCRAS', '3805', 'Male', 'Single', 'PILIPINO', NULL, '2025-05-10 15:03:11', '2025-05-10 15:03:11');

-- --------------------------------------------------------

--
-- Table structure for table `provinces`
--

CREATE TABLE `provinces` (
  `province_id` int(11) NOT NULL,
  `province_name` varchar(100) NOT NULL,
  `region_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `provinces`
--

INSERT INTO `provinces` (`province_id`, `province_name`, `region_id`) VALUES
(1, 'Ilocos Norte', 1),
(2, 'Ilocos Sur', 1),
(3, 'Metro Manila', 3);

-- --------------------------------------------------------

--
-- Table structure for table `regions`
--

CREATE TABLE `regions` (
  `region_id` int(11) NOT NULL,
  `region_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `regions`
--

INSERT INTO `regions` (`region_id`, `region_name`) VALUES
(1, 'Region I'),
(2, 'Region II'),
(3, 'NCR');

-- --------------------------------------------------------

--
-- Table structure for table `tblmedicalhistory`
--

CREATE TABLE `tblmedicalhistory` (
  `ID` int(10) NOT NULL,
  `PatientID` int(10) DEFAULT NULL,
  `BloodPressure` varchar(200) DEFAULT NULL,
  `BloodSugar` varchar(200) NOT NULL,
  `Weight` varchar(100) DEFAULT NULL,
  `Temperature` varchar(200) DEFAULT NULL,
  `MedicalPres` longtext DEFAULT NULL,
  `CreationDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tblmedicalhistory`
--

INSERT INTO `tblmedicalhistory` (`ID`, `PatientID`, `BloodPressure`, `BloodSugar`, `Weight`, `Temperature`, `MedicalPres`, `CreationDate`) VALUES
(1, 17, '120/80', '90 mg/dL', '70 kg', '36.6°C', 'Patient is healthy. No prescriptions required.', '2025-05-10 15:48:50');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `patient_name` varchar(255) NOT NULL,
  `patient_status` varchar(100) NOT NULL,
  `testimonial_text` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `patient_id`, `patient_name`, `patient_status`, `testimonial_text`, `image_url`, `is_approved`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Sarah Johnson', 'Regular Patient', 'The appointment system is so easy to use. I can book my appointments in minutes!', 'https://randomuser.me/api/portraits/women/32.jpg', 1, '2025-04-26 19:00:00', '2025-04-26 19:00:00'),
(2, NULL, 'Michael Brown', 'First-time User', 'Great experience! The system helped me find the right doctor for my needs.', 'https://randomuser.me/api/portraits/men/45.jpg', 1, '2025-04-26 19:00:00', '2025-04-26 19:00:00'),
(3, NULL, 'Emily Davis', 'Long-term Patient', 'The reminders and notifications are very helpful. Never missed an appointment!', 'https://randomuser.me/api/portraits/women/68.jpg', 1, '2025-04-26 19:00:00', '2025-04-26 19:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctorId` (`doctorId`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `barangays`
--
ALTER TABLE `barangays`
  ADD PRIMARY KEY (`barangay_id`),
  ADD KEY `city_mun_id` (`city_mun_id`);

--
-- Indexes for table `cities_municipalities`
--
ALTER TABLE `cities_municipalities`
  ADD PRIMARY KEY (`city_mun_id`),
  ADD KEY `province_id` (`province_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `specilization` (`specialization`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`patient_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email_address` (`email_address`);

--
-- Indexes for table `provinces`
--
ALTER TABLE `provinces`
  ADD PRIMARY KEY (`province_id`),
  ADD KEY `region_id` (`region_id`);

--
-- Indexes for table `regions`
--
ALTER TABLE `regions`
  ADD PRIMARY KEY (`region_id`);

--
-- Indexes for table `tblmedicalhistory`
--
ALTER TABLE `tblmedicalhistory`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `tblmedicalhistory_ibfk_1` (`PatientID`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `barangays`
--
ALTER TABLE `barangays`
  MODIFY `barangay_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cities_municipalities`
--
ALTER TABLE `cities_municipalities`
  MODIFY `city_mun_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `provinces`
--
ALTER TABLE `provinces`
  MODIFY `province_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `regions`
--
ALTER TABLE `regions`
  MODIFY `region_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barangays`
--
ALTER TABLE `barangays`
  ADD CONSTRAINT `barangays_ibfk_1` FOREIGN KEY (`city_mun_id`) REFERENCES `cities_municipalities` (`city_mun_id`);

--
-- Constraints for table `cities_municipalities`
--
ALTER TABLE `cities_municipalities`
  ADD CONSTRAINT `cities_municipalities_ibfk_1` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`province_id`);

--
-- Constraints for table `provinces`
--
ALTER TABLE `provinces`
  ADD CONSTRAINT `provinces_ibfk_1` FOREIGN KEY (`region_id`) REFERENCES `regions` (`region_id`);

--
-- Constraints for table `tblmedicalhistory`
--
ALTER TABLE `tblmedicalhistory`
  ADD CONSTRAINT `tblmedicalhistory_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `patients` (`patient_id`) ON DELETE SET NULL;

--
-- Constraints for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD CONSTRAINT `testimonials_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
