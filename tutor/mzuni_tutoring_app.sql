-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 22, 2024 at 04:04 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mzuni_tutoring_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `name`, `email`, `password_hash`, `created_at`) VALUES
(1, 'Admin User', 'admin@mzuni.ac.mw', '21232f297a57a5a743894a0e4a801fc3', '2024-10-04 12:32:53'),
(2, 'Super Admin', 'superadmin@mzuni.ac.mw', '17c4520f6cfd1ab53d8745e84681eb49', '2024-10-04 12:32:53');

-- --------------------------------------------------------

--
-- Table structure for table `availability`
--

CREATE TABLE `availability` (
  `availability_id` int(11) NOT NULL,
  `tutor_id` int(11) DEFAULT NULL,
  `available_date` date NOT NULL,
  `available_time` time NOT NULL,
  `available_up_to` time NOT NULL,
  `status` enum('Available','Booked','Unavailable') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `availability`
--

INSERT INTO `availability` (`availability_id`, `tutor_id`, `available_date`, `available_time`, `available_up_to`, `status`) VALUES
(1, 1, '2023-03-15', '09:00:00', '11:00:00', 'Booked'),
(2, 2, '2023-03-16', '10:00:00', '12:00:00', 'Booked'),
(3, 1, '2024-11-10', '14:00:00', '17:00:00', 'Booked'),
(4, 1, '2024-10-29', '09:00:00', '17:00:00', 'Available'),
(5, 3, '2024-11-12', '09:00:00', '17:00:00', 'Booked'),
(6, 3, '2024-11-19', '09:02:00', '08:02:00', 'Available'),
(7, 3, '2024-11-10', '03:04:00', '17:00:00', 'Available'),
(8, 3, '2024-11-29', '07:07:00', '17:00:00', 'Available'),
(10, 4, '2024-11-19', '09:00:00', '17:00:00', 'Booked'),
(11, 3, '2024-11-12', '15:04:00', '21:03:00', 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE `chats` (
  `chat_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `tutor_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chats`
--

INSERT INTO `chats` (`chat_id`, `student_id`, `tutor_id`, `created_at`) VALUES
(1, 1, 1, '2024-10-25 02:35:16'),
(2, 2, 2, '2024-10-25 02:35:16'),
(21, 1, 4, '2024-11-20 10:23:13'),
(22, 1, 2, '2024-11-20 11:55:42'),
(23, 5, 3, '2024-11-20 12:04:18'),
(24, 5, 3, '2024-11-20 12:04:24');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int(10) NOT NULL,
  `department_name` text DEFAULT NULL,
  `department_description` text DEFAULT NULL,
  `department_code` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`, `department_description`, `department_code`) VALUES
(1, 'Information and Communication Technology Department', 'Information and Communication Technology Department is bringing technology to solve africans problems', 'ICT'),
(2, 'Engineering', 'Department of Engineering', 'ENGES'),
(3, 'Mathematics', 'Department of Mathematics', 'MATH');

-- --------------------------------------------------------

--
-- Table structure for table `expertise`
--

CREATE TABLE `expertise` (
  `expertise_id` int(11) NOT NULL,
  `expertise_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expertise`
--

INSERT INTO `expertise` (`expertise_id`, `expertise_name`, `description`) VALUES
(1, 'Artificial Intelligence', 'Specialization in AI and Machine Learning'),
(2, 'Number Theory', 'Specialization in Number Theory');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `session_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `session_id`, `rating`, `comments`, `created_at`) VALUES
(1, 1, 5, 'Excellent session', '2024-10-04 12:32:52'),
(2, 2, 4, 'Good session', '2024-10-04 12:32:52');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `chat_id` int(11) NOT NULL,
  `sender_type` enum('student','tutor') NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message_text` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `chat_id`, `sender_type`, `sender_id`, `message_text`, `sent_at`) VALUES
(1, 1, 'student', 1, 'Hello Professor, I have a question about my assignment.', '2024-10-25 02:35:16'),
(2, 1, 'tutor', 1, 'Hi John, I am happy to help. What is your question?', '2024-10-25 02:35:16'),
(32, 21, 'student', 1, 'Hie Dr Manda', '2024-11-20 10:23:13'),
(33, 22, 'student', 1, 'Hie Dr J Wilson , i want to ask for my assignment', '2024-11-20 11:55:42'),
(34, 23, 'student', 5, 'yes Keneth how is your assignment ?', '2024-11-20 12:04:18'),
(35, 24, 'student', 5, 'yes Keneth how is your assignment ?', '2024-11-20 12:04:24');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `tutor_id` int(11) NOT NULL,
  `notification` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `student_id`, `tutor_id`, `notification`, `date`) VALUES
(1, 2, 2, 'Session on 2023-03-16 at 10:00:00 booked successfully.', '2024-11-16 13:31:53'),
(3, 1, 4, 'Session on 2024-11-20 at 13:05:00 booked successfully.', '2024-11-17 18:23:16'),
(5, 1, 4, 'Session on 2024-11-19 at 09:00:00 booked successfully.', '2024-11-17 18:36:10'),
(6, 1, 3, 'Session on 2024-11-19 at 09:02:00 booked successfully.', '2024-11-20 09:12:25'),
(7, 1, 2, 'Session on 2023-03-16 at 10:00:00 booked successfully.', '2024-11-20 09:37:24'),
(8, 1, 3, 'Session on 2024-11-12 at 09:00:00 booked successfully.', '2024-11-20 10:53:44'),
(9, 2, 1, 'Session on 2024-11-10 at 14:00:00 booked successfully.', '2024-11-22 14:04:00');

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `program_id` int(11) NOT NULL,
  `program_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`program_id`, `program_name`, `description`, `department_id`) VALUES
(1, 'Computer Science', 'Bachelor\'s degree in Computer Science', 1),
(2, 'Mathematics', 'Bachelor\'s degree in Mathematics', 3);

-- --------------------------------------------------------

--
-- Table structure for table `qualifications`
--

CREATE TABLE `qualifications` (
  `qualification_id` int(11) NOT NULL,
  `qualification_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `qualifications`
--

INSERT INTO `qualifications` (`qualification_id`, `qualification_name`, `description`) VALUES
(1, 'Ph.D. in Computer Science', 'Doctoral degree in Computer Science'),
(2, 'Masters in Mathematics', 'Postgraduate degree in Mathematics');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'Student'),
(2, 'Tutor'),
(3, 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `session_id` int(11) NOT NULL,
  `tutor_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `session_date` date NOT NULL,
  `session_time` time NOT NULL,
  `status` int(11) DEFAULT NULL,
  `feedback_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`session_id`, `tutor_id`, `student_id`, `subject_id`, `session_date`, `session_time`, `status`, `feedback_id`, `created_at`) VALUES
