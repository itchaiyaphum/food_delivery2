-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 13, 2022 at 03:41 AM
-- Server version: 5.6.38
-- PHP Version: 7.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `food_delivery`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `food_id` int(11) NOT NULL,
  `food_amount` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `food_category`
--

CREATE TABLE `food_category` (
  `id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `food_category`
--

INSERT INTO `food_category` (`id`, `restaurant_id`, `title`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 'ประเภทต้ม', 1, '2022-11-28 00:00:00', '2022-12-11 15:19:43'),
(2, 2, 'ประเภทผัด', 1, '2022-11-28 00:00:00', '2022-12-11 15:27:21'),
(4, 2, 'ประเภททอด', 1, '2022-12-11 15:30:08', '2022-12-11 15:30:08'),
(5, 0, 'ประเภทเผา', 1, '2022-12-11 16:32:52', '2022-12-11 16:32:52');

-- --------------------------------------------------------

--
-- Table structure for table `food_menu`
--

CREATE TABLE `food_menu` (
  `id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL DEFAULT '0',
  `food_category_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `price` int(11) NOT NULL DEFAULT '0',
  `discount_percent` tinyint(2) NOT NULL DEFAULT '0',
  `thumbnail` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `food_menu`
--

INSERT INTO `food_menu` (`id`, `restaurant_id`, `food_category_id`, `title`, `price`, `discount_percent`, `thumbnail`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'ต้มยำปลากระบอก (หม้อไฟ)', 100, 10, '/storage/food/91d2cd2b1e21143db7580cc3b956fffb.png', 1, '2022-11-28 00:00:00', '2022-12-11 16:30:52'),
(2, 2, 4, 'ปลากระพงทอดน้ำปลา', 300, 0, '/storage/food/ebbb2f0e5d0f2c548962073afd26b2d9.png', 1, '2022-11-28 00:00:00', '2022-12-11 16:31:22'),
(4, 2, 5, 'กุ้งเผา', 500, 10, '/storage/food/d13ccbfb9d1edc2803c569a0667f5883.png', 1, '2022-12-11 16:31:45', '2022-12-11 16:32:58');

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `restaurant_id` int(11) NOT NULL DEFAULT '0',
  `rider_id` int(11) NOT NULL DEFAULT '0',
  `total_price` double NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `review_status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`id`, `customer_id`, `restaurant_id`, `rider_id`, `total_price`, `created_at`, `updated_at`, `status`, `review_status`) VALUES
(5, 4, 2, 0, 90, '2022-12-12 00:59:36', '2022-12-12 00:59:36', 1, 0),
(6, 4, 2, 0, 900, '2022-12-12 01:48:07', '2022-12-12 10:34:19', 1, 0),
(7, 4, 2, 3, 600, '2022-12-12 01:48:28', '2022-12-12 20:13:07', 3, 0),
(8, 4, 2, 0, 270, '2022-12-12 09:31:49', '2022-12-12 09:31:49', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `order_item`
--

CREATE TABLE `order_item` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL DEFAULT '0',
  `food_id` int(11) NOT NULL DEFAULT '0',
  `food_price` double NOT NULL DEFAULT '0',
  `food_discount_price` double NOT NULL DEFAULT '0',
  `food_amount` double NOT NULL,
  `food_total` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `order_item`
--

INSERT INTO `order_item` (`id`, `order_id`, `food_id`, `food_price`, `food_discount_price`, `food_amount`, `food_total`) VALUES
(3, 3, 1, 100, 90, 1, 90),
(4, 3, 1, 100, 90, 1, 90),
(5, 3, 1, 100, 90, 1, 90),
(6, 3, 1, 100, 90, 1, 90),
(7, 3, 1, 100, 90, 1, 90),
(8, 3, 1, 100, 90, 1, 90),
(9, 3, 2, 300, 300, 1, 300),
(10, 3, 2, 300, 300, 1, 300),
(11, 3, 4, 500, 450, 1, 450),
(12, 3, 4, 500, 450, 1, 450),
(23, 5, 1, 100, 90, 1, 90),
(24, 6, 4, 500, 450, 1, 450),
(25, 6, 4, 500, 450, 1, 450),
(26, 7, 2, 300, 300, 1, 300),
(27, 7, 2, 300, 300, 1, 300),
(28, 8, 1, 100, 90, 1, 90),
(29, 8, 1, 100, 90, 1, 90),
(30, 8, 1, 100, 90, 1, 90);

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_type`
--

CREATE TABLE `restaurant_type` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `thumbnail` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `restaurant_type`
--

INSERT INTO `restaurant_type` (`id`, `title`, `thumbnail`, `status`, `created_at`, `updated_at`) VALUES
(1, 'อาหารอีสาน1', '/storage/eaa8275c0a8a016093a0034500ef722d.png', 1, '2022-11-28 16:04:15', '2022-12-13 10:12:11'),
(2, 'อาหารอินเดีย', '/storage/restaurant_type/dfb67fb48d9da0004a7e738809651979.png', 1, '2022-11-28 00:00:00', '2022-12-11 13:25:54'),
(3, 'อาหารใต้', '/storage/restaurant_type/1d5d39888193bdb72ab7fc1c9ce8302e.png', 1, '2022-11-28 00:00:00', '2022-12-11 13:26:27'),
(19, 'อาหารไทย', '/storage/restaurant_type/af895fbe81a26191cdc7d8e122a8b99b.png', 1, '2022-12-11 13:32:51', '2022-12-11 13:33:40'),
(20, 'อาหารนานาชาติ', '/storage/restaurant_type/3ac449820d44c18443b349046c62fb59.png', 1, '2022-12-11 13:34:34', '2022-12-11 13:34:34');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL DEFAULT '0',
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `restaurant_id` int(11) NOT NULL DEFAULT '0',
  `detail` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`id`, `order_id`, `customer_id`, `restaurant_id`, `detail`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 2, 'สมคำลำรือกับร้านจริงๆครับ', '2022-12-11 00:00:00', '0000-00-00 00:00:00'),
(2, 2, 4, 2, 'อร่อยที่สุดเลย', '2022-12-07 00:00:00', '0000-00-00 00:00:00'),
(5, 7, 4, 7, 'อร่อยมากครับ', '2022-12-12 09:28:05', '2022-12-12 09:28:05'),
(6, 6, 4, 6, 'กุ้งสดมากครับ', '2022-12-12 10:18:17', '2022-12-12 10:18:17');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  `email` varchar(100) COLLATE utf8_bin NOT NULL,
  `firstname` varchar(50) COLLATE utf8_bin NOT NULL,
  `lastname` varchar(50) COLLATE utf8_bin NOT NULL,
  `user_type` varchar(20) COLLATE utf8_bin NOT NULL DEFAULT 'customer',
  `thumbnail` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '/storage/profiles/no-thumbnail.jpg',
  `address` varchar(255) COLLATE utf8_bin NOT NULL,
  `restaurant_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `restaurant_type_id` int(11) NOT NULL DEFAULT '0',
  `restaurant_address` varchar(255) COLLATE utf8_bin NOT NULL,
  `restaurant_thumbnail` varchar(255) COLLATE utf8_bin NOT NULL,
  `mobile_no` varchar(50) COLLATE utf8_bin NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `password`, `email`, `firstname`, `lastname`, `user_type`, `thumbnail`, `address`, `restaurant_name`, `restaurant_type_id`, `restaurant_address`, `restaurant_thumbnail`, `mobile_no`, `status`, `created_at`, `updated_at`) VALUES
