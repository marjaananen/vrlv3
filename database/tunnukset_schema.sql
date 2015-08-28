-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 21.07.2015 klo 20:35
-- Palvelimen versio: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `vrlv3`
--

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_tunnukset`
--
-- Luotu: 21.07.2015 klo 18:19
--

CREATE TABLE IF NOT EXISTS `vrlv3_tunnukset` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(15) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `activation_code` varchar(40) DEFAULT NULL,
  `forgotten_password_code` varchar(40) DEFAULT NULL,
  `forgotten_password_time` int(11) unsigned DEFAULT NULL,
  `remember_code` varchar(40) DEFAULT NULL,
  `created_on` int(11) unsigned NOT NULL,
  `last_login` int(11) unsigned DEFAULT NULL,
  `active` tinyint(1) unsigned DEFAULT NULL,
  `tunnus` int(5) unsigned zerofill NOT NULL,
  `nimimerkki` varchar(20) NOT NULL,
  `nayta_email` smallint(1) NOT NULL DEFAULT '0',
  `laani` smallint(2),
  `syntymavuosi` date NOT NULL,
  `nayta_vuosilaani` smallint(1) NOT NULL DEFAULT '0',
  `jaahylla` datetime NOT NULL,
  `frozen` int(1) NOT NULL,
  `reason` varchar(400) NOT NULL,
  `hyvaksyi` int(5) unsigned zerofill NOT NULL,
  `hyvaksytty` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `tunnus` (`tunnus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `vrlv3_tunnukset` (`id`, `ip_address`, `username`, `password`, `salt`, `email`, `activation_code`, `forgotten_password_code`, `created_on`, `last_login`, `active`, `tunnus`, `nimimerkki`, `nayta_email`, `laani`, `syntymavuosi`, `nayta_vuosilaani`, `jaahylla`, `frozen`, `reason`, `hyvaksyi`, `hyvaksytty`) VALUES
     ('1','127.0.0.1','administrator','$2a$07$SeBknntpZror9uyftVopmu61qg0ms8Qv1yV6FG.kQOSM.9QhmTo36','','admin@admin.com','',NULL,'1268889823','1268889823','1', '0','administrator','0','1','1970-01-01','0', '1000-01-01 00:00:00', '0', '-', '0', '1000-01-01 00:00:00');

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_tunnukset_jonossa`
--
-- Luotu: 21.07.2015 klo 18:21
--

CREATE TABLE IF NOT EXISTS `vrlv3_tunnukset_jonossa` (
  `salasana` text NOT NULL,
  `nimimerkki` varchar(20) NOT NULL,
  `email` text NOT NULL,
  `syntymavuosi` date NOT NULL,
  `rekisteroitynyt` datetime NOT NULL,
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `varmistus` text NOT NULL,
  `sijainti` smallint(2) NOT NULL,
  `vahvistettu` smallint(1) NOT NULL DEFAULT '0',
  `ip` text NOT NULL,
  `kasitelty` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Hyväksyntää tai vahvistusta odottavat tunnukset';

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_tunnukset_kirjautumiset`
--
-- Luotu: 21.07.2015 klo 18:30
--

CREATE TABLE IF NOT EXISTS `vrlv3_tunnukset_kirjautumiset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tunnus` int(5) unsigned zerofill NOT NULL,
  `aika` datetime NOT NULL,
  `ip` text NOT NULL,
  `onnistuiko` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `tunnus` (`tunnus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Onnistuneet ja tarvittaessa epäonnistuneet kirjautumisyritykset';

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_tunnukset_nimimerkit`
--
-- Luotu: 21.07.2015 klo 18:30
--

CREATE TABLE IF NOT EXISTS `vrlv3_tunnukset_nimimerkit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tunnus` int(5) unsigned zerofill NOT NULL,
  `nimimerkki` varchar(20) NOT NULL,
  `vaihtanut` datetime NOT NULL,
  `piilotettu` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tunnus` (`tunnus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Käyttäjien edelliset nimimerkit';

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_tunnukset_yhteystiedot`
--
-- Luotu: 21.07.2015 klo 18:27
--

CREATE TABLE IF NOT EXISTS `vrlv3_tunnukset_yhteystiedot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tunnus` int(5) unsigned zerofill NOT NULL,
  `tyyppi` varchar(10) NOT NULL,
  `tieto` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tunnus` (`tunnus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tunnusten yhteystiedot';



--
-- Rakenne taululle `vrlv3_tunnukset_pikaviestit`
--

CREATE TABLE IF NOT EXISTS `vrlv3_tunnukset_pikaviestit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lahettaja` int(5) unsigned zerofill DEFAULT NULL,
  `vastaanottaja` int(5) unsigned zerofill NOT NULL,
  `aika` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `viesti` varchar(360) NOT NULL,
  `luettu` int(1) NOT NULL,
  `tarkea` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `lahettaja` (`lahettaja`),
  KEY `vastaanottaja` (`vastaanottaja`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Tunnusten pikaviestit';

--
-- Rajoitteet vedostauluille
--

--
-- Rajoitteet taululle `tunnukset_pikaviestit`
--
ALTER TABLE `vrlv3_tunnukset_pikaviestit`
  ADD CONSTRAINT `tunnukset_pikaviestit_ibfk_2` FOREIGN KEY (`lahettaja`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `tunnukset_pikaviestit_ibfk_1` FOREIGN KEY (`vastaanottaja`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE CASCADE ON UPDATE CASCADE;


--
--
-- Rajoitteet taululle `vrlv3_tunnukset`
--
 ALTER TABLE `vrlv3_tunnukset` 
  ADD CONSTRAINT `vrlv3_tunnukset_ibfk_1` FOREIGN KEY (`laani`) REFERENCES `vrlv3`.`vrlv3_lista_maakunnat`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

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
-- Rajoitteet taululle `vrlv3_tunnukset_yhteystiedot`
--
ALTER TABLE `vrlv3_tunnukset_yhteystiedot`
  ADD CONSTRAINT `vrlv3_tunnukset_yhteystiedot_ibfk_1` FOREIGN KEY (`tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE CASCADE ON UPDATE CASCADE;
  
 
