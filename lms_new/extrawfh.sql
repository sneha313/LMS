-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 17, 2017 at 08:53 AM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lms`
--

-- --------------------------------------------------------

--
-- Table structure for table `extrawfh`
--

CREATE TABLE `extrawfh` (
  `id` int(10) NOT NULL,
  `createdAt` datetime NOT NULL,
  `createdBy` varchar(50) NOT NULL,
  `updatedAt` datetime DEFAULT NULL,
  `updatedBy` varchar(50) NOT NULL,
  `eid` varchar(10) NOT NULL,
  `tid` varchar(10) NOT NULL,
  `date` date NOT NULL,
  `wfhHrs` time NOT NULL,
  `reason` varchar(500) NOT NULL,
  `comments` varchar(500) NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `extrawfh`
--

INSERT INTO `extrawfh` (`id`, `createdAt`, `createdBy`, `updatedAt`, `updatedBy`, `eid`, `tid`, `date`, `wfhHrs`, `reason`, `comments`, `status`) VALUES
(115, '2017-07-04 19:42:39', 'Giridhar Naga', '0000-00-00 00:00:00', '', '327856', '5958ff57c2', '2017-08-02', '00:00:04', 'testtusd', '', 'Approved'),
(116, '2017-07-04 19:44:04', 'Giridhar Naga', '0000-00-00 00:00:00', '', '327856', '5958ffaca0', '2017-08-03', '00:00:05', 'tesasasasaa', '', 'Approved'),
(117, '2017-07-04 19:50:04', 'Giridhar Naga', '0000-00-00 00:00:00', '', '327856', '59590114c5', '2017-08-16', '00:00:04', 'tessusyus', '', 'Approved'),
(118, '2017-07-04 19:59:09', 'Giridhar Naga', '0000-00-00 00:00:00', '', '327856', '5959033532', '2017-09-01', '00:00:06', 'testing', '', 'Approved'),
(119, '2017-07-04 20:11:23', 'Giridhar Naga', '0000-00-00 00:00:00', '', '327856', '59590613b1', '2017-07-30', '00:00:06', 'testing', '', 'Approved'),
(120, '2017-07-04 20:12:08', 'Giridhar Naga', '0000-00-00 00:00:00', '', '327856', '5959064009', '2017-07-25', '00:00:05', 'testing useful', '', 'Approved'),
(121, '2017-07-04 21:29:36', 'Giridhar Naga', '0000-00-00 00:00:00', '', '327856', '59591868ef', '2017-11-08', '00:00:04', 'useful', '', 'Approved'),
(122, '2017-07-04 21:31:49', 'Giridhar Naga', '0000-00-00 00:00:00', '', '327856', '595918edc7', '2017-11-29', '00:00:04', 'testing must', '', 'Approved'),
(123, '2017-07-04 21:37:21', 'Giridhar Naga', '0000-00-00 00:00:00', '', '327856', '59591a3906', '2017-09-14', '00:00:04', 'tseddddfjfjfd', '', 'Approved'),
(130, '2017-07-04 15:20:51', 'Giridhar Naga', '0000-00-00 00:00:00', '', '327856', '595a137b83', '2017-05-10', '00:00:01', 'tetsstst', '', 'Approved'),
(131, '2017-07-04 15:28:55', 'Giridhar Naga', '0000-00-00 00:00:00', '', '327856', '595a155fe9', '2017-07-18', '00:00:06', 'testtsttyutututttstt welcome', '', 'Approved'),
(132, '2017-07-04 15:30:32', 'Giridhar Naga', '2017-07-04 16:10:33', 'gnaga', '327856', '595a15c001', '2017-03-02', '00:00:05', 'testing useful', '', 'Deleted'),
(133, '2017-07-04 15:36:16', 'Giridhar Naga', '0000-00-00 00:00:00', '', '327856', '595a17187a', '2017-04-04', '00:00:02', 'tetuuii useful', '', 'Approved'),
(134, '2017-07-04 15:37:36', 'Giridhar Naga', '0000-00-00 00:00:00', '', '327856', '595a1768b6', '2017-10-24', '00:00:04', 'tekkkkkjkj welcome', '', 'Approved'),
(135, '2017-07-04 15:40:11', 'Giridhar Naga', '0000-00-00 00:00:00', '', '327856', '595a180359', '2017-09-12', '00:00:03', 'testing welcome useful ok', '', 'Approved'),
(136, '2017-07-04 16:12:26', 'Sheela Naveen', '2017-07-03 19:59:09', 'gnaga', '327856', '595a1f927e', '2017-07-27', '00:00:04', 'testing', '', 'Deleted'),
(137, '2017-07-04 17:21:20', 'Giridhar Naga', '2017-07-03 20:12:30', 'gnaga', '327856', '595a2fb515', '2017-07-19', '00:00:09', 'testing', '', 'Approved'),
(138, '2017-07-04 17:46:46', 'Giridhar Naga', '0000-00-00 00:00:00', '', '327856', '595a35ae48', '2017-07-15', '00:00:05', 'testing trial', '', 'Approved'),
(143, '2017-07-04 18:33:36', 'Giridhar Naga', '0000-00-00 00:00:00', '', '326405', '595a42d2f0', '2017-07-02', '00:00:04', 'ssss', '', 'Approved'),
(144, '2017-07-04 18:45:52', 'Giridhar Naga', '0000-00-00 00:00:00', '', '326405', '595a4380ad', '2017-07-01', '00:00:04', 'sssssssssssssss', '', 'Approved'),
(145, '2017-07-04 18:51:52', 'Giridhar Naga', '2017-07-03 15:30:13', 'gnaga', '326763', '595a44f0c2', '2017-07-02', '00:00:03', 'zzz', '', 'Approved'),
(150, '2017-07-04 15:15:46', 'Giridhar Naga', '0000-00-00 00:00:00', 'Giridhar Naga', '326763', '595b63cacd', '2017-07-04', '00:00:02', 'testing for calender for manager', '', 'Approved'),
(151, '2017-07-04 18:44:45', 'Giridhar Naga', '2017-07-04 15:15:15', 'gnaga', '327856', '595b94c59f', '2017-03-09', '00:00:08', 'testing ', '', 'Deleted'),
(152, '2017-07-04 18:46:58', 'Giridhar Naga', '0000-00-00 00:00:00', '', '327856', '595b954ae3', '2017-07-09', '00:00:10', 'mmknjk', '', 'Approved'),
(153, '2017-07-04 18:58:22', 'Giridhar Naga', '2017-07-05 08:25:38', 'gnaga', '327856', '595b97f672', '2017-07-10', '00:00:03', 'qwer', '', 'Approved'),
(154, '2017-07-04 19:20:53', 'Giridhar Naga', '0000-00-00 00:00:00', 'Giridhar Naga', '327856', '595b9d3d18', '2017-07-04', '01:45:00', 'testing for date', '', 'Pending'),
(155, '2017-07-04 19:43:36', 'Giridhar Naga', '0000-00-00 00:00:00', '', '325177', '595ba290ea', '2017-07-05', '00:00:02', 'testu', '', 'Deleted'),
(156, '2017-07-05 10:17:55', 'Giridhar Naga', '0000-00-00 00:00:00', '', '326405', '595c6f7b91', '2017-07-28', '00:00:05', 'ok', '', 'Approved'),
(157, '2017-07-05 21:37:02', 'Sneha Kumari', '0000-00-00 00:00:00', '', '327856', '595d0ea67a', '2017-07-08', '01:45:00', 'testing for alert approval', '', 'Approved'),
(158, '2017-07-05 21:55:33', 'Anil Kumar Thatavarthi', '2017-07-08 20:40:32', 'gnaga', '325177', '595d12fd0a', '2017-09-12', '00:00:03', 'testing ok checking', '', 'Approved'),
(159, '2017-07-07 17:02:30', 'Sneha Kumari', '2017-07-08 20:42:02', 'gnaga', '327856', '595f714e13', '2017-08-08', '00:00:04', 'not well', '', 'Approved'),
(160, '2017-07-07 17:09:44', 'Sneha Kumari', '0000-00-00 00:00:00', '', '327856', '595f73005e', '2017-11-15', '00:00:10', 'going for tour', 'not approved 123', 'Cancelled'),
(161, '2017-07-10 11:32:26', 'Sneha Kumari', '2017-07-10 19:58:10', 'gnaga', '327856', '596318728b', '2017-02-06', '00:00:02', 'testing for whole flow', '', 'Approved'),
(162, '2017-07-10 12:10:33', 'Sneha Kumari', '2017-07-10 19:56:02', 'gnaga', '327856', '59632161a4', '2017-01-09', '01:30:00', 'testing for value', '', 'Approved'),
(163, '2017-07-10 12:27:42', 'Sneha Kumari', '0000-00-00 00:00:00', '', '327856', '59632566c2', '2017-10-25', '00:00:04', 'cold', '', 'Approved'),
(164, '2017-07-10 17:33:01', 'Sneha Kumari', '0000-00-00 00:00:00', '', '327856', '59636cf5d5', '2017-12-26', '00:00:02', 'checking for whole flow', 'not required', 'Cancelled'),
(165, '2017-07-10 17:34:59', 'Sneha Kumari', '0000-00-00 00:00:00', '', '327856', '59636d6bc9', '2017-01-11', '00:00:02', 'checking for hr section', '', 'Approved'),
(166, '2017-07-10 18:23:51', 'Sneha Kumari', '0000-00-00 00:00:00', '', '327856', '596378dfe5', '2017-03-14', '02:30:00', 'for not approve testing', 'ok', 'Cancelled'),
(167, '2017-07-10 18:27:57', 'Giridhar Naga', '0000-00-00 00:00:00', '', '323856', '596379d524', '2017-07-19', '00:00:01', 'sdfsf', '', 'Approved'),
(168, '2017-07-10 18:33:50', 'Anil Kumar Thatavarthi', '2017-07-10 15:04:54', 'athatav', '325177', '59637b360a', '2017-07-11', '00:00:04', 'dffdfsdf', '', 'Deleted'),
(169, '2017-07-10 18:36:15', 'Anil Kumar Thatavarthi', '2017-07-10 15:08:19', 'snaveen', '325177', '59637bc74a', '2017-07-17', '00:00:02', 'fdsfsd', '', 'Approved'),
(170, '2017-07-10 22:22:01', 'Sneha Kumari', '0000-00-00 00:00:00', '', '327856', '5963b0b11c', '2017-12-01', '00:00:02', 'test for manager', '', 'Approved'),
(171, '2017-07-11 10:17:51', 'Giridhar Naga', '2017-07-11 06:48:54', 'gnaga', '323856', '596458770e', '2017-07-14', '00:00:11', 'testing', '', 'Approved'),
(172, '2017-07-12 18:50:54', 'Giridhar Naga', '0000-00-00 00:00:00', '', '323856', '59662236a3', '2017-06-12', '00:00:02', 'testing for attendance', 'testing for hr not approval form loading', 'Cancelled'),
(173, '2017-07-12 18:52:03', 'Sneha Kumari', '0000-00-00 00:00:00', '', '327856', '5966227bad', '2017-06-13', '01:45:00', 'test', '', 'Approved'),
(174, '2017-07-13 10:30:41', 'Giridhar Naga', '2017-07-13 12:34:44', 'gnaga', '323856', '5966fe794b', '2017-07-06', '00:00:00', 'ok', '', 'Pending'),
(175, '2017-07-13 12:55:17', 'Sneha Kumari', '2017-07-14 09:09:13', 'snaveen', '327856', '5967205dda', '0000-00-00', '02:00:00', 'testing for noh', '', 'Approved'),
(176, '2017-07-13 15:37:47', 'Giridhar Naga', '0000-00-00 00:00:00', '', '323856', '5967467394', '2017-07-12', '02:15:00', 'testing for hr', '', 'Approved'),
(177, '2017-07-13 15:38:15', 'Giridhar Naga', '0000-00-00 00:00:00', '', '323856', '5967468f43', '2017-07-11', '02:15:00', 'test', 'not ok', 'Cancelled'),
(178, '2017-07-13 15:39:26', 'Giridhar Naga', '0000-00-00 00:00:00', '', '323856', '596746d61a', '2017-07-01', '01:30:00', 'ccc', 'not approved for testing', 'Cancelled'),
(179, '2017-07-13 15:45:14', 'Giridhar Naga', '0000-00-00 00:00:00', '', '323856', '596748323d', '2017-07-04', '01:30:00', 'testing for date error', 'page is not loading', 'Cancelled'),
(180, '2017-07-13 16:48:49', 'Giridhar Naga', '2017-07-13 13:19:42', 'gnaga', '327856', '5967571921', '2017-01-01', '00:00:00', 'ok', '', 'Pending'),
(181, '2017-07-13 16:52:31', 'Sheela Naveen', '0000-00-00 00:00:00', '', '325020', '596757f778', '2017-07-05', '02:15:00', 'testing for hr calender', 'not ok', 'Cancelled'),
(182, '2017-07-13 16:53:22', 'Sheela Naveen', '0000-00-00 00:00:00', '', '326405', '5967582a33', '2017-07-06', '03:15:00', 'testing for hr thing', '', 'Approved'),
(183, '2017-07-13 23:27:25', 'Sneha Kumari', '0000-00-00 00:00:00', '', '327856', '5967b48597', '2017-06-14', '01:30:00', 'test for hoyur\r\n', '', 'Deleted'),
(184, '2017-07-14 11:33:09', '', '0000-00-00 00:00:00', '', '', '59685e9d4a', '0000-00-00', '00:00:00', '', '', 'Pending'),
(185, '2017-07-14 11:47:24', 'Sneha Kumari', '0000-00-00 00:00:00', '', '327856', '596861f46b', '2017-07-05', '00:00:00', 'ok', '', 'Pending'),
(186, '2017-07-14 11:49:00', 'Sneha Kumari', '0000-00-00 00:00:00', '', '327856', '5968625429', '2017-01-02', '01:30:00', 'for testing approval WFH', '', 'Approved'),
(187, '2017-07-14 12:50:34', 'Sneha Kumari', '0000-00-00 00:00:00', '', '327856', '596870c283', '2017-02-15', '01:45:00', 'testing ', '', 'Approved'),
(188, '2017-07-14 12:52:08', 'Sneha Kumari', '0000-00-00 00:00:00', '', '327856', '5968712020', '2017-03-23', '01:30:00', 'testing not napprove', 'not approveds', 'Cancelled'),
(189, '2017-07-14 12:55:07', 'Giridhar Naga', '0000-00-00 00:00:00', '', '323856', '596871d3d2', '2017-07-10', '02:15:00', 'ccccccccc', '', 'Approved'),
(190, '2017-07-14 13:02:04', 'Sneha Kumari', '0000-00-00 00:00:00', '', '327856', '5968737425', '2017-01-10', '02:00:00', 'testing for approval hr', '', 'Approved'),
(191, '2017-07-14 13:43:24', 'Sneha Kumari', '0000-00-00 00:00:00', '', '327856', '59687d2472', '2017-03-16', '01:45:00', 'testing for not approval alert\r\n', 'ok try', 'Cancelled'),
(192, '2017-07-14 16:37:50', 'Sneha Kumari', '0000-00-00 00:00:00', '', '327856', '5968a60654', '2017-07-06', '01:45:00', 'test', '', 'Pending'),
(193, '2017-07-14 16:39:50', 'Sneha Kumari', '0000-00-00 00:00:00', '', '327856', '5968a67eab', '2017-04-05', '01:30:00', 'testing for data', '', 'Pending'),
(194, '2017-07-14 16:41:16', 'Sneha Kumari', '0000-00-00 00:00:00', '', '327856', '5968a6d472', '2017-02-08', '01:30:00', 'date testing', '', 'Pending'),
(195, '2017-07-14 16:42:03', 'Sneha Kumari', '0000-00-00 00:00:00', '', '327856', '5968a70386', '2017-01-25', '01:00:00', 'test for date thing', '', 'Pending'),
(196, '2017-07-14 16:43:26', 'Sneha Kumari', '0000-00-00 00:00:00', '', '327856', '5968a75645', '2017-01-17', '02:15:00', 'testing purpose', '', 'Pending'),
(197, '2017-07-17 11:29:07', 'Giridhar Naga', '0000-00-00 00:00:00', '', '323856', '596c521305', '2017-07-05', '00:00:00', 'ok', '', 'Pending');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `extrawfh`
--
ALTER TABLE `extrawfh`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `extrawfh`
--
ALTER TABLE `extrawfh`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=198;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
