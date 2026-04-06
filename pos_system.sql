-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 06, 2026 at 11:14 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pos_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `status`) VALUES
(1, 'Beverages', 'Raksi haru', 1),
(2, 'Snacks', NULL, 1),
(3, 'Household', NULL, 1),
(4, 'Electronics', 'Phone, Laptop, TV, Electronic applicance', 1),
(5, 'Tama Pital', '', 1),
(7, 'Autoparts', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `email`, `address`, `created_at`) VALUES
(1, 'Sandip Khadka', '1234', 'sandip@gmail.com', 'MuhanPokhari, Kathmandu', '2025-12-10 12:09:33'),
(2, 'Ajay', '123123', 'ajayuser@gmail.com', 'Nepal', '2025-12-11 20:02:52'),
(3, 'Ajay', '123454321', 'ajayuser@gmail.com', 'Nepal', '2025-12-11 20:07:21'),
(4, 'Sandip2', '1231231', 'sandip2@gmail.com', '1233 nepal', '2025-12-13 10:54:49'),
(5, 'Sanpies', '7408740709', 'nabin.ebpearls@gmail.com', '', '2025-12-13 18:54:58'),
(6, 'sanpies', '74080740709', 'kitchenarniko@gmail.com', '', '2025-12-14 03:45:59'),
(7, 'hj', '9818157676', 'kitchenarniko@gmail.com', 'ikik', '2026-02-08 03:28:17'),
(8, '123', '9845653215', 'arnikokitchen@gmail.com', 'ktm', '2026-02-08 03:49:12'),
(9, 'Anish', '123456789', '', '', '2026-03-17 03:42:02'),
(10, 'Sanpies', '9865332831', '', '', '2026-03-17 04:38:11');

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `name`, `status`) VALUES
(1, 'Cash', 1),
(2, 'Card', 1),
(3, 'Esewa', 1),
(4, 'Khalti', 1),
(5, 'Mobile Wallet', 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `cost` decimal(10,2) DEFAULT 0.00,
  `stock` int(11) DEFAULT 0,
  `reorder_level` int(11) DEFAULT 5,
  `status` tinyint(4) DEFAULT 1,
  `discount_allowed` tinyint(4) DEFAULT 1,
  `product_discount` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `cost`, `stock`, `reorder_level`, `status`, `discount_allowed`, `product_discount`, `created_at`) VALUES
(1, 4, 'Iphone 17 pro max', 'Red, 512gb', 1200.00, 0.00, 2, 5, 1, 1, 2.00, '2025-12-10 19:14:27'),
(2, 1, 'Beer', '', 380.00, 0.00, 32, 5, 1, 1, 0.00, '2025-12-13 10:53:22'),
(3, 4, 'Iphone X', '', 100.00, 0.00, 100, 5, 1, 1, 0.00, '2025-12-13 11:24:57'),
(4, 5, 'Panas', '', 7408.00, 0.00, 3, 5, 1, 1, 0.00, '2025-12-13 18:40:21'),
(5, 4, 'Dell Laptop', '', 745.00, 0.00, 5, 5, 1, 1, 0.00, '2025-12-14 04:17:53'),
(6, 7, 'Clutch Cable', '', 74.00, 0.00, 3, 5, 1, 1, 0.00, '2025-12-14 04:19:27'),
(7, 3, 'Triply Cooker 3 Liter', '', 5025.00, 0.00, 10, 5, 1, 1, 0.00, '2026-03-17 02:50:50'),
(8, 3, 'Triply Cooker 5 Liter', '', 3025.00, 0.00, 10, 5, 1, 1, 0.00, '2026-03-17 02:51:24'),
(9, 3, 'Diamond Contura HA 1 liter Cooker ', '', 2350.00, 0.00, 0, 5, 1, 1, 0.00, '2026-03-17 02:52:11'),
(10, 3, 'Diamond Contura HA 2 liter Cooker ', '', 2650.00, 0.00, 0, 5, 1, 1, 0.00, '2026-03-17 02:52:29'),
(11, 3, 'Diamond Contura HA 3 liter Cooker ', '', 2850.00, 0.00, 9, 5, 1, 1, 0.00, '2026-03-17 02:52:49'),
(12, 3, 'Hwakins Triply Kadhai 20 cm', '', 3250.00, 0.00, 15, 5, 1, 1, 0.00, '2026-03-17 04:08:41'),
(13, 3, 'Hwakins Triply Kadhai 22 cm', '', 3450.00, 0.00, 14, 5, 1, 1, 0.00, '2026-03-17 04:09:08'),
(14, 3, 'Futura HA 3 liter Cooker', '', 3550.00, 0.00, 21, 5, 1, 1, 0.00, '2026-03-17 04:09:44');

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

