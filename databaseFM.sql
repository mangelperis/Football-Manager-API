-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         11.1.4-MariaDB-1:11.1.4+maria~ubu2204-log - mariadb.org binary distribution
-- SO del servidor:              debian-linux-gnu
-- HeidiSQL Versión:             12.6.0.6765
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para football_manager
DROP DATABASE IF EXISTS `football_manager`;
CREATE DATABASE IF NOT EXISTS `football_manager` /*!40100 DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci */;
USE `football_manager`;

-- Volcando estructura para tabla football_manager.clubs
DROP TABLE IF EXISTS `clubs`;
CREATE TABLE IF NOT EXISTS `clubs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `shortname` varchar(3) NOT NULL,
  `country` varchar(2) NOT NULL,
  `budget` double NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `email` (`email`),
  KEY `budget_index` (`budget`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla football_manager.clubs: ~5 rows (aproximadamente)
DELETE FROM `clubs`;
INSERT INTO `clubs` (`id`, `name`, `shortname`, `country`, `budget`, `created`, `updated`, `email`) VALUES
	(1, 'Manchester United', 'MUF', 'EN', 500000000, '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'info@manutd.com'),
	(2, 'Real Madrid', 'RMF', 'ES', 600000000, '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'info@realmadrid.com'),
	(3, 'Bayern Munich', 'FCB', 'GE', 450000000, '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'info@fcbayern.com'),
	(4, 'Paris Saint-Germain', 'PSG', 'FR', 550000000, '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'info@psg.fr'),
	(5, 'Juventus', 'JUV', 'IT', 400000000, '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'info@juventus.com');

-- Volcando estructura para tabla football_manager.coaches
DROP TABLE IF EXISTS `coaches`;
CREATE TABLE IF NOT EXISTS `coaches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `club_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `salary` double NOT NULL,
  `email` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `role` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `IDX_C413176561190A32` (`club_id`),
  KEY `name_index` (`name`),
  CONSTRAINT `FK_C413176561190A32` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla football_manager.coaches: ~10 rows (aproximadamente)
DELETE FROM `coaches`;
INSERT INTO `coaches` (`id`, `club_id`, `name`, `salary`, `email`, `created`, `updated`, `role`) VALUES
	(1, 1, 'Erik ten Hag', 500000, 'erik.tenhag@manutd.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'Head'),
	(2, 2, 'Carlo Ancelotti', 600000, 'carlo.ancelotti@realmadrid.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'Head'),
	(3, 3, 'Thomas Tuchel', 550000, 'thomas.tuchel@fcbayern.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'Head'),
	(4, 4, 'Christophe Galtier', 450000, 'christophe.galtier@psg.fr', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'Head'),
	(5, 5, 'Massimiliano Allegri', 500000, 'massimiliano.allegri@juventus.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'Head'),
	(6, 1, 'Mitchell van der Gaag', 200000, 'mitchell.vandergaag@manutd.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'Assistant'),
	(7, 2, 'Davide Ancelotti', 250000, 'davide.ancelotti@realmadrid.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'Assistant'),
	(8, 3, 'Dino Toppmöller', 220000, 'dino.toppmoller@fcbayern.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'Assistant'),
	(9, 4, 'Zsolt Löw', 180000, 'zsolt.low@psg.fr', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'Assistant'),
	(10, 5, 'Marco Landucci', 200000, 'marco.landucci@juventus.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'Assistant');

-- Volcando estructura para tabla football_manager.doctrine_migration_versions
DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Volcando datos para la tabla football_manager.doctrine_migration_versions: ~2 rows (aproximadamente)
DELETE FROM `doctrine_migration_versions`;
INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
	('DoctrineMigrations\\Version20240412221035', '2024-04-14 18:49:09', 217),
	('DoctrineMigrations\\Version20240414184530', '2024-04-14 18:49:09', 66);

-- Volcando estructura para tabla football_manager.players
DROP TABLE IF EXISTS `players`;
CREATE TABLE IF NOT EXISTS `players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `club_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `salary` double NOT NULL,
  `email` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `position` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `IDX_264E43A661190A32` (`club_id`),
  KEY `name_index` (`name`),
  CONSTRAINT `FK_264E43A661190A32` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla football_manager.players: ~25 rows (aproximadamente)
DELETE FROM `players`;
INSERT INTO `players` (`id`, `club_id`, `name`, `salary`, `email`, `created`, `updated`, `position`) VALUES
	(1, 1, 'Cristiano Ronaldo', 500000, 'cr7@manutd.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'F'),
	(2, 1, 'Bruno Fernandes', 250000, 'bruno@manutd.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'CM'),
	(3, 1, 'Marcus Rashford', 200000, 'rashford@manutd.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'F'),
	(4, 1, 'David de Gea', 375000, 'degea@manutd.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'GK'),
	(5, 1, 'Harry Maguire', 190000, 'maguire@manutd.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'D'),
	(6, 2, 'Karim Benzema', 400000, 'benzema@realmadrid.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'F'),
	(7, 2, 'Luka Modric', 300000, 'modric@realmadrid.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'CM'),
	(8, 2, 'Thibaut Courtois', 200000, 'courtois@realmadrid.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'GK'),
	(9, 2, 'Sergio Ramos', 250000, 'ramos@realmadrid.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'D'),
	(10, 2, 'Toni Kroos', 350000, 'kroos@realmadrid.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'CM'),
	(11, 3, 'Robert Lewandowski', 450000, 'lewy@fcbayern.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'F'),
	(12, 3, 'Thomas Müller', 300000, 'muller@fcbayern.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'F'),
	(13, 3, 'Manuel Neuer', 200000, 'neuer@fcbayern.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'GK'),
	(14, 3, 'Joshua Kimmich', 250000, 'kimmich@fcbayern.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'CM'),
	(15, 3, 'Leroy Sané', 350000, 'sane@fcbayern.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'F'),
	(16, 4, 'Kylian Mbappé', 600000, 'mbappe@psg.fr', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'F'),
	(17, 4, 'Neymar Jr', 700000, 'neymar@psg.fr', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'F'),
	(18, 4, 'Ángel Di María', 280000, 'dimaria@psg.fr', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'CM'),
	(19, 4, 'Keylor Navas', 180000, 'navas@psg.fr', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'GK'),
	(20, 4, 'Marquinhos', 220000, 'marquinhos@psg.fr', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'D'),
	(21, 5, 'Cristiano Ronaldo', 600000, 'cr7@juventus.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'F'),
	(22, 5, 'Paulo Dybala', 400000, 'dybala@juventus.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'F'),
	(23, 5, 'Matthijs de Ligt', 200000, 'deligt@juventus.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'D'),
	(24, 5, 'Wojciech Szczęsny', 150000, 'szczesny@juventus.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'GK'),
	(25, 5, 'Federico Chiesa', 250000, 'chiesa@juventus.com', '2024-04-14 23:46:36', '2024-04-14 23:46:36', 'F');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
