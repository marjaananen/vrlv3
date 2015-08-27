-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 28.07.2015 klo 19:54
-- Palvelimen versio: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `vrlv3`
--

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_tallirekisteri`
--

CREATE TABLE IF NOT EXISTS `vrlv3_tallirekisteri` (
  `tnro` varchar(8) NOT NULL,
  `nimi` text NOT NULL,
  `url` text NOT NULL,
  `kuvaus` text NOT NULL,
  `perustettu` datetime NOT NULL,
  `hyvaksyi` int(5) unsigned zerofill NOT NULL,
  `piilotettu` tinyint(1) NOT NULL DEFAULT '0',
  `hyvaksytty` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lopettanut` int(1) NOT NULL DEFAULT '0',
  `lopetti_pvm` datetime NOT NULL,
  `lopetti_tunnus` int(5) unsigned zerofill NOT NULL,
  PRIMARY KEY (`tnro`),
  KEY `lopetti_tunnus` (`lopetti_tunnus`),
  KEY `hyvaksyi` (`hyvaksyi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_tallirekisteri_jonossa`
--

CREATE TABLE IF NOT EXISTS `vrlv3_tallirekisteri_jonossa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nimi` text NOT NULL,
  `lyhenne` varchar(4) NOT NULL,
  `url` text NOT NULL,
  `kuvaus` text NOT NULL,
  `lisatty` datetime NOT NULL,
  `lisaaja` smallint(5) unsigned zerofill NOT NULL,
  `kategoria` smallint(2) NOT NULL,
  `kasitelty` datetime DEFAULT NULL,
  `kasittelija` int(5) unsigned zerofill DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `kategoria` (`kategoria`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5660 ;

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_tallirekisteri_kategoriat`
--

CREATE TABLE IF NOT EXISTS `vrlv3_tallirekisteri_kategoriat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tnro` varchar(8) NOT NULL,
  `kategoria` smallint(2) NOT NULL,
  `anoi` int(5) unsigned zerofill NOT NULL,
  `tarkistaja` smallint(5) unsigned zerofill NOT NULL,
  `lisatty` datetime NOT NULL,
  `tila` int(1) NOT NULL DEFAULT '0',
  `kasitelty` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tnro` (`tnro`),
  KEY `tnro_2` (`tnro`),
  KEY `kategoria` (`kategoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_tallirekisteri_omistajamuutokset`
--

CREATE TABLE IF NOT EXISTS `vrlv3_tallirekisteri_omistajamuutokset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tnro` varchar(8) NOT NULL,
  `omistaja` int(5) unsigned zerofill NOT NULL,
  `muokkasi` smallint(5) unsigned zerofill NOT NULL,
  `aika` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tnro` (`tnro`),
  KEY `omistaja` (`omistaja`),
  KEY `tnro_2` (`tnro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_tallirekisteri_omistajat`
--

CREATE TABLE IF NOT EXISTS `vrlv3_tallirekisteri_omistajat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tnro` varchar(8) NOT NULL,
  `omistaja` int(5) unsigned zerofill NOT NULL,
  `taso` smallint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tnro` (`tnro`),
  KEY `omistaja` (`omistaja`),
  KEY `tnro_2` (`tnro`),
  KEY `omistaja_2` (`omistaja`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_tallirekisteri_paivitetty`
--

CREATE TABLE IF NOT EXISTS `vrlv3_tallirekisteri_paivitetty` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tnro` varchar(8) NOT NULL,
  `paivitti` int(5) unsigned zerofill NOT NULL,
  `aika` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tnro` (`tnro`),
  KEY `tnro_2` (`tnro`),
  KEY `paivitti` (`paivitti`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_tallirekisteri_yesno`
--

CREATE TABLE IF NOT EXISTS `vrlv3_tallirekisteri_yesno` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tunnus` int(5) unsigned zerofill NOT NULL,
  `aani` smallint(1) NOT NULL,
  `tnro` varchar(8) CHARACTER SET utf8 NOT NULL,
  `aika` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tunnus_2` (`tunnus`,`tnro`),
  KEY `tnro` (`tnro`),
  KEY `tunnus` (`tunnus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_tallirekisteri_yhteistyo`
--

CREATE TABLE IF NOT EXISTS `vrlv3_tallirekisteri_yhteistyo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `t1` varchar(8) NOT NULL,
  `t2` varchar(8) NOT NULL,
  `aika` datetime NOT NULL,
  `tila` smallint(1) NOT NULL,
  `pyysi` int(5) unsigned zerofill NOT NULL,
  `hyvaksyi` int(5) unsigned zerofill NOT NULL,
  PRIMARY KEY (`id`),
  KEY `t1` (`t1`),
  KEY `t2` (`t2`),
  KEY `pyysi` (`pyysi`),
  KEY `hyvaksyi` (`hyvaksyi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Rajoitteet vedostauluille
--

--
-- Rajoitteet taululle `vrlv3_tallirekisteri`
--
ALTER TABLE `vrlv3_tallirekisteri`
  ADD CONSTRAINT `vrlv3_tallirekisteri_ibfk_1` FOREIGN KEY (`hyvaksyi`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_tallirekisteri_ibfk_2` FOREIGN KEY (`lopetti_tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_tallirekisteri_jonossa`
--
ALTER TABLE `vrlv3_tallirekisteri_jonossa`
  ADD CONSTRAINT `vrlv3_tallirekisteri_jonossa_ibfk_1` FOREIGN KEY (`kategoria`) REFERENCES `vrlv3_lista_tallikategoriat` (`kat`) ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_tallirekisteri_kategoriat`
--
ALTER TABLE `vrlv3_tallirekisteri_kategoriat`
  ADD CONSTRAINT `vrlv3_tallirekisteri_kategoriat_ibfk_2` FOREIGN KEY (`kategoria`) REFERENCES `vrlv3_lista_tallikategoriat` (`kat`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_tallirekisteri_kategoriat_ibfk_1` FOREIGN KEY (`tnro`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_tallirekisteri_omistajamuutokset`
--
ALTER TABLE `vrlv3_tallirekisteri_omistajamuutokset`
  ADD CONSTRAINT `vrlv3_tallirekisteri_omistajamuutokset_ibfk_1` FOREIGN KEY (`tnro`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_tallirekisteri_omistajamuutokset_ibfk_2` FOREIGN KEY (`omistaja`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_tallirekisteri_omistajat`
--
ALTER TABLE `vrlv3_tallirekisteri_omistajat`
  ADD CONSTRAINT `vrlv3_tallirekisteri_omistajat_ibfk_1` FOREIGN KEY (`tnro`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_tallirekisteri_omistajat_ibfk_2` FOREIGN KEY (`omistaja`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_tallirekisteri_paivitetty`
--
ALTER TABLE `vrlv3_tallirekisteri_paivitetty`
  ADD CONSTRAINT `vrlv3_tallirekisteri_paivitetty_ibfk_1` FOREIGN KEY (`tnro`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_tallirekisteri_paivitetty_ibfk_2` FOREIGN KEY (`paivitti`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_tallirekisteri_yesno`
--
ALTER TABLE `vrlv3_tallirekisteri_yesno`
  ADD CONSTRAINT `vrlv3_tallirekisteri_yesno_ibfk_1` FOREIGN KEY (`tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_tallirekisteri_yesno_ibfk_2` FOREIGN KEY (`tnro`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_tallirekisteri_yhteistyo`
--
ALTER TABLE `vrlv3_tallirekisteri_yhteistyo`
  ADD CONSTRAINT `vrlv3_tallirekisteri_yhteistyo_ibfk_1` FOREIGN KEY (`t1`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_tallirekisteri_yhteistyo_ibfk_2` FOREIGN KEY (`t2`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_tallirekisteri_yhteistyo_ibfk_3` FOREIGN KEY (`pyysi`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_tallirekisteri_yhteistyo_ibfk_4` FOREIGN KEY (`hyvaksyi`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