(1, 1, 1, 1, '2023-03-17', '11:00:00', 3, NULL, '2024-10-04 12:32:52'),
(2, 2, 2, 2, '2023-03-18', '12:00:00', 1, NULL, '2024-10-04 12:32:52'),
(5, 1, 2, 1, '2024-11-10', '14:00:00', 3, 1, '2024-10-21 22:44:21'),
(6, 1, 2, 1, '2024-10-29', '09:00:00', 3, 1, '2024-10-24 05:06:46'),
(7, 1, 3, 1, '2024-10-29', '09:00:00', 1, 1, '2024-10-28 23:19:13'),
(8, 2, 2, 2, '2023-03-16', '10:00:00', 3, 1, '2024-11-07 13:05:29'),
(12, 2, 2, 2, '2023-03-16', '10:00:00', 3, NULL, '2024-11-16 14:31:53'),
(13, 2, 1, 2, '2023-03-16', '10:00:00', 3, 1, '2024-11-17 19:22:45'),
(14, 4, 1, 2, '2024-11-20', '13:05:00', 3, 1, '2024-11-17 19:23:16'),
(15, 3, 1, 2, '2024-11-10', '03:04:00', 3, 1, '2024-11-17 19:35:09'),
(16, 4, 1, 2, '2024-11-19', '09:00:00', 1, 1, '2024-11-17 19:36:10'),
(17, 3, 1, 2, '2024-11-19', '09:02:00', 3, 1, '2024-11-20 10:12:25'),
(18, 2, 1, 2, '2023-03-16', '10:00:00', 1, 1, '2024-11-20 10:37:24'),
(19, 3, 1, 2, '2024-11-12', '09:00:00', 1, 1, '2024-11-20 11:53:44'),
(20, 1, 2, 1, '2024-11-10', '14:00:00', 1, 1, '2024-11-22 15:04:00');

-- --------------------------------------------------------

--
-- Table structure for table `session_bookings`
--

