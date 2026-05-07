-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 07, 2026 at 06:13 AM
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
-- Database: `event_managements`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `event_date` date NOT NULL,
  `guest_count` int(11) DEFAULT 50,
  `special_requests` text DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','ongoing','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `customer_id`, `package_id`, `manager_id`, `event_date`, `guest_count`, `special_requests`, `total_amount`, `status`, `created_at`) VALUES
(1, 2, 1, 5, '2026-04-29', 10000, '', 225000.00, 'completed', '2026-04-27 15:21:13');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `customer_id`, `booking_id`, `rating`, `comment`, `created_at`) VALUES
(1, 2, 1, 3, '', '2026-04-28 02:40:37'),
(2, 2, 1, 5, 'good service', '2026-04-28 10:31:28');

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `category` enum('wedding','birthday','conference','corporate','other') DEFAULT 'other',
  `description` text DEFAULT NULL,
  `venue` varchar(200) DEFAULT NULL,
  `capacity` int(11) DEFAULT 100,
  `price` decimal(10,2) NOT NULL,
  `discount` decimal(5,2) DEFAULT 0.00,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `name`, `category`, `description`, `venue`, `capacity`, `price`, `discount`, `image`, `status`, `created_at`) VALUES
(1, 'Royal Wedding Package', 'wedding', 'A luxurious wedding experience with full decoration, catering, and photography.', 'Grand Ballroom, Dhaka', 500, 250000.00, 10.00, NULL, 'active', '2026-04-26 19:25:24'),
(2, 'Birthday Bash Package', 'birthday', 'Fun-filled birthday celebration with cake, decorations, and entertainment.', 'Event Hall, Gulshan', 100, 25000.00, 5.00, NULL, 'active', '2026-04-26 19:25:24'),
(3, 'Corporate Summit Package', 'conference', 'Professional conference setup with AV equipment, seating, and catering.', 'Conference Center, Motijheel', 300, 80000.00, 0.00, NULL, 'active', '2026-04-26 19:25:24'),
(4, 'Premium Corporate Package', 'corporate', 'All-inclusive corporate event with branding, catering, and team activities.', 'Rooftop Venue, Banani', 200, 120000.00, 8.00, NULL, 'active', '2026-04-26 19:25:24'),
(5, '', '', '', '', 0, 0.00, 0.00, NULL, 'inactive', '2026-04-27 15:22:45'),
(6, 'Any ', 'other', 'hello bu', 'BU', 500, 50000.00, 15.00, NULL, 'active', '2026-04-28 07:39:52'),
(7, '', 'corporate', '0000', 'cu', 2, 10.00, 2.00, NULL, 'inactive', '2026-04-28 07:42:29'),
(8, 'hello', 'corporate', '', 'buuuu', 1, 4.00, 0.00, NULL, 'inactive', '2026-04-28 07:43:59'),
(9, 'ggg', 'other', 'hhhh', 'ggg', 10, 777.00, 1.00, NULL, 'active', '2026-04-28 15:27:02'),
(10, '', 'conference', '', '', 0, 0.00, 0.00, NULL, 'inactive', '2026-05-06 05:08:14');

-- --------------------------------------------------------

--
-- Table structure for table `package_services`
--

CREATE TABLE `package_services` (
  `id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `service_name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `package_services`
--

INSERT INTO `package_services` (`id`, `package_id`, `service_name`, `description`) VALUES
(1, 1, 'Full Decoration', 'Floral arrangements, lighting, stage setup'),
(2, 1, 'Catering', '5-course meal for all guests'),
(3, 1, 'Photography & Video', 'Professional photographers and videographers'),
(4, 1, 'Music & Entertainment', 'Live band and DJ'),
(5, 2, 'Decoration', 'Birthday themed decoration'),
(6, 2, 'Cake', 'Custom 3-tier cake'),
(7, 2, 'Catering', 'Snacks and meals for guests'),
(8, 3, 'AV Equipment', 'Projectors, microphones, sound system'),
(9, 3, 'Catering', 'Lunch and refreshments'),
(10, 3, 'Seating', 'Conference-style seating arrangement'),
(11, 4, 'Branding', 'Custom banners and branding materials'),
(12, 4, 'Full Catering', 'All meals and beverages'),
(13, 4, 'Team Activities', 'Guided team building exercises');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` enum('cash','card','bank_transfer','mobile') DEFAULT 'cash',
  `status` enum('pending','paid','refunded') DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `amount`, `method`, `status`, `paid_at`, `created_at`) VALUES
(1, 1, 225000.00, 'bank_transfer', 'paid', '2026-04-27 16:25:31', '2026-04-27 15:21:13');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `service_type` enum('catering','decoration','venue_setup','photography','music','other') DEFAULT 'other',
  `status` enum('pending','in_progress','completed') DEFAULT 'pending',
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `booking_id`, `staff_id`, `title`, `description`, `service_type`, `status`, `due_date`, `created_at`) VALUES
(1, 1, 6, 'decoration', '', 'decoration', 'completed', '2026-04-29', '2026-04-28 02:27:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','customer','manager','staff') DEFAULT 'customer',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role`, `status`, `created_at`) VALUES
(1, 'Super Admin', 'admin@eventpro.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 'admin', 'active', '2026-04-26 19:25:24'),
(2, 'Abu Bakar Miaji', 'abu.bakar.miaji@gmail.com', '$2y$10$ULdi/6Ub16Xd24brdBKKG.Q0s1KfcAYVCHnVpJEVOr0Vz7qKKLeoa', '01611820481', 'customer', 'active', '2026-04-27 15:09:13'),
(5, 'miaji', 'miaji@gmail.com', '$2y$10$TRKQ/YoWGR4JYJKgLC3RJuKSNqgfxl5YUGBh0GhMl/0vgk4Ga3cdS', '01838796365', 'manager', 'active', '2026-04-28 02:24:17'),
(6, 'Abu Bakar Miaji', 'abu@gmail.com', '$2y$10$MIU1dHaEcfD4Ip7h0.slYOiDEVhXHqFLnZ4O0QdFm0epHfzh3YTea', '01611820481', 'staff', 'active', '2026-04-28 02:24:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `package_id` (`package_id`),
  ADD KEY `manager_id` (`manager_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `package_services`
--
ALTER TABLE `package_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `package_services`
--
ALTER TABLE `package_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`),
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`);

--
-- Constraints for table `package_services`
--
ALTER TABLE `package_services`
  ADD CONSTRAINT `package_services_ibfk_1` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`);

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`),
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