CREATE TABLE `refunds` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `refund_amount` decimal(10,2) NOT NULL,
  `reason` text DEFAULT NULL,
  `processed_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `invoice_no` varchar(50) DEFAULT NULL,
  `bill_id` varchar(50) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  `bill_discount` decimal(10,2) DEFAULT 0.00,
  `bill_tax` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_refunded` tinyint(1) DEFAULT 0,
  `refunded_by` int(11) DEFAULT NULL,
  `refunded_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `invoice_no`, `bill_id`, `customer_id`, `subtotal`, `bill_discount`, `bill_tax`, `total`, `payment_method`, `created_by`, `created_at`, `is_refunded`, `refunded_by`, `refunded_at`) VALUES
(1, 'INV20251213121909', 'B1765624749', 4, 760.00, 10.00, 0.00, 684.00, 'cash', 2, '2025-12-13 11:19:09', 1, 2, '2025-12-13 17:40:20'),
(2, 'INV20251213124850', 'B1765626530', 2, 1000.00, 0.00, 0.00, 1000.00, 'cash', 2, '2025-12-13 11:48:50', 1, 2, '2025-12-14 00:27:59'),
(3, 'INV20251213195521', 'B1765652121', 5, 7408.00, 0.00, 0.00, 7408.00, 'cash', 2, '2025-12-13 18:55:21', 0, NULL, NULL),
(4, 'INV20251214051328', 'B1765685608', 5, 7408.00, 0.00, 0.00, 7408.00, 'card', 2, '2025-12-14 04:13:28', 0, NULL, NULL),
(5, 'INV20260208042828', 'B1770521308', 7, 7408.00, 0.00, 0.00, 7408.00, 'cash', 2, '2026-02-08 03:28:28', 0, NULL, NULL),
(6, 'INV20260208045145', 'B1770522705', 8, 148.00, 0.00, 13.00, 167.24, 'card', 2, '2026-02-08 03:51:45', 0, NULL, NULL),
(7, 'INV20260317044208', 'B1773718928', 9, 2350.00, 1.00, 0.00, 2326.50, 'cash', 2, '2026-03-17 03:42:08', 0, NULL, NULL),
(8, 'INV20260317053815', 'B1773722295', 10, 53250.00, 0.00, 0.00, 53250.00, 'cash', 2, '2026-03-17 04:38:15', 0, NULL, NULL),
(9, 'INV20260317053858', 'B1773722338', 10, 23500.00, 0.00, 0.00, 23500.00, 'cash', 2, '2026-03-17 04:38:58', 0, NULL, NULL),
(10, 'INV20260322040309', 'B1774148589', 8, 35250.00, 0.00, 0.00, 35250.00, 'card', 2, '2026-03-22 03:03:09', 0, NULL, NULL),
(11, 'INV20260322040422', 'B1774148662', 8, 2850.00, 0.00, 0.00, 2850.00, 'card', 2, '2026-03-22 03:04:22', 0, NULL, NULL),
(12, 'INV20260322040514', 'B1774148714', 8, 2850.00, 0.00, 0.00, 2850.00, 'cash', 2, '2026-03-22 03:05:14', 1, 4, '2026-03-23 08:32:08'),
(13, 'INV20260322040613', 'B1774148773', 5, 28500.00, 0.00, 0.00, 28500.00, 'cash', 2, '2026-03-22 03:06:13', 0, NULL, NULL),
(14, 'INV20260322041733', 'B1774149453', 8, 39050.00, 0.00, 0.00, 39050.00, 'cash', 2, '2026-03-22 03:17:33', 1, 4, '2026-03-23 08:30:48'),
(15, 'INV20260323025143', 'B1774230703', 8, 21150.00, 0.00, 0.00, 21150.00, 'cash', 2, '2026-03-23 01:51:43', 1, 2, '2026-03-23 08:29:50'),
(16, 'INV20260323034755', 'B1774234075', 2, 1000.00, 0.00, 0.00, 1000.00, 'card', 4, '2026-03-23 02:47:55', 1, 4, '2026-03-23 08:33:11'),
(17, 'INV20260323035150', 'B1774234310', 2, 12000.00, 0.00, 0.00, 12000.00, 'card', 2, '2026-03-23 02:51:50', 0, NULL, NULL),
(18, 'INV20260323035953', 'B1774234793', 8, 47650.00, 0.00, 0.00, 47650.00, 'cash', 2, '2026-03-23 02:59:53', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sales_items`
--

