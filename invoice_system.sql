-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 16, 2024 at 06:44 AM
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
-- Database: `invoice_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `advance_payments`
--

CREATE TABLE `advance_payments` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_mode` varchar(50) DEFAULT NULL,
  `reference_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `advance_payments`
--

INSERT INTO `advance_payments` (`id`, `invoice_id`, `date`, `amount`, `payment_mode`, `reference_id`) VALUES
(4, 21, '2024-08-15', 50000.00, 'cash', '1235874'),
(5, 21, '2024-08-15', 100000.00, 'online', '258963'),
(6, 21, '2024-08-15', 50000.00, 'Cheque', 'N/A'),
(7, 22, '2024-08-15', 50000.00, 'cash', '1235874'),
(8, 23, '2024-07-15', 50000.00, 'cash', '1235874');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `billed_to` varchar(255) NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `issue_date` date NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `scope_of_work` text NOT NULL,
  `description` text NOT NULL,
  `total_project_value` decimal(10,2) NOT NULL,
  `advance_payment_date` date DEFAULT NULL,
  `advance_payment_amount` decimal(10,2) DEFAULT NULL,
  `total_received` decimal(10,2) NOT NULL,
  `pending_balance` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `billed_to`, `invoice_number`, `issue_date`, `project_name`, `scope_of_work`, `description`, `total_project_value`, `advance_payment_date`, `advance_payment_amount`, `total_received`, `pending_balance`, `created_at`) VALUES
(21, 'Srikanth', 'ppfw0527-150824', '2024-08-15', 'Arya', 'qwerty', 'Wikipedia is a free online encyclopedia, created and edited by volunteers around the world and hosted by the Wikimedia Foundation.', 200000.00, NULL, NULL, 200000.00, 0.00, '2024-08-15 18:26:38'),
(22, 'Srikanth Arya', 'ppfw0528-150824', '2024-08-15', 'Arya', 'qwerty', 'Wikipedia is a free online encyclopedia, created and edited by volunteers around the world and hosted by the Wikimedia Foundation.', 200000.00, NULL, NULL, 50000.00, 150000.00, '2024-08-15 18:40:15'),
(23, 'Srikanth', 'ppfw0529-150824', '2024-07-15', 'Arya', 'qwerty', 'Wikipedia is a free online encyclopedia, created and edited by volunteers around the world and hosted by the Wikimedia Foundation.', 200000.00, NULL, NULL, 50000.00, 150000.00, '2024-08-15 18:43:53');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_counter`
--

CREATE TABLE `invoice_counter` (
  `id` int(11) NOT NULL,
  `current_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice_counter`
--

INSERT INTO `invoice_counter` (`id`, `current_number`) VALUES
(1, 530);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `advance_payments`
--
ALTER TABLE `advance_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoice_counter`
--
ALTER TABLE `invoice_counter`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `advance_payments`
--
ALTER TABLE `advance_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `invoice_counter`
--
ALTER TABLE `invoice_counter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `advance_payments`
--
ALTER TABLE `advance_payments`
  ADD CONSTRAINT `advance_payments_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
