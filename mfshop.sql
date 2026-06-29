-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th6 28, 2026 lúc 08:39 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `mfshop`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `campaigns`
--

CREATE TABLE `campaigns` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `discount_percent` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `start_date` datetime DEFAULT current_timestamp(),
  `end_date` datetime DEFAULT '2030-12-31 23:59:59'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `campaigns`
--

INSERT INTO `campaigns` (`id`, `name`, `discount_percent`, `created_at`, `start_date`, `end_date`) VALUES
(1, 'Hè mát ', 20, '2026-06-21 07:34:34', '2026-06-21 14:55:56', '2030-12-31 23:59:59');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `carts`
--

CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `color` varchar(50) DEFAULT 'Mặc định',
  `size` varchar(20) DEFAULT 'Mặc định',
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(6, 'Áo'),
(7, 'Quần'),
(8, 'Đầm & Váy'),
(9, 'Phụ kiện'),
(10, 'Giày dép'),
(11, 'Quần ống rộng');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `is_read`, `created_at`) VALUES
(1, 2, 'Đặt hàng thành công', 'Đơn hàng #1 đã được ghi nhận.', 1, '2026-06-04 16:03:55'),
(2, 2, 'Cập nhật đơn hàng #1', 'Đơn hàng của bạn đã chuyển sang trạng thái: **Đã hủy**.', 1, '2026-06-04 19:46:07'),
(3, 1, 'Đặt hàng thành công', 'Đơn hàng #2 đã được ghi nhận.', 1, '2026-06-04 20:05:27'),
(4, 1, 'Đặt hàng thành công', 'Đơn hàng #3 đã được ghi nhận.', 1, '2026-06-04 21:02:57'),
(5, 1, 'Cập nhật đơn hàng #3', 'Đơn hàng của bạn đã chuyển sang trạng thái: **Đang giao hàng**.', 1, '2026-06-04 21:03:31'),
(6, 1, 'Cập nhật đơn hàng #3', 'Đơn hàng của bạn đã chuyển sang trạng thái: **Đang giao hàng**.', 1, '2026-06-04 21:03:35'),
(7, 2, 'Đặt hàng thành công', 'Đơn hàng #4 đã được ghi nhận.', 1, '2026-06-04 22:26:13'),
(8, 1, 'Đặt hàng thành công', 'Đơn hàng #55 đã được ghi nhận.', 1, '2026-06-05 09:54:13'),
(9, 1, 'Chờ thanh toán', 'Đơn hàng #56 đã được ghi nhận. Vui lòng quét mã QR chuyển khoản để chúng tôi xác nhận đơn hàng.', 1, '2026-06-05 09:59:31'),
(10, 1, 'Cập nhật đơn hàng #56', 'Đơn hàng của bạn đã được chuyển sang trạng thái: **Hoàn thành**.', 1, '2026-06-05 10:11:07'),
(11, 1, 'Cập nhật đơn hàng #55', 'Đơn hàng của bạn đã được chuyển sang trạng thái: **Hoàn thành**.', 1, '2026-06-05 10:11:56'),
(12, 9, 'Cập nhật đơn hàng #45', 'Đơn hàng của bạn đã được chuyển sang trạng thái: **Hoàn thành**.', 0, '2026-06-05 10:12:01'),
(13, 13, 'Cập nhật đơn hàng #39', 'Đơn hàng của bạn đã được chuyển sang trạng thái: **Hoàn thành**.', 0, '2026-06-05 10:23:35'),
(14, 10, 'Cập nhật đơn hàng #24', 'Đơn hàng của bạn đã được chuyển sang trạng thái: **Hoàn thành**.', 0, '2026-06-05 10:23:39'),
(15, 1, 'Chờ thanh toán', 'Đơn hàng #57 đã được ghi nhận. Vui lòng quét mã QR chuyển khoản để chúng tôi xác nhận đơn hàng.', 1, '2026-06-05 11:35:22'),
(16, 1, 'Chờ thanh toán', 'Đơn hàng #58 đã được ghi nhận. Vui lòng quét mã QR chuyển khoản để chúng tôi xác nhận đơn hàng.', 1, '2026-06-05 11:43:12'),
(17, 1, 'Chờ thanh toán', 'Đơn hàng #59 đã được ghi nhận. Vui lòng quét mã QR chuyển khoản để chúng tôi xác nhận đơn hàng.', 1, '2026-06-05 11:45:49'),
(18, 1, 'Chờ thanh toán', 'Đơn hàng #60 đã được ghi nhận. Vui lòng quét mã QR chuyển khoản để chúng tôi xác nhận đơn hàng.', 1, '2026-06-05 11:48:18'),
(19, 1, 'Chờ thanh toán', 'Đơn hàng #61 đã được ghi nhận. Vui lòng quét mã QR chuyển khoản để chúng tôi xác nhận đơn hàng.', 1, '2026-06-05 12:02:30'),
(20, 1, 'Cập nhật đơn hàng #61', 'Đơn hàng của bạn đã được chuyển sang trạng thái: **Chờ xác nhận thanh toán**.', 1, '2026-06-05 12:03:43'),
(21, 1, 'Cập nhật đơn hàng #59', 'Đơn hàng của bạn đã được chuyển sang trạng thái: **Chờ xác nhận thanh toán**.', 1, '2026-06-05 12:03:52'),
(22, 1, 'Cập nhật đơn hàng #61', 'Đơn hàng của bạn đã được chuyển sang trạng thái: **Hoàn thành**.', 1, '2026-06-05 12:04:21'),
(23, 1, 'Cập nhật đơn hàng #57', 'Đơn hàng của bạn đã được chuyển sang trạng thái: **Hoàn thành**.', 1, '2026-06-05 12:58:17'),
(24, 10, 'Cập nhật đơn hàng #35', 'Đơn hàng của bạn đã được chuyển sang trạng thái: **Hoàn thành**.', 0, '2026-06-05 12:58:27'),
(25, 12, 'Cập nhật đơn hàng #27', 'Đơn hàng của bạn đã được chuyển sang trạng thái: **Hoàn thành**.', 0, '2026-06-05 13:03:46'),
(26, 7, 'Cập nhật đơn hàng #32', 'Đơn hàng của bạn đã được chuyển sang trạng thái: **Hoàn thành**.', 0, '2026-06-05 13:03:54'),
(27, 4, 'Cập nhật đơn hàng #6', 'Đơn hàng của bạn đã được chuyển sang trạng thái: **Hoàn thành**.', 0, '2026-06-05 13:04:02'),
(28, 2, 'Chờ thanh toán', 'Đơn hàng #62 đã được ghi nhận. Vui lòng quét mã QR chuyển khoản để chúng tôi xác nhận đơn hàng.', 1, '2026-06-05 13:08:57'),
(29, 2, 'Xác nhận thanh toán thành công', 'Thanh toán cho đơn hàng #62 đã được xác nhận. Đặt hàng thành công! Chúng tôi đang chuẩn bị hàng cho bạn.', 1, '2026-06-05 13:09:51'),
(30, 2, 'Cập nhật đơn hàng #62', 'Đơn hàng của bạn đã được chuyển sang trạng thái: **Hoàn thành**.', 1, '2026-06-05 13:10:32'),
(31, 2, 'Chờ thanh toán', 'Đơn hàng #63 đã được ghi nhận. Vui lòng quét mã QR chuyển khoản để chúng tôi xác nhận đơn hàng.', 1, '2026-06-05 13:42:53'),
(32, 2, 'Xác nhận thanh toán thành công', 'Thanh toán cho đơn hàng #63 đã được xác nhận. Đặt hàng thành công! Chúng tôi đang chuẩn bị hàng cho bạn.', 1, '2026-06-05 13:43:29'),
(33, 1, 'Chờ thanh toán', 'Đơn hàng #64 đã được ghi nhận. Vui lòng quét mã QR chuyển khoản để chúng tôi xác nhận đơn hàng.', 1, '2026-06-21 12:51:16'),
(34, 1, 'Xác nhận thanh toán thành công', 'Thanh toán cho đơn hàng #64 đã được xác nhận. Đặt hàng thành công! Chúng tôi đang chuẩn bị hàng cho bạn.', 1, '2026-06-21 12:51:54'),
(35, 1, 'Xác nhận thanh toán thành công', 'Thanh toán cho đơn hàng #64 đã được xác nhận. Đặt hàng thành công! Chúng tôi đang chuẩn bị hàng cho bạn.', 1, '2026-06-21 13:24:18'),
(36, 1, 'Cập nhật đơn hàng #64', 'Đơn hàng của bạn đã được chuyển sang trạng thái: **Hoàn thành**.', 1, '2026-06-21 13:24:21'),
(37, 2, 'Cập nhật đơn hàng #63', 'Đơn hàng của bạn đã được chuyển sang trạng thái: **Hoàn thành**.', 1, '2026-06-21 13:24:25'),
(38, 1, 'Cập nhật đơn hàng #59', 'Đơn hàng của bạn đã được chuyển sang trạng thái: **Hoàn thành**.', 1, '2026-06-21 13:24:27'),
(39, 1, 'Cập nhật đơn hàng #60', 'Đơn hàng của bạn đã được chuyển sang trạng thái: **Hoàn thành**.', 1, '2026-06-21 13:24:29'),
(40, 1, 'Đặt hàng thành công', 'Đơn hàng #65 của bạn đã được xác nhận và đang được chuẩn bị.', 1, '2026-06-21 14:16:06'),
(41, 1, 'Đặt hàng thành công', 'Đơn hàng #66 của bạn đã được xác nhận và đang được chuẩn bị.', 1, '2026-06-21 14:16:39'),
(42, 1, 'Đặt hàng thành công', 'Đơn hàng #67 của bạn đã được xác nhận và đang được chuẩn bị.', 1, '2026-06-21 14:36:49'),
(43, 1, 'Đặt hàng thành công', 'Đơn hàng #68 của bạn đã được xác nhận và đang được chuẩn bị.', 1, '2026-06-21 15:22:44'),
(44, 1, 'Đặt hàng thành công', 'Đơn hàng #69 của bạn đã được xác nhận và đang được chuẩn bị.', 1, '2026-06-21 15:36:31'),
(45, 1, 'Khách Hủy Đơn', 'Khách hàng vừa HỦY đơn hàng #69. Vui lòng kiểm tra lại kho.', 1, '2026-06-21 15:37:08'),
(46, 1, 'Yêu Cầu Hoàn Trả', 'Đơn hàng #60 có yêu cầu HOÀN TRẢ HÀNG từ khách. Vui lòng xem xét giải quyết!', 1, '2026-06-21 15:38:12'),
(47, 2, 'Chờ thanh toán', 'Đơn hàng #70 đã được ghi nhận. Vui lòng quét mã QR chuyển khoản để chúng tôi xác nhận đơn hàng.', 1, '2026-06-27 14:01:36'),
(48, 1, 'Khách Hủy Đơn', 'Khách hàng vừa HỦY đơn hàng #70. Vui lòng kiểm tra lại kho.', 1, '2026-06-27 14:01:59'),
(49, 2, 'Cập nhật đơn hàng #70', 'Đơn hàng của bạn đã được chuyển sang trạng thái: **Chờ xác nhận thanh toán**.', 0, '2026-06-27 15:29:04'),
(50, 2, 'Cập nhật đơn hàng #70', 'Đơn hàng của bạn đã được chuyển sang trạng thái: **Hoàn thành**.', 0, '2026-06-27 15:29:11'),
(51, 1, 'Chờ thanh toán', 'Đơn hàng #71 đã được ghi nhận. Vui lòng quét mã QR chuyển khoản để chúng tôi xác nhận đơn hàng.', 1, '2026-06-27 15:39:55'),
(52, 14, 'Đặt hàng thành công', 'Đơn hàng #72 của bạn đã được xác nhận và đang được chuẩn bị.', 1, '2026-06-27 15:53:51'),
(53, 15, 'Đặt hàng thành công', 'Đơn hàng #73 của bạn đã được xác nhận và đang được chuẩn bị.', 1, '2026-06-27 21:56:37'),
(54, 15, 'Cập nhật đơn hàng #73', 'Đơn hàng của bạn đã được chuyển sang trạng thái: **Hoàn thành**.', 1, '2026-06-27 22:01:18'),
(55, 14, 'Cập nhật đơn hàng #72', 'Đơn hàng của bạn đã được chuyển sang trạng thái: **Hoàn thành**.', 0, '2026-06-27 22:15:20'),
(56, 1, 'Khách Hủy Đơn', 'Khách hàng vừa HỦY đơn hàng #66. Vui lòng kiểm tra lại kho.', 1, '2026-06-27 22:15:40'),
(57, 1, 'Yêu Cầu Hoàn Trả', 'Đơn hàng #61 có yêu cầu HOÀN TRẢ HÀNG từ khách. Vui lòng xem xét giải quyết!', 1, '2026-06-27 22:16:03'),
(58, 1, 'Cập nhật đơn hàng #71', 'Đơn hàng của bạn đã được chuyển sang trạng thái: **Hoàn thành**.', 1, '2026-06-27 22:22:58'),
(59, 1, 'Cập nhật đơn hàng #69', 'Đơn hàng của bạn đã được chuyển sang trạng thái: **Hoàn thành**.', 1, '2026-06-27 22:23:02'),
(60, 1, 'Đặt hàng thành công', 'Đơn hàng #74 của bạn đã được xác nhận và đang được chuẩn bị.', 1, '2026-06-27 22:36:55'),
(61, 15, 'Đặt hàng thành công', 'Đơn hàng #75 của bạn đã được xác nhận và đang được chuẩn bị.', 0, '2026-06-27 23:27:52'),
(62, 15, 'Cập nhật đơn hàng #75', 'Đơn hàng của bạn đã được chuyển sang trạng thái: **Hoàn thành**.', 0, '2026-06-27 23:28:28'),
(63, 1, 'Đơn hàng đang giao', 'Đơn hàng #55 của bạn đã được giao cho đơn vị vận chuyển.', 1, '2026-06-28 00:29:37'),
(64, 2, 'Cập nhật đơn hàng #63', 'Đơn hàng của bạn đã được chuyển sang trạng thái: **Yêu cầu hoàn trả**.', 0, '2026-06-28 00:29:49'),
(65, 1, 'Đặt hàng thành công', 'Đơn hàng #76 của bạn đã được xác nhận và đang được chuẩn bị.', 1, '2026-06-28 12:58:26'),
(66, 1, 'Chờ thanh toán', 'Đơn hàng #77 đã được ghi nhận. Vui lòng quét mã QR chuyển khoản để chúng tôi xác nhận đơn hàng.', 1, '2026-06-28 12:58:58'),
(67, 1, 'Cập nhật đơn hàng #77', 'Đơn hàng của bạn đã được chuyển sang trạng thái: **Hoàn thành**.', 1, '2026-06-28 12:59:30'),
(68, 1, 'Chờ thanh toán', 'Đơn hàng #78 đã được ghi nhận. Vui lòng quét mã QR chuyển khoản để chúng tôi xác nhận đơn hàng.', 0, '2026-06-28 13:22:57'),
(69, 2, 'Chờ thanh toán', 'Đơn hàng #79 đã được ghi nhận. Vui lòng quét mã QR chuyển khoản để chúng tôi xác nhận đơn hàng.', 0, '2026-06-28 13:24:30'),
(70, 2, 'Chờ thanh toán', 'Đơn hàng #80 đã được ghi nhận. Vui lòng quét mã QR chuyển khoản để chúng tôi xác nhận đơn hàng.', 0, '2026-06-28 13:28:33'),
(71, 2, 'Chờ thanh toán', 'Đơn hàng #81 đã được ghi nhận. Vui lòng quét mã QR chuyển khoản để chúng tôi xác nhận đơn hàng.', 0, '2026-06-28 13:29:26'),
(72, 1, 'Chờ thanh toán', 'Đơn hàng #82 đã được ghi nhận. Vui lòng quét mã QR chuyển khoản để chúng tôi xác nhận đơn hàng.', 0, '2026-06-28 13:30:30'),
(73, 1, 'Chờ thanh toán', 'Đơn hàng #83 đã được ghi nhận. Vui lòng quét mã QR chuyển khoản để chúng tôi xác nhận đơn hàng.', 0, '2026-06-28 13:31:47'),
(74, 1, 'Chờ thanh toán', 'Đơn hàng #84 đã được ghi nhận. Vui lòng quét mã QR chuyển khoản để chúng tôi xác nhận đơn hàng.', 0, '2026-06-28 13:34:09'),
(75, 2, 'Chờ thanh toán', 'Đơn hàng #85 đã được ghi nhận. Vui lòng quét mã QR chuyển khoản để chúng tôi xác nhận đơn hàng.', 0, '2026-06-28 13:34:48');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `vat_amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Chờ xác nhận',
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `vat_amount`, `status`, `note`, `created_at`) VALUES
(3, 1, 9899120.00, 899920.00, 'Đang giao hàng', '', '2026-06-04 21:02:57'),
(4, 2, 196900.00, 17900.00, 'Đã hủy', '', '2026-06-04 22:26:13'),
(5, 2, 748000.00, 68000.00, 'Đã giao hàng', 'Giao hàng nhanh', '2026-06-01 10:30:00'),
(6, 4, 199000.00, 18091.00, 'Hoàn thành', NULL, '2026-06-01 14:20:00'),
(7, 5, 1098000.00, 99818.00, 'Đang giao hàng', 'Gọi trước khi giao', '2026-06-02 09:15:00'),
(8, 6, 449000.00, 40818.00, 'Đã hủy', 'Sai sản phẩm', '2026-05-30 16:45:00'),
(9, 7, 1299000.00, 118091.00, 'Đã giao hàng', NULL, '2026-06-03 11:00:00'),
(10, 2, 399000.00, 36273.00, 'Chờ xác nhận thanh toán', 'Thanh toán khi nhận hàng', '2026-06-04 08:30:00'),
(11, 8, 899000.00, 81727.00, 'Đang giao hàng', NULL, '2026-05-31 13:50:00'),
(12, 9, 259000.00, 23545.00, 'Đã giao hàng', 'Gói quà cẩn thận', '2026-06-02 17:20:00'),
(13, 10, 699000.00, 63545.00, 'Chờ xác nhận', NULL, '2026-06-04 10:10:00'),
(14, 3, 449000.00, 40818.00, 'Đang giao hàng', 'Giao buổi sáng', '2026-06-01 15:00:00'),
(15, 11, 1199000.00, 109000.00, 'Đã giao hàng', NULL, '2026-05-29 12:30:00'),
(16, 12, 299000.00, 27182.00, 'Đã hủy', 'Đặt nhầm', '2026-05-30 09:00:00'),
(17, 13, 799000.00, 72636.00, 'Chờ xác nhận', NULL, '2026-06-03 14:40:00'),
(18, 4, 549000.00, 49909.00, 'Đang giao hàng', NULL, '2026-06-04 09:20:00'),
(19, 5, 1299000.00, 118091.00, 'Đã giao hàng', 'Giao cuối tuần', '2026-05-28 11:10:00'),
(20, 6, 399000.00, 36273.00, 'Chờ xác nhận', NULL, '2026-06-02 18:30:00'),
(21, 7, 899000.00, 81727.00, 'Đã giao hàng', NULL, '2026-06-03 10:00:00'),
(22, 8, 259000.00, 23545.00, 'Đang giao hàng', 'Để lại bảo vệ', '2026-06-01 07:45:00'),
(23, 9, 1199000.00, 109000.00, 'Đã hủy', NULL, '2026-05-31 19:00:00'),
(24, 10, 449000.00, 40818.00, 'Hoàn thành', 'Không có nhu cầu nữa', '2026-06-04 11:15:00'),
(25, 2, 699000.00, 63545.00, 'Đã giao hàng', NULL, '2026-06-01 20:30:00'),
(26, 11, 299000.00, 27182.00, 'Đang giao hàng', NULL, '2026-06-02 12:00:00'),
(27, 12, 799000.00, 72636.00, 'Hoàn thành', 'Chuyển khoản', '2026-06-03 16:20:00'),
(28, 13, 199000.00, 18091.00, 'Đã giao hàng', NULL, '2026-05-30 10:00:00'),
(29, 4, 399000.00, 36273.00, 'Đang giao hàng', 'Giao hàng trong giờ hành chính', '2026-06-04 07:00:00'),
(30, 5, 899000.00, 81727.00, 'Đã hủy', NULL, '2026-06-02 22:15:00'),
(31, 6, 1299000.00, 118091.00, 'Chờ xác nhận', 'Thanh toán online', '2026-06-01 05:30:00'),
(32, 7, 549000.00, 49909.00, 'Hoàn thành', NULL, '2026-06-03 18:45:00'),
(33, 8, 259000.00, 23545.00, 'Đang giao hàng', 'Gọi điện trước', '2026-05-31 08:15:00'),
(34, 9, 699000.00, 63545.00, 'Chờ xác nhận', NULL, '2026-06-04 14:00:00'),
(35, 10, 449000.00, 40818.00, 'Hoàn thành', 'Quà tặng bạn gái', '2026-06-01 12:15:00'),
(36, 2, 1199000.00, 109000.00, 'Đang giao hàng', NULL, '2026-06-03 09:30:00'),
(37, 11, 299000.00, 27182.00, 'Đã hủy', 'Đổi ý', '2026-05-29 17:00:00'),
(38, 12, 799000.00, 72636.00, 'Chờ xác nhận', 'Giao hàng nhanh', '2026-06-02 13:20:00'),
(39, 13, 199000.00, 18091.00, 'Hoàn thành', NULL, '2026-06-04 15:30:00'),
(40, 4, 399000.00, 36273.00, 'Đang giao hàng', NULL, '2026-06-01 22:10:00'),
(41, 5, 899000.00, 81727.00, 'Chờ xác nhận thanh toán', 'Chuyển khoản qua Momo', '2026-06-03 11:45:00'),
(42, 6, 1299000.00, 118091.00, 'Đã giao hàng', NULL, '2026-05-31 14:20:00'),
(43, 7, 549000.00, 49909.00, 'Đang giao hàng', 'Để ngoài cửa', '2026-06-02 08:50:00'),
(44, 8, 259000.00, 23545.00, 'Đã hủy', 'Hết hàng', '2026-06-01 19:40:00'),
(45, 9, 699000.00, 63545.00, 'Hoàn thành', NULL, '2026-06-04 12:00:00'),
(46, 10, 449000.00, 40818.00, 'Đã giao hàng', NULL, '2026-05-30 15:30:00'),
(47, 2, 1199000.00, 109000.00, 'Đang giao hàng', 'Giao cuối tuần', '2026-06-03 20:15:00'),
(48, 11, 299000.00, 27182.00, 'Chờ xác nhận', NULL, '2026-06-04 16:00:00'),
(49, 12, 799000.00, 72636.00, 'Đã giao hàng', NULL, '2026-06-02 14:55:00'),
(50, 13, 199000.00, 18091.00, 'Đang giao hàng', 'Giao hàng nhanh', '2026-06-01 17:30:00'),
(51, 4, 399000.00, 36273.00, 'Đã giao hàng', NULL, '2026-05-31 09:45:00'),
(52, 5, 899000.00, 81727.00, 'Chờ xác nhận', 'Đặt hàng qua điện thoại', '2026-06-04 13:10:00'),
(53, 6, 1299000.00, 118091.00, 'Đang giao hàng', NULL, '2026-06-02 10:25:00'),
(54, 7, 549000.00, 49909.00, 'Đã hủy', 'Không liên lạc được', '2026-06-03 07:30:00'),
(55, 1, 1428900.00, 129900.00, 'Đang giao hàng', '', '2026-06-05 09:54:13'),
(56, 1, 1428900.00, 129900.00, 'Hoàn thành', '', '2026-06-05 09:59:31'),
(57, 1, 383900.00, 34900.00, 'Hoàn thành', '', '2026-06-05 11:35:22'),
(58, 1, 383900.00, 34900.00, 'Chờ xác nhận thanh toán', '', '2026-06-05 11:43:12'),
(59, 1, 0.00, 0.00, 'Hoàn thành', '', '2026-06-05 11:45:49'),
(60, 1, 0.00, 0.00, 'Yêu cầu hoàn trả', '', '2026-06-05 11:48:18'),
(61, 1, 0.00, 0.00, 'Yêu cầu hoàn trả', '', '2026-06-05 12:02:30'),
(62, 2, 878900.00, 79900.00, 'Hoàn thành', '', '2026-06-05 13:08:57'),
(63, 2, 17695700.00, 1608700.00, 'Yêu cầu hoàn trả', '', '2026-06-05 13:42:53'),
(64, 1, 1647800.00, 149800.00, 'Yêu cầu hoàn trả', '', '2026-06-21 12:51:16'),
(65, 1, 4724500.00, 429500.00, 'Đã hủy', '', '2026-06-21 14:16:06'),
(66, 1, 1373900.00, 124900.00, 'Đã hủy', '', '2026-06-21 14:16:39'),
(67, 1, 1317800.00, 119800.00, 'Đã hủy', '', '2026-06-21 14:36:49'),
(68, 1, 4702500.00, 427500.00, 'Đã hủy', '', '2026-06-21 15:22:44'),
(69, 1, 1428900.00, 129900.00, 'Hoàn thành', '', '2026-06-21 15:36:31'),
(70, 2, 49500.00, 4500.00, 'Hoàn thành', '', '2026-06-27 14:01:36'),
(71, 1, 1428900.00, 129900.00, 'Hoàn thành', '', '2026-06-27 15:39:55'),
(72, 14, 1428900.00, 129900.00, 'Hoàn thành', 'gfdg ', '2026-06-27 15:53:51'),
(73, 15, 1428900.00, 129900.00, 'Hoàn thành', '', '2026-06-27 21:56:37'),
(74, 1, 218900.00, 19900.00, 'Đang chuẩn bị hàng', '', '2026-06-27 22:36:55'),
(75, 15, 218900.00, 19900.00, 'Hoàn thành', '', '2026-06-27 23:27:52'),
(76, 1, 1428900.00, 129900.00, 'Đang chuẩn bị hàng', '', '2026-06-28 12:58:26'),
(77, 1, 1428900.00, 129900.00, 'Hoàn thành', '', '2026-06-28 12:58:58'),
(78, 1, 273900.00, 24900.00, 'Chờ xác nhận thanh toán', '', '2026-06-28 13:22:57'),
(79, 2, 659120.00, 59920.00, 'Chờ xác nhận thanh toán', '', '2026-06-28 13:24:30'),
(80, 2, 659120.00, 59920.00, 'Chờ xác nhận thanh toán', '', '2026-06-28 13:28:33'),
(81, 2, 383900.00, 34900.00, 'Chờ xác nhận thanh toán', '', '2026-06-28 13:29:26'),
(82, 1, 1428900.00, 129900.00, 'Chờ xác nhận thanh toán', '', '2026-06-28 13:30:30'),
(83, 1, 1428900.00, 129900.00, 'Chờ xác nhận thanh toán', '', '2026-06-28 13:31:47'),
(84, 1, 1428900.00, 129900.00, 'Chờ xác nhận thanh toán', '', '2026-06-28 13:34:09'),
(85, 2, 383900.00, 34900.00, 'Chờ xác nhận thanh toán', '', '2026-06-28 13:34:48');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `variant` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `variant`, `quantity`, `price`, `image_url`) VALUES
(3, 3, 53, 'đen - M, S ', 1, 8999200.00, 'uploads/1780581739_trung-suadonhang.png'),
(4, 4, 54, 'Trắng - M', 1, 179000.00, 'uploads/1780586385_vn-11134207-7r98o-lo0hd47z5myi2e@resize_w900_nl.webp'),
(5, 5, 60, 'Xanh đen - 30', 1, 599000.00, 'uploads/demo/quan_jean_nam_1.jpg'),
(6, 5, 75, 'Trắng đen - 41', 1, 899000.00, 'uploads/demo/giay_the_thao_nam_1.jpg'),
(7, 6, 55, 'Đen - M', 1, 199000.00, 'uploads/demo/ao_thun_basic_1.jpg'),
(8, 7, 57, 'Đen - L', 1, 1299000.00, 'uploads/demo/bomber_da_1.jpg'),
(9, 8, 65, 'Hoa đỏ - M', 1, 689000.00, 'uploads/demo/dam_maxi_hoa_1.jpg'),
(10, 9, 79, 'Trắng đỏ - 41', 1, 1299000.00, 'uploads/demo/sneaker_gucci_1.jpg'),
(11, 10, 68, 'Đỏ - M', 1, 499000.00, 'uploads/demo/vay_xoe_1.jpg'),
(12, 11, 98, 'Xám cam - 42', 1, 1599000.00, 'uploads/demo/giay_leo_nui_1.jpg'),
(13, 12, 71, 'Nâu - M', 1, 199000.00, 'uploads/demo/that_lung_da_1.jpg'),
(14, 12, 74, 'Đỏ caro - Free', 1, 129000.00, 'uploads/demo/khan_lua_1.jpg'),
(15, 13, 103, 'Đen - Free', 1, 699000.00, 'uploads/demo/cap_da_cong_so_1.jpg'),
(16, 14, 82, 'Đen - M', 1, 399000.00, 'uploads/demo/quan_culottes_1.jpg'),
(17, 15, 104, 'Nâu - 41', 1, 1199000.00, 'uploads/demo/boot_da_bo_1.jpg'),
(18, 16, 73, 'Đen - Free', 1, 299000.00, 'uploads/demo/kinh_mat_1.jpg'),
(19, 17, 96, 'Đen - Free', 1, 449000.00, 'uploads/demo/balo_laptop_1.jpg'),
(20, 17, 97, 'Đen - Free', 1, 89000.00, 'uploads/demo/mu_len_1.jpg'),
(21, 17, 99, 'Đen - 40', 1, 139000.00, 'uploads/demo/dep_tong_1.jpg'),
(22, 18, 66, 'Trắng - M', 1, 549000.00, 'uploads/demo/vay_trang_cong_so_1.jpg'),
(23, 19, 57, 'Nâu - L', 1, 1299000.00, 'uploads/demo/bomber_da_1.jpg'),
(24, 20, 90, 'Trắng - L', 1, 299000.00, 'uploads/demo/ao_polo_nam_1.jpg'),
(25, 20, 91, 'Đen - M', 1, 399000.00, 'uploads/demo/ao_khoac_gio_1.jpg'),
(26, 21, 92, 'Kaki - L', 1, 449000.00, 'uploads/demo/quan_baggy_1.jpg'),
(27, 21, 93, 'Xanh đen - M', 1, 219000.00, 'uploads/demo/quan_lung_nu_1.jpg'),
(28, 22, 71, 'Đen - L', 1, 199000.00, 'uploads/demo/that_lung_da_1.jpg'),
(29, 23, 78, 'Đen - 38', 1, 899000.00, 'uploads/demo/bot_cao_nu_1.jpg'),
(30, 23, 88, 'Nude - 37', 1, 549000.00, 'uploads/demo/giay_cao_got_1.jpg'),
(31, 24, 62, 'Xanh sáng - S', 1, 299000.00, 'uploads/demo/short_jean_nu_1.jpg'),
(32, 24, 64, 'Đen - M', 1, 249000.00, 'uploads/demo/legging_nu_1.jpg'),
(33, 25, 77, 'Nâu - 41', 1, 649000.00, 'uploads/demo/giay_tay_luoi_1.jpg'),
(34, 26, 70, 'Đen - Free', 1, 99000.00, 'uploads/demo/mu_luoi_trai_1.jpg'),
(35, 26, 87, 'Trắng - Free', 1, 25000.00, 'uploads/demo/tat_foot_1.jpg'),
(36, 26, 86, 'Bạc - Free', 1, 89000.00, 'uploads/demo/vong_tay_bac_1.jpg'),
(37, 27, 95, 'Xanh - M', 1, 459000.00, 'uploads/demo/vay_yem_jean_1.jpg'),
(38, 27, 94, 'Vàng sequin - M', 1, 1299000.00, 'uploads/demo/dam_da_hoi_1.jpg'),
(39, 28, 56, 'Trắng - L', 1, 399000.00, 'uploads/demo/so_mi_trang_1.jpg'),
(40, 29, 59, 'Đen - M', 1, 399000.00, 'uploads/demo/hoodie_unisex_1.jpg'),
(41, 30, 61, 'Đen - M', 1, 449000.00, 'uploads/demo/quan_tay_nu_1.jpg'),
(42, 30, 67, 'Xanh mint - M', 1, 399000.00, 'uploads/demo/dam_suong_dai_1.jpg'),
(43, 31, 79, 'Đen vàng - 42', 1, 1299000.00, 'uploads/demo/sneaker_gucci_1.jpg'),
(44, 32, 66, 'Đen - S', 1, 549000.00, 'uploads/demo/vay_trang_cong_so_1.jpg'),
(45, 33, 72, 'Be - Free', 1, 159000.00, 'uploads/demo/tui_tote_1.jpg'),
(46, 33, 74, 'Xanh caro - Free', 1, 129000.00, 'uploads/demo/khan_lua_1.jpg'),
(47, 34, 100, 'Đen - M', 2, 359000.00, 'uploads/demo/ao_dai_tay_1.jpg'),
(48, 35, 83, 'Xám - L', 1, 279000.00, 'uploads/demo/quan_vai_du_1.jpg'),
(49, 35, 84, 'Đen trắng - M', 1, 359000.00, 'uploads/demo/dam_caro_1.jpg'),
(50, 36, 98, 'Xanh đen - 43', 1, 1599000.00, 'uploads/demo/giay_leo_nui_1.jpg'),
(51, 37, 58, 'Hồng - M', 1, 499000.00, 'uploads/demo/ao_len_co_lo_1.jpg'),
(52, 38, 101, 'Xanh đen - M', 1, 299000.00, 'uploads/demo/quan_capri_1.jpg'),
(53, 38, 102, 'Đen - S', 1, 399000.00, 'uploads/demo/dam_sat_nach_1.jpg'),
(54, 39, 55, 'Xám - S', 1, 199000.00, 'uploads/demo/ao_thun_basic_1.jpg'),
(55, 40, 60, 'Xanh rêu - 32', 1, 599000.00, 'uploads/demo/quan_jean_nam_1.jpg'),
(56, 41, 75, 'Xám - 41', 1, 899000.00, 'uploads/demo/giay_the_thao_nam_1.jpg'),
(57, 42, 57, 'Đen - XL', 1, 1299000.00, 'uploads/demo/bomber_da_1.jpg'),
(58, 43, 65, 'Hoa xanh - L', 1, 689000.00, 'uploads/demo/dam_maxi_hoa_1.jpg'),
(59, 44, 76, 'Nâu - 38', 1, 349000.00, 'uploads/demo/sandal_nu_1.jpg'),
(60, 45, 108, 'Đen - Free', 1, 99000.00, 'uploads/demo/gang_tay_da_1.jpg'),
(61, 45, 109, 'Trắng hồng - 37', 1, 699000.00, 'uploads/demo/giay_the_thao_nu_1.jpg'),
(62, 46, 81, 'Xanh kẻ - L', 1, 259000.00, 'uploads/demo/so_mi_nam_tay_ngan_1.jpg'),
(63, 46, 85, 'Xám - M', 1, 599000.00, 'uploads/demo/vay_len_1.jpg'),
(64, 47, 104, 'Đen - 42', 1, 1199000.00, 'uploads/demo/boot_da_bo_1.jpg'),
(65, 48, 70, 'Trắng - Free', 1, 99000.00, 'uploads/demo/mu_luoi_trai_1.jpg'),
(66, 48, 71, 'Nâu - S', 1, 199000.00, 'uploads/demo/that_lung_da_1.jpg'),
(67, 49, 96, 'Xám - Free', 1, 449000.00, 'uploads/demo/balo_laptop_1.jpg'),
(68, 49, 97, 'Đỏ - Free', 1, 89000.00, 'uploads/demo/mu_len_1.jpg'),
(69, 49, 99, 'Nâu - 39', 1, 139000.00, 'uploads/demo/dep_tong_1.jpg'),
(70, 50, 56, 'Xanh nhạt - M', 1, 399000.00, 'uploads/demo/so_mi_trang_1.jpg'),
(71, 51, 59, 'Trắng - L', 1, 399000.00, 'uploads/demo/hoodie_unisex_1.jpg'),
(72, 52, 92, 'Đen - M', 1, 449000.00, 'uploads/demo/quan_baggy_1.jpg'),
(73, 52, 93, 'Đen - M', 1, 219000.00, 'uploads/demo/quan_lung_nu_1.jpg'),
(74, 53, 79, 'Trắng đỏ - 40', 1, 1299000.00, 'uploads/demo/sneaker_gucci_1.jpg'),
(75, 54, 61, 'Be - L', 1, 449000.00, 'uploads/demo/quan_tay_nu_1.jpg'),
(76, 54, 62, 'Xanh đen - M', 1, 299000.00, 'uploads/demo/short_jean_nu_1.jpg'),
(77, 55, 154, 'Xanh hồng - 36', 1, 1299000.00, 'uploads/1780591209_vn-11134207-820l4-mimuow5jcfewfc@resize_w900_nl.webp'),
(78, 56, 154, 'Xanh hồng - 36', 1, 1299000.00, 'uploads/1780591209_vn-11134207-820l4-mimuow5jcfewfc@resize_w900_nl.webp'),
(79, 57, 63, 'Xám đen - S', 1, 349000.00, 'uploads/demo/jogger_nam.jpg'),
(80, 58, 150, 'Trắng kẻ - M', 1, 349000.00, 'uploads/1780591543_vn-11134207-7ra0g-m6selw6swq48a1.webp'),
(81, 62, 134, 'Trắng - 38', 1, 799000.00, 'uploads/1780593546_vn-11134207-820l4-mibdnfhdo9ohc9.webp'),
(82, 63, 154, 'Xanh hồng - 36', 12, 1299000.00, 'uploads/1780591209_vn-11134207-820l4-mimuow5jcfewfc@resize_w900_nl.webp'),
(83, 63, 151, 'Đen - S', 1, 499000.00, 'uploads/1780591454_vn-11134207-81ztc-mn2kt02zkcn990.webp'),
(84, 64, 154, 'Xanh hồng - 36', 1, 1299000.00, 'uploads/1780591209_vn-11134207-820l4-mimuow5jcfewfc@resize_w900_nl.webp'),
(85, 64, 153, 'Đen - Free', 1, 199000.00, 'uploads/1780591303_vn-11134211-7r98o-lu8yi36rkcuped.webp'),
(86, 65, 154, 'Xanh hồng - 36', 2, 1299000.00, 'uploads/1780591209_vn-11134207-820l4-mimuow5jcfewfc@resize_w900_nl.webp'),
(87, 65, 57, 'Đen - S', 1, 1299000.00, 'uploads/demo/bomber_da.jpg'),
(88, 65, 153, 'Đen - Free', 2, 199000.00, 'uploads/1780591303_vn-11134211-7r98o-lu8yi36rkcuped.webp'),
(89, 66, 154, 'Xanh hồng - 36', 1, 1299000.00, 'uploads/1780591209_vn-11134207-820l4-mimuow5jcfewfc@resize_w900_nl.webp'),
(90, 67, 152, 'Trắng - S', 1, 749000.00, 'uploads/1780591354_vn-11134207-7ra0g-m7lyrxwaramb44.webp'),
(91, 67, 151, 'Đen - S', 1, 499000.00, 'uploads/1780591454_vn-11134207-81ztc-mn2kt02zkcn990.webp'),
(92, 68, 154, 'Xanh hồng - 36', 1, 1299000.00, 'uploads/1780591209_vn-11134207-820l4-mimuow5jcfewfc@resize_w900_nl.webp'),
(93, 68, 57, 'Đen - S', 2, 1299000.00, 'uploads/demo/bomber_da.jpg'),
(94, 68, 80, 'Hồng - S', 1, 179000.00, 'uploads/demo/ao_coc_tay_the_thao.jpg'),
(95, 68, 71, 'Nâu - S', 1, 199000.00, 'uploads/demo/that_lung_da.jpg'),
(96, 69, 154, 'Xanh hồng - 36', 1, 1299000.00, 'uploads/1780591209_vn-11134207-820l4-mimuow5jcfewfc@resize_w900_nl.webp'),
(97, 70, 118, 'Hồng - Free', 1, 45000.00, 'uploads/demo/kep_toc.jpg'),
(98, 71, 57, 'Đen - S', 1, 1299000.00, 'uploads/demo/bomber_da.jpg'),
(99, 72, 57, 'Đen - S', 1, 1299000.00, 'uploads/demo/bomber_da.jpg'),
(100, 73, 154, 'Xanh hồng - 36', 1, 1299000.00, 'uploads/1780591209_vn-11134207-820l4-mimuow5jcfewfc@resize_w900_nl.webp'),
(101, 74, 71, 'Nâu - S', 1, 199000.00, 'uploads/demo/that_lung_da.jpg'),
(102, 75, 71, 'Nâu - S', 1, 199000.00, 'uploads/demo/that_lung_da.jpg'),
(103, 76, 57, 'Đen - S', 1, 1299000.00, 'uploads/1782581787_sg-11134201-7rcf4-m66zaz7xl80a64.webp'),
(104, 77, 57, 'Đen - S', 1, 1299000.00, 'uploads/1782581787_sg-11134201-7rcf4-m66zaz7xl80a64.webp'),
(105, 78, 138, 'Nâu - Free', 1, 249000.00, 'uploads/1780593184_sg-11134253-824i7-mejxyf2ab9qa12.webp'),
(106, 79, 152, 'Trắng - S', 1, 599200.00, 'uploads/1780591354_vn-11134207-7ra0g-m7lyrxwaramb44.webp'),
(107, 80, 152, 'Trắng - S', 1, 599200.00, 'uploads/1780591354_vn-11134207-7ra0g-m7lyrxwaramb44.webp'),
(108, 81, 150, 'Trắng kẻ - M', 1, 349000.00, 'uploads/1780591543_vn-11134207-7ra0g-m6selw6swq48a1.webp'),
(109, 82, 57, 'Đen - S', 1, 1299000.00, 'uploads/1782581787_sg-11134201-7rcf4-m66zaz7xl80a64.webp'),
(110, 83, 57, 'Đen - S', 1, 1299000.00, 'uploads/1782581787_sg-11134201-7rcf4-m66zaz7xl80a64.webp'),
(111, 84, 57, 'Đen - S', 1, 1299000.00, 'uploads/1782581787_sg-11134201-7rcf4-m66zaz7xl80a64.webp'),
(112, 85, 150, 'Trắng kẻ - M', 1, 349000.00, 'uploads/1780591543_vn-11134207-7ra0g-m6selw6swq48a1.webp');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `gender` tinyint(1) DEFAULT 0 COMMENT '0: Unisex, 1: Nam, 2: Nữ',
  `sale_price` int(11) DEFAULT 0,
  `campaign_id` int(11) DEFAULT NULL,
  `is_hidden` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `price`, `image`, `description`, `gender`, `sale_price`, `campaign_id`, `is_hidden`) VALUES
(53, NULL, 'Áo thun', 8999200.00, 'uploads/1780581739_trung-suadonhang.png', 'Mẫu áo thun màu đen tinh tế được làm từ vải Cotton hữu cơ, kết hợp mô típ Monogram Sashiko của bộ sưu tập dành cho nam giới. Lấy cảm hứng từ nghệ thuật thủ công truyền thống Nhật Bản, sản phẩm sở hữu họa tiết Monogram nổi bật được thêu bằng chỉ màu tương phản ở cả mặt trước và mặt sau. Sự tỉ mỉ trong từng đường kim mũi chỉ mang đến dấu ấn thủ công độc đáo cho món trang phục thiết yếu này.\r\n\r\n\r\nRegular Fit\r\nMàu đen\r\n100% Cotton\r\nThêu logo thương hiệu ở phía trước và phía sau\r\nSản xuất tại Ý', 1, 0, NULL, 1),
(54, 6, 'Áo Polo Cổ V Dệt Kim Cao Cấp Phong Cách Hàn Quốc NOIRWEAR TOP 005', 179000.00, 'uploads/1780586385_vn-11134207-7r98o-lo0hd47z5myi2e@resize_w900_nl.webp', '  + Chât vải dệt kim cao cấp cho độ dày dặn, co giãn tốt và quan trọng độ bền màu cao\r\n\r\n\r\n\r\n    + Giặt ko đổ lông hay bay màu, thấm hút mồ hôi và thoải mái không gò bó khi hoạt động\r\n\r\n\r\n\r\n    + Thiệt kế cấu trúc lỗ thoáng, mắt vải mịn giúp tôn dáng cho người mặc  \r\n\r\n\r\n\r\n * Màu sắc & kích cỡ Áo Polo trơn nam có cổ (bản nâng cấp) vải cotton dệt gân nam tính, thanh lịch, sang trọng 2 màu \r\n\r\n  \r\n\r\n\r\n\r\n- Form áo regular-fit thoải mái ko gò bó khi vận động tạo nên sự năng động, trẻ trung...\r\n\r\n\r\n\r\n- Có 2 màu cực kì dễ mặc cho anh em tha hồ lựa chọn', 0, 0, NULL, 0),
(55, 6, 'Áo thun cổ tròn Basic', 199000.00, 'uploads/1782581681_d89b3a0f19afd7ca880bf45138af6a53.webp', 'Áo thun cotton 100% thoáng mát, form regular fit, phù hợp mặc hàng ngày.', 0, 0, NULL, 0),
(56, 6, 'Sơ mi nam trắng cổ đức', 399000.00, 'uploads/1782582702_vn-11134207-7ras8-mcfc44338ehe3c.webp', 'Sơ mi trắng phong cách công sở, chất vải pha không nhăn.', 1, 0, NULL, 0),
(57, 6, 'Áo khoác bomber da', 1299000.00, 'uploads/1782581787_sg-11134201-7rcf4-m66zaz7xl80a64.webp', 'Áo khoác da bóng thời trang, lớp lót chống gió.', 1, 0, NULL, 0),
(58, 6, 'Áo len cổ lọ nữ', 499000.00, 'uploads/demo/ao_len_co_lo.jpg', 'Áo len dày dặn, giữ ấm tốt, nhiều màu sắc.', 2, 0, NULL, 1),
(59, 6, 'Hoodie unisex in chữ', 399000.00, 'uploads/1782582808_vn-11134207-820l4-mg3vk4e9cutpc6.webp', 'Hoodie form rộng, mũ to, in chữ thêu nổi.', 0, 0, NULL, 0),
(60, 7, 'Quần jean nam ống slim', 599000.00, 'uploads/1782584206_vn-11134207-7ras8-m23nhxssfo2jd2.webp', 'Quần jean cao cấp, co giãn 4 chiều, form slim fit.', 1, 0, NULL, 0),
(61, 7, 'Quần tây nữ ống suông', 449000.00, 'uploads/1782584658_vn-11134207-7r98o-m079sfjchcktb5.webp', 'Quần tây công sở, vải linen pha, phom dáng đẹp.', 2, 0, NULL, 0),
(62, 7, 'Quần short jean nữ', 299000.00, 'uploads/1782581355_vn-11134211-820l4-mhlsw501gxs2ab.webp', 'Quần short rách gối, phong cách cá tính.', 2, 0, NULL, 0),
(63, 7, 'Quần jogger nam thể thao', 349000.00, 'uploads/demo/jogger_nam.jpg', 'Chất vải nỉ ấm, co giãn tốt, có túi hông.', 1, 0, NULL, 1),
(64, 7, 'Quần legging nữ cao cấp', 249000.00, 'uploads/demo/legging_nu.jpg', 'Legging co giãn 4 chiều, không thấm hút mồ hôi.', 2, 0, NULL, 1),
(65, 8, 'Đầm maxi hoa nhí', 689000.00, 'uploads/1782584784_vn-11134207-81ztc-mnuw1id5phxkab.webp', 'Đầm dài chấm gót, chất voan mềm mại, có lót.', 2, 0, NULL, 0),
(66, 8, 'Váy công sở', 549000.00, 'uploads/1782581111_vn-11134207-7r98o-ly6cu2paz89tdd.webp', 'Váy trắng phong cách thanh lịch, cổ V.', 2, 0, NULL, 0),
(67, 8, 'Đầm suông dáng dài', 399000.00, 'uploads/demo/dam_suong_dai.jpg', 'Đầm rộng thoải mái, chất thun mát.', 2, 0, NULL, 1),
(68, 8, 'Váy xòe ngắn tay bồng', 499000.00, 'uploads/demo/vay_xoe.jpg', 'Váy xòe công chúa, chất tổng hợp cao cấp.', 2, 0, NULL, 1),
(69, 8, 'Đầm sơ mi nữ', 459000.00, 'uploads/demo/dam_so_mi.jpg', 'Đầm kiểu sơ mi cổ đức, form suông, dài trên gối.', 2, 0, NULL, 1),
(70, 9, 'Mũ lưỡi trai basic', 99000.00, 'uploads/demo/mu_luoi_trai.jpg', 'Mũ lưỡi trai vải denim, có điều chỉnh size.', 0, 0, NULL, 1),
(71, 9, 'Thắt lưng da bò', 199000.00, 'uploads/1782580597_vn-11134207-7ras8-m2qybblqdpkq05.webp', 'Dây nịt da thật, khóa inox sáng bóng.', 1, 0, NULL, 0),
(72, 9, 'Túi tote vải bố', 159000.00, 'uploads/demo/tui_tote.jpg', 'Túi vải bố in họa tiết, đựng được laptop 14 inch.', 2, 0, NULL, 1),
(73, 9, 'Kính mát thời trang', 299000.00, 'uploads/demo/kinh_mat.jpg', 'Kính mát chống UV, gọng nhựa nhẹ, nhiều màu.', 0, 0, NULL, 1),
(74, 9, 'Khăn lụa cổ', 129000.00, 'uploads/1782585012_vn-11134207-7ras8-mbakd7bvoywjbf.webp', 'Khăn lụa mềm mịn, họa tiết caro.', 2, 0, NULL, 0),
(75, 10, 'Giày thể thao nam', 899000.00, 'uploads/demo/giay_the_thao_nam.jpg', 'Giày lưới êm chân, đế cao su chống trượt.', 1, 0, NULL, 1),
(76, 10, 'Sandal nữ quai ngang', 349000.00, 'uploads/demo/sandal_nu.jpg', 'Sandal đế bồng, da mềm, nhiều màu.', 2, 0, NULL, 1),
(77, 10, 'Giày tây lười nam', 649000.00, 'uploads/demo/giay_tay_luoi.jpg', 'Da bò thật, mũi tròn, lót êm.', 1, 0, NULL, 1),
(78, 10, 'Bốt cổ cao nữ', 899000.00, 'uploads/demo/bot_cao_nu.jpg', 'Bốt da lộn, đế răng cưa, dây buộc.', 2, 0, NULL, 1),
(79, 10, 'Guccy sneaker unisex', 1299000.00, 'uploads/1782583575_vn-11134207-7ras8-maxmko53iw9k2e.jpg', 'Sneaker phong cách luxury, chất da bò.', 0, 0, NULL, 0),
(80, 6, 'Áo cộc tay nữ thể thao', 179000.00, 'uploads/demo/ao_coc_tay_the_thao.jpg', 'Chất vải thun lạnh, co giãn tốt, thấm hút mồ hôi.', 2, 0, NULL, 1),
(81, 6, 'Áo sơ mi nam tay ngắn', 259000.00, 'uploads/demo/so_mi_nam_tay_ngan.jpg', 'Sơ mi họa tiết kẻ sọc, chất kate mát.', 1, 0, NULL, 1),
(82, 7, 'Quần culottes nữ', 399000.00, 'uploads/demo/quan_culottes.jpg', 'Quần ống rộng, cạp cao, tạo phom dáng thanh lịch.', 2, 0, NULL, 1),
(83, 7, 'Quần vải dù nam', 289000.00, 'uploads/demo/quan_vai_du.jpg', 'Quần mát, nhanh khô, thích hợp đi chơi, du lịch.', 1, 0, NULL, 1),
(84, 8, 'Đầm suông caro', 359000.00, 'uploads/demo/dam_caro.jpg', 'Đầm caro phong cách retro, thun cotton.', 2, 0, NULL, 1),
(85, 8, 'Váy len mùa đông', 599000.00, 'uploads/demo/vay_len.jpg', 'Váy len dày dặn, phom ôm vừa phải.', 2, 0, NULL, 1),
(86, 9, 'Vòng tay bạc', 89000.00, 'uploads/demo/vong_tay_bac.jpg', 'Vòng bạc nguyên chất, khắc tên tùy chọn.', 0, 0, NULL, 1),
(87, 9, 'Tất foot sock', 25000.00, 'uploads/demo/tat_foot.jpg', 'Tất ngắn cổ foot, nhiều màu sắc.', 0, 0, NULL, 1),
(88, 10, 'Giày cao gót nữ', 549000.00, 'uploads/demo/giay_cao_got.jpg', 'Giày cao 7cm, da bò mềm, mũi nhọn.', 2, 0, NULL, 1),
(89, 10, 'Giày sandal nam', 399000.00, 'uploads/demo/sandal_nam.jpg', 'Sandal quai kẹp, đế cao su chống trơn.', 1, 0, NULL, 1),
(90, 6, 'Áo polo nam trơn', 299000.00, 'uploads/demo/ao_polo_nam.jpg', 'Áo polo chất cá sấu, thoáng mát, form ôm.', 1, 0, NULL, 1),
(91, 6, 'Áo khoác gió unisex', 399000.00, 'uploads/demo/ao_khoac_gio.jpg', 'Áo khoác gió chống nước, nhẹ, gấp gọn.', 0, 0, NULL, 1),
(92, 7, 'Quần baggy nam', 449000.00, 'uploads/demo/quan_baggy.jpg', 'Quần ống rộng thời trang Hàn, chất kaki.', 1, 0, NULL, 1),
(93, 7, 'Quần lửng nữ', 219000.00, 'uploads/demo/quan_lung_nu.jpg', 'Quần lửng ống rộng, chất denim, phong cách cá tính.', 2, 0, NULL, 1),
(94, 8, 'Đầm dạ hội', 1299000.00, 'uploads/demo/dam_da_hoi.jpg', 'Đầm dạ hội sequin lấp lánh, xẻ tà.', 2, 0, NULL, 1),
(95, 8, 'Váy yếm jean', 459000.00, 'uploads/demo/vay_yem_jean.jpg', 'Váy yếm denim, form suông, có túi.', 2, 0, NULL, 1),
(96, 9, 'Balo laptop chống sốc', 449000.00, 'uploads/demo/balo_laptop.jpg', 'Balo chống nước, ngăn đệm laptop 15.6 inch.', 0, 0, NULL, 1),
(97, 9, 'Mũ len beanie', 89000.00, 'uploads/demo/mu_len.jpg', 'Mũ len ấm áp, thun co giãn, nhiều màu.', 0, 0, NULL, 1),
(98, 10, 'Giày leo núi', 1599000.00, 'uploads/1782585110_vn-11134207-7ras8-m23ll8uwxlln1f.webp', 'Giày trekking chống trơn, đế gai sâu.', 1, 0, NULL, 0),
(99, 10, 'Dép tông quai ngang', 139000.00, 'uploads/1782583813_vn-11134207-7ras8-m4efz16r7kwwc3.webp', 'Dép cao su mềm, đế siêu nhẹ.', 0, 0, NULL, 0),
(100, 6, 'Áo trùm đầu dài tay', 359000.00, 'uploads/1782581013_sg-11134201-82634-mkw0dhs0t5a98f.webp', 'Áo dài tay form rộng, in hình thú vui.', 0, 0, NULL, 0),
(101, 7, 'Quần capri nữ', 299000.00, 'uploads/1782580940_vn-11134207-81ztc-mo3sal2orvgl34.webp', 'Quần dài qua gối, co giãn tốt, chất jean.', 2, 0, NULL, 0),
(102, 8, 'Đầm sát nách', 399000.00, 'uploads/1782580871_vn-11134207-7r98o-lz21az6g1zmp75.webp', 'Đầm body, cắt may tinh tế, tôn dáng.', 2, 0, NULL, 0),
(103, 9, 'Cặp da công sở', 699000.00, 'uploads/1782580788_vn-11134207-820l4-mi5f87azzd3cba.webp', 'Cặp da bò thật, khóa kim loại chắc chắn.', 1, 0, NULL, 0),
(104, 10, 'Giày boot da bò', 1199000.00, 'uploads/1782580719_vn-11134207-7ra0g-m8ftiiky09ri22.webp', 'Boot cổ ngắn, dây buộc, đế đúc chắc.', 1, 0, NULL, 0),
(105, 6, 'Áo thun vintage', 179000.00, 'uploads/1782580187_vn-11134201-7ra0g-ma27wsdmtoas45.webp', 'Áo thun rộng, họa tiết retro, chất thun 100% cotton.', 0, 0, NULL, 0),
(106, 7, 'Quần vải thô', 299000.00, 'uploads/1782580113_vn-11134207-7ras8-mb3mpezf2u9ta4.webp', 'Quần vải thô mặc mát, túi hộp lớn.', 1, 0, NULL, 0),
(107, 8, 'Váy maxi trễ vai', 749000.00, 'uploads/1782580048_vn-11134207-7ras8-m3neukvi8kolf4.webp', 'Váy dài thướt tha, chất voan lụa.', 2, 0, NULL, 0),
(108, 9, 'Găng tay da', 99000.00, 'uploads/1782579975_vn-11134207-7ras8-m1wddohboibj3e.webp', 'Găng tay da mỏng, lái xe hoặc thời trang.', 0, 0, NULL, 0),
(109, 10, 'Giày thể thao nữ', 699000.00, 'uploads/1782579845_vn-11134207-81ztc-mngo17j6d5oi35.webp', 'Giày sneaker nữ, đế độn, form thể thao.', 2, 0, NULL, 0),
(110, 6, 'Áo gió nhẹ', 279000.00, 'uploads/1782579751_vn-11134207-7ra0g-mac7eqgvwi9v92.webp', 'Áo gió chống gió, gấp gọn, phù hợp đi phượt.', 0, 0, NULL, 0),
(111, 7, 'Quần kaki ống suông', 449000.00, 'uploads/1782579677_vn-11134207-820l4-mj52fgoc8xky61.webp', 'Quần kaki cao cấp, phom suông thanh lịch.', 1, 0, NULL, 0),
(112, 8, 'Đầm chữ A', 549000.00, 'uploads/1782579599_vn-11134201-23030-benry35l5vov92.webp', 'Đầm chữ A tôn vòng eo, chất vải linen.', 2, 0, NULL, 0),
(113, 9, 'Mũ bucket', 149000.00, 'uploads/1782579308_vn-11134207-7r98o-lukf3caqkphuf0.webp', 'Mũ bucket vải canvas, vành rộng che nắng.', 0, 0, NULL, 0),
(114, 10, 'Giày loafers nam', 749000.00, 'uploads/1782579198_77ed5201dd865ea52dc53272ecb9d0f4.webp', 'Giày lười da bò, mũi tròn, đế cao su.', 1, 0, NULL, 0),
(115, 6, 'Áo khoác denim', 899000.00, 'uploads/1782579101_vn-11134207-81ztc-morrfidz6rkdb0.webp', 'Áo khoác jean rách nhẹ, phong cách bụi bặm.', 0, 0, NULL, 0),
(116, 7, 'Quần short thể thao', 199000.00, 'uploads/1782578950_vn-11134207-7ras8-m58ghfd1xizb37.webp', 'Quần short 2 dây, vải thun lạnh, co giãn.', 0, 0, NULL, 0),
(117, 8, 'Váy body xẻ cổ', 399000.00, 'uploads/1782578835_vn-11134207-81ztc-mplygfsotl3f00.webp', 'Váy body ôm sát, cổ chữ V sâu.', 2, 0, NULL, 0),
(118, 9, 'Kẹp tóc đẹp', 45000.00, 'uploads/1782578672_cn-11134207-820l4-moespenx7lz5c4@resize_w900_nl.webp', 'Kẹp tóc nơ, pha lê, nhiều màu.', 2, 0, NULL, 0),
(119, 10, 'Giày đá bóng', 499000.00, 'uploads/1782578493_vn-11134207-7ras8-mchawlsyhdil37.webp', 'Giày đá bóng cỏ nhân tạo, đế TPU.', 1, 0, NULL, 0),
(120, 6, 'Áo yếm nữ', 329000.00, 'uploads/1780633762_vn-11134207-820l4-mertww88dts167.webp', 'Áo yếm thun dệt kim, mặc trong hoặc ngoài.', 2, 0, NULL, 0),
(121, 7, 'Quần ống côn', 499000.00, 'uploads/1780633649_vn-11134207-7qukw-lgmjbmodmxo31c.webp', 'Quần tây ống côn, phom dáng công sở.', 1, 0, NULL, 0),
(122, 8, 'Đầm maxi trắng', 899000.00, 'uploads/1780633216_vn-11134207-820l4-mesjdinl6kub2c.webp', 'Đầm maxi trắng tinh khôi, chất voan lụa.', 2, 0, NULL, 0),
(123, 9, 'Đồng hồ thể thao', 299000.00, 'uploads/1780633029_sg-11134253-8262h-mlwjxpwt21ab50.webp', 'Đồng hồ chống nước, mặt số tròn, dây silicone.', 0, 0, NULL, 0),
(124, 10, 'Giày Sandal nữ', 399000.00, 'uploads/1780632814_vn-11134207-7r98o-lxdo4xq9zzyz5d.webp', 'Xăng đan đế cao, quai mảnh, họa tiết nơ.', 2, 319200, 1, 0),
(125, 6, 'Áo chống nắng', 179000.00, 'uploads/1780632734_vn-11134207-81ztc-mmsouzm7un7k41.webp', 'Áo khoác chống nắng, vải mát lạnh, chống UV.', 2, 0, NULL, 0),
(126, 7, 'Quần skinny nữ', 349000.00, 'uploads/1780632669_vn-11134201-23030-0vhh93nljaovf2.webp', 'Quần bó sát, co giãn 4 chiều, tôn dáng.', 2, 279200, 1, 0),
(127, 8, 'Váy suông ngắn', 329000.00, 'uploads/1780594528_sg-11134253-8261v-mkea56q9e4n4a3.webp', 'Váy suông dáng ngắn trên gối, in họa tiết.', 2, 0, NULL, 0),
(128, 9, 'Thắt lưng vải', 89000.00, 'uploads/1780594427_vn-11134207-7ras8-m0jx2qckkjgva6.webp', 'Thắt lưng vải dù, khóa nhựa nhẹ.', 0, 0, NULL, 0),
(129, 10, 'Giày búp bê nữ', 259000.00, 'uploads/1780594286_vn-11134207-81ztc-mlg4cm7nnf9d7b.webp', 'Giày búp bê mũi tròn, nơ xinh, đế cao su.', 2, 0, NULL, 0),
(130, 6, 'Áo sơ mi tay phồng', 399000.00, 'uploads/1780594153_vn-11134207-7ra0g-m8lljfezn05e4e.webp', 'Sơ mi tay phồng, chất vải satin mềm.', 2, 0, NULL, 0),
(131, 7, 'Quần jogger nữ', 329000.00, 'uploads/1780593923_sg-11134253-8260s-mmcalradhj4fd9.webp', 'Quần jogger thun, co giãn, gấu bó.', 2, 0, NULL, 0),
(132, 8, 'Đầm sơ mi dài tay', 499000.00, 'uploads/1780593846_vn-11134207-7r98o-lzg0j0t3gkgx9d.webp', 'Đầm sơ mi dáng dài, cổ đức, thắt eo.', 2, 0, NULL, 0),
(133, 9, 'Thắt lưng da', 119000.00, 'uploads/1780593695_vn-11134207-7r98o-lqurugc8hyh050.webp', 'Bao da bọc viền, chống sốc, nhiều mẫu.', 0, 0, NULL, 0),
(134, 10, 'Giày thể thao unisex', 799000.00, 'uploads/1780593546_vn-11134207-820l4-mibdnfhdo9ohc9.webp', 'Giày sneaker form rộng, phối da lộn.', 0, 0, NULL, 0),
(135, 6, 'Áo choàng cardigan', 599000.00, 'uploads/1780593477_sg-11134253-81zuq-mj09ooak4xs1dd.webp', 'Áo choàng dệt kim dáng dài, mặc ngoài.', 2, 0, NULL, 0),
(136, 7, 'Quần vải sọc', 279000.00, 'uploads/1780593390_vn-11134207-820l4-mjybgj5mgjd1f5.webp', 'Quần vải sọc kẻ caro, phong cách retro.', 1, 0, NULL, 0),
(137, 8, 'Váy 2 dây xòe', 349000.00, 'uploads/1780593257_vn-11134207-7ra0g-m91tyk8vj77o58.webp', 'Váy 2 dây xòe tầng, chất voan mỏng.', 2, 0, NULL, 0),
(138, 9, 'Ví da nam', 249000.00, 'uploads/1780593184_sg-11134253-824i7-mejxyf2ab9qa12.webp', 'Ví da bò gập đôi, nhiều ngăn thẻ.', 1, 0, NULL, 0),
(139, 10, 'Guốc gỗ nữ', 399000.00, 'uploads/1780593034_vn-11134207-7ra0g-m7i1kb24ygc36b.webp', 'Guốc gỗ mộc, đế cao 5cm, dây da quai.', 2, 0, NULL, 0),
(140, 6, 'Áo hai dây thể thao', 129000.00, 'uploads/1780592925_vn-11134207-81ztc-moqpv4y0h5vxc4.webp', 'Áo tập yoga, chất thun co giãn, thấm hút.', 2, 0, NULL, 0),
(141, 7, 'Quần đùi nam', 189000.00, 'uploads/1780592504_vn-11134207-81ztc-mmm0fl2gvqit5f.webp', 'Quần đùi thể thao, vải lưới thoáng khí.', 1, 0, NULL, 0),
(142, 8, 'Đầm dự tiệc', 999000.00, 'uploads/1780592248_vn-11134207-7r98o-lt9rd9ur4789e5.webp', 'Đầm sequin lấp lánh, phom ôm, cổ tim.', 2, 0, NULL, 0),
(143, 9, 'Cà vạt nam', 129000.00, 'uploads/1780592160_cn-11134207-7r98o-lvkm0jfbtwh28d.webp', 'Cà vạt lụa cao cấp, màu trơn sang trọng.', 1, 0, NULL, 0),
(144, 10, 'Giày bệt nữ', 299000.00, 'uploads/1780591973_vn-11134207-7ra0g-m8g83xa4ha9w96.webp', 'Giày bệt mũi nhọn, da mềm, lót êm.', 2, 0, NULL, 0),
(145, 6, 'Áo len mỏng cổ V', 399000.00, 'uploads/1780591914_vn-11134207-7ras8-m2p4q1ta27ju23.webp', 'Áo len mỏng dệt kim, mặc mùa thu.', 0, 0, NULL, 0),
(146, 7, 'Quần thể thao nam', 349000.00, 'uploads/1780591849_vn-11134207-81ztc-mnscpgi46ltx05.webp', 'Quần thể thao vải nỉ, có dây kéo túi.', 1, 0, NULL, 0),
(147, 8, 'Váy yếm dây', 459000.00, 'uploads/1780591785_vn-11134207-820l4-mfrqug330yz32d.webp', 'Váy yếm 2 dây, chất jean mềm.', 2, 0, NULL, 0),
(148, 9, 'Mũ tai bèo', 119000.00, 'uploads/1780591718_vn-11134207-7r98o-ltvtxrgrqgi26c.webp', 'Mũ tai bèo vải kaki, che nắng tốt.', 0, 0, NULL, 0),
(149, 10, 'Giày sneaker nam', 899000.00, 'uploads/1780591626_vn-11134207-7ra0g-m6i5kt0d73q9d5.webp', 'Giày sneaker nam phối màu, đế cao su.', 1, 0, NULL, 0),
(150, 6, 'Áo sơ mi nam kẻ sọc', 349000.00, 'uploads/1780591543_vn-11134207-7ra0g-m6selw6swq48a1.webp', 'Sơ mi tay dài, chất kate mát, form vừa.', 1, 0, NULL, 0),
(151, 7, 'Quần cargo nam', 499000.00, 'uploads/1780591454_vn-11134207-81ztc-mn2kt02zkcn990.webp', 'Quần cargo nhiều túi, chất kaki chống bám bẩn.', 1, 0, NULL, 0),
(152, 8, 'Đầm ren trắng', 749000.00, 'uploads/1780591354_vn-11134207-7ra0g-m7lyrxwaramb44.webp', 'Đầm ren cao cấp, xếp tầng, lót satin.', 2, 599200, 1, 0),
(153, 9, 'Túi đeo chéo mini', 199000.00, 'uploads/1780591303_vn-11134211-7r98o-lu8yi36rkcuped.webp', 'Túi da mini, đeo chéo phong cách.', 2, 159200, 1, 0),
(154, 10, 'Giày leo núi nữ', 1299000.00, 'uploads/1780591209_vn-11134207-820l4-mimuow5jcfewfc@resize_w900_nl.webp', 'Giày trekking chống trơn, nhẹ và thoáng khí.', 2, 0, NULL, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_url`, `is_primary`) VALUES
(5, 53, 'uploads/1780581739_tuan-tracuuhoadon.png', 0),
(6, 54, 'uploads/1780586385_vn-11134207-7r98o-lo0hd47ob52y90.webp', 0),
(7, 54, 'uploads/1780586385_vn-11134207-7r98o-lo0hd47yaqgq57.webp', 0),
(8, 54, 'uploads/1780586385_vn-11134207-7r98o-lo0hd47yaqjxd3.webp', 0),
(9, 54, 'uploads/1780586385_vn-11134207-7r98o-lo0hd47yq6pmb6.webp', 0),
(10, 54, 'uploads/1780586385_vn-11134207-7r98o-lybwwphcfgi536.webp', 0),
(84, 58, 'uploads/products/product_58_1.jpg', 1),
(85, 58, 'uploads/products/product_58_2.jpg', 0),
(86, 58, 'uploads/products/product_58_3.jpg', 0),
(87, 58, 'uploads/products/product_58_4.jpg', 0),
(103, 63, 'uploads/products/product_63_1.jpg', 1),
(104, 63, 'uploads/products/product_63_2.jpg', 0),
(105, 64, 'uploads/products/product_64_1.jpg', 1),
(106, 64, 'uploads/products/product_64_2.jpg', 0),
(113, 67, 'uploads/products/product_67_1.jpg', 1),
(114, 67, 'uploads/products/product_67_2.jpg', 0),
(115, 67, 'uploads/products/product_67_3.jpg', 0),
(116, 68, 'uploads/products/product_68_1.jpg', 1),
(117, 68, 'uploads/products/product_68_2.jpg', 0),
(118, 69, 'uploads/products/product_69_1.jpg', 1),
(119, 69, 'uploads/products/product_69_2.jpg', 0),
(120, 70, 'uploads/products/product_70_1.jpg', 1),
(121, 70, 'uploads/products/product_70_2.jpg', 0),
(122, 70, 'uploads/products/product_70_3.jpg', 0),
(126, 72, 'uploads/products/product_72_1.jpg', 1),
(127, 72, 'uploads/products/product_72_2.jpg', 0),
(128, 72, 'uploads/products/product_72_3.jpg', 0),
(129, 73, 'uploads/products/product_73_1.jpg', 1),
(130, 73, 'uploads/products/product_73_2.jpg', 0),
(131, 73, 'uploads/products/product_73_3.jpg', 0),
(134, 75, 'uploads/products/product_75_1.jpg', 1),
(135, 75, 'uploads/products/product_75_2.jpg', 0),
(136, 76, 'uploads/products/product_76_1.jpg', 1),
(137, 76, 'uploads/products/product_76_2.jpg', 0),
(138, 76, 'uploads/products/product_76_3.jpg', 0),
(139, 76, 'uploads/products/product_76_4.jpg', 0),
(140, 77, 'uploads/products/product_77_1.jpg', 1),
(141, 77, 'uploads/products/product_77_2.jpg', 0),
(142, 78, 'uploads/products/product_78_1.jpg', 1),
(143, 78, 'uploads/products/product_78_2.jpg', 0),
(146, 80, 'uploads/products/product_80_1.jpg', 1),
(147, 80, 'uploads/products/product_80_2.jpg', 0),
(148, 80, 'uploads/products/product_80_3.jpg', 0),
(149, 80, 'uploads/products/product_80_4.jpg', 0),
(150, 81, 'uploads/products/product_81_1.jpg', 1),
(151, 81, 'uploads/products/product_81_2.jpg', 0),
(152, 81, 'uploads/products/product_81_3.jpg', 0),
(153, 81, 'uploads/products/product_81_4.jpg', 0),
(154, 82, 'uploads/products/product_82_1.jpg', 1),
(155, 82, 'uploads/products/product_82_2.jpg', 0),
(156, 83, 'uploads/products/product_83_1.jpg', 1),
(157, 83, 'uploads/products/product_83_2.jpg', 0),
(158, 84, 'uploads/products/product_84_1.jpg', 1),
(159, 84, 'uploads/products/product_84_2.jpg', 0),
(160, 84, 'uploads/products/product_84_3.jpg', 0),
(161, 85, 'uploads/products/product_85_1.jpg', 1),
(162, 85, 'uploads/products/product_85_2.jpg', 0),
(163, 86, 'uploads/products/product_86_1.jpg', 1),
(164, 86, 'uploads/products/product_86_2.jpg', 0),
(165, 86, 'uploads/products/product_86_3.jpg', 0),
(166, 86, 'uploads/products/product_86_4.jpg', 0),
(167, 87, 'uploads/products/product_87_1.jpg', 1),
(168, 87, 'uploads/products/product_87_2.jpg', 0),
(169, 87, 'uploads/products/product_87_3.jpg', 0),
(170, 87, 'uploads/products/product_87_4.jpg', 0),
(171, 88, 'uploads/products/product_88_1.jpg', 1),
(172, 88, 'uploads/products/product_88_2.jpg', 0),
(173, 88, 'uploads/products/product_88_3.jpg', 0),
(174, 89, 'uploads/products/product_89_1.jpg', 1),
(175, 89, 'uploads/products/product_89_2.jpg', 0),
(176, 89, 'uploads/products/product_89_3.jpg', 0),
(177, 90, 'uploads/products/product_90_1.jpg', 1),
(178, 90, 'uploads/products/product_90_2.jpg', 0),
(183, 64, 'uploads/products/product_64_3.jpg', 0),
(184, 64, 'uploads/products/product_64_4.jpg', 0),
(186, 69, 'uploads/products/product_69_3.jpg', 0),
(187, 70, 'uploads/products/product_70_4.jpg', 0),
(188, 72, 'uploads/products/product_72_4.jpg', 0),
(190, 75, 'uploads/products/product_75_3.jpg', 0),
(191, 75, 'uploads/products/product_75_4.jpg', 0),
(192, 78, 'uploads/products/product_78_3.jpg', 0),
(193, 78, 'uploads/products/product_78_4.jpg', 0),
(196, 82, 'uploads/products/product_82_3.jpg', 0),
(197, 83, 'uploads/products/product_83_3.jpg', 0),
(198, 83, 'uploads/products/product_83_4.jpg', 0),
(199, 88, 'uploads/products/product_88_4.jpg', 0),
(200, 90, 'uploads/products/product_90_3.jpg', 0),
(201, 91, 'uploads/products/product_91_1.jpg', 1),
(202, 91, 'uploads/products/product_91_2.jpg', 0),
(203, 91, 'uploads/products/product_91_3.jpg', 0),
(204, 92, 'uploads/products/product_92_1.jpg', 1),
(205, 92, 'uploads/products/product_92_2.jpg', 0),
(206, 93, 'uploads/products/product_93_1.jpg', 1),
(207, 93, 'uploads/products/product_93_2.jpg', 0),
(208, 93, 'uploads/products/product_93_3.jpg', 0),
(209, 94, 'uploads/products/product_94_1.jpg', 1),
(210, 94, 'uploads/products/product_94_2.jpg', 0),
(211, 94, 'uploads/products/product_94_3.jpg', 0),
(212, 94, 'uploads/products/product_94_4.jpg', 0),
(213, 95, 'uploads/products/product_95_1.jpg', 1),
(214, 95, 'uploads/products/product_95_2.jpg', 0),
(215, 96, 'uploads/products/product_96_1.jpg', 1),
(216, 96, 'uploads/products/product_96_2.jpg', 0),
(217, 97, 'uploads/products/product_97_1.jpg', 1),
(218, 97, 'uploads/products/product_97_2.jpg', 0),
(219, 97, 'uploads/products/product_97_3.jpg', 0),
(386, 154, 'uploads/1780591209_vn-11134207-820l4-mimuow8lq8019f@resize_w900_nl.webp', 0),
(387, 154, 'uploads/1780591209_vn-11134207-820l4-mimuow95bd3cb6.webp', 0),
(388, 153, 'uploads/1780591303_vn-11134207-7r98o-lvq3usnfvqoq40.webp', 0),
(389, 153, 'uploads/1780591303_vn-11134207-7r98o-lvq3usnfyjtm92.webp', 0),
(390, 152, 'uploads/1780591354_vn-11134207-7ra0g-m7lyrxwarb1t0f.webp', 0),
(391, 152, 'uploads/1780591354_vn-11134207-7ra0g-m7lyrxwkqwfl1b.webp', 0),
(392, 151, 'uploads/1780591454_vn-11134207-81ztc-mn2kt02dsjrc16.webp', 0),
(393, 151, 'uploads/1780591454_vn-11134207-81ztc-mn2kt02f8irl5d.webp', 0),
(394, 151, 'uploads/1780591454_vn-11134207-81ztc-mn2kt02f9xc101.webp', 0),
(395, 151, 'uploads/1780591454_vn-11134207-81ztc-mn2kt02gg2de1b.webp', 0),
(396, 150, 'uploads/1780591543_vn-11134207-7ra0g-m85tqcmv4htw6a.webp', 0),
(397, 150, 'uploads/1780591543_vn-11134207-7ras8-map5fmelxcd3c3.webp', 0),
(398, 150, 'uploads/1780591543_vn-11134207-7ras8-mb6zyoqth0ap1d.webp', 0),
(399, 150, 'uploads/1780591543_vn-11134207-7ras8-mb6zyoqth0hfff.webp', 0),
(400, 149, 'uploads/1780591626_vn-11134207-7ra0g-m6i5kt0d8hnr85.webp', 0),
(401, 149, 'uploads/1780591626_vn-11134207-7ra0g-m6i5kt0d9w8750.webp', 0),
(402, 148, 'uploads/1780591718_sg-11134201-7rd4y-m7ez8l4vos837d.webp', 0),
(403, 148, 'uploads/1780591718_vn-11134207-7r98o-ltw6srvhcb9m8b.webp', 0),
(404, 147, 'uploads/1780591785_vn-11134207-7r98o-lpe16ozqzfq357.webp', 0),
(405, 147, 'uploads/1780591785_vn-11134207-7r98o-ls00ub7zehtwb6.webp', 0),
(406, 147, 'uploads/1780591785_vn-11134207-7ras8-md23yn9f895ofa.webp', 0),
(407, 146, 'uploads/1780591849_vn-11134207-7ra0g-m9u5qsf96b8u68.webp', 0),
(408, 146, 'uploads/1780591849_vn-11134207-81ztc-mnscvwu4vo5kd0.webp', 0),
(409, 145, 'uploads/1780591914_vn-11134207-7ras8-m2p4qaitbe0q88.webp', 0),
(410, 145, 'uploads/1780591914_vn-11134207-7ras8-m2p4qaitbe9yde.webp', 0),
(411, 144, 'uploads/1780591973_vn-11134207-7ra0g-m8g83xa4ip365b.webp', 0),
(412, 144, 'uploads/1780591973_vn-11134207-7ra0g-m8g83xa4k3es6b.webp', 0),
(413, 144, 'uploads/1780591973_vn-11134207-7ra0g-m8g83xa4li8237.webp', 0),
(414, 143, 'uploads/1780592160_sg-11134201-7rcd2-lslcku6g7ca146.webp', 0),
(415, 143, 'uploads/1780592160_sg-11134201-7rcdd-lslcktqwu2xbac.webp', 0),
(416, 142, 'uploads/1780592248_vn-11134207-7r98o-lt9rd9uqzzix3e.webp', 0),
(417, 142, 'uploads/1780592248_vn-11134207-7r98o-lt9rd9ur4789e5.webp', 0),
(418, 141, 'uploads/1780592504_vn-11134207-81ztc-mmlygcg4bymgad.webp', 0),
(419, 141, 'uploads/1780592504_vn-11134207-81ztc-mmlygcg6pn9e2c.webp', 0),
(420, 141, 'uploads/1780592504_vn-11134207-81ztc-mnscpgi46ltx05.webp', 0),
(421, 140, 'uploads/1780592925_vn-11134207-7r98o-ltc5z2genzd0e5.webp', 0),
(422, 140, 'uploads/1780592925_vn-11134207-7ras8-mb6f3ban113s12.webp', 0),
(423, 140, 'uploads/1780592925_vn-11134207-820l4-mfkwu310t2xb1a.webp', 0),
(424, 140, 'uploads/1780592925_vn-11134207-820l4-mfkwu316445kd2.webp', 0),
(425, 139, 'uploads/1780593034_vn-11134207-7ra0g-m7i1lpwujfhv7e.webp', 0),
(426, 139, 'uploads/1780593034_vn-11134207-7ra0g-m7i1mhw1lir710.webp', 0),
(427, 139, 'uploads/1780593034_vn-11134207-7ra0g-m7i1mw0cxnuk3c.webp', 0),
(428, 139, 'uploads/1780593034_vn-11134207-7ras8-m2j2z8ijo9f8fb.webp', 0),
(429, 138, 'uploads/1780593184_7a0acc8a59e198dab3f8aa3110efbb6d.webp', 0),
(430, 138, 'uploads/1780593184_179c7d9815b32653379cb78135136e43.webp', 0),
(431, 138, 'uploads/1780593184_05074f5086a66ccd225d76c5b73ce404.webp', 0),
(432, 137, 'uploads/1780593257_vn-11134207-7ra0g-m91tyk8vkm0ya5.webp', 0),
(433, 137, 'uploads/1780593257_vn-11134207-7ra0g-m91tyk9fidz8b6.webp', 0),
(434, 137, 'uploads/1780593257_vn-11134207-7ra0g-m91tyk9phzd0c8.webp', 0),
(435, 136, 'uploads/1780593390_vn-11134207-7r98o-lz0qr9s8xbxd26.webp', 0),
(436, 136, 'uploads/1780593390_vn-11134207-7ras8-m0rk2qlhyqy571.webp', 0),
(437, 136, 'uploads/1780593390_vn-11134207-820l4-milt7eo3o1dz06.webp', 0),
(438, 135, 'uploads/1780593477_sg-11134201-22100-0achiw9d4uiva1.webp', 0),
(439, 135, 'uploads/1780593477_sg-11134201-22100-wp8p6mak4uiv79.webp', 0),
(440, 134, 'uploads/1780593546_vn-11134207-820l4-mibdnfhglm9w07.webp', 0),
(441, 134, 'uploads/1780593546_vn-11134207-820l4-mibdnfihv2m9c8.webp', 0),
(442, 134, 'uploads/1780593546_vn-11134211-81ztc-mk6i2msjsfes78.webp', 0),
(443, 133, 'uploads/1780593695_vn-11134207-7r98o-lqurugc8jd1g94.webp', 0),
(444, 133, 'uploads/1780593695_vn-11134207-7r98o-lqus0pbowq8428.webp', 0),
(445, 133, 'uploads/1780593695_vn-11134207-7r98o-lqus0pboy4skad.webp', 0),
(446, 132, 'uploads/1780593846_vn-11134207-7r98o-lzg0j0t32ij162.webp', 0),
(447, 132, 'uploads/1780593846_vn-11134207-7r98o-lzg0j0t313yl8d.webp', 0),
(448, 131, 'uploads/1780593923_vn-11134207-7qukw-ljcx2fuopnma2e.webp', 0),
(449, 131, 'uploads/1780593923_vn-11134207-7r98o-llfagqbiqpqg46.webp', 0),
(450, 131, 'uploads/1780593923_vn-11134207-7ra0g-m698cexa9f0636.webp', 0),
(451, 130, 'uploads/1780594153_vn-11134207-7ra0g-m8lljfezoepu8e.webp', 0),
(452, 130, 'uploads/1780594153_vn-11134207-7ras8-mboxum9ljw4qfa.webp', 0),
(453, 129, 'uploads/1780594286_vn-11134207-81ztc-mlg4cm7khn9hc9.webp', 0),
(454, 129, 'uploads/1780594286_vn-11134207-81ztc-mlg4cm7uk1s587.webp', 0),
(455, 128, 'uploads/1780594427_vn-11134207-7ras8-m0fetbdzxbzh1c (1).webp', 0),
(456, 128, 'uploads/1780594427_vn-11134207-7ras8-m0fetbdzxbzh1c.webp', 0),
(457, 128, 'uploads/1780594427_vn-11134207-7ras8-m0fetbdzxcb3da.webp', 0),
(458, 127, 'uploads/1780594528_sg-11134253-8262m-mkeartrim9l1ca (1).webp', 0),
(459, 127, 'uploads/1780594528_sg-11134253-82606-mke995qd0w7996.webp', 0),
(460, 126, 'uploads/1780632669_a1f6af08fdd5141d02fe838e5408a7bb.webp', 0),
(461, 126, 'uploads/1780632669_vn-11134201-23030-0j5i465ljaov07.webp', 0),
(462, 126, 'uploads/1780632669_vn-11134201-23030-6o2oovyljaov67.webp', 0),
(463, 125, 'uploads/1780632734_vn-11134207-81ztc-mmsouzm6vj0hab.webp', 0),
(464, 125, 'uploads/1780632734_vn-11134207-81ztc-mmsouzm8xz461a.webp', 0),
(465, 125, 'uploads/1780632734_vn-11134207-81ztc-mmsouzmd8hl121.webp', 0),
(466, 124, 'uploads/1780632814_vn-11134207-7r98o-lxdo4xqa1ejfd1.webp', 0),
(467, 124, 'uploads/1780632814_vn-11134207-7r98o-lxdo57lp4ezf34.webp', 0),
(468, 123, 'uploads/1780633029_cn-11134301-7qukw-lk7w5tcwcu590b.webp', 0),
(469, 123, 'uploads/1780633029_sg-11134253-82626-mlwn16y2tyx409.webp', 0),
(470, 123, 'uploads/1780633029_sg-11134301-7renu-m1o0hb82m14v71.webp', 0),
(471, 122, 'uploads/1780633216_vn-11134207-7r98o-lvvcga0urf15f3.webp', 0),
(472, 122, 'uploads/1780633216_vn-11134207-7r98o-lvvcga0ustll5b.webp', 0),
(473, 121, 'uploads/1780633649_vn-11134207-7ras8-m1pelx9kpry7e9.webp', 0),
(474, 121, 'uploads/1780633649_vn-11134207-7ras8-m1pelx9unyhf96.webp', 0),
(475, 120, 'uploads/1780633762_vn-11134207-820l4-mertww8qddl1aa.webp', 0),
(476, 120, 'uploads/1780633762_vn-11134207-820l4-mertww9ags1x0a.webp', 0),
(477, 119, 'uploads/1782578493_vn-11134207-7ras8-mchas30csgx9fc.webp', 0),
(478, 119, 'uploads/1782578493_vn-11134207-7ras8-mchas31go1uk71.webp', 0),
(479, 119, 'uploads/1782578493_vn-11134207-7ras8-mchas36ggq7hff.webp', 0),
(480, 119, 'uploads/1782578493_vn-11134207-7ras8-mchas316r92l8a.webp', 0),
(481, 118, 'uploads/1782578672_cn-11134207-820l4-moesp6xn3xn0d4.webp', 0),
(482, 118, 'uploads/1782578672_sg-11134201-7rfge-m9cqw4bmcimxda.webp', 0),
(483, 118, 'uploads/1782578672_sg-11134201-7rfid-m9cqw7jlmsh518.webp', 0),
(484, 117, 'uploads/1782578835_vn-11134207-81ztc-mplyh45fgphn82.webp', 0),
(485, 117, 'uploads/1782578835_vn-11134207-81ztc-mplyhe1ajmrkee.webp', 0),
(486, 116, 'uploads/1782578950_vn-11134207-7r98o-lueg4c1i37yp2f.webp', 0),
(487, 116, 'uploads/1782578950_vn-11134207-7r98o-lueg4c1iio7l87.webp', 0),
(488, 116, 'uploads/1782578950_vn-11134207-81ztc-mmm0fl2gvqit5f.webp', 0),
(489, 115, 'uploads/1782579101_vn-11134207-7ras8-m1p4xz5158zj22.webp', 0),
(490, 115, 'uploads/1782579101_vn-11134207-7ras8-m1p4y6rfzo7n98.webp', 0),
(491, 115, 'uploads/1782579101_vn-11134207-7ras8-mdb8k9zsvinz07.webp', 0),
(492, 115, 'uploads/1782579101_vn-11134207-81ztc-morrfabjx8gf1a.webp', 0),
(493, 114, 'uploads/1782579198_c577a1a893f88f1bf35658a53a55e6c7.webp', 0),
(494, 114, 'uploads/1782579198_sg-11134201-22100-gh7yfkmynliv80.webp', 0),
(495, 114, 'uploads/1782579198_sg-11134201-22100-s7l2rimynlivd2.webp', 0),
(496, 113, 'uploads/1782579308_vn-11134207-7ras8-mc0l52okvuy2dc.webp', 0),
(497, 113, 'uploads/1782579308_vn-11134207-7ras8-mc0l52okx9ii53.webp', 0),
(498, 113, 'uploads/1782579308_vn-11134207-7ras8-mc0l52pet84y5b.webp', 0),
(499, 112, 'uploads/1782579599_vn-11134201-23030-lvccto5l5vov1a.webp', 0),
(500, 112, 'uploads/1782579599_vn-11134201-23030-reudec6l5vov2c.webp', 0),
(501, 112, 'uploads/1782579599_vn-11134207-7qukw-lh5865dbmeb702.jpg', 0),
(502, 112, 'uploads/1782579599_vn-11134207-7r98o-lzw26spdg90h3e.webp', 0),
(503, 111, 'uploads/1782579677_vn-11134207-820l4-mj52fgo74x6u3b.webp', 0),
(504, 111, 'uploads/1782579677_vn-11134207-820l4-mj52fgo76brae1.webp', 0),
(505, 111, 'uploads/1782579677_vn-11134207-820l4-mj52fgo794w606.webp', 0),
(506, 111, 'uploads/1782579677_vn-11134207-820l4-mj52fgoc1wqvcb.webp', 0),
(507, 111, 'uploads/1782579677_vn-11134207-820l4-mj52fgombc3m4a.webp', 0),
(508, 111, 'uploads/1782579677_vn-11134207-820l4-mj52fgpv5amb53.webp', 0),
(509, 110, 'uploads/1782579751_vn-11134207-7ra0g-mac7eqgvwi9v92.webp', 0),
(510, 110, 'uploads/1782579751_vn-11134207-7ra0g-mac7es2bhmy010.webp', 0),
(511, 110, 'uploads/1782579751_vn-11134207-7ra0g-mac7ozi5xyej45.webp', 0),
(512, 110, 'uploads/1782579751_vn-11134207-7ra0g-mac7ozifrxij77.webp', 0),
(513, 110, 'uploads/1782579751_vn-11134207-7ras8-mbn7ig02bmk4d2.webp', 0),
(514, 110, 'uploads/1782579751_vn-11134207-81ztc-mnw7exyp1b7p83.webp', 0),
(515, 110, 'uploads/1782579751_vn-11134207-81ztc-mnw7exysxs068d.webp', 0),
(516, 110, 'uploads/1782579751_vn-11134207-81ztc-mnw7exyu13wo44.webp', 0),
(517, 109, 'uploads/1782579845_vn-11134207-81ztc-mngv33vgsc1wf1.webp', 0),
(518, 109, 'uploads/1782579845_vn-11134207-81ztc-mngo0jcek45hcf.webp', 0),
(519, 109, 'uploads/1782579845_vn-11134207-81ztc-mngo0swftxxga8.webp', 0),
(520, 109, 'uploads/1782579845_vn-11134207-81ztc-mngo0sxjo3r4c7.webp', 0),
(521, 109, 'uploads/1782579845_vn-11134207-81ztc-mngo17j3sg7c41.webp', 0),
(522, 109, 'uploads/1782579845_vn-11134207-81ztc-mngo17j6br4214.webp', 0),
(523, 109, 'uploads/1782579845_vn-11134207-81ztc-mngo17j68xz634.webp', 0),
(524, 108, 'uploads/1782579975_vn-11134207-7ras8-m1wddohbpwlve7.webp', 0),
(525, 108, 'uploads/1782579975_vn-11134207-7ras8-m1wddohbpwvz53.webp', 0),
(526, 108, 'uploads/1782579975_vn-11134207-7ras8-m1wddohbrbgf7e.webp', 0),
(527, 108, 'uploads/1782579975_vn-11134207-7ras8-m1wddohlo3f72a.webp', 0),
(528, 107, 'uploads/1782580048_vn-11134207-7ras8-m3neuox6e7qt81.webp', 0),
(529, 107, 'uploads/1782580048_vn-11134207-7ras8-m3neusy0fgywb8.webp', 0),
(530, 107, 'uploads/1782580048_vn-11134207-81ztc-mkpctxiip1xh43.webp', 0),
(531, 107, 'uploads/1782580048_vn-11134207-81ztc-mo442tois4jsb8.webp', 0),
(532, 106, 'uploads/1782580113_vn-11134207-7ras8-mb3mpezf5nep5d.webp', 0),
(533, 106, 'uploads/1782580113_vn-11134207-7ras8-mb3mpezf48u906.webp', 0),
(534, 106, 'uploads/1782580113_vn-11134207-7ras8-mb3mpezp2fnld8.webp', 0),
(535, 106, 'uploads/1782580113_vn-11134207-7ras8-mbc4xkh2mz770a.webp', 0),
(536, 105, 'uploads/1782580187_vn-11134201-7ra0g-ma27wsdcu1ke9e.webp', 0),
(537, 105, 'uploads/1782580187_vn-11134201-7ra0g-ma27wsdcu35u78.webp', 0),
(538, 105, 'uploads/1782580187_vn-11134201-7ra0g-ma27wsdwt89m6d.webp', 0),
(539, 105, 'uploads/1782580187_vn-11134201-7ra0g-ma27wshinzno34.webp', 0),
(540, 105, 'uploads/1782580187_vn-11134201-7ra0g-ma27wsiwlx5m0c.webp', 0),
(541, 71, 'uploads/1782580597_vn-11134207-7ras8-m2qybh8u4hza9a.webp', 0),
(542, 71, 'uploads/1782580597_vn-11134207-7ras8-m2qybmbepbju18.webp', 0),
(543, 71, 'uploads/1782580597_vn-11134207-7ras8-m2qybsm3hlhid8.webp', 0),
(544, 71, 'uploads/1782580597_vn-11134207-7ras8-m2qybxikbcmy61.webp', 0),
(545, 104, 'uploads/1782580719_vn-11134207-7ra0g-m8ftiikxt8xz12 (1).webp', 0),
(546, 104, 'uploads/1782580719_vn-11134207-7ra0g-m8ftiikxt8xz12.webp', 0),
(547, 104, 'uploads/1782580719_vn-11134207-7ra0g-m8ftiikxunhq5b.webp', 0),
(548, 104, 'uploads/1782580719_vn-11134207-7ra0g-m8ftiikxw226e8.webp', 0),
(549, 103, 'uploads/1782580788_vn-11134207-820l4-mi5f8e3w5rt381.webp', 0),
(550, 103, 'uploads/1782580788_vn-11134207-820l4-mi5f8l5wptzbb5.webp', 0),
(551, 103, 'uploads/1782580788_vn-11134207-820l4-mi5f8q2nt0qob3.webp', 0),
(552, 103, 'uploads/1782580788_vn-11134207-820l4-mi5f83z03egy97.webp', 0),
(553, 102, 'uploads/1782580871_vn-11134207-7r98o-lz21az6g3e751c - Sao chép.webp', 0),
(554, 102, 'uploads/1782580871_vn-11134207-7r98o-lz21az6g3e751c.webp', 0),
(555, 102, 'uploads/1782580871_vn-11134207-7r98o-lz21az6q066l9c.webp', 0),
(556, 101, 'uploads/1782580940_vn-11134207-81ztc-mo3nanh2ckch86.webp', 0),
(557, 101, 'uploads/1782580940_vn-11134207-81ztc-mo3nat90tfk271.webp', 0),
(558, 101, 'uploads/1782580940_vn-11134207-81ztc-mo3oayj0y1hkf6.webp', 0),
(559, 101, 'uploads/1782580940_vn-11134207-81ztc-mo3sal2ob0n5bf.webp', 0),
(560, 101, 'uploads/1782580940_vn-11134207-820l4-mhk531j5xkox9d.webp', 0),
(561, 100, 'uploads/1782581013_sg-11134201-8260b-mkw0djuben7qfa.webp', 0),
(562, 100, 'uploads/1782581013_sg-11134201-8261p-mkw0di8z7pj552.webp', 0),
(563, 100, 'uploads/1782581013_sg-11134201-8262m-mkw0dixattdy81.webp', 0),
(564, 100, 'uploads/1782581013_sg-11134201-8262o-mkw0dki4oikg8f.webp', 0),
(565, 100, 'uploads/1782581013_sg-11134201-8262p-mkw0dims5ibmc9.webp', 0),
(566, 100, 'uploads/1782581013_sg-11134201-82601-mkw0djgc2ha8f0.webp', 0),
(567, 66, 'uploads/1782581111_vn-11134207-7r98o-ly6cu2paz89tdd.webp', 0),
(568, 66, 'uploads/1782581111_vn-11134207-7r98o-ly6cua7txxl9a4.webp', 0),
(569, 66, 'uploads/1782581111_vn-11134207-7r98o-ly6cua7txxupdb.webp', 0),
(570, 66, 'uploads/1782581111_vn-11134207-7r98o-ly6cuacjr1cx15 - Sao chép.webp', 0),
(571, 66, 'uploads/1782581111_vn-11134207-7r98o-ly6cuacjr1cx15.webp', 0),
(572, 62, 'uploads/1782581355_vn-11134207-820l4-mfy62iwssgsp8b.webp', 0),
(573, 62, 'uploads/1782581355_vn-11134207-820l4-mfy62iwyu6tp0b.webp', 0),
(574, 62, 'uploads/1782581355_vn-11134207-820l4-mfy62ixws8w9cb.webp', 0),
(575, 62, 'uploads/1782581355_vn-11134207-820l4-mfy62iyn2zny8e.webp', 0),
(576, 62, 'uploads/1782581355_vn-11134207-820l4-mfy62j22xe6h1b.webp', 0),
(577, 55, 'uploads/1782581682_68844429180f1c2f792065fa02e5580f (1).webp', 0),
(578, 55, 'uploads/1782581682_68844429180f1c2f792065fa02e5580f.webp', 0),
(579, 55, 'uploads/1782581682_f2db8710060f55fcbaaf580cf7a91fed.webp', 0),
(580, 55, 'uploads/1782581682_f33a3a8bc296c7099451db224defda4c.webp', 0),
(581, 57, 'uploads/1782581787_sg-11134201-7rcd2-m66zcv7q36l348.webp', 0),
(582, 57, 'uploads/1782581787_sg-11134201-7rcdf-m66zd585fp1z00.webp', 0),
(583, 57, 'uploads/1782581787_sg-11134201-7rce9-m66zb4gvwpw7e0.webp', 0),
(584, 57, 'uploads/1782581787_sg-11134201-7rcf0-m66zbzpg70cgce.webp', 0),
(585, 57, 'uploads/1782581787_vn-11134207-7ras8-mcwzan86wk0s6e.webp', 0),
(586, 56, 'uploads/1782582702_vn-11134207-7ras8-mcfc5i4e0eozf9.webp', 0),
(587, 56, 'uploads/1782582702_vn-11134207-7ras8-mcfc9q4tk60356.webp', 0),
(588, 56, 'uploads/1782582702_vn-11134207-7ras8-mcfc31alzyw3ad.webp', 0),
(589, 56, 'uploads/1782582702_vn-11134207-7ras8-mcfc71ocppb780.webp', 0),
(590, 59, 'uploads/1782582808_vn-11134207-820l4-mg3vrmown021ec.webp', 0),
(591, 59, 'uploads/1782582808_vn-11134207-820l4-mg3w1murfqq698.webp', 0),
(592, 59, 'uploads/1782582808_vn-11134207-820l4-mg3w1musg9hr28.webp', 0),
(593, 59, 'uploads/1782582808_vn-11134207-820l4-mg3w1muwtl3hcf.webp', 0),
(594, 59, 'uploads/1782582808_vn-11134207-820l4-mg3w32nmdf6365.webp', 0),
(595, 79, 'uploads/1782583575_vn-11134207-7ras8-maxmko53kau08a.jpg', 0),
(596, 99, 'uploads/1782583813_vn-11134207-7ras8-m4efz16r8zwv6f.webp', 0),
(597, 99, 'uploads/1782583813_vn-11134207-7ras8-m4efz16r66cg80.webp', 0),
(598, 99, 'uploads/1782583813_vn-11134207-7ras8-m4efz16r66rz27 (1).webp', 0),
(599, 99, 'uploads/1782583813_vn-11134207-7ras8-m4efz16r66rz27.webp', 0),
(600, 60, 'uploads/1782584206_vn-11134207-7ras8-m4a5pkg2s35zfd.webp', 0),
(601, 60, 'uploads/1782584206_vn-11134207-7ras8-m33f5xtfx9jvc7.webp', 0),
(602, 60, 'uploads/1782584206_vn-11134207-7ras8-m33f621hqra3a8.webp', 0),
(603, 60, 'uploads/1782584206_vn-11134207-7ras8-m45uqzp1c70028.webp', 0),
(604, 60, 'uploads/1782584206_vn-11134207-7ras8-m62fsrrmmrybab.webp', 0),
(605, 60, 'uploads/1782584206_vn-11134207-7ras8-m62ftemb6vpfcf.webp', 0),
(606, 60, 'uploads/1782584206_vn-11134207-7ras8-m62fu7sijge00b.webp', 0),
(607, 61, 'uploads/1782584658_vn-11134207-7r98o-m079sfhyjdnx7a.webp', 0),
(608, 61, 'uploads/1782584658_vn-11134207-7r98o-m079sfhyjdzj7b.webp', 0),
(609, 61, 'uploads/1782584658_vn-11134207-7r98o-m079sfhyks8d46.webp', 0),
(610, 61, 'uploads/1782584658_vn-11134207-7r98o-m079sfhyksjz1c.webp', 0),
(611, 61, 'uploads/1782584658_vn-11134207-7r98o-m079sfi8izdb24.webp', 0),
(612, 65, 'uploads/1782584784_vn-11134207-81ztc-mlepkqx19bm7aa.webp', 0),
(613, 65, 'uploads/1782584784_vn-11134207-81ztc-mliy4pfbck5c1a.webp', 0),
(614, 65, 'uploads/1782584784_vn-11134207-81ztc-mnuvmit7pu6b42.webp', 0),
(615, 65, 'uploads/1782584784_vn-11134207-81ztc-mnuvy4jyc7pfb1.webp', 0),
(616, 65, 'uploads/1782584784_vn-11134207-81ztc-mnuw1memoohyab.webp', 0),
(617, 65, 'uploads/1782584784_vn-11134207-81ztc-mnuw1spokwzq88.webp', 0),
(618, 74, 'uploads/1782585012_vn-11134207-7ras8-mcjjdpx4263g14.webp', 0),
(619, 74, 'uploads/1782585012_vn-11134207-7ras8-mcjjdpxdrwz1b6.webp', 0),
(620, 74, 'uploads/1782585012_vn-11134207-7ras8-mcjjf7efhhngb3.webp', 0),
(621, 74, 'uploads/1782585012_vn-11134207-7ras8-mddd1lop63l956.webp', 0),
(622, 74, 'uploads/1782585012_vn-11134207-7ras8-mddd1xcs3djw8b.webp', 0),
(623, 74, 'uploads/1782585012_vn-11134207-7ras8-mddd2vx7ib1820.webp', 0),
(624, 98, 'uploads/1782585110_sg-11134201-7reoc-m2295yofxgqf8f.webp', 0),
(625, 98, 'uploads/1782585110_sg-11134201-7reoe-m2295y9qke2k6a.webp', 0),
(626, 98, 'uploads/1782585110_sg-11134201-7reoo-m2295wbtepwg4d.webp', 0),
(627, 98, 'uploads/1782585110_sg-11134201-7rep9-m2295wpysl8f57.webp', 0),
(628, 98, 'uploads/1782585110_vn-11134207-7ras8-m23llrul54ny64.webp', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `size` varchar(20) DEFAULT NULL,
  `stock` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `color`, `size`, `stock`) VALUES
