-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 30.09.2015 klo 21:03
-- Palvelimen versio: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `vrlv3`
--

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_tiedotukset`
--

CREATE TABLE IF NOT EXISTS `vrlv3_tiedotukset` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `aika` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `otsikko` text CHARACTER SET latin1 NOT NULL,
  `teksti` text CHARACTER SET latin1 NOT NULL,
  `lahettaja` int(5) unsigned zerofill NOT NULL,
  `julkinen` int(1) NOT NULL DEFAULT '1',
  `muokkaaja` int(5) unsigned zerofill NOT NULL COMMENT 'Viimeisin muokkaaja',
  `muokpvm` datetime NOT NULL,
  PRIMARY KEY (`tid`),
  KEY `muokkaaja` (`muokkaaja`),
  KEY `lahettaja` (`lahettaja`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_tiedotukset_kategoriat`
--

CREATE TABLE IF NOT EXISTS `vrlv3_tiedotukset_kategoriat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `kid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `kid` (`kid`),
  KEY `kid_2` (`kid`),
  KEY `tid` (`tid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Rajoitteet vedostauluille
--

--
-- Rajoitteet taululle `vrlv3_tiedotukset`
--
ALTER TABLE `vrlv3_tiedotukset`
  ADD CONSTRAINT `vrlv3_tiedotukset_ibfk_1` FOREIGN KEY (`muokkaaja`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_tiedotukset_ibfk_2` FOREIGN KEY (`lahettaja`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_tiedotukset_kategoriat`
--
ALTER TABLE `vrlv3_tiedotukset_kategoriat`
  ADD CONSTRAINT `vrlv3_tiedotukset_kategoriat_ibfk_1` FOREIGN KEY (`tid`) REFERENCES `vrlv3_tiedotukset` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_tiedotukset_kategoriat_ibfk_2` FOREIGN KEY (`kid`) REFERENCES `vrlv3_lista_tiedotuskategoriat` (`kid`);
