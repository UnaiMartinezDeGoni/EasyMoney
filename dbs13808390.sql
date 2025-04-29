-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-04-2025 a las 14:48:23
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `dbs13808390`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `token`, `expires_at`) VALUES
(1, 1, '37a92b4b68811f6d', '2025-02-17 00:18:54'),
(2, 1, '78620d75baf514f8', '2025-02-18 11:10:24'),
(3, 2, 'b1bfb0e96fe06a3c', '2025-02-17 09:19:40'),
(4, 2, '2ebeb74dbc882b86', '2025-02-20 09:19:40'),
(5, 1, '3af2f3d5c0a0abf9', '2025-02-21 11:10:24'),
(6, 2, 'e7113ea16f59ed8e', '2025-02-23 13:24:55'),
(7, 4, 'adb71f1a32d72e19', '2025-02-23 14:31:43'),
(8, 5, 'bbd4fbea54160c15', '2025-03-02 18:45:29'),
(9, 6, '6b494b62c4979fdf', '2025-04-08 11:29:51'),
(10, 6, 'd3fd36462046d26d', '2025-04-08 11:33:05'),
(11, 6, 'cf6aa4f45b8bb5fd', '2025-04-11 11:33:05'),
(12, 5, '60a8d21ae5779b89', '2025-04-15 17:53:44'),
(13, 5, '9c4e143606258192', '2025-04-22 10:48:56'),
(14, 5, '5806f1a9e56d5cfb', '2025-04-22 10:51:22'),
(15, 5, '02f49a6f4637e0b4', '2025-04-22 11:09:00'),
(16, 5, '39e14afdf487d872', '2025-04-22 11:13:36'),
(17, 5, '29a6c7aac8e7eaa4', '2025-04-22 11:15:47'),
(18, 5, '836398d4b7b121e3', '2025-04-22 11:38:11'),
(19, 5, 'c01b759e2220e16e', '2025-04-22 11:42:28'),
(20, 5, '788ec5ec06980e92', '2025-04-22 11:44:19'),
(21, 5, '8cd6c4c02865f776', '2025-04-22 11:44:58'),
(22, 5, '2c91ff260d8b550c', '2025-04-22 11:45:46'),
(23, 5, '82129ddaec5146c5', '2025-04-22 11:47:38'),
(24, 5, '870205eadbf4cf09', '2025-04-22 11:52:01'),
(25, 5, '402d5c5a31b68d40', '2025-04-22 11:52:47'),
(26, 5, 'a1b8a6e5fe44f67f', '2025-04-22 12:50:40'),
(27, 5, '7a4e8a2f2500db3c', '2025-04-22 12:53:14'),
(28, 5, '86ee484498dcdd88', '2025-04-22 12:56:56'),
(29, 5, '56d7eb4d7abb71c2', '2025-04-22 13:01:48'),
(30, 5, '887dd30a5d62db73', '2025-04-22 13:13:13'),
(31, 5, '35cf6f19ce21bf45', '2025-04-22 13:17:24'),
(32, 5, '3e01b308cce9481b', '2025-04-22 13:19:32'),
(33, 5, '3327b4b9f0a347c1', '2025-04-22 13:24:47'),
(34, 5, 'd54df7bad4425d24', '2025-04-22 13:30:16'),
(35, 5, 'bbb5aba85569b564', '2025-04-22 13:32:26'),
(36, 5, 'd1b336098ba0560b', '2025-04-22 13:35:30'),
(37, 5, '6c0699c3576bf7f3', '2025-04-22 18:17:08'),
(38, 5, 'c601fa0b8bc85b06', '2025-04-25 18:17:08'),
(39, 7, '519441af67be6642', '2025-04-26 13:05:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `streamers`
--

CREATE TABLE `streamers` (
  `id` varchar(20) NOT NULL,
  `login` varchar(50) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `type` varchar(20) DEFAULT NULL,
  `broadcaster_type` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `profile_image_url` varchar(255) DEFAULT NULL,
  `offline_image_url` varchar(255) DEFAULT NULL,
  `view_count` int(11) DEFAULT 0,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `streamers`
--

INSERT INTO `streamers` (`id`, `login`, `display_name`, `type`, `broadcaster_type`, `description`, `profile_image_url`, `offline_image_url`, `view_count`, `created_at`) VALUES
('1', 'elsmurfoz', 'elsmurfoz', '', '', '', 'https://static-cdn.jtvnw.net/user-default-pictures-uv/215b7342-def9-11e9-9a66-784f43822e80-profile_image-300x300.png', '', 0, '2007-05-22 10:37:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `top_games`
--

CREATE TABLE `top_games` (
  `game_id` varchar(50) NOT NULL,
  `game_name` varchar(255) NOT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `top_games`
--

INSERT INTO `top_games` (`game_id`, `game_name`, `updated_at`) VALUES
('1984929743', 'The Elder Scrolls IV: Oblivion Remastered', '2025-04-23 13:24:04'),
('21779', 'League of Legends', '2025-04-23 10:33:23'),
('32399', 'Counter-Strike', '2025-04-23 10:33:23'),
('32982', 'Grand Theft Auto V', '2025-04-23 10:33:23'),
('509658', 'Just Chatting', '2025-04-23 10:33:23'),
('516575', 'VALORANT', '2025-04-23 10:33:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `top_videos`
--

CREATE TABLE `top_videos` (
  `id` int(11) NOT NULL,
  `game_id` varchar(50) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `total_videos` int(11) NOT NULL,
  `total_views` bigint(20) NOT NULL,
  `most_viewed_title` varchar(255) NOT NULL,
  `most_viewed_views` int(11) NOT NULL,
  `most_viewed_duration` varchar(50) NOT NULL,
  `most_viewed_created_at` datetime NOT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `top_videos`
--

INSERT INTO `top_videos` (`id`, `game_id`, `user_name`, `total_videos`, `total_views`, `most_viewed_title`, `most_viewed_views`, `most_viewed_duration`, `most_viewed_created_at`, `updated_at`) VALUES
(1, '509658', 'KaiCenat', 36, 414852641, '🦃 MAFIATHON 2 🦃 KAI X KEVIN HART X DRUSKI 🦃 DAY 27 🦃 20% OF REVENUE GOING TO SCHOOL IN NIGERIA 🦃 ALL MONTH 🦃 CLICK HERE 🦃 !Subathon', 24867746, '22h5m32s', '2024-11-28 02:06:07', '2025-04-23 10:33:24'),
(2, '509658', 'juansguarnizo', 1, 5731012, 'Tráiler del canal de juansguarnizo', 5731012, '1m0s', '2020-11-05 20:27:21', '2025-04-23 10:33:24'),
(3, '509658', 'Lacy', 1, 5318180, '🎄 FAZE X JYNXZI 24 HOUR STREAM 🎄 DAY 3/7 CHRISTMAS MARATHON 🎄 | !subgoal !po !yt !xmas', 5318180, '47h59m57s', '2024-12-20 20:07:42', '2025-04-23 10:33:24'),
(4, '509658', 'Ninja', 1, 4468041, 'Starting soon!', 4468041, '9h53m18s', '2020-09-10 18:46:13', '2025-04-23 10:33:24'),
(5, '509658', 'elxokas', 1, 4260881, 'TRAILER DE JESUCRISTO', 4260881, '59s', '2020-05-15 17:27:17', '2025-04-23 10:33:24'),
(6, '32399', 'ESLCS', 26, 108484321, 'LIVE: Team Spirit vs Heroic - IEM Rio 2022 - Champions Stage Quaterfinal', 5925091, '9h30m2s', '2022-11-11 15:40:22', '2025-04-23 10:33:24'),
(7, '32399', 'PGL', 12, 47021605, 'PGL Major Antwerp - Grand Final', 5242829, '6h1m13s', '2022-05-22 15:01:13', '2025-04-23 10:33:24'),
(8, '32399', 'shroud', 1, 4102730, 'i knew the whole time. here we go deep dive. !announcement', 4102730, '13h54m20s', '2023-03-22 16:52:37', '2025-04-23 10:33:24'),
(9, '32399', 'ESLCSb', 1, 3800409, 'LIVE: MOUZ vs Team Vitality - IEM Rio 2022 - Legends Stage Round #3', 3800409, '11h21m8s', '2022-11-06 12:55:51', '2025-04-23 10:33:24'),
(10, '21779', 'Riot Games', 26, 124973788, 'WORLDS 22 FINALS COUNTDOWN', 11620692, '9h25m12s', '2022-11-05 21:00:23', '2025-04-23 10:33:24'),
(11, '21779', 'Riot_esports_Korea', 13, 58211531, 'T1 vs DRX | 2022 월드 챔피언십 | FINALS', 7863050, '7h38m46s', '2022-11-05 22:50:16', '2025-04-23 10:33:24'),
(12, '21779', 'LEC', 1, 3420969, 'Warm Up Finals Preshow | LEC Summer (2020) |League of Legends', 3420969, '11h23m24s', '2020-09-06 08:00:39', '2025-04-23 10:33:24'),
(13, '516575', 'Ninja', 3, 7705401, 'V a l o r a n t Grind begins | Among us tonight at 8 CENTRAL with a SOLID crew! ', 4549589, '15h15m21s', '2020-09-11 18:52:09', '2025-04-23 10:33:24'),
(14, '516575', 'shroud', 36, 67841024, 'SEN GAME!! | @shroud FOLLOW ME!!', 2954968, '15h36m18s', '2021-09-12 15:57:30', '2025-04-23 10:33:24'),
(15, '516575', '加藤純一うん〇ちゃん', 1, 2027736, '18時よりﾑﾗｹﾞVSリドル戦を応援する男', 2027736, '14h8m37s', '2025-02-04 07:52:05', '2025-04-23 10:33:24'),
(16, '509658', 'KaiCenat', 36, 414863074, '🦃 MAFIATHON 2 🦃 KAI X KEVIN HART X DRUSKI 🦃 DAY 27 🦃 20% OF REVENUE GOING TO SCHOOL IN NIGERIA 🦃 ALL MONTH 🦃 CLICK HERE 🦃 !Subathon', 24869994, '22h5m32s', '2024-11-28 02:06:07', '2025-04-23 10:33:24'),
(17, '509658', 'juansguarnizo', 1, 5734495, 'Tráiler del canal de juansguarnizo', 5734495, '1m0s', '2020-11-05 20:27:21', '2025-04-23 10:33:24'),
(18, '509658', 'Ninja', 1, 4468046, 'Starting soon!', 4468046, '9h53m18s', '2020-09-10 18:46:13', '2025-04-23 10:33:24'),
(19, '509658', 'elxokas', 1, 4265097, 'TRAILER DE JESUCRISTO', 4265097, '59s', '2020-05-15 17:27:17', '2025-04-23 10:33:24'),
(20, '509658', 'shroud', 1, 3761487, 'Escape from DROPS | Follow @shroud on socials', 3761487, '20h25m53s', '2021-01-02 00:24:27', '2025-04-23 10:33:24'),
(21, '21779', 'Riot Games', 26, 124973965, 'WORLDS 22 FINALS COUNTDOWN', 11620706, '9h25m12s', '2022-11-05 21:00:23', '2025-04-23 10:33:24'),
(22, '21779', 'Riot_esports_Korea', 13, 58211531, 'T1 vs DRX | 2022 월드 챔피언십 | FINALS', 7863050, '7h38m46s', '2022-11-05 22:50:16', '2025-04-23 10:33:24'),
(23, '21779', 'LEC', 1, 3420969, 'Warm Up Finals Preshow | LEC Summer (2020) |League of Legends', 3420969, '11h23m24s', '2020-09-06 08:00:39', '2025-04-23 10:33:24'),
(24, '516575', 'Ninja', 3, 7705401, 'V a l o r a n t Grind begins | Among us tonight at 8 CENTRAL with a SOLID crew! ', 4549589, '15h15m21s', '2020-09-11 18:52:09', '2025-04-23 10:33:24'),
(25, '516575', 'shroud', 36, 67841024, 'SEN GAME!! | @shroud FOLLOW ME!!', 2954968, '15h36m18s', '2021-09-12 15:57:30', '2025-04-23 10:33:24'),
(26, '516575', '加藤純一うん〇ちゃん', 1, 2027736, '18時よりﾑﾗｹﾞVSリドル戦を応援する男', 2027736, '14h8m37s', '2025-02-04 07:52:05', '2025-04-23 10:33:24'),
(27, '509658', 'KaiCenat', 36, 414863078, '🦃 MAFIATHON 2 🦃 KAI X KEVIN HART X DRUSKI 🦃 DAY 27 🦃 20% OF REVENUE GOING TO SCHOOL IN NIGERIA 🦃 ALL MONTH 🦃 CLICK HERE 🦃 !Subathon', 24869994, '22h5m32s', '2024-11-28 02:06:07', '2025-04-23 10:33:24'),
(28, '509658', 'juansguarnizo', 1, 5734495, 'Tráiler del canal de juansguarnizo', 5734495, '1m0s', '2020-11-05 20:27:21', '2025-04-23 10:33:24'),
(29, '509658', 'Ninja', 1, 4468046, 'Starting soon!', 4468046, '9h53m18s', '2020-09-10 18:46:13', '2025-04-23 10:33:24'),
(30, '509658', 'elxokas', 1, 4265097, 'TRAILER DE JESUCRISTO', 4265097, '59s', '2020-05-15 17:27:17', '2025-04-23 10:33:24'),
(31, '509658', 'shroud', 1, 3761487, 'Escape from DROPS | Follow @shroud on socials', 3761487, '20h25m53s', '2021-01-02 00:24:27', '2025-04-23 10:33:24'),
(32, '21779', 'Riot Games', 26, 124973965, 'WORLDS 22 FINALS COUNTDOWN', 11620706, '9h25m12s', '2022-11-05 21:00:23', '2025-04-23 10:33:24'),
(33, '21779', 'Riot_esports_Korea', 13, 58211531, 'T1 vs DRX | 2022 월드 챔피언십 | FINALS', 7863050, '7h38m46s', '2022-11-05 22:50:16', '2025-04-23 10:33:24'),
(34, '21779', 'LEC', 1, 3420969, 'Warm Up Finals Preshow | LEC Summer (2020) |League of Legends', 3420969, '11h23m24s', '2020-09-06 08:00:39', '2025-04-23 10:33:24'),
(35, '516575', 'Ninja', 3, 7705401, 'V a l o r a n t Grind begins | Among us tonight at 8 CENTRAL with a SOLID crew! ', 4549589, '15h15m21s', '2020-09-11 18:52:09', '2025-04-23 10:33:24'),
(36, '516575', 'shroud', 36, 67841024, 'SEN GAME!! | @shroud FOLLOW ME!!', 2954968, '15h36m18s', '2021-09-12 15:57:30', '2025-04-23 10:33:24'),
(37, '516575', '加藤純一うん〇ちゃん', 1, 2027736, '18時よりﾑﾗｹﾞVSリドル戦を応援する男', 2027736, '14h8m37s', '2025-02-04 07:52:05', '2025-04-23 10:33:24'),
(38, '509658', 'KaiCenat', 36, 414863091, '🦃 MAFIATHON 2 🦃 KAI X KEVIN HART X DRUSKI 🦃 DAY 27 🦃 20% OF REVENUE GOING TO SCHOOL IN NIGERIA 🦃 ALL MONTH 🦃 CLICK HERE 🦃 !Subathon', 24870003, '22h5m32s', '2024-11-28 02:06:07', '2025-04-23 10:33:24'),
(39, '509658', 'juansguarnizo', 1, 5734495, 'Tráiler del canal de juansguarnizo', 5734495, '1m0s', '2020-11-05 20:27:21', '2025-04-23 10:33:24'),
(40, '509658', 'Ninja', 1, 4468046, 'Starting soon!', 4468046, '9h53m18s', '2020-09-10 18:46:13', '2025-04-23 10:33:24'),
(41, '509658', 'elxokas', 1, 4265140, 'TRAILER DE JESUCRISTO', 4265140, '59s', '2020-05-15 17:27:17', '2025-04-23 10:33:24'),
(42, '509658', 'shroud', 1, 3761487, 'Escape from DROPS | Follow @shroud on socials', 3761487, '20h25m53s', '2021-01-02 00:24:27', '2025-04-23 10:33:24'),
(43, '21779', 'Riot Games', 26, 124973965, 'WORLDS 22 FINALS COUNTDOWN', 11620706, '9h25m12s', '2022-11-05 21:00:23', '2025-04-23 10:33:24'),
(44, '21779', 'Riot_esports_Korea', 13, 58211531, 'T1 vs DRX | 2022 월드 챔피언십 | FINALS', 7863050, '7h38m46s', '2022-11-05 22:50:16', '2025-04-23 10:33:24'),
(45, '21779', 'LEC', 1, 3420969, 'Warm Up Finals Preshow | LEC Summer (2020) |League of Legends', 3420969, '11h23m24s', '2020-09-06 08:00:39', '2025-04-23 10:33:24'),
(46, '509658', 'KaiCenat', 36, 414863444, '🦃 MAFIATHON 2 🦃 KAI X KEVIN HART X DRUSKI 🦃 DAY 27 🦃 20% OF REVENUE GOING TO SCHOOL IN NIGERIA 🦃 ALL MONTH 🦃 CLICK HERE 🦃 !Subathon', 24870080, '22h5m32s', '2024-11-28 02:06:07', '2025-04-23 10:33:24'),
(47, '509658', 'juansguarnizo', 1, 5734676, 'Tráiler del canal de juansguarnizo', 5734676, '1m0s', '2020-11-05 20:27:21', '2025-04-23 10:33:24'),
(48, '509658', 'Ninja', 1, 4468046, 'Starting soon!', 4468046, '9h53m18s', '2020-09-10 18:46:13', '2025-04-23 10:33:24'),
(49, '509658', 'elxokas', 1, 4265556, 'TRAILER DE JESUCRISTO', 4265556, '59s', '2020-05-15 17:27:17', '2025-04-23 10:33:24'),
(50, '509658', 'shroud', 1, 3761487, 'Escape from DROPS | Follow @shroud on socials', 3761487, '20h25m53s', '2021-01-02 00:24:27', '2025-04-23 10:33:24'),
(51, '21779', 'Riot Games', 26, 124973974, 'WORLDS 22 FINALS COUNTDOWN', 11620708, '9h25m12s', '2022-11-05 21:00:23', '2025-04-23 10:33:24'),
(52, '21779', 'Riot_esports_Korea', 13, 58211531, 'T1 vs DRX | 2022 월드 챔피언십 | FINALS', 7863050, '7h38m46s', '2022-11-05 22:50:16', '2025-04-23 10:33:24'),
(53, '21779', 'LEC', 1, 3420969, 'Warm Up Finals Preshow | LEC Summer (2020) |League of Legends', 3420969, '11h23m24s', '2020-09-06 08:00:39', '2025-04-23 10:33:24'),
(54, '32982', 'らっだぁ', 32, 41089276, '【VCR2日目】初めての犯罪！ 鳩禁指示禁', 2109319, '13h27m6s', '2023-12-11 04:23:05', '2025-04-23 10:33:24'),
(55, '32982', 'Squeezie', 1, 1810286, 'La grosse soirée GTA RP ! (on débarque sur FlashBack) !rp !histoire', 1810286, '5h21m56s', '2021-04-22 17:57:15', '2025-04-23 10:33:24'),
(56, '32982', 'Agent00', 1, 1717901, 'WEIGHT LOSS MARATHON | DAY 1.5 & 2', 1717901, '38h58m16s', '2025-01-02 05:16:30', '2025-04-23 10:33:24'),
(57, '32982', 'Bkinho', 1, 1364882, 'O GRANDE DIA CHEGOU!', 1364882, '47h21m52s', '2025-02-08 02:09:36', '2025-04-23 10:33:24'),
(58, '32982', 'PaulinhoLOKObr', 1, 1171833, '🔥PAULO POLICIA na SITUAÇÃO! GTA RP +18 Paulinho o LOKO', 1171833, '2h44m47s', '2025-02-14 00:06:20', '2025-04-23 10:33:24'),
(59, '32982', 'RebirthzTV', 3, 3199956, 'ตู้ โอคอนเนอร์ SS1 ถ้าล้มเราอาจจะได้แผล ถ้ายอมแพ้เราอาจจะไม่ได้อะไรเลย Omnuay ', 1098540, '8h48m36s', '2021-10-23 14:02:55', '2025-04-23 10:33:24'),
(60, '32982', 'NOBRU', 1, 1062464, 'ASSALTO AO BANCO CENTRAL - RP🔥', 1062464, '2h33m34s', '2020-07-30 02:05:03', '2025-04-23 10:33:24'),
(61, '509658', 'KaiCenat', 36, 453690022, '🦃 MAFIATHON 2 🦃 KAI X KEVIN HART X DRUSKI 🦃 DAY 27 🦃 20% OF REVENUE GOING TO SCHOOL IN NIGERIA 🦃 ALL MONTH 🦃 CLICK HERE 🦃 !Subathon', 24902785, '22h5m32s', '2024-11-28 02:06:07', '2025-04-23 10:33:24'),
(62, '509658', 'loud_coringa', 3, 20115781, 'LIVE 7 DIAS - DIA 1', 8030723, '44h30m27s', '2025-04-07 00:42:59', '2025-04-23 10:33:24'),
(63, '509658', 'juansguarnizo', 1, 5827570, 'Tráiler del canal de juansguarnizo', 5827570, '1m0s', '2020-11-05 20:27:21', '2025-04-23 10:33:24'),
(64, '21779', 'Riot Games', 25, 121601253, 'WORLDS 22 FINALS COUNTDOWN', 11621305, '9h25m12s', '2022-11-05 21:00:23', '2025-04-23 10:33:24'),
(65, '21779', 'Riot_esports_Korea', 13, 58211553, 'T1 vs DRX | 2022 월드 챔피언십 | FINALS', 7863050, '7h38m46s', '2022-11-05 22:50:16', '2025-04-23 10:33:24'),
(66, '21779', 'LEC', 1, 3420981, 'Warm Up Finals Preshow | LEC Summer (2020) |League of Legends', 3420981, '11h23m24s', '2020-09-06 08:00:39', '2025-04-23 10:33:24'),
(67, '21779', 'ibai', 1, 3408461, '7 Días Siendo Jugador Profesional - Día 3 - Hemos dejado el LoL', 3408461, '47h59m53s', '2025-03-19 18:17:14', '2025-04-23 10:33:24'),
(68, '32982', 'らっだぁ', 34, 43204741, '【VCR2日目】初めての犯罪！ 鳩禁指示禁', 2115874, '13h27m6s', '2023-12-11 04:23:05', '2025-04-23 10:33:24'),
(69, '32982', 'Squeezie', 1, 1810311, 'La grosse soirée GTA RP ! (on débarque sur FlashBack) !rp !histoire', 1810311, '5h21m56s', '2021-04-22 17:57:15', '2025-04-23 10:33:24'),
(70, '32982', 'RebirthzTV', 3, 3199964, 'ตู้ โอคอนเนอร์ SS1 ถ้าล้มเราอาจจะได้แผล ถ้ายอมแพ้เราอาจจะไม่ได้อะไรเลย Omnuay ', 1098542, '8h48m36s', '2021-10-23 14:02:55', '2025-04-23 10:33:24'),
(71, '32982', 'NOBRU', 1, 1062480, 'ASSALTO AO BANCO CENTRAL - RP🔥', 1062480, '2h33m34s', '2020-07-30 02:05:03', '2025-04-23 10:33:24'),
(72, '32982', 'ギルくん', 1, 1006452, '【ストグラ】298日目鳩禁指示禁【ケインオー】', 1006452, '25h51m8s', '2025-03-31 13:18:31', '2025-04-23 10:33:24'),
(73, '509658', 'KaiCenat', 36, 453690043, '🦃 MAFIATHON 2 🦃 KAI X KEVIN HART X DRUSKI 🦃 DAY 27 🦃 20% OF REVENUE GOING TO SCHOOL IN NIGERIA 🦃 ALL MONTH 🦃 CLICK HERE 🦃 !Subathon', 24902788, '22h5m32s', '2024-11-28 02:06:07', '2025-04-23 10:33:24'),
(74, '509658', 'loud_coringa', 3, 20115794, 'LIVE 7 DIAS - DIA 1', 8030729, '44h30m27s', '2025-04-07 00:42:59', '2025-04-23 10:33:24'),
(75, '509658', 'juansguarnizo', 1, 5827576, 'Tráiler del canal de juansguarnizo', 5827576, '1m0s', '2020-11-05 20:27:21', '2025-04-23 10:33:24'),
(76, '21779', 'Riot Games', 25, 121601253, 'WORLDS 22 FINALS COUNTDOWN', 11621305, '9h25m12s', '2022-11-05 21:00:23', '2025-04-23 10:33:24'),
(77, '21779', 'Riot_esports_Korea', 13, 58211553, 'T1 vs DRX | 2022 월드 챔피언십 | FINALS', 7863050, '7h38m46s', '2022-11-05 22:50:16', '2025-04-23 10:33:24'),
(78, '21779', 'LEC', 1, 3420981, 'Warm Up Finals Preshow | LEC Summer (2020) |League of Legends', 3420981, '11h23m24s', '2020-09-06 08:00:39', '2025-04-23 10:33:24'),
(79, '21779', 'ibai', 1, 3408463, '7 Días Siendo Jugador Profesional - Día 3 - Hemos dejado el LoL', 3408463, '47h59m53s', '2025-03-19 18:17:14', '2025-04-23 10:33:24'),
(80, '32982', 'らっだぁ', 34, 43204760, '【VCR2日目】初めての犯罪！ 鳩禁指示禁', 2115875, '13h27m6s', '2023-12-11 04:23:05', '2025-04-23 10:33:24'),
(81, '32982', 'Squeezie', 1, 1810311, 'La grosse soirée GTA RP ! (on débarque sur FlashBack) !rp !histoire', 1810311, '5h21m56s', '2021-04-22 17:57:15', '2025-04-23 10:33:24'),
(82, '32982', 'RebirthzTV', 3, 3199964, 'ตู้ โอคอนเนอร์ SS1 ถ้าล้มเราอาจจะได้แผล ถ้ายอมแพ้เราอาจจะไม่ได้อะไรเลย Omnuay ', 1098542, '8h48m36s', '2021-10-23 14:02:55', '2025-04-23 10:33:24'),
(83, '32982', 'NOBRU', 1, 1062480, 'ASSALTO AO BANCO CENTRAL - RP🔥', 1062480, '2h33m34s', '2020-07-30 02:05:03', '2025-04-23 10:33:24'),
(84, '32982', 'ギルくん', 1, 1006453, '【ストグラ】298日目鳩禁指示禁【ケインオー】', 1006453, '25h51m8s', '2025-03-31 13:18:31', '2025-04-23 10:33:24'),
(85, '509658', 'KaiCenat', 36, 453690058, '🦃 MAFIATHON 2 🦃 KAI X KEVIN HART X DRUSKI 🦃 DAY 27 🦃 20% OF REVENUE GOING TO SCHOOL IN NIGERIA 🦃 ALL MONTH 🦃 CLICK HERE 🦃 !Subathon', 24902791, '22h5m32s', '2024-11-28 02:06:07', '2025-04-23 10:33:24'),
(86, '509658', 'loud_coringa', 3, 20115806, 'LIVE 7 DIAS - DIA 1', 8030738, '44h30m27s', '2025-04-07 00:42:59', '2025-04-23 10:33:24'),
(87, '509658', 'juansguarnizo', 1, 5827576, 'Tráiler del canal de juansguarnizo', 5827576, '1m0s', '2020-11-05 20:27:21', '2025-04-23 10:33:24'),
(88, '21779', 'Riot Games', 25, 121601253, 'WORLDS 22 FINALS COUNTDOWN', 11621305, '9h25m12s', '2022-11-05 21:00:23', '2025-04-23 10:33:24'),
(89, '21779', 'Riot_esports_Korea', 13, 58211553, 'T1 vs DRX | 2022 월드 챔피언십 | FINALS', 7863050, '7h38m46s', '2022-11-05 22:50:16', '2025-04-23 10:33:24'),
(90, '21779', 'LEC', 1, 3420981, 'Warm Up Finals Preshow | LEC Summer (2020) |League of Legends', 3420981, '11h23m24s', '2020-09-06 08:00:39', '2025-04-23 10:33:24'),
(91, '21779', 'ibai', 1, 3408463, '7 Días Siendo Jugador Profesional - Día 3 - Hemos dejado el LoL', 3408463, '47h59m53s', '2025-03-19 18:17:14', '2025-04-23 10:33:24'),
(92, '32982', 'らっだぁ', 34, 43204769, '【VCR2日目】初めての犯罪！ 鳩禁指示禁', 2115875, '13h27m6s', '2023-12-11 04:23:05', '2025-04-23 10:33:24'),
(93, '32982', 'Squeezie', 1, 1810311, 'La grosse soirée GTA RP ! (on débarque sur FlashBack) !rp !histoire', 1810311, '5h21m56s', '2021-04-22 17:57:15', '2025-04-23 10:33:24'),
(94, '32982', 'RebirthzTV', 3, 3199964, 'ตู้ โอคอนเนอร์ SS1 ถ้าล้มเราอาจจะได้แผล ถ้ายอมแพ้เราอาจจะไม่ได้อะไรเลย Omnuay ', 1098542, '8h48m36s', '2021-10-23 14:02:55', '2025-04-23 10:33:24'),
(95, '32982', 'NOBRU', 1, 1062480, 'ASSALTO AO BANCO CENTRAL - RP🔥', 1062480, '2h33m34s', '2020-07-30 02:05:03', '2025-04-23 10:33:24'),
(96, '32982', 'ギルくん', 1, 1006453, '【ストグラ】298日目鳩禁指示禁【ケインオー】', 1006453, '25h51m8s', '2025-03-31 13:18:31', '2025-04-23 10:33:24'),
(97, '509658', 'KaiCenat', 36, 453690058, '🦃 MAFIATHON 2 🦃 KAI X KEVIN HART X DRUSKI 🦃 DAY 27 🦃 20% OF REVENUE GOING TO SCHOOL IN NIGERIA 🦃 ALL MONTH 🦃 CLICK HERE 🦃 !Subathon', 24902791, '22h5m32s', '2024-11-28 02:06:07', '2025-04-23 10:33:24'),
(98, '509658', 'loud_coringa', 3, 20115806, 'LIVE 7 DIAS - DIA 1', 8030738, '44h30m27s', '2025-04-07 00:42:59', '2025-04-23 10:33:24'),
(99, '509658', 'juansguarnizo', 1, 5827576, 'Tráiler del canal de juansguarnizo', 5827576, '1m0s', '2020-11-05 20:27:21', '2025-04-23 10:33:24'),
(100, '21779', 'Riot Games', 25, 121601253, 'WORLDS 22 FINALS COUNTDOWN', 11621305, '9h25m12s', '2022-11-05 21:00:23', '2025-04-23 10:33:24'),
(101, '21779', 'Riot_esports_Korea', 13, 58211553, 'T1 vs DRX | 2022 월드 챔피언십 | FINALS', 7863050, '7h38m46s', '2022-11-05 22:50:16', '2025-04-23 10:33:24'),
(102, '21779', 'LEC', 1, 3420981, 'Warm Up Finals Preshow | LEC Summer (2020) |League of Legends', 3420981, '11h23m24s', '2020-09-06 08:00:39', '2025-04-23 10:33:24'),
(103, '21779', 'ibai', 1, 3408463, '7 Días Siendo Jugador Profesional - Día 3 - Hemos dejado el LoL', 3408463, '47h59m53s', '2025-03-19 18:17:14', '2025-04-23 10:33:24'),
(104, '32982', 'らっだぁ', 34, 43204769, '【VCR2日目】初めての犯罪！ 鳩禁指示禁', 2115875, '13h27m6s', '2023-12-11 04:23:05', '2025-04-23 10:33:24'),
(105, '32982', 'Squeezie', 1, 1810311, 'La grosse soirée GTA RP ! (on débarque sur FlashBack) !rp !histoire', 1810311, '5h21m56s', '2021-04-22 17:57:15', '2025-04-23 10:33:24'),
(106, '32982', 'RebirthzTV', 3, 3199964, 'ตู้ โอคอนเนอร์ SS1 ถ้าล้มเราอาจจะได้แผล ถ้ายอมแพ้เราอาจจะไม่ได้อะไรเลย Omnuay ', 1098542, '8h48m36s', '2021-10-23 14:02:55', '2025-04-23 10:33:24'),
(107, '32982', 'NOBRU', 1, 1062480, 'ASSALTO AO BANCO CENTRAL - RP🔥', 1062480, '2h33m34s', '2020-07-30 02:05:03', '2025-04-23 10:33:24'),
(108, '32982', 'ギルくん', 1, 1006453, '【ストグラ】298日目鳩禁指示禁【ケインオー】', 1006453, '25h51m8s', '2025-03-31 13:18:31', '2025-04-23 10:33:24'),
(109, '509658', 'KaiCenat', 36, 453693202, '🦃 MAFIATHON 2 🦃 KAI X KEVIN HART X DRUSKI 🦃 DAY 27 🦃 20% OF REVENUE GOING TO SCHOOL IN NIGERIA 🦃 ALL MONTH 🦃 CLICK HERE 🦃 !Subathon', 24903327, '22h5m32s', '2024-11-28 02:06:07', '2025-04-23 13:24:04'),
(110, '509658', 'loud_coringa', 3, 20117735, 'LIVE 7 DIAS - DIA 1', 8031755, '44h30m27s', '2025-04-07 00:42:59', '2025-04-23 13:24:04'),
(111, '509658', 'juansguarnizo', 1, 5830022, 'Tráiler del canal de juansguarnizo', 5830022, '1m0s', '2020-11-05 20:27:21', '2025-04-23 13:24:04'),
(112, '1984929743', 'shroud', 1, 1510683, 'MY FIRST RPG IS COMING BACK GAME TIME', 1510683, '14h51m54s', '2025-04-22 14:33:19', '2025-04-23 13:24:05'),
(113, '1984929743', 'MOONMOON', 1, 320314, '(master) u know what it is mang | !youtube is back', 320314, '9h53m10s', '2025-04-22 19:56:54', '2025-04-23 13:24:05'),
(114, '1984929743', 'Xop0', 1, 147540, 'FIRS TIME I\'ve only played Morrowind! ⚠️ The Elder Scrolls IV: Oblivion - Remastered™ I haven\'t even finished Skyrim #PCGamePassPartner', 147540, '4h47m37s', '2025-04-22 17:28:58', '2025-04-23 13:24:05'),
(115, '1984929743', 'AlphaCast', 1, 141378, 'Salut alors la ZLAN c\'était coo-.. OBLIVION REMAKE OMG OMG | !HyperX !ThermosAlpha !Nutripure', 141378, '6h50m16s', '2025-04-22 15:41:03', '2025-04-23 13:24:05'),
(116, '1984929743', 'luality', 1, 129562, 'OBLIVION REMASTER REVEAL WATCH PARTY', 129562, '11h13m58s', '2025-04-22 14:27:23', '2025-04-23 13:24:05'),
(117, '1984929743', 'Altair', 1, 121574, 'OBLIVION HYPE - !1440p !holy', 121574, '10h16m47s', '2025-04-22 14:11:14', '2025-04-23 13:24:05'),
(118, '1984929743', 'MauriceWeber', 1, 112311, 'Release heute? Wir schauen den Bethesda-Stream', 112311, '7h42m34s', '2025-04-22 14:42:32', '2025-04-23 13:24:05'),
(119, '1984929743', 'moistcr1tikal', 1, 95950, 'Oblivion remaster is here', 95950, '3h36m32s', '2025-04-23 02:27:13', '2025-04-23 13:24:05'),
(120, '1984929743', 'KarmikKoala', 1, 88898, 'я не играл в облу, давайте без спойлеров', 88898, '9h53m44s', '2025-04-22 16:19:18', '2025-04-23 13:24:05'),
(121, '1984929743', 'jeensoff', 1, 88025, 'СЕГОДНЯ!!!!!! в ожидании ремастера обливиона, я верю... !tg @jeensoff', 88025, '5h53m40s', '2025-04-22 14:23:47', '2025-04-23 13:24:05'),
(122, '1984929743', 'Distortion2', 1, 83493, 'DISTONE EXPLORES OBLIVION - I was wrong... its here', 83493, '6h3m20s', '2025-04-22 20:08:01', '2025-04-23 13:24:05'),
(123, '1984929743', 'Tomato', 1, 77978, 'mine goblet spilleth over with grease', 77978, '7h26m21s', '2025-04-22 16:42:36', '2025-04-23 13:24:05'),
(124, '1984929743', 'Putrefy', 1, 76829, '🔥PREMIERA OBLIVION REMASTERED🔥Kup Taniej na Instant Gaming -> !o🔥PROPAGANDA SUKCESU ACShadows ->!P | Niepokojąca Przyszłość FromSoft ->!F', 76829, '8h0m49s', '2025-04-22 16:20:38', '2025-04-23 13:24:05'),
(125, '1984929743', 'GeneralSam123', 1, 71571, 'Jean-Luc Dickhard is Mr. Oblivion', 71571, '4h20m50s', '2025-04-22 22:52:38', '2025-04-23 13:24:05'),
(126, '1984929743', 'Jabo', 1, 65910, 'Oblivion Remastered LAUNCH DAY - Expert Difficulty ALL ACHIEVEMENTS', 65910, '9h55m53s', '2025-04-22 14:41:30', '2025-04-23 13:24:05'),
(127, '1984929743', 'Shisheyu', 1, 62066, 'OBLIVION JAMAIS JOUÉ EVER C KOI CE POULET ? !holy', 62066, '5h43m55s', '2025-04-22 17:36:50', '2025-04-23 13:24:05'),
(128, '1984929743', 'Criken', 1, 58354, 'Hist Frizzle and the Magic Murder Wagon | Oblivion Remastered DAY 1!!!', 58354, '7h8m40s', '2025-04-22 19:18:37', '2025-04-23 13:24:05'),
(129, '1984929743', 'Ramez05', 1, 56206, 'Nostalgia Simulator🟥2 Days In A Row Streaming🟥Elder Scrolls IV: Oblivion Gameplay, Builds, Guides & Tips!', 56206, '11h35m46s', '2025-04-22 17:06:52', '2025-04-23 13:24:05'),
(130, '1984929743', 'AdmiralBahroo', 1, 51920, 'I have never actually played Oblivion before', 51920, '5h5m42s', '2025-04-22 16:12:48', '2025-04-23 13:24:05'),
(131, '1984929743', 'Bawkbasoup', 1, 51393, 'Todd Howard Will Save us All With Oblivion', 51393, '12h0m2s', '2025-04-22 14:45:45', '2025-04-23 13:24:05'),
(132, '1984929743', 'DiNIceTea', 1, 48273, 'РЕМАСТЕР ОБЛИВИОНА | СМОТРИМ НОВИНКУ | СКОРО КУКИНГ', 48273, '3h44m50s', '2025-04-22 18:02:41', '2025-04-23 13:24:05'),
(133, '1984929743', 'SleDuck', 1, 42905, 'Да ну нафиг!? - Oblivion Remastered - !тг', 42905, '5h29m20s', '2025-04-22 16:19:23', '2025-04-23 13:24:05'),
(134, '1984929743', 'Insym', 1, 42688, 'OBLIVION REMASTER - First Time EVER Playing Oblivion', 42688, '4h2m24s', '2025-04-22 17:11:46', '2025-04-23 13:24:05'),
(135, '1984929743', 'Stormfall33', 1, 40378, 'IT\'S ACTUALLY REAL | [FIRST TIME] NEW OBLIVION REMASTER', 40378, '9h35m49s', '2025-04-22 17:18:56', '2025-04-23 13:24:05'),
(136, '1984929743', 'Gladd', 1, 39759, 'Never Actually Played Oblivion B4 | !NewFlavor', 39759, '8h2m30s', '2025-04-22 22:10:43', '2025-04-23 13:24:05'),
(137, '1984929743', 'Dangar', 1, 38297, 'Тестим новый !ПК | Oblivion и т.д. (22.04.2025)', 38297, '8h17m52s', '2025-04-22 16:05:30', '2025-04-23 13:24:05'),
(138, '1984929743', 'Joov', 1, 38049, 'Oblivion Remastered Showcase...shadowdrop plz?', 38049, '6h41m17s', '2025-04-22 14:28:54', '2025-04-23 13:24:05'),
(139, '1984929743', 'Vargskelethor', 1, 36377, 'Joel || The Elder Scrolls IV: Oblivion Remastered', 36377, '4h12m21s', '2025-04-23 02:35:55', '2025-04-23 13:24:05'),
(140, '1984929743', 'Pressea', 1, 34293, '📜 “Tu es enfin réveillé...” – Ah non, mauvais jeu 😅 !Holy !Zephyr', 34293, '3h38m37s', '2025-04-22 16:15:58', '2025-04-23 13:24:05'),
(141, '1984929743', 'ZeplaHQ', 1, 27889, 'OBLIVION STREAM WATCH PARTY🌱 1st TIMER.. LAUNCH TODAY??!🌱  |  !secretlab', 27889, '9h40m47s', '2025-04-22 14:53:07', '2025-04-23 13:24:05'),
(142, '1984929743', 'GameStar', 1, 27751, 'Elder Scrolls Shadow Drop?! | Oblivion-Remake zocken! | !frage', 27751, '2h51m12s', '2025-04-22 14:35:17', '2025-04-23 13:24:05'),
(143, '1984929743', 'Momo', 1, 25333, 'ITS TIME... OBLIVION COMES PLEASE??? |【VTuber】 !merch #mature #charity', 25333, '10h2m51s', '2025-04-22 14:53:08', '2025-04-23 13:24:05'),
(144, '1984929743', 'CaptainRichard', 1, 24339, '⚔️ FIRST TIME OBLIVION PLAYER! - Oblivion Remastered Day 1 ⚔️ Courtesy of Bethesda ⚔️ !Elgato !LogitechG !Maingear', 24339, '6h32m48s', '2025-04-22 20:01:42', '2025-04-23 13:24:05'),
(145, '1984929743', 'AngryJoeShow', 1, 22947, 'AJ & Crew - OBLIVION IS BACK BABY!!!', 22947, '4h10m52s', '2025-04-22 19:15:34', '2025-04-23 13:24:05'),
(146, '1984929743', 'BlindWalkerBoy', 1, 22575, 'Я буквально не верю...', 22575, '10h25m54s', '2025-04-22 13:55:11', '2025-04-23 13:24:05'),
(147, '1984929743', 'ViktorZu', 1, 18934, 'ШЭДОУУУДРООООП', 18934, '8h58m11s', '2025-04-22 14:28:55', '2025-04-23 13:24:05'),
(148, '1984929743', 'BOBLEGOB', 1, 18632, 'I🔞I BOBLEGOB : OBLIVION QUE L\'AVENTURE COMMENCE !!', 18632, '6h10m21s', '2025-04-22 17:57:46', '2025-04-23 13:24:05'),
(149, '1984929743', 'MaLaRiaTV', 1, 18624, 'OBLIVION Remaster Reveal !!', 18624, '16h17m13s', '2025-04-22 14:46:20', '2025-04-23 13:24:05'),
(150, '1984929743', 'DAVlDFlSHER', 1, 17697, 'Тодд, не подведи во второй раз | !тг', 17697, '9h40m3s', '2025-04-22 14:30:45', '2025-04-23 13:24:05'),
(151, '1984929743', 'Welderb', 1, 16672, 'Заценим новый ремейк обливиона, рыбак на новом месте', 16672, '9h27m31s', '2025-04-22 22:15:48', '2025-04-23 13:24:05'),
(152, '32982', 'らっだぁ', 34, 43205756, '【VCR2日目】初めての犯罪！ 鳩禁指示禁', 2115934, '13h27m6s', '2023-12-11 04:23:05', '2025-04-23 13:24:05'),
(153, '32982', 'Squeezie', 1, 1810311, 'La grosse soirée GTA RP ! (on débarque sur FlashBack) !rp !histoire', 1810311, '5h21m56s', '2021-04-22 17:57:15', '2025-04-23 13:24:05'),
(154, '32982', 'RebirthzTV', 3, 3199964, 'ตู้ โอคอนเนอร์ SS1 ถ้าล้มเราอาจจะได้แผล ถ้ายอมแพ้เราอาจจะไม่ได้อะไรเลย Omnuay ', 1098542, '8h48m36s', '2021-10-23 14:02:55', '2025-04-23 13:24:05'),
(155, '32982', 'NOBRU', 1, 1062491, 'ASSALTO AO BANCO CENTRAL - RP🔥', 1062491, '2h33m34s', '2020-07-30 02:05:03', '2025-04-23 13:24:05'),
(156, '32982', 'ギルくん', 1, 1006477, '【ストグラ】298日目鳩禁指示禁【ケインオー】', 1006477, '25h51m8s', '2025-03-31 13:18:31', '2025-04-23 13:24:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `api_key` varchar(32) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `email`, `api_key`, `created_at`) VALUES
(1, 'israelhualde@gmail.com', '9b6903fc9b4c0fa9', '2025-02-16 20:59:08'),
(2, 'unaimartinez650@gmail.com', 'f0650826cf418ac3', '2025-02-17 09:08:39'),
(3, 'martinez.147151@e.unavarra.es', '11199b796762a800', '2025-02-18 18:48:00'),
(4, 'hualde.146905@e.unavarra.es', '08b3c45f894f3a72', '2025-02-20 13:21:42'),
(5, 'unaimartinez650@gmail.con', '60a4628f5a0d149b', '2025-02-27 18:43:00'),
(6, 'usuario@example.com', '8106b56d547da053', '2025-04-08 11:26:36'),
(7, 'usuario@example2.com', '603f872640387935', '2025-04-23 13:02:08'),
(8, 'usuario2222@example.com', 'c563a6ab7e948a2d', '2025-04-29 12:45:44');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `streamers`
--
ALTER TABLE `streamers`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `top_games`
--
ALTER TABLE `top_games`
  ADD PRIMARY KEY (`game_id`);

--
-- Indices de la tabla `top_videos`
--
ALTER TABLE `top_videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de la tabla `top_videos`
--
ALTER TABLE `top_videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `top_videos`
--
ALTER TABLE `top_videos`
  ADD CONSTRAINT `top_videos_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `top_games` (`game_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