(6, 53, 'đen', 'M, S ', 4),
(14, 54, 'Trắng', 'M', 50),
(15, 54, 'Đen', 'M', 50),
(16, 54, 'Trắng', 'L', 50),
(17, 54, 'Trắng', 'XL', 50),
(18, 54, 'Đen', 'L', 50),
(19, 54, 'Đen', 'XL', 50),
(40, 58, 'Hồng', 'S', 60),
(41, 58, 'Hồng', 'M', 80),
(42, 58, 'Hồng', 'L', 45),
(43, 58, 'Xám', 'S', 55),
(44, 58, 'Xám', 'M', 70),
(45, 58, 'Xám', 'L', 40),
(46, 58, 'Đen', 'S', 50),
(47, 58, 'Đen', 'M', 65),
(78, 63, 'Xám đen', 'S', 50),
(79, 63, 'Xám đen', 'M', 70),
(80, 63, 'Xám đen', 'L', 60),
(81, 63, 'Xám đen', 'XL', 40),
(82, 63, 'Xanh dương', 'M', 45),
(83, 63, 'Xanh dương', 'L', 55),
(84, 63, 'Xanh dương', 'XL', 35),
(85, 64, 'Đen', 'S', 150),
(86, 64, 'Đen', 'M', 200),
(87, 64, 'Đen', 'L', 120),
(88, 64, 'Xám', 'S', 100),
(89, 64, 'Xám', 'M', 160),
(90, 64, 'Xám', 'L', 90),
(91, 64, 'Nâu', 'M', 80),
(104, 67, 'Xanh mint', 'S', 55),
(105, 67, 'Xanh mint', 'M', 80),
(106, 67, 'Xanh mint', 'L', 45),
(107, 67, 'Hồng phấn', 'S', 50),
(108, 67, 'Hồng phấn', 'M', 70),
(109, 67, 'Hồng phấn', 'L', 40),
(110, 68, 'Đỏ', 'S', 30),
(111, 68, 'Đỏ', 'M', 45),
(112, 68, 'Đỏ', 'L', 35),
(113, 68, 'Xanh navy', 'S', 25),
(114, 68, 'Xanh navy', 'M', 40),
(115, 68, 'Xanh navy', 'L', 30),
(116, 68, 'Vàng', 'M', 20),
(117, 69, 'Trắng', 'S', 50),
(118, 69, 'Trắng', 'M', 70),
(119, 69, 'Trắng', 'L', 45),
(120, 69, 'Xanh than', 'S', 35),
(121, 69, 'Xanh than', 'M', 55),
(122, 69, 'Xanh than', 'L', 35),
(123, 70, 'Đen', 'Free', 200),
(124, 70, 'Trắng', 'Free', 180),
(125, 70, 'Xanh', 'Free', 150),
(126, 70, 'Đỏ', 'Free', 120),
(132, 72, 'Be', 'Free', 150),
(133, 72, 'Đen', 'Free', 130),
(134, 72, 'Họa tiết', 'Free', 100),
(135, 73, 'Đen', 'Free', 120),
(136, 73, 'Nâu', 'Free', 100),
(137, 73, 'Xanh dương', 'Free', 90),
(138, 73, 'Hồng', 'Free', 70),
(142, 75, 'Trắng đen', '39', 40),
(143, 75, 'Trắng đen', '40', 60),
(144, 75, 'Trắng đen', '41', 55),
(145, 75, 'Trắng đen', '42', 45),
(146, 75, 'Xám', '40', 35),
(147, 75, 'Xám', '41', 40),
(148, 75, 'Xám', '42', 30),
(149, 76, 'Nâu', '36', 50),
(150, 76, 'Nâu', '37', 60),
(151, 76, 'Nâu', '38', 55),
(152, 76, 'Nâu', '39', 40),
(153, 76, 'Đen', '36', 45),
(154, 76, 'Đen', '37', 55),
(155, 76, 'Đen', '38', 50),
(156, 76, 'Đen', '39', 35),
(157, 76, 'Trắng', '37', 40),
(158, 76, 'Trắng', '38', 45),
(159, 77, 'Nâu', '39', 30),
(160, 77, 'Nâu', '40', 45),
(161, 77, 'Nâu', '41', 50),
(162, 77, 'Nâu', '42', 40),
(163, 77, 'Đen', '40', 35),
(164, 77, 'Đen', '41', 40),
(165, 77, 'Đen', '42', 30),
(166, 78, 'Đen', '36', 25),
(167, 78, 'Đen', '37', 35),
(168, 78, 'Đen', '38', 40),
(169, 78, 'Đen', '39', 30),
(170, 78, 'Nâu', '37', 20),
(171, 78, 'Nâu', '38', 30),
(172, 78, 'Nâu', '39', 25),
(180, 80, 'Hồng', 'S', 90),
(181, 80, 'Hồng', 'M', 110),
(182, 80, 'Hồng', 'L', 70),
(183, 80, 'Xanh', 'S', 80),
(184, 80, 'Xanh', 'M', 100),
(185, 80, 'Xanh', 'L', 60),
(186, 80, 'Đen', 'M', 95),
(187, 81, 'Trắng kẻ', 'M', 50),
(188, 81, 'Trắng kẻ', 'L', 60),
(189, 81, 'Trắng kẻ', 'XL', 40),
(190, 81, 'Xanh kẻ', 'M', 45),
(191, 81, 'Xanh kẻ', 'L', 55),
(192, 82, 'Đen', 'S', 60),
(193, 82, 'Đen', 'M', 80),
(194, 82, 'Đen', 'L', 50),
(195, 82, 'Be', 'S', 45),
(196, 82, 'Be', 'M', 65),
(197, 82, 'Be', 'L', 40),
(198, 82, 'Xanh da trời', 'M', 55),
(199, 83, 'Xanh đen', 'S', 40),
(200, 83, 'Xanh đen', 'M', 60),
(201, 83, 'Xanh đen', 'L', 55),
(202, 83, 'Xanh đen', 'XL', 35),
(203, 83, 'Xám', 'M', 50),
(204, 83, 'Xám', 'L', 45),
(205, 83, 'Xám', 'XL', 30),
(206, 84, 'Đen trắng', 'S', 50),
(207, 84, 'Đen trắng', 'M', 70),
(208, 84, 'Đen trắng', 'L', 45),
(209, 84, 'Xanh trắng', 'S', 40),
(210, 84, 'Xanh trắng', 'M', 60),
(211, 84, 'Xanh trắng', 'L', 40),
(212, 85, 'Xám', 'S', 45),
(213, 85, 'Xám', 'M', 60),
(214, 85, 'Xám', 'L', 35),
(215, 85, 'Nâu', 'S', 35),
(216, 85, 'Nâu', 'M', 50),
(217, 85, 'Nâu', 'L', 30),
(218, 85, 'Đen', 'M', 55),
(219, 86, 'Bạc', 'Free', 200),
(220, 86, 'Vàng hồng', 'Free', 150),
(221, 87, 'Trắng', 'Free', 500),
(222, 87, 'Đen', 'Free', 450),
(223, 87, 'Xám', 'Free', 400),
(224, 87, 'Họa tiết', 'Free', 300),
(225, 88, 'Đen', '36', 30),
(226, 88, 'Đen', '37', 45),
(227, 88, 'Đen', '38', 50),
(228, 88, 'Đen', '39', 35),
(229, 88, 'Nude', '36', 25),
(230, 88, 'Nude', '37', 40),
(231, 88, 'Nude', '38', 45),
(232, 88, 'Nude', '39', 30),
(233, 88, 'Đỏ', '37', 20),
(234, 88, 'Đỏ', '38', 25),
(235, 89, 'Nâu', '40', 45),
(236, 89, 'Nâu', '41', 50),
(237, 89, 'Nâu', '42', 40),
(238, 89, 'Nâu', '43', 30),
(239, 89, 'Đen', '40', 40),
(240, 89, 'Đen', '41', 45),
(241, 89, 'Đen', '42', 35),
(242, 90, 'Trắng', 'M', 60),
(243, 90, 'Trắng', 'L', 70),
(244, 90, 'Trắng', 'XL', 45),
(245, 90, 'Đen', 'M', 55),
(246, 90, 'Đen', 'L', 65),
(247, 90, 'Đen', 'XL', 40),
(248, 90, 'Xanh than', 'M', 50),
(249, 90, 'Xanh than', 'L', 60),
(250, 91, 'Đen', 'S', 40),
(251, 91, 'Đen', 'M', 60),
(252, 91, 'Đen', 'L', 50),
(253, 91, 'Đen', 'XL', 35),
(254, 91, 'Xanh dương', 'M', 45),
(255, 91, 'Xanh dương', 'L', 55),
(256, 91, 'Xanh dương', 'XL', 30),
(257, 91, 'Đỏ', 'M', 35),
(258, 91, 'Đỏ', 'L', 40),
(259, 92, 'Kaki', 'S', 35),
(260, 92, 'Kaki', 'M', 50),
(261, 92, 'Kaki', 'L', 55),
(262, 92, 'Kaki', 'XL', 40),
(263, 92, 'Đen', 'M', 45),
(264, 92, 'Đen', 'L', 50),
(265, 92, 'Đen', 'XL', 35),
(266, 92, 'Xám', 'M', 40),
(267, 92, 'Xám', 'L', 45),
(268, 93, 'Xanh đen', 'S', 80),
(269, 93, 'Xanh đen', 'M', 100),
(270, 93, 'Xanh đen', 'L', 70),
(271, 93, 'Đen', 'S', 75),
(272, 93, 'Đen', 'M', 95),
(273, 93, 'Đen', 'L', 65),
(274, 94, 'Vàng sequin', 'S', 15),
(275, 94, 'Vàng sequin', 'M', 25),
(276, 94, 'Vàng sequin', 'L', 15),
(277, 94, 'Hồng sequin', 'S', 10),
(278, 94, 'Hồng sequin', 'M', 20),
(279, 94, 'Hồng sequin', 'L', 12),
(280, 95, 'Xanh', 'S', 50),
(281, 95, 'Xanh', 'M', 70),
(282, 95, 'Xanh', 'L', 45),
(283, 95, 'Đen', 'S', 40),
(284, 95, 'Đen', 'M', 60),
(285, 95, 'Đen', 'L', 40),
(286, 96, 'Đen', 'Free', 100),
(287, 96, 'Xám', 'Free', 80),
(288, 96, 'Xanh dương', 'Free', 70),
(289, 97, 'Đen', 'Free', 120),
(290, 97, 'Xám', 'Free', 100),
(291, 97, 'Đỏ', 'Free', 90),
(292, 97, 'Nâu', 'Free', 80),
(678, 154, 'Xanh hồng', '36', 25),
(679, 154, 'Xanh hồng', '37', 35),
(680, 154, 'Xanh hồng', '38', 40),
(681, 154, 'Xanh hồng', '39', 30),
(682, 154, 'Xám hồng', '36', 20),
(683, 154, 'Xám hồng', '37', 30),
(684, 154, 'Xám hồng', '38', 35),
(685, 154, 'Xám hồng', '39', 25),
(686, 153, 'Đen', 'Free', 120),
(687, 153, 'Nâu', 'Free', 110),
(688, 153, 'Đỏ', 'Free', 100),
(689, 153, 'Xanh', 'Free', 90),
(690, 152, 'Trắng', 'S', 25),
(691, 152, 'Trắng', 'M', 35),
(692, 152, 'Trắng', 'L', 25),
(693, 152, 'Kem', 'S', 20),
(694, 152, 'Kem', 'M', 30),
(695, 152, 'Kem', 'L', 20),
(696, 151, 'Đen', 'S', 45),
(697, 151, 'Đen', 'M', 65),
(698, 151, 'Đen', 'L', 55),
(699, 151, 'Đen', 'XL', 40),
(700, 151, 'Kaki', 'M', 60),
(701, 151, 'Kaki', 'L', 50),
(702, 151, 'Kaki', 'XL', 35),
(703, 151, 'Xám', 'M', 55),
(704, 150, 'Trắng kẻ', 'M', 45),
(705, 150, 'Trắng kẻ', 'L', 55),
(706, 150, 'Trắng kẻ', 'XL', 40),
(707, 150, 'Xanh kẻ', 'M', 40),
(708, 150, 'Xanh kẻ', 'L', 50),
(709, 150, 'Xanh kẻ', 'XL', 35),
(710, 149, 'Trắng xanh', '39', 40),
(711, 149, 'Trắng xanh', '40', 60),
(712, 149, 'Trắng xanh', '41', 65),
(713, 149, 'Trắng xanh', '42', 55),
(714, 149, 'Đen đỏ', '40', 50),
(715, 149, 'Đen đỏ', '41', 55),
(716, 149, 'Đen đỏ', '42', 45),
(717, 149, 'Xám', '41', 50),
(718, 148, 'Kaki', 'Free', 100),
(719, 148, 'Đen', 'Free', 90),
(720, 148, 'Xanh rêu', 'Free', 80),
(721, 148, 'Nâu', 'Free', 70),
(722, 147, 'Xanh', 'S', 45),
(723, 147, 'Xanh', 'M', 65),
(724, 147, 'Xanh', 'L', 50),
(725, 147, 'Đen', 'S', 40),
(726, 147, 'Đen', 'M', 60),
(727, 147, 'Đen', 'L', 45),
(728, 147, 'Kem', 'M', 55),
(729, 146, 'Đen', 'S', 60),
(730, 146, 'Đen', 'M', 80),
(731, 146, 'Đen', 'L', 70),
(732, 146, 'Đen', 'XL', 55),
(733, 146, 'Xám', 'M', 75),
(734, 146, 'Xám', 'L', 65),
(735, 146, 'Xám', 'XL', 50),
(736, 146, 'Xanh dương', 'M', 70),
(737, 145, 'Xám', 'S', 55),
(738, 145, 'Xám', 'M', 75),
(739, 145, 'Xám', 'L', 60),
(740, 145, 'Xanh', 'S', 50),
(741, 145, 'Xanh', 'M', 70),
(742, 145, 'Xanh', 'L', 55),
(743, 145, 'Đen', 'M', 65),
(744, 144, 'Đen', '36', 50),
(745, 144, 'Đen', '37', 70),
(746, 144, 'Đen', '38', 65),
(747, 144, 'Đen', '39', 50),
(748, 144, 'Nâu', '36', 45),
(749, 144, 'Nâu', '37', 65),
(750, 144, 'Nâu', '38', 60),
(751, 144, 'Nâu', '39', 45),
(752, 144, 'Hồng', '37', 40),
(753, 144, 'Hồng', '38', 45),
(754, 143, 'Đỏ sọc', 'Free', 80),
(755, 143, 'Xanh sọc', 'Free', 75),
(756, 143, 'Đen sọc', 'Free', 70),
(757, 142, 'Đỏ', 'S', 20),
(758, 142, 'Đỏ', 'M', 35),
(759, 142, 'Đỏ', 'L', 25),
(760, 142, 'Xanh đen', 'S', 15),
(761, 142, 'Xanh đen', 'M', 30),
(762, 142, 'Xanh đen', 'L', 20),
(763, 142, 'Vàng', 'M', 25),
(764, 141, 'Đen', 'S', 60),
(765, 141, 'Đen', 'M', 80),
(766, 141, 'Đen', 'L', 70),
(767, 141, 'Đen', 'XL', 50),
(768, 141, 'Xám', 'M', 75),
(769, 141, 'Xám', 'L', 65),
(770, 141, 'Xám', 'XL', 45),
(771, 141, 'Xanh', 'M', 70),
(772, 140, 'Đen', 'S', 80),
(773, 140, 'Đen', 'M', 100),
(774, 140, 'Đen', 'L', 70),
(775, 140, 'Xám', 'S', 75),
(776, 140, 'Xám', 'M', 95),
(777, 140, 'Xám', 'L', 65),
(778, 140, 'Hồng', 'M', 85),
(779, 139, 'Nâu', '36', 40),
(780, 139, 'Nâu', '37', 55),
(781, 139, 'Nâu', '38', 60),
(782, 139, 'Nâu', '39', 45),
(783, 139, 'Đen', '36', 35),
(784, 139, 'Đen', '37', 50),
(785, 139, 'Đen', '38', 55),
(786, 139, 'Đen', '39', 40),
(787, 138, 'Nâu', 'Free', 120),
(788, 138, 'Đen', 'Free', 130),
(789, 138, 'Xám', 'Free', 90),
(790, 137, 'Đen', 'S', 45),
(791, 137, 'Đen', 'M', 65),
(792, 137, 'Đen', 'L', 50),
(793, 137, 'Hoa', 'S', 40),
(794, 137, 'Hoa', 'M', 60),
(795, 137, 'Hoa', 'L', 45),
(796, 136, 'Sọc đen trắng', 'S', 50),
(797, 136, 'Sọc đen trắng', 'M', 70),
(798, 136, 'Sọc đen trắng', 'L', 60),
(799, 136, 'Sọc xanh trắng', 'M', 65),
(800, 136, 'Sọc xanh trắng', 'L', 55),
(801, 135, 'Đen', 'S', 45),
(802, 135, 'Đen', 'M', 65),
(803, 135, 'Đen', 'L', 50),
(804, 135, 'Xám', 'S', 40),
(805, 135, 'Xám', 'M', 60),
(806, 135, 'Xám', 'L', 45),
(807, 135, 'Nâu', 'M', 55),
(808, 134, 'Trắng', '38', 40),
(809, 134, 'Trắng', '39', 60),
(810, 134, 'Trắng', '40', 70),
(811, 134, 'Trắng', '41', 65),
(812, 134, 'Trắng', '42', 55),
(813, 134, 'Đen', '38', 35),
(814, 134, 'Đen', '39', 55),
(815, 134, 'Đen', '40', 65),
(816, 134, 'Đen', '41', 60),
(817, 134, 'Đen', '42', 50),
(818, 133, 'Đen', 'Free', 200),
(819, 133, 'Hồng', 'Free', 180),
(820, 133, 'Xanh', 'Free', 150),
(821, 133, 'Trong suốt', 'Free', 250),
(822, 132, 'Trắng', 'S', 35),
(823, 132, 'Trắng', 'M', 55),
(824, 132, 'Trắng', 'L', 40),
(825, 132, 'Xanh nhạt', 'S', 30),
(826, 132, 'Xanh nhạt', 'M', 50),
(827, 132, 'Xanh nhạt', 'L', 35),
(828, 131, 'Đen', 'S', 70),
(829, 131, 'Đen', 'M', 90),
(830, 131, 'Đen', 'L', 65),
(831, 131, 'Xám', 'S', 65),
(832, 131, 'Xám', 'M', 85),
(833, 131, 'Xám', 'L', 60),
(834, 131, 'Hồng', 'M', 75),
(835, 130, 'Trắng', 'S', 40),
(836, 130, 'Trắng', 'M', 60),
(837, 130, 'Trắng', 'L', 45),
(838, 130, 'Hồng', 'S', 35),
(839, 130, 'Hồng', 'M', 55),
(840, 130, 'Hồng', 'L', 40),
(841, 130, 'Xanh', 'M', 50),
(842, 129, 'Đen', '36', 50),
(843, 129, 'Đen', '37', 70),
(844, 129, 'Đen', '38', 65),
(845, 129, 'Đen', '39', 50),
(846, 129, 'Nâu', '36', 45),
(847, 129, 'Nâu', '37', 65),
(848, 129, 'Nâu', '38', 60),
(849, 129, 'Nâu', '39', 45),
(850, 129, 'Đỏ', '37', 40),
(851, 129, 'Đỏ', '38', 45),
(852, 128, 'Đen', 'S', 150),
(853, 128, 'Đen', 'M', 200),
(854, 128, 'Đen', 'L', 150),
(855, 128, 'Kaki', 'M', 180),
(856, 128, 'Kaki', 'L', 130),
(857, 128, 'Xanh rêu', 'M', 160),
(858, 127, 'Kẻ caro', 'S', 45),
(859, 127, 'Kẻ caro', 'M', 65),
(860, 127, 'Kẻ caro', 'L', 50),
(861, 127, 'Hoa nhí', 'S', 40),
(862, 127, 'Hoa nhí', 'M', 60),
(863, 127, 'Hoa nhí', 'L', 45),
(864, 126, 'Đen', 'S', 70),
(865, 126, 'Đen', 'M', 90),
(866, 126, 'Đen', 'L', 65),
(867, 126, 'Xanh đen', 'S', 65),
(868, 126, 'Xanh đen', 'M', 85),
(869, 126, 'Xanh đen', 'L', 60),
(870, 126, 'Xám', 'M', 80),
(871, 125, 'Trắng', 'S', 60),
(872, 125, 'Trắng', 'M', 80),
(873, 125, 'Trắng', 'L', 65),
(874, 125, 'Hồng', 'S', 55),
(875, 125, 'Hồng', 'M', 75),
(876, 125, 'Hồng', 'L', 60),
(877, 125, 'Xanh', 'M', 70),
(888, 124, 'Nâu', '36', 45),
(889, 124, 'Nâu', '37', 60),
(890, 124, 'Nâu', '38', 55),
(891, 124, 'Nâu', '39', 40),
(892, 124, 'Đen', '36', 40),
(893, 124, 'Đen', '37', 55),
(894, 124, 'Đen', '38', 50),
(895, 124, 'Đen', '39', 35),
(896, 124, 'Vàng', '37', 35),
(897, 124, 'Vàng', '38', 40),
(898, 123, 'Đen', 'Free', 100),
(899, 123, 'Trắng', 'Free', 90),
(900, 123, 'Xanh', 'Free', 80),
(901, 123, 'Đỏ', 'Free', 70),
(902, 122, 'Trắng', 'S', 35),
(903, 122, 'Trắng', 'M', 50),
(904, 122, 'Trắng', 'L', 40),
(905, 122, 'Kem', 'S', 30),
(906, 122, 'Kem', 'M', 45),
(907, 122, 'Kem', 'L', 35),
(908, 121, 'Đen', 'S', 50),
(909, 121, 'Đen', 'M', 70),
(910, 121, 'Đen', 'L', 60),
(911, 121, 'Đen', 'XL', 45),
(912, 121, 'Xám', 'M', 65),
(913, 121, 'Xám', 'L', 55),
(914, 121, 'Xám', 'XL', 40),
(915, 121, 'Xanh than', 'M', 60),
(916, 120, 'Đen', 'S', 60),
(917, 120, 'Đen', 'M', 80),
(918, 120, 'Đen', 'L', 55),
(919, 120, 'Trắng', 'S', 55),
(920, 120, 'Trắng', 'M', 75),
(921, 120, 'Trắng', 'L', 50),
(922, 120, 'Nâu', 'M', 70),
(923, 119, 'Đen trắng', '39', 30),
(924, 119, 'Đen trắng', '40', 45),
(925, 119, 'Đen trắng', '41', 50),
(926, 119, 'Đen trắng', '42', 40),
(927, 119, 'Đỏ đen', '40', 35),
(928, 119, 'Đỏ đen', '41', 40),
(929, 119, 'Đỏ đen', '42', 30),
(930, 118, 'Hồng', 'Free', 200),
(931, 118, 'Trắng', 'Free', 180),
(932, 118, 'Đen', 'Free', 150),
(933, 118, 'Xanh', 'Free', 120),
(934, 117, 'Đen', 'S', 45),
(935, 117, 'Đen', 'M', 65),
(936, 117, 'Đen', 'L', 50),
(937, 116, 'Đen', 'S', 80),
(938, 116, 'Đen', 'M', 100),
(939, 116, 'Đen', 'L', 80),
(940, 115, 'Xanh sáng', 'S', 40),
(941, 115, 'Xanh sáng', 'M', 60),
(942, 115, 'Xanh sáng', 'L', 55),
(943, 115, 'Xanh sáng', 'XL', 40),
(944, 115, 'Xanh đen', 'M', 55),
(945, 115, 'Xanh đen', 'L', 50),
(946, 115, 'Xanh đen', 'XL', 35),
(947, 114, 'Nâu', '39', 30),
(948, 114, 'Nâu', '40', 45),
(949, 114, 'Nâu', '41', 50),
(950, 114, 'Nâu', '42', 40),
(951, 114, 'Đen', '40', 40),
(952, 114, 'Đen', '41', 45),
(953, 114, 'Đen', '42', 35),
(954, 113, 'Đen', 'Free', 100),
(955, 113, 'Kaki', 'Free', 90),
(956, 113, 'Xanh rêu', 'Free', 80),
(957, 113, 'Cam đất', 'Free', 70),
(958, 112, 'Đen', 'S', 50),
(959, 112, 'Đen', 'M', 70),
(960, 112, 'Đen', 'L', 55),
(961, 112, 'Xanh navy', 'S', 45),
(962, 112, 'Xanh navy', 'M', 65),
(963, 112, 'Xanh navy', 'L', 50),
(964, 112, 'Đỏ burgundy', 'M', 60),
(965, 111, 'Kaki nhạt', 'S', 50),
(966, 111, 'Kaki nhạt', 'M', 70),
(967, 111, 'Kaki nhạt', 'L', 60),
(968, 111, 'Kaki nhạt', 'XL', 45),
(969, 111, 'Kaki đậm', 'M', 65),
(970, 111, 'Kaki đậm', 'L', 55),
(971, 111, 'Kaki đậm', 'XL', 40),
(972, 111, 'Xám', 'M', 60),
(973, 110, 'Xanh dương', 'S', 45),
(974, 110, 'Xanh dương', 'M', 65),
(975, 110, 'Xanh dương', 'L', 55),
(976, 110, 'Xanh dương', 'XL', 40),
(977, 110, 'Đen', 'M', 60),
(978, 110, 'Đen', 'L', 50),
(979, 110, 'Đen', 'XL', 35),
(980, 110, 'Đỏ', 'M', 45),
(981, 109, 'Trắng hồng', '36', 40),
(982, 109, 'Trắng hồng', '37', 55),
(983, 109, 'Trắng hồng', '38', 60),
(984, 109, 'Trắng hồng', '39', 45),
(985, 109, 'Xám hồng', '36', 35),
(986, 109, 'Xám hồng', '37', 50),
(987, 109, 'Xám hồng', '38', 55),
(988, 109, 'Xám hồng', '39', 40),
(989, 108, 'Đen', 'Free', 100),
(990, 108, 'Nâu', 'Free', 80),
(991, 108, 'Đỏ', 'Free', 60),
(992, 107, 'Hồng phấn', 'S', 35),
(993, 107, 'Hồng phấn', 'M', 50),
(994, 107, 'Hồng phấn', 'L', 35),
(995, 107, 'Xanh mint', 'S', 30),
(996, 107, 'Xanh mint', 'M', 45),
(997, 107, 'Xanh mint', 'L', 30),
(998, 107, 'Trắng', 'M', 40),
(999, 106, 'Be', 'S', 50),
(1000, 106, 'Be', 'M', 70),
(1001, 106, 'Be', 'L', 55),
(1002, 106, 'Be', 'XL', 40),
(1003, 106, 'Nâu', 'M', 60),
(1004, 106, 'Nâu', 'L', 50),
(1005, 106, 'Nâu', 'XL', 35),
(1006, 106, 'Xám', 'M', 55),
(1007, 105, 'Vintage vàng', 'S', 45),
(1008, 105, 'Vintage vàng', 'M', 65),
(1009, 105, 'Vintage vàng', 'L', 50),
(1010, 105, 'Xanh rêu', 'S', 40),
(1011, 105, 'Xanh rêu', 'M', 60),
(1012, 105, 'Xanh rêu', 'L', 45),
(1013, 71, 'Nâu', 'S', 80),
(1014, 71, 'Nâu', 'M', 120),
(1015, 71, 'Nâu', 'L', 100),
(1016, 71, 'Đen', 'M', 110),
(1017, 71, 'Đen', 'L', 90),
(1018, 104, 'Nâu', '39', 25),
(1019, 104, 'Nâu', '40', 35),
(1020, 104, 'Nâu', '41', 40),
(1021, 104, 'Nâu', '42', 30),
(1022, 104, 'Đen', '40', 30),
(1023, 104, 'Đen', '41', 35),
(1024, 104, 'Đen', '42', 25),
(1025, 103, 'Đen', 'Free', 80),
(1026, 103, 'Nâu', 'Free', 70),
(1027, 103, 'Nâu đen', 'Free', 60),
(1028, 102, 'Đen', 'S', 40),
(1029, 102, 'Đen', 'M', 60),
(1030, 102, 'Đen', 'L', 45),
(1031, 102, 'Đỏ', 'S', 30),
(1032, 102, 'Đỏ', 'M', 50),
(1033, 102, 'Đỏ', 'L', 35),
(1034, 102, 'Xanh dương', 'M', 55),
(1035, 101, 'Xanh đen', 'S', 60),
(1036, 101, 'Xanh đen', 'M', 80),
(1037, 101, 'Xanh đen', 'L', 55),
(1038, 101, 'Đen', 'S', 50),
(1039, 101, 'Đen', 'M', 70),
(1040, 101, 'Đen', 'L', 45),
(1041, 101, 'Xám', 'M', 65),
(1042, 100, 'Trắng', 'S', 50),
(1043, 100, 'Trắng', 'M', 70),
(1044, 100, 'Trắng', 'L', 60),
(1045, 100, 'Đen', 'M', 65),
(1046, 100, 'Đen', 'L', 55),
(1047, 100, 'Đen', 'XL', 40),
(1048, 100, 'Xám', 'M', 60),
(1049, 100, 'Xám', 'L', 50),
(1050, 66, 'Trắng', 'S', 45),
(1051, 66, 'Trắng', 'M', 70),
(1052, 66, 'Trắng', 'L', 50),
(1053, 66, 'Đen', 'S', 35),
(1054, 66, 'Đen', 'M', 60),
(1055, 66, 'Đen', 'L', 40),
(1056, 62, 'Xanh sáng', 'S', 100),
(1057, 62, 'Xanh sáng', 'M', 120),
(1058, 62, 'Xanh sáng', 'L', 80),
(1059, 62, 'Xanh đen', 'S', 90),
(1060, 62, 'Xanh đen', 'M', 110),
(1061, 62, 'Xanh đen', 'L', 75),
(1062, 55, 'Trắng', 'S', 100),
(1063, 55, 'Trắng', 'M', 150),
(1064, 55, 'Trắng', 'L', 120),
(1065, 55, 'Đen', 'S', 80),
(1066, 55, 'Đen', 'M', 200),
(1067, 55, 'Đen', 'L', 140),
(1068, 55, 'Xám', 'S', 60),
(1069, 55, 'Xám', 'M', 130),
(1070, 55, 'Xám', 'L', 90),
(1071, 57, 'Đen', 'S', 20),
(1072, 57, 'Đen', 'M', 35),
(1073, 57, 'Đen', 'L', 40),
(1074, 57, 'Đen', 'XL', 25),
(1075, 57, 'Nâu', 'M', 15),
(1076, 57, 'Nâu', 'L', 28),
(1077, 56, 'Trắng', 'M', 50),
(1078, 56, 'Trắng', 'L', 70),
(1079, 56, 'Trắng', 'XL', 40),
(1080, 56, 'Xanh nhạt', 'M', 30),
(1081, 56, 'Xanh nhạt', 'L', 45),
(1082, 59, 'Trắng', 'S', 40),
(1083, 59, 'Trắng', 'M', 60),
(1084, 59, 'Trắng', 'L', 55),
(1085, 59, 'Trắng', 'XL', 30),
(1086, 59, 'Đen', 'M', 70),
(1087, 59, 'Đen', 'L', 65),
(1088, 59, 'Đen', 'XL', 40),
(1089, 59, 'Xám', 'S', 35),
(1090, 59, 'Xám', 'M', 45),
(1091, 79, 'Trắng đỏ', '39', 15),
(1092, 79, 'Trắng đỏ', '40', 25),
(1093, 79, 'Trắng đỏ', '41', 30),
(1094, 79, 'Trắng đỏ', '42', 20),
(1095, 79, 'Đen vàng', '40', 20),
(1096, 79, 'Đen vàng', '41', 25),
(1097, 79, 'Đen vàng', '42', 15),
(1098, 99, 'Đen', '38', 100),
(1099, 99, 'Đen', '39', 120),
(1100, 99, 'Đen', '40', 110),
(1101, 99, 'Đen', '41', 90),
(1102, 99, 'Nâu', '38', 80),
(1103, 99, 'Nâu', '39', 100),
(1104, 99, 'Nâu', '40', 95),
(1105, 99, 'Nâu', '41', 75),
(1106, 99, 'Xanh', '39', 70),
(1107, 99, 'Xanh', '40', 80),
(1108, 60, 'Xanh đen', '28', 30),
(1109, 60, 'Xanh đen', '30', 45),
(1110, 60, 'Xanh đen', '32', 50),
(1111, 60, 'Xanh đen', '34', 35),
(1112, 60, 'Xanh rêu', '30', 25),
(1113, 60, 'Xanh rêu', '32', 40),
(1114, 60, 'Xanh rêu', '34', 30),
(1115, 61, 'Đen', 'S', 60),
(1116, 61, 'Đen', 'M', 80),
(1117, 61, 'Đen', 'L', 55),
(1118, 61, 'Be', 'S', 45),
(1119, 61, 'Be', 'M', 65),
(1120, 61, 'Be', 'L', 40),
(1121, 61, 'Xám', 'S', 40),
(1122, 61, 'Xám', 'M', 55),
(1123, 65, 'Hoa đỏ', 'S', 40),
(1124, 65, 'Hoa đỏ', 'M', 60),
(1125, 65, 'Hoa đỏ', 'L', 35),
(1126, 65, 'Hoa xanh', 'S', 30),
(1127, 65, 'Hoa xanh', 'M', 50),
(1128, 65, 'Hoa xanh', 'L', 30),
(1129, 74, 'Đỏ caro', 'Free', 80),
(1130, 74, 'Xanh caro', 'Free', 75),
(1131, 74, 'Vàng', 'Free', 60),
(1132, 98, 'Xám cam', '40', 20),
(1133, 98, 'Xám cam', '41', 30),
(1134, 98, 'Xám cam', '42', 35),
(1135, 98, 'Xám cam', '43', 25),
(1136, 98, 'Xanh đen', '40', 15),
(1137, 98, 'Xanh đen', '41', 25),
(1138, 98, 'Xanh đen', '42', 30),
(1139, 98, 'Xanh đen', '43', 20);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `promotions`
--

