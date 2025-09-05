-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 05, 2025 at 09:18 AM
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
-- Database: `sari_sari_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_unsent` tinyint(1) DEFAULT 0,
  `deleted_by_sender` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_by_receiver` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `sender_id`, `receiver_id`, `message`, `created_at`, `is_unsent`, `deleted_by_sender`, `deleted_by_receiver`) VALUES
(1, 16, 15, 'wakwak', '2025-09-05 05:24:54', 0, 0, 0),
(2, 16, 17, 'wakwak', '2025-09-05 05:26:43', 0, 0, 0),
(3, 16, 17, 'pangittt', '2025-09-05 05:26:53', 0, 0, 0),
(4, 17, 16, '', '2025-09-05 05:29:32', 1, 0, 0),
(5, 17, 16, '', '2025-09-05 05:54:48', 1, 0, 0),
(7, 16, 17, 'samad ka??', '2025-09-05 05:58:23', 0, 0, 0),
(8, 17, 16, '', '2025-09-05 06:04:33', 1, 0, 0),
(9, 16, 17, 'asdqweqwe', '2025-09-05 06:04:53', 0, 0, 0),
(10, 16, 14, 'sadqweqwe', '2025-09-05 06:04:59', 0, 0, 0),
(21, 16, 17, 'sad', '2025-09-05 06:25:32', 1, 0, 0),
(22, 17, 16, 'haysssss ana2 lang', '2025-09-05 06:27:27', 0, 0, 0),
(23, 17, 16, 'buanggggggggg', '2025-09-05 06:29:04', 0, 0, 0),
(24, 16, 17, 'ikaw sad!', '2025-09-05 06:29:14', 0, 0, 0),
(25, 16, 17, 'wakwak', '2025-09-05 06:42:21', 0, 0, 0),
(26, 16, 17, 'sadqweqwe', '2025-09-05 06:47:01', 0, 0, 0),
(27, 17, 16, '', '2025-09-05 06:47:12', 1, 0, 0),
(28, 17, 16, 'hi poo', '2025-09-05 07:14:12', 0, 0, 0),
(29, 17, 16, 'kumusta kana akii?', '2025-09-05 07:14:19', 0, 0, 0),
(30, 16, 17, 'okay lang naman! ikaw ba?', '2025-09-05 07:14:27', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `deleted_messages`
--

CREATE TABLE `deleted_messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deleted_messages`
--

INSERT INTO `deleted_messages` (`id`, `user_id`, `message_id`) VALUES
(12, 16, 2),
(11, 16, 3),
(21, 16, 4),
(6, 16, 5),
(19, 16, 7),
(22, 16, 8),
(20, 16, 9),
(9, 16, 22),
(10, 16, 23),
(8, 16, 24),
(2, 16, 25),
(1, 16, 26),
(23, 16, 27),
(13, 17, 2),
(17, 17, 3),
(18, 17, 7),
(14, 17, 9),
(15, 17, 21),
(7, 17, 22),
(5, 17, 23),
(4, 17, 24),
(3, 17, 25),
(16, 17, 26);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_log`
--

