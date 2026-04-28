-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for progress
CREATE DATABASE IF NOT EXISTS `progress` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `progress`;

-- Dumping structure for table progress.admins
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `prenom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table progress.admins: ~2 rows (approximately)
REPLACE INTO `admins` (`id`, `username`, `password`, `nom`, `prenom`) VALUES
	(1, 'mekhloudi', '$2y$12$Rjkq1f86Q/RVFASN9p2fuu/1BKAjniywGEqsPGoFvCsYoYKcPwKTy', 'MIKHALDI', '(NULL)'),
	(2, 'kedouri', '$2y$12$Rjkq1f86Q/RVFASN9p2fuu/1BKAjniywGEqsPGoFvCsYoYKcPwKTy', 'KADOURI', NULL);

-- Dumping structure for table progress.modules
CREATE TABLE IF NOT EXISTS `modules` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `intitule` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `coefficient` int NOT NULL DEFAULT (1),
  `teacher_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module_teacher` (`teacher_id`) USING BTREE,
  CONSTRAINT `module_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table progress.modules: ~8 rows (approximately)
REPLACE INTO `modules` (`id`, `intitule`, `coefficient`, `teacher_id`) VALUES
	(1, 'ARCHI', 3, 1),
	(2, 'BDD', 3, 2),
	(3, 'THG', 2, 3),
	(4, 'SYS', 3, 4),
	(5, 'DEV', 3, 5),
	(6, 'GL', 3, 6),
	(7, 'english', 1, 7),
	(9, 'kutyhgkh', 2, 1);

-- Dumping structure for table progress.notes
CREATE TABLE IF NOT EXISTS `notes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `student_id` int unsigned NOT NULL,
  `module_id` int unsigned NOT NULL,
  `note` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_id_module_id` (`student_id`,`module_id`),
  KEY `notes_student` (`student_id`),
  KEY `notes_module` (`module_id`) USING BTREE,
  CONSTRAINT `notes_module` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`),
  CONSTRAINT `notes_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table progress.notes: ~14 rows (approximately)
REPLACE INTO `notes` (`id`, `student_id`, `module_id`, `note`) VALUES
	(1, 1, 1, 20),
	(2, 1, 2, 1),
	(3, 1, 3, 20),
	(4, 1, 4, 20),
	(5, 1, 5, 20),
	(6, 1, 6, 20),
	(7, 7, 7, 20),
	(22, 2, 4, 20),
	(24, 3, 4, 20),
	(25, 4, 4, 20),
	(28, 3, 1, 15),
	(29, 2, 1, 20),
	(30, 2, 5, 11),
	(32, 8, 1, 20);

