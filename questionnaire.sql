-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 14 mars 2025 à 15:38
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `questionnaire`
--

CREATE DATABASE IF NOT EXISTS `questionnaire` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `groupes`
--

CREATE TABLE `groupes` (
  `id` int(11) NOT NULL,
  `nom` varchar(128) NOT NULL,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `groupes`
--

-- --------------------------------------------------------

--
-- Structure de la table `questionnaire`
--

CREATE TABLE `questionnaire` (
  `id` int(11) NOT NULL,
  `nom` varchar(128) NOT NULL,
  `theme` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `creation_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `questionnaire`
--

INSERT INTO `questionnaire` (`id`, `nom`, `theme`, `created_by`, `creation_date`) VALUES
(1, 'Arithmétique', 1, 2, '2025-01-23 20:47:56'),
(2, 'Accords', 2, 2, '2025-01-31 16:42:12'),
(3, 'Soustractions', 1, 2, '2025-02-07 12:37:38');

-- --------------------------------------------------------

--
-- Structure de la table `questions`
--

CREATE TABLE `questions` (
  `id_question` int(11) NOT NULL,
  `question` varchar(128) NOT NULL,
  `type` int(11) NOT NULL,
  `choix` longtext NOT NULL,
  `reponses` longtext NOT NULL,
  `id_questionnaire` int(11) NOT NULL,
  `id_creator` int(11) NOT NULL,
  `points` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `questions`
--

INSERT INTO `questions` (`id_question`, `question`, `type`, `choix`, `reponses`, `id_questionnaire`, `id_creator`, `points`) VALUES
(1, '2+2=?', 2, '2,3,4', '4', 1, 2, '0,0,1'),
(2, '1+1=2', 1, 'Vrai,Faux', 'Vrai', 1, 2, '1,0'),
(3, '2+2=5', 1, 'Vrai,Faux', 'Faux', 1, 2, '0,2'),
(4, '1+1=3', 1, 'Vrai,Faux', 'Faux', 1, 2, '0,2'),
(5, 'Léa et Camille \n? du vélo.', 2, 'fais,fait,font', 'font', 2, 2, '0,0,1'),
(6, 'Ces ? immeubles sont en feu.', 2, 'grands,grand,gran', 'grands', 2, 2, '1,0,0'),
(7, 'Ces machines sont ? pour faire ça.', 2, 'conçu,conçues,conçue', 'conçues', 2, 2, '0,1,0'),
(8, 'Ces caves ? me font peur.', 2, 'sombres,sombre,Je ne sais pas', 'sombres', 2, 2, '1,0,0'),
(9, 'Ces immeubles ? me font peur.', 2, 'abandonné,abandonnés,abandonnées', 'abandonnées', 2, 2, '0,0,1'),
(10, 'Elles ont tout ? !', 2, 'ratées,ratée,raté', 'raté', 2, 2, '0,0,1'),
(15, '4+4 = 8', 1, 'Vrai,Faux', 'Vrai', 1, 2, '1,0'),
(12, 'Les cahiers orange sont à ramasser.', 1, 'Vrai,Faux', 'Vrai', 2, 2, '1,0'),
(14, '15-5 = 10', 1, 'Vrai,Faux', 'Vrai', 1, 2, '1,0'),
(16, '2-2 = 0', 1, 'Vrai,Faux', 'Vrai', 3, 2, '1,-1'),
(17, '15-5+2-2 = ?', 2, '10,15,12', '10', 3, 2, '1,0,0');

-- --------------------------------------------------------

--
-- Structure de la table `scores`
--

CREATE TABLE `scores` (
  `id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `score_on` int(11) NOT NULL,
  `reponses` longtext NOT NULL,
  `id_questionnaire` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `scores`
--

-- --------------------------------------------------------

--
-- Structure de la table `theme`
--

CREATE TABLE `theme` (
  `id_theme` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `theme`
--

INSERT INTO `theme` (`id_theme`, `nom`) VALUES
(1, 'Mathématique'),
(2, 'Français');

-- --------------------------------------------------------

--
-- Structure de la table `types`
--

CREATE TABLE `types` (
  `id_type` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `types`
--

INSERT INTO `types` (`id_type`, `nom`) VALUES
(1, 'Vrai/Faux'),
(2, 'Multiples');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(64) NOT NULL,
  `password` varchar(256) NOT NULL,
  `role` int(11) NOT NULL DEFAULT 0,
  `email` text NOT NULL,
  `groupe_id` int(11) NOT NULL,
  `token` text NOT NULL,
  `token_expire` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `users`
--

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `groupes`
--
ALTER TABLE `groupes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `questionnaire`
--
ALTER TABLE `questionnaire`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id_question`);

--
-- Index pour la table `scores`
--
ALTER TABLE `scores`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `theme`
--
ALTER TABLE `theme`
  ADD PRIMARY KEY (`id_theme`);

--
-- Index pour la table `types`
--
ALTER TABLE `types`
  ADD PRIMARY KEY (`id_type`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `groupes`
--
ALTER TABLE `groupes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `questionnaire`
--
ALTER TABLE `questionnaire`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `questions`
--
ALTER TABLE `questions`
  MODIFY `id_question` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `scores`
--
ALTER TABLE `scores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `theme`
--
ALTER TABLE `theme`
  MODIFY `id_theme` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `types`
--
ALTER TABLE `types`
  MODIFY `id_type` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
