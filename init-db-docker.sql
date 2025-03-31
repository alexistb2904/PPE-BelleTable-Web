SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `quizdb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `quizdb`;

CREATE TABLE `choix` (
  `id` int NOT NULL,
  `id_question` int NOT NULL,
  `texte` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `est_reponse` tinyint(1) NOT NULL DEFAULT '0',
  `points` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `choix` (`id`, `id_question`, `texte`, `est_reponse`, `points`) VALUES
(1, 1, 'Vrai', 0, 0),
(2, 1, 'Faux', 1, 1),
(3, 2, 'Vrai', 1, 1),
(4, 2, 'Faux', 0, 0),
(5, 3, 'Océan Atlantique', 0, 0),
(6, 3, 'Océan Indien', 0, 0),
(7, 3, 'Océan Pacifique', 1, 1),
(8, 4, 'Vrai', 0, 0),
(9, 4, 'Faux', 1, 1),
(10, 5, 'Vrai', 0, 0),
(11, 5, 'Faux', 1, 1),
(12, 6, 'Python', 1, 1),
(13, 6, 'HTML', 0, 0),
(14, 6, 'JavaScript', 1, 1),
(15, 7, 'Vrai', 1, 1),
(16, 7, 'Faux', 0, 0),
(17, 8, 'Vrai', 0, 0),
(18, 8, 'Faux', 1, 1),
(19, 9, '3', 1, 1),
(20, 9, '4', 0, 0),
(21, 9, '5', 1, 1),
(22, 9, '6', 0, 0),
(23, 10, 'Vrai', 1, 1),
(24, 10, 'Faux', 0, 0),
(25, 11, 'HTTP', 0, 0),
(26, 11, 'HTTPS', 1, 1),
(27, 11, 'SSL/TLS', 1, 1),
(28, 11, 'FTP', 0, 0),
(29, 12, 'Vrai', 1, 1),
(30, 12, 'Faux', 0, 0);

CREATE TABLE `groupes` (
  `id` int NOT NULL,
  `nom` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date_creation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `groupes` (`id`, `nom`, `date_creation`) VALUES
(1, 'BTS SIO2', NOW()),
(2, 'BTS SIO1', NOW()),
(3, 'LP DWEB', NOW());

CREATE TABLE `questionnaire` (
  `id` int NOT NULL,
  `nom` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `theme` int NOT NULL,
  `created_by` int NOT NULL,
  `creation_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `questionnaire` (`id`, `nom`, `theme`, `created_by`, `creation_date`) VALUES
(1, 'Quiz Culture Générale', 1, 1, NOW()),
(2, 'Quiz Informatique', 2, 1, NOW()),
(3, 'Quiz Mathématiques', 3, 1, NOW()),
(4, 'Vocabulaire Anglais', 4, 1, NOW()),
(5, 'Quiz Réseau et Sécurité', 2, 4, NOW() - INTERVAL 2 DAY);

