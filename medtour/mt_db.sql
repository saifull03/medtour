-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 19, 2024 at 04:45 PM
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
-- Database: `mt_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`) VALUES
(4, 'Rushad', 'rushadkabir@gmail.com', '$2y$10$7jWKbJRX6lonYpaMP9pJoe43bJX//cuF73ymCibzmQsra.4txxscm');

-- --------------------------------------------------------

--
-- Table structure for table `hospital_appointments`
--

CREATE TABLE `hospital_appointments` (
  `id` int(11) NOT NULL,
  `patient_id` varchar(50) NOT NULL,
  `doctor_name` varchar(100) NOT NULL,
  `hospital_name` varchar(100) NOT NULL,
  `appointment_time` time NOT NULL,
  `appointment_date` date NOT NULL,
  `cancer_type` varchar(100) NOT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hotel_bookings`
--

CREATE TABLE `hotel_bookings` (
  `id` int(11) NOT NULL,
  `patient_id` varchar(50) NOT NULL,
  `hotel_name` varchar(100) NOT NULL,
  `checkin_date` date NOT NULL,
  `checkin_time` time NOT NULL,
  `checkout_date` date NOT NULL,
  `num_guests` int(11) NOT NULL,
  `room_type` varchar(50) NOT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transport_bookings`
--

CREATE TABLE `transport_bookings` (
  `id` int(11) NOT NULL,
  `patient_id` varchar(50) NOT NULL,
  `transport_type` varchar(50) NOT NULL,
  `pickup_location` varchar(100) NOT NULL,
  `destination` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transport_bookings`
--

INSERT INTO `transport_bookings` (`id`, `patient_id`, `transport_type`, `pickup_location`, `destination`, `date`, `time`, `booking_date`) VALUES
(31, '8', 'Train', 'Biman Bandar', 'Banani', '2024-04-26', '18:04:00', '2024-04-24 07:59:43');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`) VALUES
(5, 'saifulsoton', 'saifulshaikat.me@gmail.com', '$2y$10$zmsrEgC4MXWq9a8fuhvDQ.kDeAV8FVeX1aepkmIdls/u5cEya8OPe'),
(6, 'Soton', '1233@gmail.com', '$2y$10$4D8BJBMSmC7QjByAI6et3OQ61AJroO4mxkxUS.fEhv14u06XQJuVa');

-- --------------------------------------------------------

--
-- Table structure for table `visa_assistance`
--

CREATE TABLE `visa_assistance` (
  `id` int(11) NOT NULL,
  `patient_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `country` varchar(100) NOT NULL,
  `passport_number` varchar(50) NOT NULL,
  `visa_type` varchar(100) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `appointment_date` date DEFAULT NULL,
  `appointment_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_appointments`
--
ALTER TABLE `hospital_appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hotel_bookings`
--
ALTER TABLE `hotel_bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transport_bookings`
--
ALTER TABLE `transport_bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `visa_assistance`
--
ALTER TABLE `visa_assistance`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `hospital_appointments`
--
ALTER TABLE `hospital_appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `hotel_bookings`
--
ALTER TABLE `hotel_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transport_bookings`
--
ALTER TABLE `transport_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `visa_assistance`
--
ALTER TABLE `visa_assistance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
