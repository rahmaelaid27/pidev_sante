-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 27, 2025 at 11:58 PM
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
-- Database: `hasna3a`
--

-- --------------------------------------------------------

--
-- Table structure for table `avis`
--

CREATE TABLE `avis` (
  `ref` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `professional_id` int(11) NOT NULL,
  `note` int(11) NOT NULL,
  `commentaire` varchar(255) NOT NULL,
  `date_avis` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `avis`
--

INSERT INTO `avis` (`ref`, `id_user`, `professional_id`, `note`, `commentaire`, `date_avis`) VALUES
(1, 3, 1, 2, 'tres bien', '2025-12-12'),
(2, 3, 1, 4, 'very good', '2024-12-11'),
(3, 3, 1, 5, 'sooo good', '2024-12-12'),
(5, 5, 1, 3, 'good job', '2025-12-12'),
(6, 5, 3, 5, 'handsome doctor', '2025-02-08'),
(7, 6, 3, 5, 'jawwou behy', '2025-02-06'),
(8, 6, 3, 5, 'jawwou behy', '2025-02-06'),
(9, 6, 3, 4, 'not bad', '2025-02-06'),
(10, 6, 3, 1, 'tbib mrigel', '2025-02-13'),
(11, 6, 1, 5, 'gooood', '2025-02-14'),
(12, 6, 1, 5, 'efzefzef', '2025-02-08'),
(13, 6, 1, 1, 'chwya barcha', '2025-02-15');

-- --------------------------------------------------------

--
-- Table structure for table `consultation`
--

CREATE TABLE `consultation` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_professionnel` int(11) NOT NULL,
  `date_consultation` date DEFAULT NULL,
  `rendez_vous_id` int(11) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `consultation`
--

INSERT INTO `consultation` (`id`, `id_user`, `id_professionnel`, `date_consultation`, `rendez_vous_id`, `reason`) VALUES
(1, 5, 1, '2028-03-17', 8, NULL),
(2, 5, 1, '2025-02-22', 5, NULL),
(3, 5, 5, '2025-04-03', 10, NULL),
(4, 5, 4, '2025-02-28', 13, 'kk'),
(5, 1, 4, '2025-02-22', 15, 'ee'),
(6, 5, 4, '2025-02-20', 16, 'gagagga'),
(7, 4, 4, '2029-12-26', 20, 'ADHD'),
(8, 4, 4, '2025-02-28', 25, 'sick');

-- --------------------------------------------------------

--
-- Table structure for table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prescription`
--

CREATE TABLE `prescription` (
  `id` int(11) NOT NULL,
  `id_consultation` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `prescription`
--

INSERT INTO `prescription` (`id`, `id_consultation`, `description`, `created_at`) VALUES
(5, 2, 'sickk', NULL),
(6, 3, 'aaa', NULL),
(7, 3, 'malahala', NULL),
(8, 6, 'doliprane', '2025-02-22'),
(9, 6, 'aaa', '2025-02-22'),
(10, 7, 'rrrrrrrr', '2025-02-22'),
(11, 6, 'gripex', '2025-02-23'),
(12, 6, 'rara', '2025-02-23'),
(13, 6, 'rarar', '2025-02-23'),
(14, 6, 'dabbouza', '2025-02-23'),
(15, 6, 'hahahah', '2025-02-23'),
(16, 6, 'hahahah', '2025-02-23'),
(17, 6, 'hahahah', '2025-02-23'),
(18, 6, 'zae', '2025-02-23'),
(19, 6, 'zae', '2025-02-23'),
(20, 6, 'zae', '2025-02-23'),
(21, 6, 'ch9af', '2025-02-23'),
(22, 6, 'ch9af', '2025-02-23'),
(23, 6, 'ch9af', '2025-02-23'),
(24, 6, 'mrabba3', '2025-02-23'),
(25, 6, 'BMW', '2025-02-23'),
(26, 6, 'gripex, panadole', '2025-02-23'),
(27, 6, 'gripex, panadole', '2025-02-23'),
(28, 6, 'gripex, panadole', '2025-02-23'),
(29, 6, 'zhaymer', '2025-02-23'),
(30, 6, 'raraz', '2025-02-23'),
(31, 6, 'aa', '2025-02-23'),
(32, 6, 'zz', '2025-02-23'),
(33, 6, 'zeze', '2025-02-23'),
(34, 6, 'eze', '2025-02-23'),
(35, 6, 'eze', '2025-02-23'),
(36, 6, 'girpex, doliprane et panadole', '2025-02-23'),
(37, 6, 'inceline,', '2025-02-23'),
(38, 6, 'insuline', '2025-02-24');

-- --------------------------------------------------------

--
-- Table structure for table `rendez_vous`
--

CREATE TABLE `rendez_vous` (
  `id` int(11) NOT NULL,
  `consultation_id` int(11) DEFAULT NULL,
  `date_rdv` date NOT NULL,
  `status_rdv` varchar(255) NOT NULL,
  `id_user` int(11) NOT NULL,
  `professional_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rendez_vous`
--

INSERT INTO `rendez_vous` (`id`, `consultation_id`, `date_rdv`, `status_rdv`, `id_user`, `professional_id`) VALUES
(1, NULL, '2025-02-19', 'en attente', 5, 3),
(2, NULL, '2025-01-31', 'accepted', 5, 2),
(3, NULL, '2025-01-31', 'accepted', 5, 1),
(4, NULL, '2025-03-07', 'accepted', 5, 5),
(5, NULL, '2025-02-08', 'accepted', 5, 1),
(6, NULL, '2025-02-22', 'accepted', 5, 1),
(7, NULL, '2025-03-07', 'accepted', 5, 1),
(8, NULL, '2025-02-17', 'accepted', 5, 1),
(9, NULL, '2025-02-07', 'en attente', 5, 1),
(10, NULL, '2025-02-06', 'accepted', 5, 5),
(11, NULL, '2025-02-06', 'accepted', 5, 5),
(12, NULL, '2025-02-27', 'en attente', 5, 1),
(13, NULL, '2000-12-10', 'accepted', 5, 5),
(14, NULL, '2000-12-12', 'en attente', 5, 5),
(15, NULL, '2000-02-02', 'accepted', 5, 5),
(16, NULL, '2025-01-31', 'accepted', 5, 4),
(17, NULL, '2025-02-08', 'accepted', 5, 4),
(18, NULL, '2025-03-08', 'accepted', 5, 4),
(19, NULL, '2000-12-12', 'accepted', 4, 4),
(20, NULL, '2025-02-08', 'accepted', 4, 4),
(21, NULL, '2025-02-04', 'en attente', 4, 3),
(22, NULL, '2025-02-08', 'en attente', 4, 5),
(23, NULL, '2025-02-15', 'en attente', 4, 1),
(24, NULL, '2025-02-21', 'accepted', 4, 4),
(25, NULL, '2025-01-30', 'accepted', 4, 4);

-- --------------------------------------------------------

--
-- Table structure for table `reponse`
--

CREATE TABLE `reponse` (
  `id` int(11) NOT NULL,
  `id_avis` int(11) NOT NULL,
  `professional_id` int(11) NOT NULL,
  `reponse` varchar(255) NOT NULL,
  `date_reponse` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reponse`
--

INSERT INTO `reponse` (`id`, `id_avis`, `professional_id`, `reponse`, `date_reponse`) VALUES
(1, 1, 5, 'i agree', '2025-02-17'),
(2, 1, 5, 'oui oui', '2025-02-17'),
(3, 3, 5, 'i agree', '2025-02-17'),
(4, 5, 5, 'ez rrrrrrr', '2025-02-17'),
(5, 5, 5, 'excellent', '2025-02-17'),
(6, 2, 5, 'dazdazdaz', '2025-02-17'),
(7, 6, 6, 'je confirme', '2025-02-23');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `ref` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '(DC2Type:json)' CHECK (json_valid(`roles`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`ref`, `nom`, `email`, `password`, `roles`) VALUES
(1, 'hasna', 'hasnabenalaya30@gmail.com', '$2y$13$bN9hAYlup7VR30uFfMm2Ae6iVnSsTum5yyFkW7ELiUxVAUcqjRONK', '[\"ROLE_PATIENT\"]'),
(2, 'youssef', 'youssef@gmail.com', '$2y$13$uIzF9iTOd6w0dO/GDEqqG.XgQim2Y8npsWFaO6/ZT4d0ns3QsNww2', '[\"ROLE_PROFESSIONAL\"]'),
(3, 'zied', 'abidijasser001@gmail.com', '$2y$13$wxwCvZsutso1sJAcyLdPoer9QTKj1yBvOlnF038mvMYF8E0YikGhy', '[\"ROLE_PROFESSIONAL\"]'),
(4, 'rahma', 'rahma@gmail.com', '$2y$13$6N3jHmA2sBmNde//3G8X9.QiVnoMcJ94Nc2raBvhnJmsNynKpiGkq', '[\"ROLE_PROFESSIONAL\"]'),
(5, 'jass', 'rahmaelaid6@gmail.com', '$2y$13$9.AIHd/n0TOUy6.YItJrbOMtyKRnd3sTVPlpatKsKRdrR86oLu4y2', '[\"ROLE_PATIENT\"]'),
(6, 'jasser', 'jasserabidi00@gmail.com', '$2y$13$Wl1PrXEbMpdlbCwiJkBUoeF.u1zweJ8AwSY.74sS4gcKA5VeipdTC', '[\"ROLE_PATIENT\"]');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`ref`),
  ADD KEY `IDX_8F91ABF06B3CA4B` (`id_user`),
  ADD KEY `IDX_8F91ABF0DB77003` (`professional_id`);

--
-- Indexes for table `consultation`
--
ALTER TABLE `consultation`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_964685A691EF7EAA` (`rendez_vous_id`),
  ADD KEY `IDX_964685A66B3CA4B` (`id_user`),
  ADD KEY `IDX_964685A6C400106A` (`id_professionnel`);

--
-- Indexes for table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- Indexes for table `prescription`
--
ALTER TABLE `prescription`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_1FBFB8D9B587F0D4` (`id_consultation`);

--
-- Indexes for table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_65E8AA0A62FF6CDF` (`consultation_id`),
  ADD KEY `IDX_65E8AA0A6B3CA4B` (`id_user`),
  ADD KEY `IDX_65E8AA0ADB77003` (`professional_id`);

--
-- Indexes for table `reponse`
--
ALTER TABLE `reponse`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5FB6DEC74B1B7F2` (`id_avis`),
  ADD KEY `IDX_5FB6DEC7DB77003` (`professional_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`ref`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `avis`
--
ALTER TABLE `avis`
  MODIFY `ref` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `consultation`
--
ALTER TABLE `consultation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prescription`
--
ALTER TABLE `prescription`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `reponse`
--
ALTER TABLE `reponse`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `ref` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `avis`
--
ALTER TABLE `avis`
  ADD CONSTRAINT `FK_8F91ABF06B3CA4B` FOREIGN KEY (`id_user`) REFERENCES `user` (`ref`),
  ADD CONSTRAINT `FK_8F91ABF0DB77003` FOREIGN KEY (`professional_id`) REFERENCES `user` (`ref`);

--
-- Constraints for table `consultation`
--
ALTER TABLE `consultation`
  ADD CONSTRAINT `FK_964685A66B3CA4B` FOREIGN KEY (`id_user`) REFERENCES `user` (`ref`),
  ADD CONSTRAINT `FK_964685A691EF7EAA` FOREIGN KEY (`rendez_vous_id`) REFERENCES `rendez_vous` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_964685A6C400106A` FOREIGN KEY (`id_professionnel`) REFERENCES `user` (`ref`);

--
-- Constraints for table `prescription`
--
ALTER TABLE `prescription`
  ADD CONSTRAINT `FK_1FBFB8D9B587F0D4` FOREIGN KEY (`id_consultation`) REFERENCES `consultation` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD CONSTRAINT `FK_65E8AA0A62FF6CDF` FOREIGN KEY (`consultation_id`) REFERENCES `consultation` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_65E8AA0A6B3CA4B` FOREIGN KEY (`id_user`) REFERENCES `user` (`ref`),
  ADD CONSTRAINT `FK_65E8AA0ADB77003` FOREIGN KEY (`professional_id`) REFERENCES `user` (`ref`);

--
-- Constraints for table `reponse`
--
ALTER TABLE `reponse`
  ADD CONSTRAINT `FK_5FB6DEC74B1B7F2` FOREIGN KEY (`id_avis`) REFERENCES `avis` (`ref`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_5FB6DEC7DB77003` FOREIGN KEY (`professional_id`) REFERENCES `user` (`ref`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
