-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 23, 2026 at 04:00 PM
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
-- Database: `pocket_ai`
--

-- --------------------------------------------------------

--
-- Table structure for table `daily_quests`
--

CREATE TABLE `daily_quests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quest_name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_done` tinyint(1) NOT NULL DEFAULT 0,
  `reward_xp` int(11) NOT NULL DEFAULT 50,
  `reward_cash` decimal(10,2) NOT NULL DEFAULT 5.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daily_quests`
--

INSERT INTO `daily_quests` (`id`, `user_id`, `quest_name`, `is_active`, `is_done`, `reward_xp`, `reward_cash`) VALUES
(1, 1, 'Menabung RM5 Hari Ini', 1, 0, 50, 5.00),
(2, 1, 'Log Masuk Pocket AI', 1, 0, 50, 5.00),
(3, 1, 'Selesaikan Ring Keperluan', 1, 0, 50, 5.00),
(4, 2, 'Menabung RM5 Hari Ini', 1, 0, 50, 5.00),
(5, 2, 'Log Masuk Pocket AI', 1, 0, 50, 5.00),
(6, 2, 'Selesaikan Ring Keperluan', 1, 0, 50, 5.00);

-- --------------------------------------------------------

--
-- Table structure for table `financials`
--

CREATE TABLE `financials` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `monthly_allowance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `current_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `savings_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `spending_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bills_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `streak_count` int(11) NOT NULL DEFAULT 1,
  `total_wins` int(11) DEFAULT 0,
  `xp` int(11) NOT NULL DEFAULT 10,
  `last_streak_date` varchar(50) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `financials`
--

INSERT INTO `financials` (`id`, `user_id`, `monthly_allowance`, `current_balance`, `savings_amount`, `spending_amount`, `bills_amount`, `streak_count`, `total_wins`, `xp`, `last_streak_date`) VALUES
(1, 1, 100.00, 320.00, 150.00, 70.00, 200.00, 1, 8, 10, ''),
(2, 2, 700.00, 705.00, 0.00, 0.00, 0.00, 1, 1, 10, '');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` enum('income','spending','bill','saving') NOT NULL,
  `date_created` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `title`, `amount`, `type`, `date_created`) VALUES
(1, 1, 'Elaun Bulanan Mula Setup', 100.00, 'income', '23/05/2026'),
(2, 1, 'duit mama bagi', 50.00, 'saving', '23/05/2026'),
(3, 1, 'yuran', 200.00, 'bill', '23/05/2026'),
(4, 1, 'Quest Reward: Transport', 5.00, 'income', '23/05/2026'),
(5, 1, 'Quest Reward: Transport', 5.00, 'income', '23/05/2026'),
(6, 1, 'Quest Reward: Transport', 5.00, 'income', '23/05/2026'),
(7, 1, 'Quest Reward: Transport', 5.00, 'income', '23/05/2026'),
(8, 1, 'Quest Reward: Transport', 5.00, 'income', '23/05/2026'),
(9, 1, 'Quest Reward: Transport', 5.00, 'income', '23/05/2026'),
(10, 1, 'Quest Reward: Nasi Lemak', 5.00, 'income', '23/05/2026'),
(11, 1, 'Quest Reward: Nasi Lemak', 5.00, 'income', '23/05/2026'),
(12, 2, 'Elaun Bulanan Mula Setup', 700.00, 'income', '23/05/2026'),
(13, 2, 'Quest Completed: Nasi Lemak', 5.00, 'income', '23/05/2026'),
(14, 1, 'untung meniaga', 100.00, 'saving', '23/05/2026'),
(15, 1, 'beli pop mart', 55.00, 'spending', '23/05/2026'),
(16, 1, 'latte', 15.00, 'spending', '23/05/2026'),
(17, 1, 'zakat', 300.00, 'income', '23/05/2026');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `created_at`) VALUES
(1, 'Balqis', 'balqiskhairudin03@gmail.com', '$2y$10$Ltxw5HhKaBAiTGjmM310CuMVxJ9yziBf5vlcnqiUeAzyh2.nSJe4a', '2026-05-23 01:51:42'),
(2, 'Kautsar', 'kosa@gmail.com', '$2y$10$7c.3Z82gtmE9D8HDpqrfZ.19g.pf52wVhk6jb2weMQHkN4uC0DHDa', '2026-05-23 02:46:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `daily_quests`
--
ALTER TABLE `daily_quests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `financials`
--
ALTER TABLE `financials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `daily_quests`
--
ALTER TABLE `daily_quests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `financials`
--
ALTER TABLE `financials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `daily_quests`
--
ALTER TABLE `daily_quests`
  ADD CONSTRAINT `daily_quests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `financials`
--
ALTER TABLE `financials`
  ADD CONSTRAINT `financials_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