CREATE TABLE `questions` (
  `id_question` int NOT NULL,
  `question` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `type` int NOT NULL,
  `id_questionnaire` int NOT NULL,
  `id_creator` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `questions` (`id_question`, `question`, `type`, `id_questionnaire`, `id_creator`) VALUES
(1, 'La Terre est plate.', 1, 1, 1),
(2, 'Napoléon est mort en 1821.', 1, 1, 1),
(3, 'Quel est le plus grand océan ?', 2, 1, 1),
(4, 'HTML est un langage de programmation.', 1, 2, 1),
(5, 'PHP est exécuté côté client.', 1, 2, 1),
(6, 'Quels sont des langages de programmation ?', 2, 2, 1),
(7, '2 + 2 = 4.', 1, 3, 1),
(8, 'La racine carrée de 9 est 4.', 1, 3, 1),
(9, 'Les nombres premiers parmi ceux-ci sont :', 2, 3, 1),
(10, 'Un firewall sert à bloquer le trafic réseau non autorisé.', 1, 5, 4),
(11, 'Quels sont des protocoles de sécurité pour le web ?', 2, 5, 4),
(12, 'Le port standard pour HTTPS est 443.', 1, 5, 4);

CREATE TABLE `reponses_utilisateur` (
  `id` int NOT NULL,
  `id_score` int NOT NULL,
  `id_question` int NOT NULL,
  `id_choix` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `reponses_utilisateur` (`id`, `id_score`, `id_question`, `id_choix`) VALUES
(1, 11, 2, 3),
(2, 11, 1, 2),
(3, 11, 3, 7),
(4, 12, 1, 2),
(5, 12, 2, 3),
(6, 12, 3, 6),
(7, 13, 4, 9),
(8, 13, 5, 11),
(9, 13, 6, 12),
(10, 13, 6, 13),
(11, 13, 6, 14),
(12, 14, 7, 15),
(13, 14, 8, 17),
(14, 14, 9, 20),
(15, 15, 1, 2),
(16, 15, 2, 3),
(17, 15, 3, 7),
(18, 16, 4, 9),
(19, 16, 5, 11),
(20, 16, 6, 12),
(21, 16, 6, 14),
(22, 18, 1, 2),
(23, 18, 2, 4),
(24, 18, 3, 7),
(25, 22, 4, 9),
(26, 22, 5, 11),
(27, 22, 6, 12),
(28, 22, 6, 13),
(29, 26, 7, 15),
(30, 26, 8, 18),
(31, 26, 9, 19),
(32, 27, 10, 23),
(33, 27, 11, 26),
(34, 27, 11, 27),
(35, 27, 12, 29),
(36, 28, 10, 23),
(37, 28, 11, 25),
(38, 28, 12, 29),
(39, 29, 10, 23),
(40, 29, 11, 26),
(41, 29, 12, 29); 

CREATE TABLE `scores` (
  `id` int NOT NULL,
  `score` int NOT NULL,
  `score_on` int NOT NULL,
  `id_questionnaire` int NOT NULL,
  `id_user` int NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `scores` (`id`, `score`, `score_on`, `id_questionnaire`, `id_user`, `date`) VALUES
(11, 3, 3, 1, 1, NOW()),
(12, 2, 3, 1, 3, NOW() - INTERVAL 1 DAY),
(13, 5, 6, 2, 3, NOW() - INTERVAL 2 DAY),
(14, 1, 2, 3, 3, NOW() - INTERVAL 3 DAY),
(15, 3, 3, 1, 4, NOW() - INTERVAL 1 HOUR),
(16, 6, 6, 2, 4, NOW() - INTERVAL 2 HOUR),
(17, 2, 2, 3, 4, NOW() - INTERVAL 3 HOUR),
(18, 2, 3, 1, 5, NOW() - INTERVAL 4 DAY),
(19, 4, 6, 2, 5, NOW() - INTERVAL 5 DAY),
(20, 1, 2, 3, 5, NOW() - INTERVAL 6 DAY),
(21, 3, 3, 1, 6, NOW() - INTERVAL 2 DAY),
(22, 5, 6, 2, 6, NOW() - INTERVAL 3 DAY),
(23, 1, 2, 3, 6, NOW() - INTERVAL 4 DAY),
(24, 2, 3, 1, 7, NOW() - INTERVAL 5 HOUR),
(25, 3, 6, 2, 7, NOW() - INTERVAL 6 HOUR),
(26, 2, 2, 3, 7, NOW() - INTERVAL 7 HOUR),
(27, 3, 3, 5, 3, NOW() - INTERVAL 1 DAY),
(28, 2, 3, 5, 5, NOW() - INTERVAL 2 DAY),
(29, 3, 3, 5, 6, NOW() - INTERVAL 3 DAY);

CREATE TABLE `theme` (
  `id_theme` int NOT NULL,
  `nom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `theme` (`id_theme`, `nom`) VALUES
(1, 'Culture générale'),
(2, 'Informatique'),
(3, 'Mathématiques'),
(4, 'Anglais');

CREATE TABLE `types` (
  `id_type` int NOT NULL,
  `nom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `types` (`id_type`, `nom`) VALUES
(1, 'Vrai/Faux'),
(2, 'Choix multiples');

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` int NOT NULL DEFAULT '0',
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `groupe_id` int NOT NULL DEFAULT '0',
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `token_expire` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `username`, `password`, `role`, `email`, `groupe_id`, `token`, `token_expire`) VALUES
(1, 'admin', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', 1, 'admin@example.com', 1, NULL, NULL),
(2, 'user', '04f8996da763b7a969b1028ee3007569eaf3a635486ddab211d512c85b9df8fb', 0, 'user@example.com', 0, NULL, NULL),
(3, 'claude', '5994471abb01112afcc18159f6cc74b4f511b99806da59b3caf5a9c173cacfc5', 0, 'claude@example.com', 1, NULL, NULL),
(4, 'professeur', 'ef797c8118f02dfb649607dd5d3f8c7623048c9c063d532cc95c5ed7a898a64f', 1, 'professeur@example.com', 1, NULL, NULL),
(5, 'marie', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220f5e06e17aa77a216b3a3b822', 0, 'marie@example.com', 2, NULL, NULL),
(6, 'pierre', '71e9a4e599d6f1c512d3595b1523f7f8a3f74b3c14966bab18e9887c5f43b42d', 0, 'pierre@example.com', 3, NULL, NULL),
(7, 'julie', 'c76c7f5aae1bcf2e10c609a27c4c067c4174d84d580bddca4e0afa30e9c5bc2d', 0, 'julie@example.com', 3, NULL, NULL);



ALTER TABLE `choix`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_question` (`id_question`);

ALTER TABLE `groupes`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `questionnaire`
  ADD PRIMARY KEY (`id`),
  ADD KEY `theme` (`theme`),
  ADD KEY `created_by` (`created_by`);

ALTER TABLE `questions`
  ADD PRIMARY KEY (`id_question`),
  ADD KEY `type` (`type`),
  ADD KEY `id_questionnaire` (`id_questionnaire`),
  ADD KEY `id_creator` (`id_creator`);

ALTER TABLE `reponses_utilisateur`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_score` (`id_score`),
  ADD KEY `id_question` (`id_question`),
  ADD KEY `id_choix` (`id_choix`);

ALTER TABLE `scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_questionnaire` (`id_questionnaire`),
  ADD KEY `id_user` (`id_user`);

ALTER TABLE `theme`
  ADD PRIMARY KEY (`id_theme`);

ALTER TABLE `types`
  ADD PRIMARY KEY (`id_type`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `groupe_id` (`groupe_id`);


ALTER TABLE `choix`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

ALTER TABLE `groupes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `questionnaire`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `questions`
  MODIFY `id_question` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

ALTER TABLE `reponses_utilisateur`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

ALTER TABLE `scores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

ALTER TABLE `theme`
  MODIFY `id_theme` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `types`
  MODIFY `id_type` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;


ALTER TABLE `questionnaire`
  ADD CONSTRAINT `questionnaire_ibfk_1` FOREIGN KEY (`theme`) REFERENCES `theme` (`id_theme`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `questionnaire_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`id_questionnaire`) REFERENCES `questionnaire` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `questions_ibfk_2` FOREIGN KEY (`id_creator`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `questions_ibfk_3` FOREIGN KEY (`type`) REFERENCES `types` (`id_type`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `reponses_utilisateur`
  ADD CONSTRAINT `reponses_utilisateur_ibfk_1` FOREIGN KEY (`id_choix`) REFERENCES `choix` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `reponses_utilisateur_ibfk_2` FOREIGN KEY (`id_question`) REFERENCES `questions` (`id_question`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `reponses_utilisateur_ibfk_3` FOREIGN KEY (`id_score`) REFERENCES `scores` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

ALTER TABLE `scores`
  ADD CONSTRAINT `scores_ibfk_1` FOREIGN KEY (`id_questionnaire`) REFERENCES `questionnaire` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `scores_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

CREATE USER IF NOT EXISTS 'appuser'@'%' IDENTIFIED BY 'password123';
GRANT ALL PRIVILEGES ON quizdb.* TO 'appuser'@'%';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
