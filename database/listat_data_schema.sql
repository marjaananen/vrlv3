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
