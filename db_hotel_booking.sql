CREATE DATABASE IF NOT EXISTS hotel_booking CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE hotel_booking;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','staff','customer') DEFAULT 'customer',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE rooms (
  id INT AUTO_INCREMENT PRIMARY KEY,
  room_number VARCHAR(20) NOT NULL,
  type VARCHAR(50) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  status ENUM('available','booked','maintenance') DEFAULT 'available',
  stock INT NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_code VARCHAR(30) NOT NULL UNIQUE,
  customer_name VARCHAR(100) NOT NULL,
  customer_email VARCHAR(100),
  checkin_date DATE NOT NULL,
  checkout_date DATE NOT NULL,
  total_amount DECIMAL(12,2) NOT NULL,
  status ENUM('pending','paid','cancelled') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE booking_rooms (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  room_id INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  nights INT NOT NULL,
  subtotal DECIMAL(12,2) NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
  FOREIGN KEY (room_id) REFERENCES rooms(id)
);

CREATE TABLE payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  method VARCHAR(50),
  note VARCHAR(255),
  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

INSERT INTO rooms (room_number, type, price) VALUES
('101', 'Single', 200000),
('102', 'Double', 350000),
('201', 'Suite', 700000),
('202', 'Double', 350000),
('301', 'Single', 180000);


INSERT INTO users (name, email, password, role) VALUES
('Administrator', 'admin@hotel.test', '$2y$10$exampleplaceholderhash', 'admin');

-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 24, 2025 at 04:52 AM
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
-- Database: `hotel_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking_rooms`
--

CREATE TABLE `booking_rooms` (
  `id` int NOT NULL,
  `booking_id` int NOT NULL,
  `room_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `nights` int NOT NULL,
  `subtotal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_rooms`
--

INSERT INTO `booking_rooms` (`id`, `booking_id`, `room_id`, `quantity`, `price`, `nights`, `subtotal`) VALUES
(1, 1, 2, 0, '350000.00', 2, '700000.00'),
(2, 2, 1, 0, '200000.00', 2, '400000.00'),
(3, 3, 1, 0, '200000.00', 2, '400000.00'),
(4, 4, 1, 0, '200000.00', 2, '400000.00'),
(5, 5, 1, 0, '200000.00', 2, '400000.00'),
(6, 6, 5, 0, '180000.00', 2, '360000.00'),
(7, 7, 1, 0, '200000.00', 2, '400000.00'),
(8, 8, 1, 0, '200000.00', 2, '400000.00'),
(15, 15, 1, 0, '200000.00', 2, '400000.00'),
(16, 16, 2, 0, '350000.00', 2, '700000.00'),
(17, 17, 4, 0, '350000.00', 2, '700000.00'),
(18, 18, 3, 0, '700000.00', 2, '1400000.00'),
(19, 29, 5, 1, '180000.00', 2, '360000.00'),
(20, 30, 5, 1, '180000.00', 2, '360000.00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking_rooms`
--
ALTER TABLE `booking_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `room_id` (`room_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking_rooms`
--
ALTER TABLE `booking_rooms`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking_rooms`
--
ALTER TABLE `booking_rooms`
  ADD CONSTRAINT `booking_rooms_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_rooms_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);
COMMIT;


