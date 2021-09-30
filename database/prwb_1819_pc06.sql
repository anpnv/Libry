-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Lun 20 Mai 2019 à 20:43
-- Version du serveur :  5.7.11
-- Version de PHP :  5.6.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `prwb_1819_pc06`
--
DROP DATABASE IF EXISTS `prwb_1819_pc06`;
CREATE DATABASE IF NOT EXISTS `prwb_1819_pc06` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `prwb_1819_pc06`;
-- --------------------------------------------------------

--
-- Structure de la table `book`
--

CREATE TABLE `book` (
  `id` int(11) NOT NULL,
  `isbn` char(13) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `editor` varchar(255) NOT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `nbCopies` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `book`
--

INSERT INTO `book` (`id`, `isbn`, `title`, `author`, `editor`, `picture`, `nbCopies`) VALUES
(1, '9782412020913', 'Réalisez HTML 5 et CSS 3', 'Mathieu Nebra', 'Eyrolles', NULL, 7),
(2, '9782212674743', 'Web Design with HTML, CSS, JavaScript and jQuery Set', 'Jon Duckett', 'John Wiley &amp; Sons', 'Jon Duckett1554590976.png', 10),
(3, '9782212675221', 'Apprenez à programmer en Java', 'Cyrille Herby', 'Eyrolles', NULL, 14),
(4, '9782412020869', 'Java pour les Nuls grand format, 3e édition', ' Barry Burd', 'First Interactive', ' Barry Burd1554221119.png', 12),
(5, '9782746046184', 'Algorithmique', 'Laurent Debrauwer', 'Editions ENI', NULL, 2),
(6, '9782746044173', 'Programmzation C/C#/C++', 'Robert Anderson', 'CreateSpace', 'Robert Anderson1554225274.png', 8),
(7, '9782212143935', 'Découvrez le langage Swift', 'Rudy de Visscher', 'Eyrolles', NULL, 15),
(8, '9782126747502', 'Concevez votre site web avec PHP et MySQL', 'Mathieu Nebra', 'Eyrolles', 'Mathieu Nebra1554225421.png', 6),
(9, '9782253129400', 'Le Loup de Wall Street', 'Jordan Belfort', 'Le Livre de Poche', 'Jordan Belfort1554225581.png', 9),
(10, '9782212567489', 'Elon Musk: Tesla, Paypal, SpaceX : l\'entrepreneur qui va changer le monde', 'Ashlee Vance', 'Eyrolles', 'Ashlee Vance1554225673.png', 6),
(14, '9782212567472', 'Les guide de survie en projet web', 'Andrei Ponamarev', 'EPFC', NULL, 9);

-- --------------------------------------------------------

--
-- Structure de la table `rental`
--

CREATE TABLE `rental` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `book` int(11) NOT NULL,
  `rentaldate` datetime DEFAULT NULL,
  `returndate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `rental`
--

INSERT INTO `rental` (`id`, `user`, `book`, `rentaldate`, `returndate`) VALUES
(762, 3, 3, '2019-05-20 02:17:39', '2019-05-20 02:22:23'),
(764, 15, 3, '2019-05-20 02:17:48', '2019-05-20 02:22:34'),
(770, 1, 3, '2019-04-01 02:17:36', '2019-05-19 00:00:00'),
(771, 1, 1, '2019-03-08 02:17:36', NULL),
(772, 7, 4, '2019-05-20 02:17:44', '2019-05-20 02:22:30'),
(773, 7, 3, '2019-02-20 02:17:44', NULL),
(774, 7, 2, '2019-05-20 02:17:44', '2019-05-20 22:39:05'),
(775, 15, 7, '2019-01-20 02:17:48', NULL),
(776, 15, 6, '2019-05-20 02:17:48', '2019-05-20 22:39:03'),
(778, 2, 1, '2019-01-20 22:37:25', NULL),
(779, 2, 2, '2018-05-20 22:37:25', NULL),
(780, 2, 5, '2014-05-20 22:37:25', '2019-05-15 00:00:00'),
(781, 2, 7, '2019-05-20 22:37:25', '2019-05-20 22:39:10'),
(783, 3, 5, '2019-05-20 22:37:31', NULL),
(784, 3, 4, '2019-05-20 22:37:31', NULL),
(785, 3, 2, '2019-05-20 22:37:31', '2019-05-20 22:39:07'),
(786, 3, 1, '2019-05-20 22:37:31', NULL),
(787, 3, 10, NULL, NULL),
(788, 4, 3, '2019-05-20 22:37:52', NULL),
(789, 4, 2, '2019-05-20 22:37:52', '2019-05-20 22:39:08'),
(790, 4, 7, '2019-05-20 22:37:52', NULL),
(791, 5, 3, NULL, NULL),
(792, 5, 2, NULL, NULL),
(793, 7, 8, NULL, NULL),
(794, 7, 7, NULL, NULL),
(795, 7, 14, NULL, NULL),
(796, 12, 4, '2019-05-20 22:38:10', NULL),
(797, 12, 2, '2019-05-20 22:38:10', NULL),
(798, 12, 3, '2019-05-20 22:38:10', NULL),
(799, 1, 2, '2019-05-20 22:38:29', NULL),
(800, 13, 2, '2019-05-20 22:38:49', '2019-05-20 22:39:12'),
(801, 13, 1, '2019-05-20 22:38:49', NULL),
(802, 13, 7, '2019-05-20 22:38:49', '2019-05-20 22:39:14'),
(803, 15, 8, NULL, NULL),
(804, 15, 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(64) NOT NULL,
  `birthdate` date DEFAULT NULL,
  `role` enum('admin','manager','member') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `fullname`, `email`, `birthdate`, `role`) VALUES
(1, 'admin', 'c6aa01bd261e501b1fea93c41fe46dc7', 'Andrei Ponamarev', 'andrei.ponamarev@outlook.com', '1994-12-02', 'admin'),
(2, 'mike99', 'f498b1fe7dced1d818d92de1520b9e44', 'Mike Ross', 'mike@ross.com', NULL, 'manager'),
(3, 'Harvey123', 'ac4755d91f406f3f8f691ed90ce608e6', 'Harvey Specter', 'info@specter.com', '1974-09-03', 'manager'),
(4, 'LouisdeParis', 'ed2829b283dbda3b54f04e78b2d083ef', 'Louis Litt', 'Litt@louis.us', '1980-03-11', 'member'),
(5, 'Freddy', '35319100872964c97adc17cbd1a15ee4', 'Krueger', 'freddy@contrejason.com', '1942-02-27', 'member'),
(7, 'Leonard', '502eb238a8710429c3bf18049616fe0c', 'De vinci', 'Leonard@de.vinci', '1452-04-15', 'member'),
(8, 'Thrall', 'f77fa36936bc37ad71f31a0679d5598c', 'Fils de Durotan', 'Orgrimmar@city.com', NULL, 'member'),
(12, 'Boris', 'f6b7c480a90bee9a3cb508ef4d49baf3', 'Boris Verhaegen', 'boris@verhaegen.me', NULL, 'admin'),
(13, 'memberBySignTest', '81a86a0f3c6e74e645fb75c79205bedb', 'Member inscrit depuis Signuptest', 'signuzp@member.com', '2019-04-06', 'member'),
(15, 'createbymanager', '9d8ec7a64627b5dd91b380186a89f202', 'User create by Manager', 'user@create.by', '2019-04-06', 'member');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isbn_UNIQUE` (`isbn`);

--
-- Index pour la table `rental`
--
ALTER TABLE `rental`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_rentalitem_book1_idx` (`book`),
  ADD KEY `fk_rentalitem_user1_idx` (`user`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username_unique` (`username`) USING BTREE,
  ADD UNIQUE KEY `email_unique` (`email`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `book`
--
ALTER TABLE `book`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT pour la table `rental`
--
ALTER TABLE `rental`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=805;
--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `rental`
--
ALTER TABLE `rental`
  ADD CONSTRAINT `fk_rentalitem_book` FOREIGN KEY (`book`) REFERENCES `book` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_rentalitem_user1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
