-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 30, 2025 at 01:53 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cnpm_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `room` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `roomName` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fullname` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `checkin` date NOT NULL,
  `total` int DEFAULT '0',
  `checkout` date NOT NULL,
  `time` datetime NOT NULL,
  `bookingCode` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `TrangThaiThanhToan` tinyint(1) DEFAULT '0',
  `TrangThaiPhong` int DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `bookingCode` (`bookingCode`)
) ENGINE=MyISAM AUTO_INCREMENT=132 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quanlykhachhang`
--

DROP TABLE IF EXISTS `quanlykhachhang`;
CREATE TABLE IF NOT EXISTS `quanlykhachhang` (
  `id` int NOT NULL AUTO_INCREMENT,
  `maKH` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `ten` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `gioiTinh` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sdt` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ngaySinh` date DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `diaChi` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `maKH` (`maKH`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quanlyphong`
--

DROP TABLE IF EXISTS `quanlyphong`;
CREATE TABLE IF NOT EXISTS `quanlyphong` (
  `id` int NOT NULL AUTO_INCREMENT,
  `maPhong` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `tenPhong` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `kieuPhong` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `giaPhong` int NOT NULL,
  `hinhAnh` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `loaiGiuong` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tienNghi` text COLLATE utf8mb4_general_ci,
  `dienTich` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sucChua` int DEFAULT NULL,
  `tinhTrang` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `maPhong` (`maPhong`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quanlyphong`
--

INSERT INTO `quanlyphong` (`id`, `maPhong`, `tenPhong`, `kieuPhong`, `giaPhong`, `hinhAnh`, `loaiGiuong`, `tienNghi`, `dienTich`, `sucChua`, `tinhTrang`) VALUES
(17, 'R003', 'Cao cấp', '3', 2000000, 'images/Messi.webp', '1 giường Queen', 'Wifi, Tv, điều hoà', '30m2', 2, '1'),
(22, 'R005', 'Phòng Đơn', '1', 250000, 'images/narr.jpg', '1 giường đơn', 'Wifi, Smart TV, Điều Hoà', '25m2', NULL, '1'),
(15, 'R001', 'VIP', '3', 2000000, 'images/_101770618_christianoronaldo.jpg.webp', '1 giường King', 'Wifi, Tv, điều hoà', '30m2', 2, '1'),
(19, 'R004', 'Phòng Đôi', '2', 400000, 'images/VN.webp', '1 giường lớn', 'Wifi, Smart TV, Điều Hoà', '30m2', 2, '0'),
(23, 'R006', 'Siêu cấp VIP PRO', '3', 5000000, 'images/STU.jpg', '2 giường King', 'Wifi, Điều hoà, Smart TV, Hồ Bơi,....', '35m2', NULL, '1');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `gender` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `reset_code` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `phone`, `email`, `gender`, `dob`, `address`, `username`, `password`, `reset_code`, `role`) VALUES
(14, 'User', '0521452156', 'User@gmail.com', 'Nam', '2004-07-02', '180 Cao Lỗ', 'User', '$2y$10$r2JcfnrpGB8gTJyPwLVAruYa1zP4/T3./H4VIqcR4A2iitwB9uf72', NULL, 'user'),
(12, 'Lê Thành Đạt', '0215984563', 'abcd@gmail.com', 'Nam', '2004-07-02', '180 Cao Lỗ', 'Admin', '$2y$10$lHxNswpqANqVzXo2YcTrpuConvYvsMhByCdSlvl.iQy4H2rbsdpAS', NULL, 'admin');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