CREATE TABLE `promotions` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_amount` int(11) DEFAULT 0,
  `discount_percent` int(11) DEFAULT 0,
  `min_order` int(11) DEFAULT 0,
  `usage_limit` int(11) DEFAULT 100,
  `used_count` int(11) DEFAULT 0,
  `expiry_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_freeship` tinyint(4) DEFAULT 0,
  `start_date` datetime DEFAULT current_timestamp(),
  `end_date` datetime DEFAULT '2030-12-31 23:59:59'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `promotions`
--

INSERT INTO `promotions` (`id`, `code`, `discount_amount`, `discount_percent`, `min_order`, `usage_limit`, `used_count`, `expiry_date`, `created_at`, `is_freeship`, `start_date`, `end_date`) VALUES
(1, 'TODAY05', 50000, 0, 200000, 100, 2, '2026-07-09', '2026-06-21 07:03:23', 0, '2026-06-21 14:55:56', '2030-12-31 23:59:59');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` tinyint(1) DEFAULT 0,
  `avatar` varchar(255) DEFAULT 'default.png',
  `role` tinyint(1) DEFAULT 1,
  `otp_code` varchar(6) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL,
  `is_2fa_enabled` tinyint(1) DEFAULT 1,
  `reset_otp` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `name`, `phone`, `address`, `dob`, `gender`, `avatar`, `role`, `otp_code`, `otp_expiry`, `is_2fa_enabled`, `reset_otp`) VALUES
