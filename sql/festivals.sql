-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- 생성 시간: 26-06-03 09:43
-- 서버 버전: 10.4.32-MariaDB
-- PHP 버전: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 데이터베이스: `2024contest`
--

-- --------------------------------------------------------

--
-- 테이블 구조 `festivals`
--

CREATE TABLE `festivals` (
  `idx` int(11) NOT NULL,
  `name` varchar(300) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `address` varchar(400) NOT NULL,
  `image` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 테이블의 덤프 데이터 `festivals`
--

INSERT INTO `festivals` (`idx`, `name`, `start_date`, `end_date`, `address`, `image`) VALUES
(1, '황매산 억새축제', '2024-10-15', '2024-10-30', '경상남도 합천군 가회면 황매산공원길 4', '/assets/축제 안내/1.jpg'),
(2, '거제 섬꽃축제', '2024-10-26', '2024-11-03', '경상남도 거제시 거제면 거제남서로 3577', '/assets/축제 안내/2.jpg'),
(3, '여수여자만갯벌노을체험행사', '2024-09-01', '2024-09-01', '전라남도 여수시 소라면 서부로 785-24', '/assets/축제 안내/3.jpg'),
(4, '전주페스타 2024', '2024-10-03', '2024-10-27', '전라북도 전주시 덕진구 기린대로 451', '/assets/축제 안내/4.jpg'),
(5, '거제맥주축제', '2024-08-01', '2024-08-31', '경상남도 거제시 장승로 146-12', '/assets/축제 안내/5.jpg'),
(6, '제28회 부산바다축제', '2024-08-01', '2024-08-06', '부산광역시 해운대구 중동2로 11', '/assets/축제 안내/6.jpg'),
(7, '꺅두기 축제', '2026-06-01', '2026-06-20', '이태원 서울디지텍고등학교', '/assets/festivals/꺅두기.png');

--
-- 덤프된 테이블의 인덱스
--

--
-- 테이블의 인덱스 `festivals`
--
ALTER TABLE `festivals`
  ADD PRIMARY KEY (`idx`);

--
-- 덤프된 테이블의 AUTO_INCREMENT
--

--
-- 테이블의 AUTO_INCREMENT `festivals`
--
ALTER TABLE `festivals`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
