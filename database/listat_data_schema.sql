-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 22.07.2015 klo 12:00
-- Palvelimen versio: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `vrlv3`
--

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_lista_maakunnat`
--

DROP TABLE IF EXISTS `vrlv3_lista_maakunnat`;
CREATE TABLE `vrlv3_lista_maakunnat` (
  `id` smallint(2) NOT NULL AUTO_INCREMENT,
  `maakunta` tinytext COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=22 ;

--
-- Vedos taulusta `vrlv3_lista_maakunnat`
--

INSERT INTO `vrlv3_lista_maakunnat` (`id`, `maakunta`) VALUES
(0, 'Ei saatavilla'),
(1, 'Ahvenanmaa'),
(2, 'Etelä-Karjala'),
(3, 'Etelä-Pohjanmaa'),
(4, 'Etelä-Savo'),
(5, 'Itä-Uusimaa'),
(6, 'Kainuu'),
(7, 'Kanta-Häme'),
(8, 'Keski-Pohjanmaa'),
(9, 'Keski-Suomi'),
(10, 'Kymenlaakso'),
(11, 'Lappi'),
(12, 'Pirkanmaa'),
(13, 'Pohjanmaa'),
(14, 'Pohjois-Karjala'),
(15, 'Pohjois-Pohjanmaa'),
(16, 'Pohjois-Savo'),
(17, 'Päijät-Häme'),
(18, 'Satakunta'),
(19, 'Uusimaa'),
(20, 'Varsinais-Suomi'),
(21, 'Ulkomaat');


-- 
-- Rakenne taululle `vrlv3_lista_tallikategoriat`
-- 

CREATE TABLE IF NOT EXISTS `vrlv3_lista_tallikategoriat` (
  `kat` smallint(2) NOT NULL auto_increment,
  `kategoria` varchar(20) character set utf8 NOT NULL,
  `katelyh` varchar(3) character set utf8 NOT NULL,
  `katnro` varchar(4) collate utf8_swedish_ci NOT NULL,
  PRIMARY KEY  (`kat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=14 ;

-- 
-- Vedostetaan dataa taulusta `lista_tallikategoriat`
-- 

INSERT INTO `vrlv3_lista_tallikategoriat` (`kat`, `kategoria`, `katelyh`, `katnro`) VALUES 
(1, 'ratsastuskoulu', 'RK', 'KAT1'),
(2, 'siittola', 'ST', 'KAT2'),
(3, 'kilpailukeskus', 'KK', 'KAT3'),
(4, 'valjakkotalli', 'VT', 'KAT4'),
(5, 'ravitalli', 'RT', 'KAT4'),
(6, 'laukkatalli', 'LK', 'KAT4'),
(7, 'westerntalli', 'WT', 'KAT4'),
(8, 'myyntitalli', 'MT', 'KAT5'),
(9, 'oriasema', 'OA', 'KAT6'),
(10, 'yksityistalli', 'YT', 'KAT7'),
(11, 'muu kilpatalli', 'KT', 'KAT4'),
(12, 'tamma-asema', 'TA', 'KAT6'),
(13, 'harrastetalli', 'HT', 'KAT0');