(1, 'admin', '0192023a7bbd73250516f069df18b500', 'admin@mfshop.com', 'Quản Trị Viên', '56566666', 'gftfygyhyhghgg', '2026-06-16', 0, '1780563732_Thêm tiêu đề phụ (1).png', 0, '176137', '2026-06-04 09:22:05', 0, NULL),
(2, 'tuan', 'ef775988943825d2871e1cfa75473ec0', 'anhtuanprovip05@gmail.com', 'Tuấn', '979797977979', 'qn', '2006-11-11', 0, 'uploads/1780639576_z7763994928548_89c0f8cf54935a1bf8080b29f430c54d.jpg', 1, NULL, NULL, 0, NULL),
(3, 'txs', 'ef775988943825d2871e1cfa75473ec0', 'anhtuan5.pcvn@gmail.com', 'Áo khoác', '0862060347', 'higyuy', '2026-06-19', 0, 'default.png', 1, NULL, NULL, 1, NULL),
(4, 'nguyenvanA', 'e10adc3949ba59abbe56e057f20f883e', 'nguyenvana@email.com', 'Nguyễn Văn A', '0912345678', 'Số 1, Lê Lợi, Q1, TP.HCM', '1990-01-15', 1, 'default.png', 1, NULL, NULL, 1, NULL),
(5, 'tranthib', 'e10adc3949ba59abbe56e057f20f883e', 'tranthib@email.com', 'Trần Thị B', '0987654321', 'Số 2, Nguyễn Huệ, Q1, TP.HCM', '1992-05-20', 2, 'default.png', 1, NULL, NULL, 1, NULL),
(6, 'lehoangc', 'e10adc3949ba59abbe56e057f20f883e', 'lehoangc@email.com', 'Lê Hoàng C', '0978123456', 'Số 3, Hai Bà Trưng, Q3, TP.HCM', '1988-11-30', 1, 'default.png', 1, NULL, NULL, 1, NULL),
(7, 'phamthid', 'e10adc3949ba59abbe56e057f20f883e', 'phamthid@email.com', 'Phạm Thị D', '0965432187', 'Số 4, Võ Văn Tần, Q3, TP.HCM', '1995-07-12', 2, 'default.png', 1, NULL, NULL, 1, NULL),
(8, 'hoange', 'e10adc3949ba59abbe56e057f20f883e', 'hoange@email.com', 'Hoàng E', '0945678123', 'Số 5, Nguyễn Đình Chiểu, Q1, TP.HCM', '1993-09-25', 1, 'default.png', 1, NULL, NULL, 1, NULL),
(9, 'vuthif', 'e10adc3949ba59abbe56e057f20f883e', 'vuthif@email.com', 'Vũ Thị F', '0934567890', 'Số 6, Cách Mạng Tháng 8, Q10, TP.HCM', '1991-03-18', 2, 'default.png', 1, NULL, NULL, 1, NULL),
(10, 'dohuungg', 'e10adc3949ba59abbe56e057f20f883e', 'dohuungg@email.com', 'Đỗ Hùng G', '0923456789', 'Số 7, Trần Hưng Đạo, Q5, TP.HCM', '1989-12-05', 1, 'default.png', 1, NULL, NULL, 1, NULL),
(11, 'ngothih', 'e10adc3949ba59abbe56e057f20f883e', 'ngothih@email.com', 'Ngô Thị H', '0912987654', 'Số 8, Lý Thường Kiệt, Q11, TP.HCM', '1994-06-22', 2, 'default.png', 1, NULL, NULL, 1, NULL),
(12, 'buiminhk', 'e10adc3949ba59abbe56e057f20f883e', 'buiminhk@email.com', 'Bùi Minh K', '0909876543', 'Số 9, Phạm Ngũ Lão, Q1, TP.HCM', '1996-08-14', 1, 'default.png', 1, NULL, NULL, 1, NULL),
(13, 'dangthil', 'e10adc3949ba59abbe56e057f20f883e', 'dangthil@email.com', 'Đặng Thị L', '0976543210', 'Số 10, Nguyễn Trãi, Q5, TP.HCM', '1997-02-28', 2, 'default.png', 1, NULL, NULL, 1, NULL),
(14, 'tung', '96e79218965eb72c92a549dd5a330112', 'tungthmq21@gmail.com', 'tung', '000001', 'tt', '2000-09-21', 0, 'default.png', 1, NULL, NULL, 1, NULL),
(15, 'txs99', 'ef775988943825d2871e1cfa75473ec0', 'ciuctx@gmail.com', 'TXS', '5555', 'sanghouse', '2026-06-24', 0, 'default.png', 1, NULL, NULL, 1, '525660');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `campaigns`
--
ALTER TABLE `campaigns`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Chỉ mục cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `campaigns`
--
ALTER TABLE `campaigns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT cho bảng `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=155;

--
-- AUTO_INCREMENT cho bảng `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=629;

--
-- AUTO_INCREMENT cho bảng `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1140;

--
-- AUTO_INCREMENT cho bảng `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carts_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Các ràng buộc cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
