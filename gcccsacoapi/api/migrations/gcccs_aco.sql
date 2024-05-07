-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 07, 2024 at 03:08 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gcccs_aco`
--

-- --------------------------------------------------------

--
-- Table structure for table `gc_admin`
--

CREATE TABLE `gc_admin` (
  `admin_id` int(11) NOT NULL DEFAULT 12000000,
  `admin_email` varchar(40) NOT NULL,
  `password` varchar(128) NOT NULL,
  `faculty_lastname` varchar(50) NOT NULL,
  `faculty_firstname` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gc_admin`
--

INSERT INTO `gc_admin` (`admin_id`, `admin_email`, `password`, `faculty_lastname`, `faculty_firstname`) VALUES
(12000000, 'bastianlacap55@gmail.com', '$2y$10$fs.K4yN1jJYI/zXMxmqYJeWiICOsMKpmQ2JUk8j48Z7wL7pPV0CQq', 'Lacap', 'Karl Bastian');

-- --------------------------------------------------------

--
-- Table structure for table `gc_alumni`
--

CREATE TABLE `gc_alumni` (
  `alumni_id` int(11) NOT NULL,
  `alumni_lastname` varchar(50) NOT NULL,
  `alumni_firstname` varchar(50) NOT NULL,
  `alumni_middlename` varchar(50) NOT NULL,
  `alumni_birthday` date NOT NULL,
  `alumni_age` tinyint(4) NOT NULL,
  `isVisible` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gc_alumni`
--

INSERT INTO `gc_alumni` (`alumni_id`, `alumni_lastname`, `alumni_firstname`, `alumni_middlename`, `alumni_birthday`, `alumni_age`, `isVisible`) VALUES
(4, 'Lacap', 'Karl', 'Bastian Cunanan', '2003-11-03', 20, 1);

-- --------------------------------------------------------

--
-- Table structure for table `gc_alumni_contact`
--

CREATE TABLE `gc_alumni_contact` (
  `alumni_id` int(11) NOT NULL,
  `alumni_email` varchar(80) NOT NULL,
  `alumni_number` bigint(20) NOT NULL,
  `alumni_address` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gc_alumni_contact`
--

INSERT INTO `gc_alumni_contact` (`alumni_id`, `alumni_email`, `alumni_number`, `alumni_address`) VALUES
(4, 'bastianlacap55@gmail.com', 9940282036, 'Zambales');

-- --------------------------------------------------------

--
-- Table structure for table `gc_alumni_education`
--

CREATE TABLE `gc_alumni_education` (
  `alumni_id` int(11) NOT NULL,
  `year_graduated` year(4) NOT NULL,
  `alumni_program` varchar(50) NOT NULL,
  `education_upgrade` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gc_alumni_education`
--

INSERT INTO `gc_alumni_education` (`alumni_id`, `year_graduated`, `alumni_program`, `education_upgrade`) VALUES
(4, '2027', 'BSIT', 'No');

-- --------------------------------------------------------

--
-- Table structure for table `gc_alumni_family`
--

CREATE TABLE `gc_alumni_family` (
  `alumni_id` int(11) NOT NULL,
  `alumni_marital_status` varchar(50) NOT NULL,
  `alumni_no_of_children` bigint(20) NOT NULL,
  `alumni_spousename` varchar(50) NOT NULL,
  `alumni_race` varchar(50) NOT NULL,
  `alumni_religion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gc_alumni_family`
--

INSERT INTO `gc_alumni_family` (`alumni_id`, `alumni_marital_status`, `alumni_no_of_children`, `alumni_spousename`, `alumni_race`, `alumni_religion`) VALUES
(4, 'Single', 0, 'Cattleya Crisolo', 'Filipino', 'Catholic');

-- --------------------------------------------------------

--
-- Table structure for table `gc_history`
--

CREATE TABLE `gc_history` (
  `alumni_id` int(11) NOT NULL,
  `employment_status` varchar(50) NOT NULL,
  `working_in_abroad` varchar(50) NOT NULL,
  `working_in_industry` varchar(50) NOT NULL,
  `years_of_experience` year(4) NOT NULL,
  `current_job` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gc_history`
--

INSERT INTO `gc_history` (`alumni_id`, `employment_status`, `working_in_abroad`, `working_in_industry`, `years_of_experience`, `current_job`) VALUES
(4, 'Self Employed', '0', '0', '2004', 'Student');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gc_admin`
--
ALTER TABLE `gc_admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `gc_alumni`
--
ALTER TABLE `gc_alumni`
  ADD PRIMARY KEY (`alumni_id`);

--
-- Indexes for table `gc_alumni_contact`
--
ALTER TABLE `gc_alumni_contact`
  ADD PRIMARY KEY (`alumni_id`);

--
-- Indexes for table `gc_alumni_education`
--
ALTER TABLE `gc_alumni_education`
  ADD PRIMARY KEY (`alumni_id`);

--
-- Indexes for table `gc_alumni_family`
--
ALTER TABLE `gc_alumni_family`
  ADD PRIMARY KEY (`alumni_id`);

--
-- Indexes for table `gc_history`
--
ALTER TABLE `gc_history`
  ADD PRIMARY KEY (`alumni_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gc_alumni`
--
ALTER TABLE `gc_alumni`
  MODIFY `alumni_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gc_alumni_contact`
--
ALTER TABLE `gc_alumni_contact`
  ADD CONSTRAINT `gc_alumni_contact_ibfk_1` FOREIGN KEY (`alumni_id`) REFERENCES `gc_alumni` (`alumni_id`);

--
-- Constraints for table `gc_alumni_education`
--
ALTER TABLE `gc_alumni_education`
  ADD CONSTRAINT `gc_alumni_education_ibfk_1` FOREIGN KEY (`alumni_id`) REFERENCES `gc_alumni` (`alumni_id`);

--
-- Constraints for table `gc_alumni_family`
--
ALTER TABLE `gc_alumni_family`
  ADD CONSTRAINT `gc_alumni_family_ibfk_1` FOREIGN KEY (`alumni_id`) REFERENCES `gc_alumni` (`alumni_id`);

--
-- Constraints for table `gc_history`
--
ALTER TABLE `gc_history`
  ADD CONSTRAINT `gc_history_ibfk_1` FOREIGN KEY (`alumni_id`) REFERENCES `gc_alumni` (`alumni_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