CREATE TABLE `inventory_log` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` enum('add','remove','adjust') NOT NULL,
  `quantity` int(11) NOT NULL,
  `old_stock` int(11) NOT NULL,
  `new_stock` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `stock`, `description`, `created_at`) VALUES
(1, 'Red Horse', 120.00, 0, '1L', '2025-05-27 14:26:42'),
(4, 'Sardines (555 or Ligo)', 20.00, 5, '155g can ', '2025-05-27 16:11:49'),
(6, 'Lucky Me! Pancit Canton', 13.00, 0, 'Per pack', '2025-05-27 18:04:54'),
(7, 'Argentina Corned Beef', 35.00, 10, '150g can', '2025-05-27 18:06:20'),
(8, 'Piattos', 10.00, 9, 'Small pack', '2025-05-27 18:06:41'),
(9, 'Chippy', 10.00, 9, 'Small Pack', '2025-05-27 18:07:05'),
(10, 'Rebisco Crackers', 8.00, 10, 'Single Pack', '2025-05-27 18:07:29'),
(11, 'Hansel Mocha Biscuits', 8.00, 10, 'Small Pack', '2025-05-27 18:07:57'),
(12, 'White Rabbit Candy', 1.00, 4, 'Per piece', '2025-05-27 18:08:16'),
(13, 'Maxx Menthol Candy', 1.00, 8, 'Per piece', '2025-05-27 18:08:31'),
(14, 'Tang Orange Powder', 8.00, 10, 'Sachet (25g)', '2025-05-27 18:08:51'),
(15, 'Milo Powder', 10.00, 10, 'Sachet (22g)', '2025-05-27 18:09:16'),
(16, 'Nescafé Classic', 7.00, 10, 'Sachet (2g)', '2025-05-27 18:09:36'),
(17, 'Kopiko Blanca', 8.00, 10, 'Sachet (27g)', '2025-05-27 18:09:59'),
(18, 'Bottled Water (Nature Spring)', 15.00, 9, '500ml', '2025-05-27 18:10:35'),
(19, 'Coke/Sprite (Glass bottle)', 15.00, 8, '250ml', '2025-05-27 18:10:57'),
(20, 'Cooking Oil', 25.00, 10, '250ml (in bottle/plastic)', '2025-05-27 18:11:17'),
(21, 'Ice cubes', 15.00, 3, 'Per pack', '2025-05-28 02:39:59'),
(22, '     Stick Bread', 45.00, 4, '200g', '2025-05-28 05:36:39'),
(25, 'Colt45s', 35.00, 150, '500ml (in bottle)', '2025-09-05 07:15:58');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `receipt_number` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `cash_received` decimal(10,2) NOT NULL,
  `change_given` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `receipt_number`, `total_amount`, `cash_received`, `change_given`, `created_at`) VALUES
(14, 'RECEIPT-683ff16704356', 24000.00, 25000.00, 1000.00, '2025-06-04 07:10:31'),
(15, 'RECEIPT-68b2d61a5c9fc', 75000.00, 75500.00, 500.00, '2025-08-30 10:44:42'),
(16, 'RECEIPT-68ba8e3aeea47', 175.00, 200.00, 25.00, '2025-09-05 07:16:10');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_items`
--

CREATE TABLE `transaction_items` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_items`
--

INSERT INTO `transaction_items` (`id`, `transaction_id`, `product_name`, `quantity`, `price`, `created_at`) VALUES
(29, 14, 'Redhorse', 200, 120.00, '2025-06-04 07:10:31'),
(30, 15, 'Redhorse', 600, 125.00, '2025-08-30 10:44:42'),
(31, 16, 'Colt45s', 5, 35.00, '2025-09-05 07:16:10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user','cashier') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_active` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `email`, `password`, `role`, `created_at`, `last_active`) VALUES
(7, '', 'Neil Vincent Dionio', 'neilvincentdionio@gmail.com', '$2y$10$6B7nwqEYi85nWwjB6nU85.oEidmQ/4tb28/qyz4Cd9W6LlKR0NKPO', 'user', '2025-05-27 18:13:29', NULL),
(14, '', 'wakwak', 'wakwak123@gmail.com', '$2y$10$U10SWUnSkGY4RMN28yYhYueWkNHH2.qC3cwcg2vD3WNyULlg5bY.a', 'user', '2025-08-30 10:43:42', NULL),
(16, '', 'akiyori143', 'akiyori143@gmail.com', '$2y$10$TRQjDLv9OJIbBgP2efrnDuQ5sdt.opwKdoAHtYQsfvrDu4ZGMHNX2', 'user', '2025-09-05 04:43:22', '2025-09-05 15:17:13'),
(17, '', 'abbygarciaa', 'abbygarciaa@gmail.com', '$2y$10$ENiR5iKoejBOLJ6s4Gzfoe5w7fkMiDdrv6sva13thprEH.Iy.xnLy', 'user', '2025-09-05 05:25:40', '2025-09-05 15:16:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deleted_messages`
--
ALTER TABLE `deleted_messages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_delete` (`user_id`,`message_id`);

--
-- Indexes for table `inventory_log`
--
ALTER TABLE `inventory_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `deleted_messages`
--
ALTER TABLE `deleted_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `inventory_log`
--
ALTER TABLE `inventory_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventory_log`
--
ALTER TABLE `inventory_log`
  ADD CONSTRAINT `inventory_log_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `inventory_log_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD CONSTRAINT `transaction_items_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
