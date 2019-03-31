-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: 31.03.2019 klo 09:07
-- Palvelimen versio: 10.1.28-MariaDB
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vrl`
--

-- --------------------------------------------------------

--
-- Rakenne taululle `ci_sessions`
--

CREATE TABLE `ci_sessions` (
  `id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `data` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_groups`
--

CREATE TABLE `vrlv3_groups` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Vedos taulusta `vrlv3_groups`
--

INSERT INTO `vrlv3_groups` (`id`, `name`, `description`) VALUES
(1, 'admin', 'Administrator'),
(2, 'members', 'General User');

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_tunnukset`
--

CREATE TABLE `vrlv3_tunnukset` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(15) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `activation_selector` varchar(255) DEFAULT NULL,
  `activation_code` varchar(255) DEFAULT NULL,
  `forgotten_password_selector` varchar(255) DEFAULT NULL,
  `forgotten_password_code` varchar(255) DEFAULT NULL,
  `forgotten_password_time` int(11) UNSIGNED DEFAULT NULL,
  `remember_selector` varchar(255) DEFAULT NULL,
  `remember_code` varchar(255) DEFAULT NULL,
  `created_on` int(11) UNSIGNED NOT NULL,
  `last_login` int(11) UNSIGNED DEFAULT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `tunnus` int(5) UNSIGNED ZEROFILL NOT NULL,
  `nimimerkki` varchar(20) NOT NULL,
  `nayta_email` smallint(1) NOT NULL DEFAULT '0',
  `jaahylla` datetime NOT NULL,
  `frozen` int(1) NOT NULL,
  `reason` varchar(400) NOT NULL,
  `hyvaksyi` int(5) UNSIGNED ZEROFILL NOT NULL,
  `hyvaksytty` datetime NOT NULL,
  `laani` smallint(2) DEFAULT NULL,
  `syntymavuosi` date NOT NULL,
  `nayta_vuosi` smallint(1) NOT NULL DEFAULT '0',
  `nayta_laani` smallint(1) NOT NULL DEFAULT '0',
  `suositus` varchar(20) NOT NULL,
  `rekisteroitynyt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

INSERT INTO `vrlv3_tunnukset` (
`id`, `ip_address`, `username`, `password`, `salt`, `email`, `activation_selector`, `activation_code`, `forgotten_password_selector`, `forgotten_password_code`, `forgotten_password_time`, `remember_selector`, `remember_code`, `created_on`, `last_login`, `active`, `tunnus`, `nimimerkki`, `nayta_email`, `jaahylla`, `frozen`, `reason`, `hyvaksyi`, `hyvaksytty`, `laani`, `syntymavuosi`, `nayta_vuosi`, `nayta_laani`, `suositus`, `rekisteroitynyt`) 
VALUES (
1, '', '00000', '$2y$12$cICW4ptvxyjJpOvuHHKA2.ColzeKwEFe8p6qtiRoU2xEylbIg/ECO', NULL, 'admin@admin.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '1553943885', '1', '00000', 'Testimarsu', '0', '0000-00-00 00:00:00', '0', '', '00000', '0000-00-00 00:00:00', NULL, '0000-00-00', '0', '0', '', '0000-00-00 00:00:00');

--
-- Rakenne taululle `vrlv3_tunnukset_jonossa`
--

CREATE TABLE `vrlv3_tunnukset_jonossa` (
  `salasana` text NOT NULL,
  `nimimerkki` varchar(20) NOT NULL,
  `email` text NOT NULL,
  `rekisteroitynyt` datetime NOT NULL,
  `id` int(4) NOT NULL,
  `varmistus` text NOT NULL,
  `vahvistettu` smallint(1) NOT NULL DEFAULT '0',
  `ip` text NOT NULL,
  `kasitelty` datetime DEFAULT NULL,
  `kasittelija` int(5) UNSIGNED ZEROFILL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Hyväksyntää tai vahvistusta odottavat tunnukset';

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_tunnukset_kirjautumiset`
--

