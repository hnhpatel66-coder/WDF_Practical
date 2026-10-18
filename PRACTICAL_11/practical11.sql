-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 13, 2026 at 01:04 PM
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
-- Database: `practical11`
--

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `course` varchar(100) NOT NULL,
  `semester` int(11) NOT NULL,
  `phone` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `email`, `course`, `semester`, `phone`) VALUES
(1, 'Aarav Patel', 'aarav@example.com', 'IT', 3, '9876543201'),
(2, 'Krisha Gohel', 'krisha@example.com', 'IT', 3, '9876543202'),
(3, 'Riya Desai', 'riya@example.com', 'CE', 4, '9876543203'),
(4, 'Dev Mehta', 'dev@example.com', 'IT', 3, '9876543204'),
(5, 'Priya Joshi', 'priya@example.com', 'CSE', 5, '9876543205'),
(6, 'Harsh Patel', 'harsh@example.com', 'CE', 4, '9876543206'),
(7, 'Neha Shah', 'neha@example.com', 'IT', 3, '9876543207'),
(8, 'Yash Trivedi', 'yash@example.com', 'CSE', 5, '9876543208'),
(9, 'Mahi Desai', 'mahi@example.com', 'IT', 3, '9876543209'),
(10, 'Raj Parmar', 'raj@example.com', 'CE', 4, '9876543210'),
(11, 'Jiya Mehta', 'jiya@example.com', 'IT', 3, '9876543211'),
(12, 'Om Shah', 'om@example.com', 'CSE', 5, '9876543212'),
(13, 'Kavya Patel', 'kavya@example.com', 'CE', 4, '9876543213'),
(14, 'Dhruv Joshi', 'dhruv@example.com', 'IT', 3, '9876543214'),
(15, 'Ananya Desai', 'ananya@example.com', 'CSE', 5, '9876543215'),
(16, 'Meet Patel', 'meet@example.com', 'IT', 3, '9876543216'),
(17, 'Isha Shah', 'isha@example.com', 'CE', 4, '9876543217'),
(18, 'Nirav Mehta', 'nirav@example.com', 'CSE', 5, '9876543218'),
(20, 'Rohan Desai', 'rohan@example.com', 'CE', 4, '9876543220'),
(21, 'Jatin Gangvani', 'jatin@gmail.com', 'IT', 3, '8238555452');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