CREATE TABLE `sales_items` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_items`
--

INSERT INTO `sales_items` (`id`, `sale_id`, `product_id`, `qty`, `price`, `discount`, `total`) VALUES
(1, 1, 2, 2, 380.00, 0.00, 760.00),
(2, 2, 3, 10, 100.00, 0.00, 1000.00),
(3, 3, 4, 1, 7408.00, 0.00, 7408.00),
(4, 4, 4, 1, 7408.00, 0.00, 7408.00),
(5, 5, 4, 1, 7408.00, 0.00, 7408.00),
(6, 6, 6, 2, 74.00, 0.00, 148.00),
(7, 7, 9, 1, 2350.00, 0.00, 2350.00),
(8, 8, 14, 15, 3550.00, 0.00, 53250.00),
(9, 9, 9, 10, 2350.00, 0.00, 23500.00),
(10, 10, 9, 15, 2350.00, 0.00, 35250.00),
(11, 11, 11, 1, 2850.00, 0.00, 2850.00),
(12, 12, 11, 1, 2850.00, 0.00, 2850.00),
(13, 13, 11, 10, 2850.00, 0.00, 28500.00),
(14, 14, 14, 11, 3550.00, 0.00, 39050.00),
(15, 15, 9, 9, 2350.00, 0.00, 21150.00),
(16, 16, 3, 10, 100.00, 0.00, 1000.00),
(17, 17, 1, 10, 1200.00, 0.00, 12000.00),
(18, 18, 9, 9, 2350.00, 0.00, 21150.00),
(19, 18, 10, 10, 2650.00, 0.00, 26500.00);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `store_name` varchar(255) DEFAULT NULL,
  `store_address` text DEFAULT NULL,
  `store_phone` varchar(50) DEFAULT NULL,
  `bill_tax_enabled` tinyint(4) DEFAULT 0,
  `bill_tax_rate` decimal(5,2) DEFAULT 0.00,
  `receipt_footer` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `store_name`, `store_address`, `store_phone`, `bill_tax_enabled`, `bill_tax_rate`, `receipt_footer`) VALUES
(1, 'My POS Store', '123 street Baluwatar, Kathmandu', '9876543210', 0, 0.00, 'Thank you for shopping!');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','cashier') DEFAULT 'cashier',
  `status` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `role`, `status`, `created_at`) VALUES
(1, 'Admin', 'admin', '0192023a7bbd73250516f069df18b500', 'admin', 1, '2025-12-10 08:11:07'),
(2, 'Admin', 'admin123', 'admin123', 'admin', 1, '2025-12-10 11:03:59'),
(4, 'user', 'testuser', 'user@123', 'cashier', 1, '2025-12-10 11:40:49'),
(5, 'sanpies', 'sanpies7', '$2y$10$FHXjtEI7pP9GgEzNnawqYOurKvt3wBC62yByYb3AEG//HvDc/i9pS', 'cashier', 1, '2025-12-14 03:57:06'),
(6, 'Sandip Khadka', 'Sandip', '$2y$10$i8iFBt3pgL0g58Hry/8Um.aUL9XbaORLE3yJF/mCQP2Btp5yTwCXq', 'cashier', 1, '2026-03-17 02:47:59'),
(7, 'Sudridh Dangi', 'Sudridh', '$2y$10$mV5fc3/HwlkSShTJC5tQfelaAYr1P75RWsPXgBDYwoFgX0cBmUSRK', 'cashier', 1, '2026-03-17 03:48:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `processed_by` (`processed_by`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_no` (`invoice_no`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `sales_items`
--
ALTER TABLE `sales_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `refunds`
--
ALTER TABLE `refunds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `sales_items`
--
ALTER TABLE `sales_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `refunds`
--
ALTER TABLE `refunds`
  ADD CONSTRAINT `refunds_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`),
  ADD CONSTRAINT `refunds_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `refunds_ibfk_3` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `sales_items`
--
ALTER TABLE `sales_items`
  ADD CONSTRAINT `sales_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`),
  ADD CONSTRAINT `sales_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
