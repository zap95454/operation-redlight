-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 19, 2026 at 11:43 PM
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
-- Database: `operation_redlight`
--

-- --------------------------------------------------------

--
-- Table structure for table `ambulances`
--

CREATE TABLE `ambulances` (
  `ambulance_id` int(11) NOT NULL,
  `hospital_id` int(11) DEFAULT NULL,
  `driver_name` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ambulances`
--

INSERT INTO `ambulances` (`ambulance_id`, `hospital_id`, `driver_name`, `status`) VALUES
(1, 1, 'Rahim Uddin', 'Busy'),
(2, 1, 'Karim Mia', 'Busy'),
(3, 2, 'Kamrul Hasan', 'Busy'),
(4, 3, 'Tariqul Islam', 'Busy'),
(5, 4, 'Md. Shafiq', 'Busy'),
(6, 5, 'Arif Hossain', 'Available'),
(7, 6, 'Nurul Amin', 'Available'),
(8, 7, 'Jahangir Alam', 'Available'),
(9, 8, 'Zakir Miah', 'Available'),
(10, 9, 'Rubel Ahmed', 'Busy'),
(11, 10, 'Faisal Mahmud', 'Available'),
(12, 11, 'Imran Khan', 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `appointment_date` date DEFAULT NULL,
  `serial_number` int(11) DEFAULT NULL,
  `status` enum('Pending','Completed','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `patient_id`, `doctor_id`, `room_id`, `appointment_date`, `serial_number`, `status`) VALUES
(20, 18, 18, NULL, '2026-04-20', 1, 'Completed'),
(21, 18, 6, 1, '2026-04-20', 1, 'Pending'),
(22, 18, 6, 1, '2026-04-20', 2, 'Pending'),
(23, 18, 18, NULL, '2026-04-20', 2, 'Pending'),
(24, 18, 15, 10, '2026-04-20', 1, 'Pending'),
(25, 18, 7, 2, '2026-04-20', 1, 'Pending');

--
-- Triggers `appointments`
--
DELIMITER $$
CREATE TRIGGER `after_appointment_insert` AFTER INSERT ON `appointments` FOR EACH ROW BEGIN
    INSERT INTO audit_logs (action, appointment_id) VALUES ('New Appointment Created', NEW.appointment_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_appointment_update` AFTER UPDATE ON `appointments` FOR EACH ROW BEGIN
    IF NEW.status != OLD.status THEN
        INSERT INTO audit_logs (action, appointment_id) VALUES (CONCAT('Status changed to ', NEW.status), NEW.appointment_id);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `action_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`log_id`, `action`, `appointment_id`, `action_time`) VALUES
(8, 'New Appointment Created', 17, '2026-04-19 20:55:09'),
(9, 'New Appointment Created', 18, '2026-04-19 20:55:16'),
(10, 'New Appointment Created', 19, '2026-04-19 20:55:20'),
(11, 'New Appointment Created', 20, '2026-04-19 21:34:57'),
(12, 'Status changed to Completed', 20, '2026-04-19 21:35:41'),
(13, 'New Appointment Created', 21, '2026-04-19 21:37:51'),
(14, 'New Appointment Created', 22, '2026-04-19 21:39:41'),
(15, 'New Appointment Created', 23, '2026-04-19 21:40:53'),
(16, 'New Appointment Created', 24, '2026-04-19 21:41:19'),
(17, 'New Appointment Created', 25, '2026-04-19 21:41:28');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `doctor_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `hospital_id` int(11) DEFAULT NULL,
  `experience_years` int(11) DEFAULT 0,
  `bio_data` text DEFAULT NULL,
  `current_workplace` varchar(255) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `credentials_file` varchar(255) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT 'default_doc.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`doctor_id`, `name`, `email`, `password`, `gender`, `specialization`, `hospital_id`, `experience_years`, `bio_data`, `current_workplace`, `position`, `credentials_file`, `profile_pic`) VALUES
(1, 'Dr. Smith', NULL, NULL, NULL, 'Cardiologist', 1, 0, NULL, NULL, NULL, NULL, 'default_doc.png'),
(2, 'Dr. Jones', NULL, NULL, NULL, 'Neurologist', 1, 0, NULL, NULL, NULL, NULL, 'default_doc.png'),
(4, 'DR.Rahim', NULL, NULL, NULL, 'Orthopedic', 1, 0, NULL, NULL, NULL, NULL, 'default_doc.png'),
(6, 'Dr. Nusrat Jahan', 'nusrat@doc.com', '$2y$10$AUD89ZAUGOgUTQwqcm2G.emfkXZUeecNAb57LOYZC/Fw8FwEPtJZ6', 'Female', 'Gynecology', 2, 0, NULL, NULL, NULL, NULL, 'default_doc.png'),
(7, 'Dr. Farhana Amin', 'farhana@doc.com', '$2y$10$AUD89ZAUGOgUTQwqcm2G.emfkXZUeecNAb57LOYZC/Fw8FwEPtJZ6', 'Female', 'Gynecology', 3, 0, NULL, NULL, NULL, NULL, 'default_doc.png'),
(8, 'Dr. Shahinur Rahman', 'shahinur@doc.com', '$2y$10$AUD89ZAUGOgUTQwqcm2G.emfkXZUeecNAb57LOYZC/Fw8FwEPtJZ6', 'Male', 'Cardiology', 4, 0, NULL, NULL, NULL, NULL, 'default_doc.png'),
(9, 'Dr. Ayesha Siddiqa', 'ayesha@doc.com', '$2y$10$AUD89ZAUGOgUTQwqcm2G.emfkXZUeecNAb57LOYZC/Fw8FwEPtJZ6', 'Female', 'Pediatrics', 5, 0, NULL, NULL, NULL, NULL, 'default_doc.png'),
(10, 'Dr. Mahmudul Hasan', 'mahmudul@doc.com', '$2y$10$AUD89ZAUGOgUTQwqcm2G.emfkXZUeecNAb57LOYZC/Fw8FwEPtJZ6', 'Male', 'Neurology', 6, 0, NULL, NULL, NULL, NULL, 'default_doc.png'),
(11, 'Dr. Tania Akter', 'tania@doc.com', '$2y$10$AUD89ZAUGOgUTQwqcm2G.emfkXZUeecNAb57LOYZC/Fw8FwEPtJZ6', 'Female', 'Dermatology', 7, 0, NULL, NULL, NULL, NULL, 'default_doc.png'),
(12, 'Dr. Rafiqul Islam', 'rafiqul@doc.com', '$2y$10$AUD89ZAUGOgUTQwqcm2G.emfkXZUeecNAb57LOYZC/Fw8FwEPtJZ6', 'Male', 'Orthopedics', 8, 0, NULL, NULL, NULL, NULL, 'default_doc.png'),
(13, 'Dr. Sabrina Chowdhury', 'sabrina@doc.com', '$2y$10$AUD89ZAUGOgUTQwqcm2G.emfkXZUeecNAb57LOYZC/Fw8FwEPtJZ6', 'Female', 'Psychiatry', 9, 0, NULL, NULL, NULL, NULL, 'default_doc.png'),
(14, 'Dr. Anisur Rahman', 'anisur@doc.com', '$2y$10$AUD89ZAUGOgUTQwqcm2G.emfkXZUeecNAb57LOYZC/Fw8FwEPtJZ6', 'Male', 'Oncology', 10, 0, NULL, NULL, NULL, NULL, 'default_doc.png'),
(15, 'Dr. Salma Begum', 'salma@doc.com', '$2y$10$AUD89ZAUGOgUTQwqcm2G.emfkXZUeecNAb57LOYZC/Fw8FwEPtJZ6', 'Female', 'Ophthalmology', 11, 0, NULL, NULL, NULL, NULL, 'default_doc.png'),
(18, 'Miraj Kishur', 'something70@gmail.com', '$2y$10$SLswx1fXeLM2kkaXf0FF9ONTJKV0/BqhJNpedAqywtJaXRx5R8L86', 'Male', 'Cardiology', 12, 5, 'A Very Good Doctor 👍👍', 'Niter Medical College And Hospital', 'Asst. Professor', 'doc_18_1776634451.jpg', 'pic_18_1776634451.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `emergency_requests`
--

CREATE TABLE `emergency_requests` (
  `request_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `ambulance_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospitals`
--

CREATE TABLE `hospitals` (
  `hospital_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `location` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospitals`
--

INSERT INTO `hospitals` (`hospital_id`, `name`, `location`) VALUES
(1, 'City General', 'Downtown'),
(2, 'Dhaka Medical College Hospital', 'Shahbagh, Dhaka'),
(3, 'Square Hospitals', 'Panthapath, Dhaka'),
(4, 'Evercare Hospital', 'Bashundhara, Dhaka'),
(5, 'United Hospital', 'Gulshan, Dhaka'),
(6, 'BIRDEM General Hospital', 'Shahbagh, Dhaka'),
(7, 'Kurmitola General Hospital', 'Kurmitola, Dhaka'),
(8, 'Sir Salimullah Medical College', 'Mitford, Dhaka'),
(9, 'Labaid Specialized Hospital', 'Dhanmondi, Dhaka'),
(10, 'Popular Diagnostic Centre', 'Dhanmondi, Dhaka'),
(11, 'Ibn Sina Hospital', 'Kalyanpur, Dhaka'),
(12, 'Enam Medical College And Hospital', 'Savar');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `patient_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`patient_id`, `name`, `age`, `gender`, `email`, `password`) VALUES
(18, 'Mst. Jannat', 22, 'Female', 'something68@gmail.com', '$2y$10$a9.0xw1EZFG8Nke1wa7T3O5/UNrNDf.8nB9bvWqGHWDlhzGcPadXq');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `patient_id`, `doctor_id`, `rating`, `comment`) VALUES
(11, 18, 18, 1, 'Terrible Doctor');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `room_number` varchar(10) DEFAULT NULL,
  `floor_number` int(11) DEFAULT NULL,
  `hospital_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `room_number`, `floor_number`, `hospital_id`) VALUES
(1, '101A', 1, 2),
(2, '205B', 2, 3),
(3, '302C', 3, 4),
(4, '410D', 4, 5),
(5, '105E', 1, 6),
(6, '220F', 2, 7),
(7, '315G', 3, 8),
(8, '401H', 4, 9),
(9, '112I', 1, 10),
(10, '208J', 2, 11),
(11, '45C', 3, 9),
(12, '23D', 4, 6);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ambulances`
--
ALTER TABLE `ambulances`
  ADD PRIMARY KEY (`ambulance_id`),
  ADD KEY `hospital_id` (`hospital_id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`doctor_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `hospital_id` (`hospital_id`);

--
-- Indexes for table `emergency_requests`
--
ALTER TABLE `emergency_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `ambulance_id` (`ambulance_id`);

--
-- Indexes for table `hospitals`
--
ALTER TABLE `hospitals`
  ADD PRIMARY KEY (`hospital_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`patient_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD KEY `hospital_id` (`hospital_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ambulances`
--
ALTER TABLE `ambulances`
  MODIFY `ambulance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `doctor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `emergency_requests`
--
ALTER TABLE `emergency_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hospitals`
--
ALTER TABLE `hospitals`
  MODIFY `hospital_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ambulances`
--
ALTER TABLE `ambulances`
  ADD CONSTRAINT `ambulances_ibfk_1` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`hospital_id`) ON DELETE CASCADE;

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE SET NULL;

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`hospital_id`) ON DELETE CASCADE;

--
-- Constraints for table `emergency_requests`
--
ALTER TABLE `emergency_requests`
  ADD CONSTRAINT `emergency_requests_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `emergency_requests_ibfk_2` FOREIGN KEY (`ambulance_id`) REFERENCES `ambulances` (`ambulance_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`) ON DELETE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`hospital_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
