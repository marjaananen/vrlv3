-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 07.10.2015 klo 21:12
-- Palvelimen versio: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `vrlv3`
--

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_kasvattajanimet`
--

CREATE TABLE IF NOT EXISTS `vrlv3_kasvattajanimet` (
  `kid` int(11) NOT NULL AUTO_INCREMENT,
  `rekisteroity` datetime NOT NULL DEFAULT '2010-01-01 00:00:00',
  `kasvattajanimi` text CHARACTER SET latin1 NOT NULL,
  `tallinid` varchar(8) DEFAULT NULL,
  `tila` tinyint(1) NOT NULL DEFAULT '0',
  `hyvaksyi` int(5) unsigned zerofill NOT NULL,
  `hyvaksytty` datetime NOT NULL,
  PRIMARY KEY (`kid`),
  KEY `tallinid` (`tallinid`),
  KEY `hyvaksyi` (`hyvaksyi`),
  FULLTEXT KEY `kasvattajanimi` (`kasvattajanimi`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_kasvattajanimet_jono`
--

CREATE TABLE IF NOT EXISTS `vrlv3_kasvattajanimet_jonossa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kasvattajanimi` text CHARACTER SET latin1 NOT NULL,
  `lisatty` datetime NOT NULL,
  `lisaaja` int(5) unsigned zerofill NOT NULL,
  `tallinid` varchar(8) DEFAULT NULL,
  `kasvatit` text CHARACTER SET latin1 NOT NULL,
  `rotu` smallint(3) NOT NULL,
  `kasitelty` datetime DEFAULT NULL,
  `kasittelija` int(5) unsigned zerofill DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rotu` (`rotu`),
  KEY `tallinid` (`tallinid`),
  KEY `lisaaja` (`lisaaja`),
  KEY `kasittelija` (`kasittelija`)
  FULLTEXT KEY `kasvattajanimi` (`kasvattajanimi`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_kasvattajanimet_omistajat`
--

CREATE TABLE IF NOT EXISTS `vrlv3_kasvattajanimet_omistajat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kid` int(11) NOT NULL,
  `omistaja` int(5) unsigned zerofill NOT NULL,
  `taso` smallint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `kid` (`kid`),
  KEY `omistaja` (`omistaja`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_kasvattajanimet_rodut`
--

CREATE TABLE IF NOT EXISTS `vrlv3_kasvattajanimet_rodut` (
  `id` int(11) NOT NULL,
  `kid` int(11) NOT NULL,
  `rotunro` smallint(3) NOT NULL,
  `rekisteroity` datetime NOT NULL DEFAULT '2010-01-01 00:00:00',
  `hyvaksyi` int(5) unsigned zerofill NOT NULL,
  `hyvaksytty` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `hyvaksyi` (`hyvaksyi`),
  KEY `rotunro` (`rotunro`),
  KEY `kid` (`kid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_kasvattajanimet_rotujono`
--

CREATE TABLE IF NOT EXISTS `vrlv3_kasvattajanimet_rodut_jonossa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kid` int(11) NOT NULL,
  `rotu` smallint(3) NOT NULL,
  `lisatty` datetime NOT NULL,
  `lisaaja` int(5) unsigned zerofill NOT NULL,
  `kasvatit` text CHARACTER SET latin1 NOT NULL,
  `kasitelty` datetime DEFAULT NULL,
  `kasittelija` int(5) unsigned zerofill DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kid` (`kid`),
  KEY `lisaaja` (`lisaaja`)
  KEY `kasittelija` (`kasittelija`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Rajoitteet vedostauluille
--

--
-- Rajoitteet taululle `vrlv3_kasvattajanimet`
--
ALTER TABLE `vrlv3_kasvattajanimet`
  ADD CONSTRAINT `vrlv3_kasvattajanimet_ibfk_2` FOREIGN KEY (`hyvaksyi`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_kasvattajanimet_ibfk_1` FOREIGN KEY (`tallinid`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_kasvattajanimet_jono`
--
ALTER TABLE `vrlv3_kasvattajanimet_jonossa`
  ADD CONSTRAINT `vrlv3_kasvattajanimet_jono_ibfk_4` FOREIGN KEY (`kasittelija`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_kasvattajanimet_jono_ibfk_3` FOREIGN KEY (`lisaaja`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_kasvattajanimet_jono_ibfk_1` FOREIGN KEY (`tallinid`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_kasvattajanimet_jono_ibfk_2` FOREIGN KEY (`rotu`) REFERENCES `vrlv3_lista_rodut` (`rotunro`) ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_kasvattajanimet_omistajat`
--
ALTER TABLE `vrlv3_kasvattajanimet_omistajat`
  ADD CONSTRAINT `vrlv3_kasvattajanimet_omistajat_ibfk_2` FOREIGN KEY (`omistaja`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_kasvattajanimet_omistajat_ibfk_1` FOREIGN KEY (`knro`) REFERENCES `vrlv3_kasvattajanimet` (`kid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_kasvattajanimet_rodut`
--
ALTER TABLE `vrlv3_kasvattajanimet_rodut`
  ADD CONSTRAINT `vrlv3_kasvattajanimet_rodut_ibfk_4` FOREIGN KEY (`kid`) REFERENCES `vrlv3_kasvattajanimet` (`kid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_kasvattajanimet_rodut_ibfk_2` FOREIGN KEY (`rotunro`) REFERENCES `vrlv3_lista_rodut` (`rotunro`) ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_kasvattajanimet_rodut_ibfk_3` FOREIGN KEY (`hyvaksyi`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_kasvattajanimet_rotujono`
--
ALTER TABLE `vrlv3_kasvattajanimet_rodut_jonossa`
  ADD CONSTRAINT `vrlv3_kasvattajanimet_rotujono_ibfk_3` FOREIGN KEY (`kasittelija`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_kasvattajanimet_rotujono_ibfk_2` FOREIGN KEY (`lisaaja`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_kasvattajanimet_rotujono_ibfk_1` FOREIGN KEY (`kid`) REFERENCES `vrlv3_kasvattajanimet` (`kid`) ON DELETE CASCADE ON UPDATE CASCADE;
