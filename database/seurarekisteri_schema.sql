-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 07.10.2015 klo 21:54
-- Palvelimen versio: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `vrlv3`
--

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_seurarekisteri`
--

CREATE TABLE IF NOT EXISTS `vrlv3_seurarekisteri` (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `slyh` varchar(10) NOT NULL,
  `nimi` text NOT NULL,
  `kuvaus` text NOT NULL,
  `url` text NOT NULL,
  `perustettu` datetime NOT NULL,
  `lopettanut` int(1) NOT NULL,
  `lopetti_pvm` datetime DEFAULT NULL,
  `lopetti_tunnus` int(5) unsigned zerofill DEFAULT NULL,
  `hyvaksyi` int(5) unsigned zerofill NOT NULL,
  `hyvaksytty` datetime NOT NULL,
  PRIMARY KEY (`sid`),
  KEY `hyvaksyi` (`hyvaksyi`),
  KEY `lopetti_tunnus` (`lopetti_tunnus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_seurarekisteri_jasenet`
--

CREATE TABLE IF NOT EXISTS `vrlv3_seurarekisteri_jasenet` (
  `tunnus` int(5) unsigned zerofill NOT NULL,
  `sid` int(11) NOT NULL,
  `aika` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hyvaksytty` int(1) NOT NULL DEFAULT '0',
  `varsinainen` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tunnus`,`sid`),
  KEY `sid` (`sid`),
  KEY `tunnus` (`tunnus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_seurarekisteri_jasentallit`
--

CREATE TABLE IF NOT EXISTS `vrlv3_seurarekisteri_jasentallit` (
  `sid` int(11) NOT NULL,
  `tnro` varchar(8) CHARACTER SET utf8 NOT NULL,
  `aika` datetime NOT NULL,
  `hyvaksytty` int(1) NOT NULL,
  PRIMARY KEY (`sid`,`tnro`),
  KEY `tnro` (`tnro`),
  KEY `sid` (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_seurarekisteri_jonossa`
--

CREATE TABLE IF NOT EXISTS `vrlv3_seurarekisteri_jonossa` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `slyh` varchar(10) NOT NULL,
  `nimi` text NOT NULL,
  `kuvaus` text NOT NULL,
  `url` text NOT NULL,
  `kasitelty` datetime DEFAULT NULL,
  `kasittelija` int(5) unsigned zerofill DEFAULT NULL,
  `lisatty` datetime NOT NULL,
  `lisaaja` int(5) unsigned zerofill NOT NULL,
  PRIMARY KEY (`id`),
  KEY `kasittelija` (`kasittelija`),
  KEY `lisaaja` (`lisaaja`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_seurarekisteri_omistajamuutokset`
--

CREATE TABLE IF NOT EXISTS `vrlv3_seurarekisteri_omistajamuutokset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) NOT NULL,
  `omistaja` int(5) unsigned zerofill NOT NULL,
  `muokkasi` int(5) unsigned zerofill NOT NULL,
  `aika` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sid` (`sid`),
  KEY `omistaja` (`omistaja`),
  KEY `muokkasi` (`muokkasi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_seurarekisteri_omistajat`
--

CREATE TABLE IF NOT EXISTS `vrlv3_seurarekisteri_omistajat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) NOT NULL,
  `omistaja` int(5) unsigned zerofill NOT NULL,
  `taso` smallint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sid` (`sid`),
  KEY `omistaja` (`omistaja`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Rajoitteet vedostauluille
--

--
-- Rajoitteet taululle `vrlv3_seurarekisteri`
--
ALTER TABLE `vrlv3_seurarekisteri`
  ADD CONSTRAINT `vrlv3_seurarekisteri_ibfk_4` FOREIGN KEY (`lopetti_tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_seurarekisteri_ibfk_1` FOREIGN KEY (`hyvaksyi`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE;


--
-- Rajoitteet taululle `vrlv3_seurarekisteri_jasenet`
--
ALTER TABLE `vrlv3_seurarekisteri_jasenet`
  ADD CONSTRAINT `vrlv3_seurarekisteri_jasenet_ibfk_4` FOREIGN KEY (`tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_seurarekisteri_jasenet_ibfk_2` FOREIGN KEY (`sid`) REFERENCES `vrlv3_seurarekisteri` (`sid`),
  ADD CONSTRAINT `vrlv3_seurarekisteri_jasenet_ibfk_3` FOREIGN KEY (`tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_seurarekisteri_jasentallit`
--
ALTER TABLE `vrlv3_seurarekisteri_jasentallit`
  ADD CONSTRAINT `vrlv3_seurarekisteri_jasentallit_ibfk_2` FOREIGN KEY (`tnro`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_seurarekisteri_jasentallit_ibfk_1` FOREIGN KEY (`sid`) REFERENCES `vrlv3_seurarekisteri` (`sid`);

--
-- Rajoitteet taululle `vrlv3_seurarekisteri_jonossa`
--
ALTER TABLE `vrlv3_seurarekisteri_jonossa`
  ADD CONSTRAINT `vrlv3_seurarekisteri_jonossa_ibfk_3` FOREIGN KEY (`lisaaja`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_seurarekisteri_jonossa_ibfk_1` FOREIGN KEY (`kasittelija`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_seurarekisteri_omistajamuutokset`
--
ALTER TABLE `vrlv3_seurarekisteri_omistajamuutokset`
  ADD CONSTRAINT `vrlv3_seurarekisteri_omistajamuutokset_ibfk_3` FOREIGN KEY (`muokkasi`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_seurarekisteri_omistajamuutokset_ibfk_1` FOREIGN KEY (`sid`) REFERENCES `vrlv3_seurarekisteri` (`sid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_seurarekisteri_omistajamuutokset_ibfk_2` FOREIGN KEY (`omistaja`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_seurarekisteri_omistajat`
--
ALTER TABLE `vrlv3_seurarekisteri_omistajat`
  ADD CONSTRAINT `vrlv3_seurarekisteri_omistajat_ibfk_1` FOREIGN KEY (`sid`) REFERENCES `vrlv3_seurarekisteri` (`sid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_seurarekisteri_omistajat_ibfk_2` FOREIGN KEY (`omistaja`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE;