CREATE TABLE `vrlv3_tunnukset_kirjautumiset` (
  `id` int(11) NOT NULL,
  `tunnus` int(5) UNSIGNED ZEROFILL NOT NULL,
  `aika` datetime NOT NULL,
  `ip` text NOT NULL,
  `onnistuiko` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Onnistuneet ja tarvittaessa epäonnistuneet kirjautumisyritykset';

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_tunnukset_nimimerkit`
--

CREATE TABLE `vrlv3_tunnukset_nimimerkit` (
  `id` int(11) NOT NULL,
  `tunnus` int(5) UNSIGNED ZEROFILL NOT NULL,
  `nimimerkki` varchar(20) NOT NULL,
  `vaihtanut` datetime NOT NULL,
  `piilotettu` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Käyttäjien edelliset nimimerkit';

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_tunnukset_pikaviestit`
--

CREATE TABLE `vrlv3_tunnukset_pikaviestit` (
  `id` int(11) NOT NULL,
  `lahettaja` int(5) UNSIGNED ZEROFILL DEFAULT NULL,
  `vastaanottaja` int(5) UNSIGNED ZEROFILL NOT NULL,
  `aika` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `viesti` varchar(360) NOT NULL,
  `luettu` int(1) NOT NULL,
  `tarkea` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tunnusten pikaviestit';

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_tunnukset_yhteystiedot`
--

CREATE TABLE `vrlv3_tunnukset_yhteystiedot` (
  `id` int(11) NOT NULL,
  `tunnus` int(5) UNSIGNED ZEROFILL NOT NULL,
  `tyyppi` varchar(10) NOT NULL,
  `tieto` varchar(200) NOT NULL,
  `nayta` smallint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tunnusten yhteystiedot';

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_users_groups`
--

CREATE TABLE `vrlv3_users_groups` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `group_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Vedos taulusta `vrlv3_users_groups`
--

INSERT INTO `vrlv3_users_groups` (`id`, `user_id`, `group_id`) VALUES
(1, 1, 1),
(2, 1, 2);


-- Rakenne taululle `vrlv3_login_attempts`
--

CREATE TABLE `vrlv3_login_attempts` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(15) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Indexes for dumped tables
--

--
-- Indexes for table `ci_sessions`
--
ALTER TABLE `ci_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `last_activity_idx` (`last_activity`);

--
-- Indexes for table `vrlv3_groups`
--
ALTER TABLE `vrlv3_groups`
  ADD PRIMARY KEY (`id`);  
 

--
-- Indexes for table `vrlv3_tunnukset`
--
ALTER TABLE `vrlv3_tunnukset`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tunnus` (`tunnus`);
  
--
-- Indexes for table `vrlv3_login_attempts`
--
ALTER TABLE `vrlv3_login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vrlv3_tunnukset_jonossa`
--
ALTER TABLE `vrlv3_tunnukset_jonossa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kasittelija` (`kasittelija`);

--
-- Indexes for table `vrlv3_tunnukset_kirjautumiset`
--
ALTER TABLE `vrlv3_tunnukset_kirjautumiset`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vrlv3_tunnukset_nimimerkit`
--
ALTER TABLE `vrlv3_tunnukset_nimimerkit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tunnus` (`tunnus`);

--
-- Indexes for table `vrlv3_tunnukset_pikaviestit`
--
ALTER TABLE `vrlv3_tunnukset_pikaviestit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lahettaja` (`lahettaja`),
  ADD KEY `vastaanottaja` (`vastaanottaja`);

--
-- Indexes for table `vrlv3_tunnukset_yhteystiedot`
--
ALTER TABLE `vrlv3_tunnukset_yhteystiedot`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tunnus` (`tunnus`);

--
-- Indexes for table `vrlv3_users_groups`
--
ALTER TABLE `vrlv3_users_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uc_users_groups` (`user_id`,`group_id`),
  ADD KEY `fk_users_groups_users1_idx` (`user_id`),
  ADD KEY `fk_users_groups_groups1_idx` (`group_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `vrlv3_groups`
--
ALTER TABLE `vrlv3_groups`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vrlv3_tunnukset`
--
ALTER TABLE `vrlv3_tunnukset`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vrlv3_tunnukset_jonossa`
--
ALTER TABLE `vrlv3_tunnukset_jonossa`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vrlv3_tunnukset_kirjautumiset`
--
ALTER TABLE `vrlv3_tunnukset_kirjautumiset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vrlv3_tunnukset_nimimerkit`
--
ALTER TABLE `vrlv3_tunnukset_nimimerkit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vrlv3_tunnukset_pikaviestit`
--
ALTER TABLE `vrlv3_tunnukset_pikaviestit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vrlv3_tunnukset_yhteystiedot`
--
ALTER TABLE `vrlv3_tunnukset_yhteystiedot`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vrlv3_users_groups`
--
ALTER TABLE `vrlv3_users_groups`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
  
  
 --
-- AUTO_INCREMENT for table `vrlv3_login_attempts`
--
ALTER TABLE `vrlv3_login_attempts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

--
-- Rajoitteet vedostauluille
--

--
-- Rajoitteet taululle `vrlv3_tunnukset_jonossa`
--
ALTER TABLE `vrlv3_tunnukset_jonossa`
  ADD CONSTRAINT `vrlv3_tunnukset_jonossa_ibfk_1` FOREIGN KEY (`kasittelija`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_tunnukset_kirjautumiset`
--
ALTER TABLE `vrlv3_tunnukset_kirjautumiset`
  ADD CONSTRAINT `vrlv3_tunnukset_kirjautumiset_ibfk_1` FOREIGN KEY (`tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_tunnukset_nimimerkit`
--
ALTER TABLE `vrlv3_tunnukset_nimimerkit`
  ADD CONSTRAINT `vrlv3_tunnukset_nimimerkit_ibfk_1` FOREIGN KEY (`tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_tunnukset_pikaviestit`
--
ALTER TABLE `vrlv3_tunnukset_pikaviestit`
  ADD CONSTRAINT `tunnukset_pikaviestit_ibfk_1` FOREIGN KEY (`vastaanottaja`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tunnukset_pikaviestit_ibfk_2` FOREIGN KEY (`lahettaja`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_tunnukset_yhteystiedot`
--
ALTER TABLE `vrlv3_tunnukset_yhteystiedot`
  ADD CONSTRAINT `vrlv3_tunnukset_yhteystiedot_ibfk_1` FOREIGN KEY (`tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_users_groups`
--
ALTER TABLE `vrlv3_users_groups`
  ADD CONSTRAINT `fk_users_groups_groups1` FOREIGN KEY (`group_id`) REFERENCES `vrlv3_groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_users_groups_users1` FOREIGN KEY (`user_id`) REFERENCES `vrlv3_tunnukset` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