CREATE TABLE `session_bookings` (
  `booking_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `tutor_id` int(11) DEFAULT NULL,
  `session_id` int(11) DEFAULT NULL,
  `booked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `session_bookings`
--

INSERT INTO `session_bookings` (`booking_id`, `student_id`, `tutor_id`, `session_id`, `booked_at`) VALUES
(1, 1, 1, 1, '2024-10-04 12:32:53'),
(2, 2, 2, 2, '2024-10-04 12:32:53');

-- --------------------------------------------------------

--
-- Table structure for table `status_lookup`
--

CREATE TABLE `status_lookup` (
  `status_id` int(11) NOT NULL,
  `status_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status_lookup`
--

INSERT INTO `status_lookup` (`status_id`, `status_name`, `description`) VALUES
(1, 'Scheduled', 'Session scheduled'),
(2, 'Completed', 'Session completed'),
(3, 'Cancelled', 'Session cancelled');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `registration_number` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `program_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `password_hash` varchar(256) NOT NULL,
  `department_id` int(10) DEFAULT NULL,
  `year_of_study` int(11) DEFAULT NULL,
  `academic_level` varchar(50) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `language` varchar(100) DEFAULT NULL,
  `technical_skills` text DEFAULT NULL,
  `hobbies` text DEFAULT NULL,
  `goals_motivation` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `registration_number`, `name`, `email`, `phone_number`, `program_id`, `created_at`, `password_hash`, `department_id`, `year_of_study`, `academic_level`, `date_of_birth`, `nationality`, `language`, `technical_skills`, `hobbies`, `goals_motivation`) VALUES
(1, 'CS001', 'John Doe', 'john.doe@mzuni.ac.mw', '0888888888', 1, '2024-10-04 12:32:50', '527bd5b5d689e2c32ae974c6229ff785', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Bedict2619', 'Jane Smith', 'jane.smith@mzuni.ac.mw', '0999999999', 2, '2024-10-04 12:32:50', '5844a15e76563fedd11840fd6f40ea7b', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'BMATH0324', 'Andrew Stima', 'stima@my.mzuni.ac.mw', '+265888001347', 2, '2024-10-28 22:39:29', '0e55666a4ad822e0e34299df3591d979', 3, 3, 'Bachelor', '1993-10-13', 'Malawian', 'English', 'Graphical designing', 'Reading', 'apply mathematics in solving community prgrams'),
(5, 'DMATH0124', 'Kenneth Banda', 'keth@zeroone.ggg', '+2656667788', 2, '2024-11-17 10:39:20', 'b59c67bf196a4758191e42f76670ceba', 3, 1, 'Diploma', '2024-11-28', 'Malawian', 'English', 'solve', 'code', 'here we go');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject_name`, `description`) VALUES
(1, 'Data Structures', 'Course on data structures and algorithms'),
(2, 'Calculus', 'Course on differential calculus');

-- --------------------------------------------------------

--
-- Table structure for table `tutors`
--

CREATE TABLE `tutors` (
  `tutor_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `password_hash` varchar(256) NOT NULL,
  `qualification` varchar(255) DEFAULT NULL,
  `expertise` varchar(255) DEFAULT NULL,
  `year_of_graduation` date DEFAULT NULL,
  `years_experience` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tutors`
--

INSERT INTO `tutors` (`tutor_id`, `name`, `email`, `phone_number`, `created_at`, `password_hash`, `qualification`, `expertise`, `year_of_graduation`, `years_experience`) VALUES
(1, 'Dr. John Taylor', 'jtaylor@mzuni.ac.mw', '0777777777', '2024-10-04 12:32:50', '6c72103eb1c20f139f8bf00a5d2351f0', '2', '1', NULL, NULL),
(2, 'Dr. Jane Wilson', 'jwilson@mzuni.ac.mw', '0666666666', '2024-10-04 12:32:50', 'b2fd71222e997881ce80bb4550aecb8c', '1', '2', NULL, NULL),
(3, 'j chando', 'd.phiri@mzuni.ac.mw', '+26588823455', '2024-10-29 00:14:40', 'd93591bdf7860e1e4ee2fca799911215', '2', '2', '2024-09-30', 3),
(4, 'Agatha Manda', 'agamanda@zeroone.ggg', '+26561234567', '2024-11-17 08:21:16', '4a7d1ed414474e4033ac29ccb8653d9b', '1', '2', '2024-11-22', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tutor_expertise`
--

CREATE TABLE `tutor_expertise` (
  `tutor_expertise_id` int(11) NOT NULL,
  `tutor_id` int(11) DEFAULT NULL,
  `expertise_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tutor_expertise`
--

INSERT INTO `tutor_expertise` (`tutor_expertise_id`, `tutor_id`, `expertise_id`) VALUES
(1, 1, 1),
(2, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tutor_qualifications`
--

CREATE TABLE `tutor_qualifications` (
  `tutor_qualification_id` int(11) NOT NULL,
  `tutor_id` int(11) DEFAULT NULL,
  `qualification_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tutor_qualifications`
--

INSERT INTO `tutor_qualifications` (`tutor_qualification_id`, `tutor_id`, `qualification_id`) VALUES
(1, 1, 1),
(2, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `user_role_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`user_role_id`, `user_id`, `role_id`, `created_at`) VALUES
(1, 1, 1, '2024-10-04 12:32:53'),
(2, 2, 2, '2024-10-04 12:32:53'),
(3, 1, 3, '2024-10-04 12:32:53'),
(4, 2, 3, '2024-10-04 12:32:53');

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
-- Indexes for table `availability`
--
ALTER TABLE `availability`
  ADD PRIMARY KEY (`availability_id`),
  ADD KEY `tutor_id` (`tutor_id`);

--
-- Indexes for table `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`chat_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `tutor_id` (`tutor_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `expertise`
--
ALTER TABLE `expertise`
  ADD PRIMARY KEY (`expertise_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `chat_id` (`chat_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`program_id`);

--
-- Indexes for table `qualifications`
--
ALTER TABLE `qualifications`
  ADD PRIMARY KEY (`qualification_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `tutor_id` (`tutor_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `feedback_id` (`feedback_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `session_bookings`
--
ALTER TABLE `session_bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `tutor_id` (`tutor_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `status_lookup`
--
ALTER TABLE `status_lookup`
  ADD PRIMARY KEY (`status_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `fk_department` (`department_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`);

--
-- Indexes for table `tutors`
--
ALTER TABLE `tutors`
  ADD PRIMARY KEY (`tutor_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `tutor_expertise`
--
ALTER TABLE `tutor_expertise`
  ADD PRIMARY KEY (`tutor_expertise_id`),
  ADD KEY `tutor_id` (`tutor_id`),
  ADD KEY `expertise_id` (`expertise_id`);

--
-- Indexes for table `tutor_qualifications`
--
ALTER TABLE `tutor_qualifications`
  ADD PRIMARY KEY (`tutor_qualification_id`),
  ADD KEY `tutor_id` (`tutor_id`),
  ADD KEY `qualification_id` (`qualification_id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_role_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `availability`
--
ALTER TABLE `availability`
  MODIFY `availability_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `chat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `expertise`
--
ALTER TABLE `expertise`
  MODIFY `expertise_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `program_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `qualifications`
--
ALTER TABLE `qualifications`
  MODIFY `qualification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `session_bookings`
--
ALTER TABLE `session_bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `status_lookup`
--
ALTER TABLE `status_lookup`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tutors`
--
ALTER TABLE `tutors`
  MODIFY `tutor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tutor_expertise`
--
ALTER TABLE `tutor_expertise`
  MODIFY `tutor_expertise_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tutor_qualifications`
--
ALTER TABLE `tutor_qualifications`
  MODIFY `tutor_qualification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `user_role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `availability`
--
ALTER TABLE `availability`
  ADD CONSTRAINT `availability_ibfk_1` FOREIGN KEY (`tutor_id`) REFERENCES `tutors` (`tutor_id`);

--
-- Constraints for table `chats`
--
ALTER TABLE `chats`
  ADD CONSTRAINT `chats_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `chats_ibfk_2` FOREIGN KEY (`tutor_id`) REFERENCES `tutors` (`tutor_id`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`session_id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`chat_id`) REFERENCES `chats` (`chat_id`);

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`tutor_id`) REFERENCES `tutors` (`tutor_id`),
  ADD CONSTRAINT `sessions_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `sessions_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`),
  ADD CONSTRAINT `sessions_ibfk_4` FOREIGN KEY (`feedback_id`) REFERENCES `feedback` (`feedback_id`),
  ADD CONSTRAINT `sessions_ibfk_5` FOREIGN KEY (`status`) REFERENCES `status_lookup` (`status_id`);

--
-- Constraints for table `session_bookings`
--
ALTER TABLE `session_bookings`
  ADD CONSTRAINT `session_bookings_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `session_bookings_ibfk_2` FOREIGN KEY (`tutor_id`) REFERENCES `tutors` (`tutor_id`),
  ADD CONSTRAINT `session_bookings_ibfk_3` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`session_id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`),
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`);

--
-- Constraints for table `tutor_expertise`
--
ALTER TABLE `tutor_expertise`
  ADD CONSTRAINT `tutor_expertise_ibfk_1` FOREIGN KEY (`tutor_id`) REFERENCES `tutors` (`tutor_id`),
  ADD CONSTRAINT `tutor_expertise_ibfk_2` FOREIGN KEY (`expertise_id`) REFERENCES `expertise` (`expertise_id`);

--
-- Constraints for table `tutor_qualifications`
--
ALTER TABLE `tutor_qualifications`
  ADD CONSTRAINT `tutor_qualifications_ibfk_1` FOREIGN KEY (`tutor_id`) REFERENCES `tutors` (`tutor_id`),
  ADD CONSTRAINT `tutor_qualifications_ibfk_2` FOREIGN KEY (`qualification_id`) REFERENCES `qualifications` (`qualification_id`);

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `admins` (`admin_id`),
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
