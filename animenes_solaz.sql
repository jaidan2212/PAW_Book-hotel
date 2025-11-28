-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 27 Nov 2025 pada 22.34
-- Versi server: 10.6.23-MariaDB-cll-lve-log
-- Versi PHP: 8.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `animenes_solaz`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `booking_code` varchar(30) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `checkin_date` date NOT NULL,
  `checkout_date` date NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `status` enum('pending','paid','cancelled') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bookings`
--

INSERT INTO `bookings` (`id`, `booking_code`, `customer_name`, `customer_email`, `checkin_date`, `checkout_date`, `total_amount`, `status`, `created_at`) VALUES
(1, 'BKB30838A2', 'zaidan', 'ainurraftuzaki02@gmail.com', '2025-11-13', '2025-11-14', 190000.00, 'paid', '2025-11-24 12:57:18'),
(2, 'BK796BCFE5', 'zaidan', 'ainurraftuzaki02@gmail.com', '2025-11-25', '2025-11-26', 171000.00, 'pending', '2025-11-24 15:38:26'),
(3, 'BK0786E9EA', 'zaidan', 'ainurraftuzaki02@gmail.com', '2025-11-29', '2025-11-30', 180000.00, 'paid', '2025-11-24 15:38:50'),
(4, 'BK2D86144F', 'zaidan', 'ainurraftuzaki02@gmail.com', '2025-11-25', '2025-11-26', 665000.00, 'paid', '2025-11-24 15:39:28'),
(5, 'BKEA72EB81', 'Hendrik Purwanto', 'hendrikpurwanto281@gmail.com', '2025-11-25', '2025-11-26', 332500.00, 'paid', '2025-11-25 01:41:35'),
(6, 'BKF8E65779', 'izzul', 'izzulgtg@gmail.com', '2025-11-25', '2025-11-26', 665000.00, 'paid', '2025-11-25 01:48:08'),
(7, 'BK81C96715', 'izzul', 'izzulgtg@gmail.com', '2025-11-25', '2025-11-26', 665000.00, 'paid', '2025-11-25 02:04:06'),
(8, 'BK45F3B0DD', 'Zaidan', 'zaidannabil2212@gmail.com', '2025-11-26', '2025-11-27', 665000.00, 'paid', '2025-11-25 03:18:13'),
(9, 'BK6317C9D9', 'Idan', 'idan@gmail.com', '2025-11-25', '2025-11-26', 332500.00, 'paid', '2025-11-25 09:45:49');

-- --------------------------------------------------------

--
-- Struktur dari tabel `booking_rooms`
--

CREATE TABLE `booking_rooms` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `nights` int(11) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `booking_rooms`
--

INSERT INTO `booking_rooms` (`id`, `booking_id`, `room_id`, `price`, `nights`, `subtotal`, `quantity`) VALUES
(1, 1, 1, 190000.00, 1, 190000.00, 1),
(2, 2, 5, 171000.00, 1, 171000.00, 1),
(3, 3, 5, 180000.00, 1, 180000.00, 1),
(4, 4, 6, 665000.00, 1, 665000.00, 1),
(5, 5, 2, 332500.00, 1, 332500.00, 1),
(6, 6, 6, 665000.00, 1, 665000.00, 1),
(7, 7, 3, 665000.00, 1, 665000.00, 1),
(8, 8, 6, 665000.00, 1, 665000.00, 1),
(9, 9, 4, 332500.00, 1, 332500.00, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_date` timestamp NULL DEFAULT current_timestamp(),
  `method` varchar(50) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `payment_status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `amount`, `payment_date`, `method`, `note`, `payment_status`) VALUES
(1, 1, 190000.00, '2025-11-24 13:10:22', 'Transfer', '', 'pending'),
(2, 3, 180000.00, '2025-11-24 15:39:00', 'Cash', '', 'pending'),
(3, 4, 665000.00, '2025-11-24 15:39:30', 'Cash', '', 'pending'),
(4, 5, 332500.00, '2025-11-25 01:41:38', 'Cash', '', 'pending'),
(5, 6, 665000.00, '2025-11-25 01:48:18', 'Cash', '', 'pending'),
(6, 7, 665000.00, '2025-11-25 02:04:12', 'Cash', '', 'pending'),
(7, 8, 665000.00, '2025-11-25 03:18:19', 'Cash', '', 'pending'),
(8, 9, 332500.00, '2025-11-25 09:46:12', 'Cash', '', 'pending');

-- --------------------------------------------------------

--
-- Struktur dari tabel `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_number` varchar(20) NOT NULL,
  `type` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `max_person` int(11) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('available','booked','maintenance') DEFAULT 'available',
  `stock` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `rooms`
--

INSERT INTO `rooms` (`id`, `room_number`, `type`, `price`, `max_person`, `description`, `image`, `status`, `stock`, `created_at`) VALUES
(1, '101', 'Single', 200000.00, 1, NULL, NULL, 'booked', 0, '2025-11-24 05:15:15'),
(2, '102', 'Double', 350000.00, 1, NULL, NULL, 'booked', 0, '2025-11-24 05:15:15'),
(3, '201', 'Suite', 700000.00, 1, NULL, NULL, 'booked', 0, '2025-11-24 05:15:15'),
(4, '202', 'Double', 350000.00, 1, NULL, NULL, 'booked', 0, '2025-11-24 05:15:15'),
(5, '301', 'Single', 180000.00, 1, NULL, NULL, 'booked', 0, '2025-11-24 05:15:15'),
(6, '201', 'Deluxe', 700000.00, 1, NULL, NULL, 'booked', 0, '2025-11-24 13:34:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','customer') DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Administrator', 'admin@hotel.test', '$2y$10$exampleplaceholderhash', 'admin', '2025-11-24 05:15:15');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_code` (`booking_code`);

--
-- Indeks untuk tabel `booking_rooms`
--
ALTER TABLE `booking_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indeks untuk tabel `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indeks untuk tabel `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `booking_rooms`
--
ALTER TABLE `booking_rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `booking_rooms`
--
ALTER TABLE `booking_rooms`
  ADD CONSTRAINT `booking_rooms_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_rooms_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

--
-- Ketidakleluasaan untuk tabel `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