-- Dumping structure for table progress.students
CREATE TABLE IF NOT EXISTS `students` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `matricule` varchar(50) NOT NULL DEFAULT '',
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `prenom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `dob` date NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `niveau` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `last_active` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `matricule` (`matricule`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table progress.students: ~10 rows (approximately)
REPLACE INTO `students` (`id`, `matricule`, `username`, `password`, `nom`, `prenom`, `dob`, `email`, `niveau`, `last_active`) VALUES
	(1, '00000001', 'student1', '$2y$12$Rjkq1f86Q/RVFASN9p2fuu/1BKAjniywGEqsPGoFvCsYoYKcPwKTy', 'HAFI', 'ABDERRAOUF', '2006-01-01', 'aaaa@gmail.com', 'L2', '2026-04-28 13:31:40'),
	(2, '00000002', 'student2', '$2y$12$Rjkq1f86Q/RVFASN9p2fuu/1BKAjniywGEqsPGoFvCsYoYKcPwKTy', 'bbbb', 'bbbb', '2006-01-02', 'bbbbb@gmail.com', 'L2', '2026-04-28 11:36:58'),
	(3, '00000003', 'student3', '$2y$12$Rjkq1f86Q/RVFASN9p2fuu/1BKAjniywGEqsPGoFvCsYoYKcPwKTy', 'cccccc', 'cccccc', '2006-01-03', 'ccccc@gmail.com', 'L2', '2026-04-28 11:05:39'),
	(4, '00000004', 'student4', '$12$Rjkq1f86Q/RVFASN9p2fuu/1BKAjniywGEqsPGoFvCsYoYKcPwKTy', 'ddddd', 'dddd', '2006-01-04', 'ddddd@gmail.com', 'L2', NULL),
	(5, '00000005', 'student5', '$2y$12$Rjkq1f86Q/RVFASN9p2fuu/1BKAjniywGEqsPGoFvCsYoYKcPwKTy', 'eeeee', 'eeeee', '2006-01-05', 'eeeee@gmail.com', 'L2', NULL),
	(6, '00000006', 'student6', '$2y$12$Rjkq1f86Q/RVFASN9p2fuu/1BKAjniywGEqsPGoFvCsYoYKcPwKTy', 'fffff', 'fffff', '2006-01-06', 'fffff@gmail.com', 'L2', NULL),
	(7, '00000007', 'student7', '$2y$12$Rjkq1f86Q/RVFASN9p2fuu/1BKAjniywGEqsPGoFvCsYoYKcPwKTy', 'ggggg', 'ggggg', '2006-01-07', 'ggggg@gmail.com', 'L2', NULL),
	(8, '00000008', 'student8', '$2y$12$Rjkq1f86Q/RVFASN9p2fuu/1BKAjniywGEqsPGoFvCsYoYKcPwKTy', 'hhhhh', 'hhhhh', '2006-01-08', 'hhhhh@gmail.com', 'L2', NULL),
	(9, '00000009', 'student9', '$2y$12$Rjkq1f86Q/RVFASN9p2fuu/1BKAjniywGEqsPGoFvCsYoYKcPwKTy', 'iiiii', 'iiiii', '2006-01-09', 'iiiii@gmail.com', 'L2', NULL),
	(10, '00000010', 'student10', '$2y$12$Rjkq1f86Q/RVFASN9p2fuu/1BKAjniywGEqsPGoFvCsYoYKcPwKTy', 'AA', 'AA', '2006-01-10', 'AA@gmail.com', 'L2', NULL);

-- Dumping structure for table progress.teachers
CREATE TABLE IF NOT EXISTS `teachers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `matricule` varchar(50) NOT NULL DEFAULT '',
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `prenom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `last_active` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `matricule` (`matricule`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table progress.teachers: ~7 rows (approximately)
REPLACE INTO `teachers` (`id`, `matricule`, `username`, `password`, `nom`, `prenom`, `last_active`) VALUES
	(1, '00000001', 'archi', '$2y$12$Rjkq1f86Q/RVFASN9p2fuu/1BKAjniywGEqsPGoFvCsYoYKcPwKTy', 'Teacher', '01', '2026-04-28 13:26:13'),
	(2, '00000002', 'bdd', '$2y$12$Rjkq1f86Q/RVFASN9p2fuu/1BKAjniywGEqsPGoFvCsYoYKcPwKTy', 'Teacher', '02', NULL),
	(3, '00000003', 'thg', '$2y$12$Rjkq1f86Q/RVFASN9p2fuu/1BKAjniywGEqsPGoFvCsYoYKcPwKTy', 'Teacher', '03', NULL),
	(4, '00000004', 'sys', '$2y$12$Rjkq1f86Q/RVFASN9p2fuu/1BKAjniywGEqsPGoFvCsYoYKcPwKTy', 'Teacher', '04', '2026-04-25 00:36:03'),
	(5, '00000005', 'dev', '$2y$12$Rjkq1f86Q/RVFASN9p2fuu/1BKAjniywGEqsPGoFvCsYoYKcPwKTy', 'Teacher', '05', NULL),
	(6, '00000006', 'gl', '$2y$12$Rjkq1f86Q/RVFASN9p2fuu/1BKAjniywGEqsPGoFvCsYoYKcPwKTy', 'Teacher', '06', NULL),
	(7, '00000007', 'english', '$2y$12$Rjkq1f86Q/RVFASN9p2fuu/1BKAjniywGEqsPGoFvCsYoYKcPwKTy', 'Teacher', '07', NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