(1, '969d4e0dac684705b014dd00b501e63c', 'admin@demo.com', 'admin', 'demo', 'admin', '/storage/ecf308c276207a3a93d8550b5b833c71.png', '240 Nai Mueang, Mueang, Chaiyaphum', '', 0, '', '', '096-520-7008', 1, '2022-11-21 04:02:24', '2022-12-13 01:37:47'),
(2, '969d4e0dac684705b014dd00b501e63c', 'restaurant@demo.com', 'restaurant', 'demo', 'restaurant', '/storage/e8c3fac855dc95fd78a08363cf459a5f.png', '210/136 Moo.5', 'ร้านไก่ย่าง', 20, '172 m.1', '/storage/3e3a7eb614f32a8e11720400f921f4c9.png', '+66850176149', 1, '2022-12-12 10:47:06', '2022-12-13 01:41:01'),
(3, '969d4e0dac684705b014dd00b501e63c', 'rider@demo.com', 'rider', 'demo', 'rider', '/storage/e8c3fac855dc95fd78a08363cf459a5f.png', '18/116 ซอยสุขสวัสดิ์ 30 แยก 8-2', '', 0, '', '', '0965207008', 1, '2022-11-21 04:02:24', '2022-12-13 01:29:49'),
(4, '969d4e0dac684705b014dd00b501e63c', 'customer@demo.com', 'customer', 'demo', 'customer', '/storage/2dd345c57ed613648079d161721700fd.png', '18/116 ซอยสุขสวัสดิ์ 30 แยก 8-2', '', 0, '', '', '0965207008', 1, '2022-11-21 04:02:24', '2022-12-13 01:25:06'),
(8, 'd9729feb74992cc3482b350163a1a010', 'wannapong.kjp@gmail.com', 'Wannapong', 'Kumjumpon', 'restaurant', '/storage/e8c3fac855dc95fd78a08363cf459a5f.png', '210/136 Moo.5', 'ร้าน MK', 3, 'sddew', '/storage/6f8225107bc367e850760bfbc5a689fb.png', '0850176149', 0, '2022-12-13 01:10:17', '2022-12-13 01:10:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `food_category`
--
ALTER TABLE `food_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `food_menu`
--
ALTER TABLE `food_menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `restaurant_type`
--
ALTER TABLE `restaurant_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `food_category`
--
ALTER TABLE `food_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `food_menu`
--
ALTER TABLE `food_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `order_item`
--
ALTER TABLE `order_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `restaurant_type`
--
ALTER TABLE `restaurant_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
